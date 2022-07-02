<?php

/**
 * session
 */
class session extends factory
{
	/**
	 * 過期時間
	 */
	protected $expires;

	/**
	 * 名稱
	 */
	private $cookieName;

	/**
	 * 內部初始化
	 */
	private function __construct(implementsDB $DB)
	{
		parent::__construct($DB);
		$config = config::settingArray('session');
		if (!isset($config['expires'])) {
			$config['expires'] = 86400;
		}
		if (!isset($config['cookieName'])) {
			$config['cookieName'] = 86400;
		}
		$this->expires = $config['expires'];
		$this->cookieName = $config['cookieName'];
	}

	/**
	 * 使用者伺服器儲存空間初始化
	 * @param string $uid 使用者ID
	 */
	public function init(string $uid)
	{
		$jwt = tool::uuid();
		$expires = time() + $this->expires;

		parent::insert('session', array(array('id' => $jwt, 'oid' => $uid, 'expires' => $expires, 'data' => '{}')));

		setcookie($this->cookieName, $jwt . '.' . password_hash($jwt, PASSWORD_DEFAULT), array('httponly' => true, 'expires' => $expires, 'path' => '/'));

		return $jwt;
	}

	/**
	 * 使用者伺服器空間擁有者
	 */
	public function owner(){
		$rs = parent::select('session', array('oid'), array('id' => $this->ID()), true);
		return $rs ? $rs['oid'] : '';
	}

	/**
	 * 取得/驗證使用者伺服器儲存空間資料ID
	 */
	private function ID(): string
	{
		$ID = isset($_COOKIE[$this->cookieName]) ? $_COOKIE[$this->cookieName] : '';
		$tmp = explode('.', $ID, 2);
		return (isset($tmp[1]) && (password_verify($tmp[1], $tmp[0]))) ? $tmp[0] : '';
	}

	/**
	 * 取得使用者伺服器儲存空間資料
	 */
	public function data(): array
	{
		$rs = parent::select('session', array('data'), array('id' => $this->ID()), true);
		return $rs ? (array) json_decode($rs['data']) : array();
	}

	/**
	 * 使用者伺服器儲存空間設定
	 * @param string $index account token data index
	 * @param {*} $value account token index data
	 */
	public function setting(array $data = array()): void
	{
		$origin = $this->data();
		foreach($data as $index => $value){
			$origin[$index] = $value;
		}
		$expires = time() + $this->expires;
		parent::update('session', array('data' => json_encode($data), 'expires' => $expires), array('id' => $this->ID()));
	}

	/**
	 * 使用者伺服器儲存空間移除
	 */
	public function clear()
	{
		parent::update('session', array('data' => json_encode(array()), 'expires' => 10), array('id' => $this->ID()));
		setcookie($this->cookieName, '', array('httponly' => true, 'expires' => time() - 3600, 'path' => '/'));
	}
}
