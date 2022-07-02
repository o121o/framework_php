<?php

/**
 * 解析URL
 */
class router
{
	/**
	 * 執行 router
	 * @param string $basePath 網站基礎路徑
	 */
	public static function run($basePath = '/')
	{
		$param = array();
		$config = config::settingArray('router');
		$targetModule = isset($config['defaultModule']) ? $config['defaultModule'] : 'index';

		$url = parse_url($_SERVER['REQUEST_URI']);
		$path = isset($url['path']) ? $url['path'] : '/';

		if ($basePath && $basePath != '/') {
			$path = mb_ereg_replace('^(' . $basePath . ')', '', $path);
		}

		$param = explode('/', $path);
		array_shift($param);
		$module = array_shift($param);

		if ($module && is_dir(WEBROOT . '/' . $module)) {
			$targetModule = $module;
		}

		$targetModule::router($param);
	}
}
