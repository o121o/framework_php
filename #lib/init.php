<?php
// 啟用嚴格模式
declare(strict_types=1);

//根目路定義
define('WEBROOT', dirname(__DIR__));

define('DOMAIN', $_SERVER['SERVER_NAME']);

// 錯誤顯示
// error_reporting(E_ALL);
// ini_set('display_errors', 'ON');

//快取設定
header('Cache-Control: no-cache, must-revalidate');

//iframe 禁止
header('X-Frame-Options: DENY');

//頁面編碼設定
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

//時區設定
date_default_timezone_set('Asia/Taipei');

//錯誤處理
register_shutdown_function(function () {
	$errorInfo = error_get_last();
	if (isset($errorInfo['type']) && $errorInfo['type'] != 8) {
		$errorFile = fopen(WEBROOT . 'error/system-' . date('Y-m') . '.log', 'a+');
		$eTrace = '';
		foreach (debug_backtrace() as $key => $errorTrace) {
			$eTrace .= '[' . $key . '] => ' . (isset($errorTrace['file']) ? $errorTrace['file'] : '') . ' : ' . (isset($errorTrace['line']) ? $errorTrace['line'] : '') . ' : ' . (isset($errorTrace['function']) ? $errorTrace['function'] : '') . PHP_EOL;
		}
		fwrite($errorFile, '[' . date('Y/m/d H:i:s') . '] file:' . $errorInfo['file'] . ' line:' . $errorInfo['line']  . PHP_EOL . 'message: ' . $errorInfo['message'] . PHP_EOL . $eTrace . PHP_EOL);
	}
});

//自動載入
spl_autoload_register(function ($class) {
	$classPath = WEBROOT . '/#lib/' . $class . '.php';
	$factoryPath = WEBROOT . '/#factory/' . $class . '.php';
	$systemModulePath = WEBROOT . '/#' . $class . '/#lib.php';
	$modulePath = WEBROOT . '/' . $class . '/#lib.php';
	if (is_file($classPath)) {
		include $classPath;
	} elseif (is_file($factoryPath)) {
		include $factoryPath;
	} elseif (is_file($systemModulePath)) {
		include $systemModulePath;
	} elseif (is_file($modulePath)) {
		include $modulePath;
	}
});


/**
 * 模組基本需求
 */
abstract class module
{
	/**
	 * 呼叫入口 Router
	 */
	abstract public static function router($param): void;

	/**
	 * 權限驗證
	 */
	abstract public static function auth(): bool;
}
