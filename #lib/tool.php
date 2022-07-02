<?php

/**
 * 工具雜項
 */
class tool
{
	/**
	 * 接值
	 * @param string $paramName 參數名稱
	 * @param string $paramType 參數類型
	 *  - string 字串(default)
	 *  - array 陣列
	 *  - number 數字
	 *  - bool 布林
	 */
	public static function param(string $paramName, string $paramType = 'string')
	{
		$paramTypeArray = array(
			'string' => array(
				'default' => '',
				'function' => function ($param) {
					return $param;
				},
			),
			'array' => array(
				'default' => array(),
				'function' => function ($param) {
					return is_array($param) ? $param : array($param);
				},
			),
			'number' => array(
				'default' => 0,
				'function' => function ($param) {
					$param = explode('.', $param);
					$param = $param[0] . ((isset($param[1])) ? '.' . $param[1] : '');
					return mb_ereg_replace('[^\d|\.]', '', $param) * 1;
				},
			),
			'bool' => array(
				'default' => false,
				'function' => function ($param) {
					return ($param === 'true');
				},
			),
		);

		if (!isset($paramTypeArray[$paramType])) {
			$paramType = 'string';
		}

		if (isset($_POST[$paramName])) {
			$result = $_POST[$paramName];
		} elseif (isset($_GET[$paramName])) {
			$result = $_GET[$paramName];
		} else {
			$result = $paramTypeArray[$paramType]['default'];
		}

		return $paramTypeArray[$paramType]['function']($result);
	}

	/**
	 * 防XSS，將特殊字元編碼
	 * @param string $val 處理的字串
	 */
	public static function anti_xss(string $val): string
	{
		return htmlspecialchars($val, ENT_QUOTES | ENT_HTML5);
	}

	/**
	 * 反編碼
	 * @param string $val 處理的字串
	 */
	public static function anti_xss_decode(string $val): string
	{
		return htmlspecialchars_decode($val, ENT_QUOTES | ENT_HTML5);
	}

	/**
	 * UUID string(31)
	 */
	public static function uuid(): string
	{
		do {
			$result = self::radomString(32, 'charnumber', true);
		} while (mb_ereg_match('^\d+$', $result));
		return $result;
	}

	/**
	 * 隨機字串
	 * @param int $length length
	 * @param string $mode
	 *  - number 純數字
	 *  - char 純字母
	 *  - charnumber 英數
	 *  - full 英數大小寫
	 * @param bool $security 安全性
	 */
	public static function radomString(int $length, string $mode, bool $security = false): string
	{
		$result = '';
		$number = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
		$char = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
		$charTop = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

		$pool = array();
		switch ($mode) {
			case 'number':
				$pool = $number;
				break;

			case 'char':
				$pool = $char;
				break;

			case 'charnumber':
				$pool = array_merge($char, $number);
				break;

			case 'full':
				$pool = array_merge($number, $char, $charTop);
				break;
		}

		$poolLength = count($pool);

		for ($i = 0; $i < $length; $i++) {
			if ($security) {
				$index = ord(openssl_random_pseudo_bytes(1));
			} else {
				$index = mt_rand();
			}
			$result .= $pool[$index % $poolLength];
		}

		return $result;
	}

	/**
	 * 取得IP
	 */
	public static function IP(): string
	{
		$result = '';
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR', 'HTTP_VIA') as $header) {
			if (isset($_SERVER[$header]) && $_SERVER[$header]) {
				$result = $_SERVER[$header];
				break;
			}
		}

		return $result;
	}
}
