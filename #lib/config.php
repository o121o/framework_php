<?php

/**
 * 讀取設定檔
 */
class config
{
	/**
	 * @param string $target 讀取目標
	 * @param string $targetFile 設定檔名稱
	 */
	static function settingArray(string $target, string $targetFile = 'config')
	{
		static $config = null;
		if (!isset($config)) {
			$config = parse_ini_file('#' . $targetFile . '.php', true);
		}

		if (!isset($config[$target])) {
			$config[$target] = false;
		}

		return $config[$target];
	}
}
