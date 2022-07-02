<?php

/**
 * 使用者
 */
class user extends factory
{
	private $session;
	/**
	 * 內部初始化
	 */
	private function __construct()
	{
		$DB = new DB();
		parent::__construct($DB);
		$this->session = new session($DB);
	}

	/**
	 * 使用者登入驗證
	 * @return string user id
	 */
	public function isLogin(): string
	{
		return $this->session->owner();
	}

	/**
	 * 使用者登入
	 * @param string $acc 帳號
	 * @param string $pwd 密碼
	 * @param string $source 來源
	 *  - user 一般使用者
	 *  - manager 管理者
	 *  - admin 最高管理者
	 */
	public function login(string $acc, string $pwd, string $source = 'user'): string
	{
		$updateData = array();
		$jwt = '';

		$rs = parent::select($source, array('id', 'account', 'password'), array('account' => $acc), true);
		if ($rs && password_verify($pwd, $rs['password'])) {
			if (password_needs_rehash($rs['password'], PASSWORD_DEFAULT)) {
				$updateData['password'] = password_hash($pwd, PASSWORD_DEFAULT);
			}
			$updateData['lastlogin'] = time();

			$this->session->init($rs['id']);
			$this->setting($rs['id'], $updateData, $source);
		}

		return $jwt;
	}

	/**
	 * 使用者登出
	 */
	public function logout()
	{
		$this->session->clear();
	}

	/**
	 * 篩選使用者
	 * @param array $colArray col name
	 * @param array $filter 篩選條件
	 * @param string $source 來源
	 */
	public function filter(array $colArray, array $filter = array(), string $source = 'user'): array
	{
		return parent::select($source, $colArray, $filter);
	}

	/**
	 * 設定使用者資料
	 * @param string $id ID
	 * @param array $data 設定資料
	 * @param string $source 來源
	 */
	public function setting(string $id, array $data, string $source = 'user'): int
	{
		if ($id) {
			return parent::update($source, $data, array('id' => $id));
		} else {
			return parent::insert($source, array($data));
		}
	}
}
