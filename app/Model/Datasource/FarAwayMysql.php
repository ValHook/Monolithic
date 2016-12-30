<?php

App::uses('DboSource', 'Model/Datasource');
class FarAwayMysql extends DboSource {
	
	public $description = "Faraway MySQL DBO Driver";
	protected $_baseConfig = array(
		'persistent' => true,
		'host' => 'localhost',
		'login' => 'root',
		'password' => '',
		'database' => 'cake',
		'port' => '3305',
		'rsa' => '',
		'remote_username' => '',
		'remote_ip' => ''
	);
	protected $_connection = null;
	protected $_useAlias = true;
	public function connect() {
	$config = $this->config;
	$this->connected = false;
	try {
		$flags = array(
		PDO::ATTR_PERSISTENT => $config['persistent'],
		PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	);
	if (!empty($config['encoding'])) {
		$flags[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $config['encoding'];
	}
	if (empty($config['unix_socket'])) {
		$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
	} else {
		$dsn = "mysql:unix_socket={$config['unix_socket']};dbname={$config['database']}";
	}
	$this->_connection = new PDO(
	$dsn,
	$config['login'],
	$config['password'],
	$flags
	);
	$this->connected = true;
	} catch (PDOException $e) {
		echo "ssh -i {$config['rsa']} {$config['remote_username']}@{$config['remote_ip']} -L3307:{$config['host']}:3306 -N";
		shell_exec("ssh -i {$config['rsa']} {$config['remote_username']}@{$config['remote_ip']} -L3307:{$config['host']}:3306 -N");
	}
	try {
	$flags = array(
		PDO::ATTR_PERSISTENT => $config['persistent'],
		PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	);
	if (!empty($config['encoding'])) {
		$flags[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $config['encoding'];
	}
	if (empty($config['unix_socket'])) {
		$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
	} else {
		$dsn = "mysql:unix_socket={$config['unix_socket']};dbname={$config['database']}";
	}
	$this->_connection = new PDO(
	$dsn,
	$config['login'],
	$config['password'],
	$flags
	);
	$this->connected = true;
	} catch (PDOException $e) {
	throw new MissingConnectionException(array(
	'class' => get_class($this),
	'message' => $e->getMessage()
	));
	}
	$this->_useAlias = (bool)version_compare($this->getVersion(), "4.1", ">=");
	return $this->connected;
	}
}

?>