<?php

/**
 * 資料庫物件標準接口
 */
interface implementsDB
{
	/**
	 * 執行查詢
	 */
	public function sql(string $sql, array $param = array()): PDOStatement;
	/**
	 * 執行預處理
	 */
	public function prepare(string $sql, array $option = array()): PDOStatement;
	/**
	 * 取得新增ID
	 */
	public function lastInsertId(): int;
};

/**
 * 資料庫封裝
 */
class DB extends PDO implements implementsDB
{
	/**
	 * 是否開啟DEBUG模式
	 */
	private $debug = 0;

	/**
	 * @param string $DBTarget 目標資料庫設定名稱
	 */
	public function __construct(string $DBTarget = 'default')
	{
		$setting = config::settingArray('DB_' . $DBTarget, 'configDB');
		$option = array();
		$option[PDO::ATTR_PERSISTENT] = true;
		$option[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_ASSOC;
		$option[PDO::ATTR_EMULATE_PREPARES] = false;
		$option[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		if (isset($setting['debug']) && $setting['debug']) {
			$this->debug = 1;
		}
		try {
			parent::__construct($setting['databasetype'] . ':host=' . $setting['host'] . ';dbname=' . $setting['database'] . ';charset=utf8', $setting['acc'], $setting['pwd'], $option);
		} catch (PDOException $e) {
			echo "Dtabase connection error!";
			if ($this->debug) {
				var_dump($e->getMessage());
			}
			errorHandler::writeError($e->getMessage());
		}
	}

	/**
	 * 執行SQL指令
	 * @param string $sql SQL
	 * @param array $param 參數
	 */
	public function sql(string $sql, array $param = array()): PDOStatement
	{
		if (!$sql) {
			die('Error Query Input');
		}
		$prep = parent::prepare($sql);
		if (!$prep || !$prep->execute($param)) {
			$eMsg = 'query is error';
			var_dump($eMsg);
			if ($this->debug) {
				var_dump($sql, $param);
			}
			errorHandler::writeError($eMsg . $sql . PHP_EOL);
			die();
		}
		return $prep;
	}
}
