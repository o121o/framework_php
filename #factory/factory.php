<?php

/**
 * 工廠基底
 */
class factory
{
	private $writeDB;
	private $readDB;

	/**
	 * 設定連線
	 * @param implementsDB $DBTarget 資料庫連線物件 new DB('DB')
	 */
	public function __construct(implementsDB $writeDBTarget, implementsDB $readDBTarget = null)
	{
		$this->writeDB = $writeDBTarget;
		$this->readDB = $readDBTarget;

		if (is_null($readDBTarget)) {
			$this->readDB = $this->writeDB;
		}
	}

	/**
	 * SQL 資料綁定
	 * @param array $bindData 綁定資料
	 */
	public static function bindSQL(array $bindData): array
	{
		$bindSQL = '';
		$param = array();
		foreach ($bindData as $colName => $value) {
			$bindSQL .= ' AND `' . $colName . '`';
			if (is_null($value)) {
				$bindSQL .= ' IS NULL';
			} elseif (!is_array($value)) {
				$bindSQL .= ' = ?';
				$param[] = $value;
			} elseif ($value) {
				$bindSQL .= ' IN (' . implode(', ', array_fill(0, count($value), '?')) . ')';
				$param = array_merge($param, $value);
			} else {
				$bindSQL .= ' != `' . $colName . '`';
			}
		}

		return array('SQL' => $bindSQL, 'param' => $param);
	}

	/**
	 * 資料庫查詢
	 * @param string $tableName table name
	 * @param array $colArray table column name
	 * @param array $whereData 查詢條件
	 * @param bool $single 是否為單筆資料
	 * @param string $appendSQL 附加SQL
	 * @param array $appendParam 附加資料
	 */
	public function select(string $tableName, array $colArray, array $whereData = array(), bool $single = false, string $appendSQL = '', array $appendParam = array()): array
	{
		if (!$colArray) {
			die('Error Select Input');
		}

		$bind = self::bindSQL($whereData);

		$rs = $this->readDB->sql('SELECT `' . implode('`, `', $colArray) . '` FROM `' . $tableName . '` WHERE 1 = 1' . $bind['SQL'] . $appendSQL, array_merge($bind['param'], $appendParam));

		if ($single) {
			return $rs->fetch();
		}
		return $rs->fetchAll();
	}

	/**
	 * 資料庫新增，支援批次新增，
	 * 單筆時亦可$insert = array(資料)
	 * 回傳ID 或 筆數
	 * @param string $tableName table name
	 * @param array $insertData 插入資料 $insertData = array(列數 => array(資料))
	 * @param array $insertCol 插入欄位
	 * @param string $idMode 資料庫 PK 模式
	 *   - uuid return rowCount
	 *   - ai auto_increment return lastInsertId
	 */
	public function insert(string $tableName, array $insertData, array $insertCol = array(), $idMode = 'uuid'): int
	{
		if (!$insertData) {
			die('Error Insert Input');
		}

		if (!isset($insertData[0])) {
			$insertData = array($insertData);
		}

		if (!$insertCol) {
			$insertCol = array_keys($insertData[0]);
		}

		$insertSQL = 'INSERT INTO `' . $tableName . '` (`' . implode('`,`', $insertCol) . '`) VALUE (' . implode(',', array_fill(0, count($insertData[0]), '?')) . ')';
		$prep = $this->writeDB->prepare($insertSQL);
		try {
			foreach ($insertData as $row) {
				if (!$prep->execute(array_values($row))) {
					$eMsg = 'insert is error';
					var_dump($eMsg);
					if ($this->debug) {
						var_dump($eMsg, $insertSQL, array_values($row));
					}
					errorHandler::writeError($eMsg . PHP_EOL . $insertSQL);
					return 0;
				}
			}
		} catch (PDOException $e) {
			if ($this->debug) {
				var_dump($e->getMessage());
			}
			errorHandler::writeError($e->getMessage());
		}
		if ($idMode === 'uuid') {
			return $prep->rowCount();
		} else {
			return $this->writeDB->lastInsertId();
		}
	}

	/**
	 * 資料庫更新
	 * @param string $tableName table name
	 * @param array $updateData 更新資料
	 * @param array $whereData 更新條件
	 * @param string $appendSQL 附加SQL
	 * @param array $appendParam 附加資料
	 */
	public function update(string $tableName, array $updateData, array $whereData = array(), string $appendSQL = '', array $appendParam = array()): int
	{
		if (!$updateData) {
			die('Error Update Input');
		}

		$bind = self::bindSQL($whereData);

		return $this->writeDB->sql('UPDATE `' . $tableName . '` SET `' . implode('` = ?, `', array_keys($updateData)) . '` = ? WHERE 1 = 1' . $bind['SQL'] . $appendSQL, array_merge(array_values($updateData), $bind['param'], $appendParam))->rowCount();
	}

	/**
	 * 資料庫刪除
	 * @param string $tableName table name
	 * @param array $whereData 刪除條件
	 * @param string $appendSQL 附加SQL
	 * @param array $appendParam 附加資料
	 */
	public function delete(string $tableName, array $whereData = array(), string $appendSQL = '', array $appendParam = array()): int
	{
		$bind = self::bindSQL($whereData);

		return $this->writeDB->sql('DELETE FROM `' . $tableName . '` WHERE 1 = 1' . $bind['SQL'] . $appendSQL, array_merge($bind['param'], $appendParam))->rowCount();
	}
}
