<?php


class db_conn{
	private $host = 'localhost';
	private $user = 'id16722336_gabrielonso';
	private $password = '';
	private $dbName = 'id16722336_mystoremanagerdb';

	private $pdo;
	
	//trigger db connection
	function __construct()
	{
		$this->connect_db();
	}

	//returns connection status
	 public function conn() {
       return $this->pdo;
    }

    	//establish db connection
	protected function connect_db() {
		try {
			$dsn = 'mysql:host='.$this->host.';dbname='.$this->dbName;
			$this->pdo = new PDO ($dsn, $this->user, $this->password);
			// set the PDO error mode to exception
			$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			//echo 'Connected';

		} catch (PDOException $e) {

			$this->pdo = null;
			echo 'Connection failed: ' . $e->getMessage();
		}
		
		//return $this->pdo;;
		
	}
}

//pdo connection
$pdo = new db_conn;

//pdo connection object
$pdo = $pdo->conn();

//check connection status, if null
if ($pdo === null) {
    die("No database connection");
}

session_start();
