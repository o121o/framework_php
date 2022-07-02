<?php

/**
 * 雜項變數
 */
class variable extends factory
{
	/**
	 * 取得變數
	 * @param string $index 要取得的變數名稱
	 */
	public static function getVariable(string $index): string
	{
		$rs = parent::select('variable', array('value'), array('index' => $index), true);

		return $rs ? $rs['value'] : '';
	}

	/**
	 * 設定變數
	 * @param string $index 要取得的變數名稱
	 * @param string $value 要設定的變數資料
	 */
	public static function setVariable(string $index, string $value): int
	{
		return parent::update('variable', array('value' => $value), array('index' => $index));
	}
}
