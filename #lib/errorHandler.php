<?php

/**
 * 錯誤處理
 */
class errorHandler
{
	static $config = null;

	/**
	 * 初始化
	 */
	public static function init()
	{
		if (!isset(self::$config)) {
			self::$config = config::settingArray('error');

			if (!isset(self::$config['dir'])) {
				self::$config['dir'] = '/error';
			}
			if (!isset(self::$config['base'])) {
				self::$config['base'] = 'WEBROOT';
			}

			if (defined(self::$config['base'])) {
				self::$config['base'] = constant(self::$config['base']);
			}
		}

		if (!is_dir(self::$config['base'] . self::$config['dir'])) {
			mkdir(self::$config['base'] . self::$config['dir']);
		}
	}

	/**
	 * 寫入錯誤記錄檔
	 * @param string $eMessage 錯誤訊息
	 * @param string $level 寫入等級
	 *  - app 應用層級
	 *  - sys 系統層級
	 */
	static function writeError(string $eMessage = '', string $level = 'app')
	{
		self::init();
		$errorInfo = error_get_last();
		$fileBaseName = isset(self::$config[$level]) ? self::$config[$level] : '';
		$errorFile = fopen(self::$config['base'] . self::$config['dir'] . '/' . $fileBaseName . date('Y-m') . '.log', 'a+');
		$eTrace = '';
		foreach (debug_backtrace() as $key => $errorTrace) {
			$eTrace .= '[' . $key . '] => ' . $errorTrace['file'] . ' : ' . (isset($errorTrace['line']) ? $errorTrace['line'] : '') . ' : ' . (isset($errorTrace['function']) ? $errorTrace['function'] : '') . PHP_EOL;
		}
		fwrite($errorFile, '[' . date('Y/m/d H:i:s') . '] ' . $eMessage . PHP_EOL . 'file:' . $errorInfo['file'] . ':' . $errorInfo['line'] . ' message: ' . $errorInfo['message'] . PHP_EOL . $eTrace . PHP_EOL);
	}
}
