<?php

/**
 * 樣板
 */
class template
{
	/**
	 * 引用頁面並帶入參數
	 * @param string $path 路徑
	 * @param array $data 資料
	 */
	public static function templateRequire(string $path, array $data = array())
	{
		ob_start();
		foreach ($data as $key => $value) {
			$$key = $value;
		}
		include WEBROOT . '/#template/' . $path;
		return ob_get_clean();
	}

	/**
	 * 輸出HEAD
	 * @param array $option 參數
	 *  - libCss 套件CSS
	 *  - customCss 自訂CSS
	 *  - (other) HEAD 參數
	 */
	public static function head(array $option = array())
	{
		return self::templateRequire('head.php', $option) . PHP_EOL . self::templateRequire('top.php', $option);
	}

	/**
	 * 輸出結尾
	 * @param array $option 參數
	 *  - libJs 套件JS
	 *  - customJs 自訂JS
	 */
	public static function foot(array $option = array())
	{
		return self::templateRequire('bottom.php', $option) . PHP_EOL . self::templateRequire('foot.php', $option);
	}
}
