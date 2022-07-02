<?php
/**
 * 首頁
 */
class index{
	public static function router($param){
		self::view();
	}

	public static function view(){
		echo template::head();
		echo '';
		echo template::foot();
	}
}