<?php

/**
 * 地區相關
 */
class land extends factory
{
	private $factory = null;

	private $config;

	/**
	 * 階層
	 */
	public static $level = array();

	private static function init(): void
	{
		if (!isset(self::$factory)) {
			$config = config::settingArray(__CLASS__);
			self::$factory = parent::__construct(new DB($config['DB']));
			self::$level = explode(',', $config['level']);
			unset($config['DB']);
			unset($config['level']);
			self::$config = $config;
		}
	}

	/**
	 * 搜尋
	 * @param string $target 搜尋目標
	 * @param array $data 篩選資料
	 */
	public static function filter(string $target, $data = array()): array
	{
		self::init();
		$result = array();
		$col = array('id', $target);

		if (isset(self::$config[$target . '.sort'])) {
			$col[] = self::$config[$target . '.sort'];

			if (isset(self::$config[self::$config[$target . '.top'] . 'top'])) {
				$col[] = self::$config[$target . '.top'];
			}
	
			if (!isset($data['disabled'])) {
				$data['disabled'] = 0;
			}
	
			foreach (self::$factory->select($target, $col, $data) as $row) {
				$result[$row['id']] = $row;
			}
	
			uasort($result, function ($a, $b) use ($target) {
				return $a[self::$config[$target . '.sort']] > $b[self::$config[$target . '.sort']];
			});
		}

		return $result;
	}
}
