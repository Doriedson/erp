<?php

namespace database;

use PDO;
use PDOException;

class Connection {

	private static $cn = null;
	private $rs;
	protected $query;
	protected $data;

	public function __construct() {

		$this->Connect();
	}

	private static function getDsn(): string {

        $host = getenv('DB_HOST');
        $db   = getenv('DB_NAME');
        $port = getenv('DB_PORT') ?: 3306;
        return "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
    }

    public static function getInstance(): PDO {

        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        return new PDO(self::getDsn(), $user, $pass, $options);
    }
	// public function __destruct() {

	// 	$this = null;
	// }

	public function getResult() {

		return $this->rs->fetch(PDO::FETCH_ASSOC);
	}

	public function rowCount() {

		return $this->rs->rowCount();
	}

	public function lastInsertId() {

		return self::$cn->lastInsertId();
	}

	protected function Execute() {

		$this->rs = self::$cn->prepare($this->query);

		try {

// $warnings = $this->PDO->query("SHOW WARNINGS")->fetchObject();
// example output of $warnings OR null
// stdClass Object
// (
//        [Level] => Warning
//        [Code] => 1264
//        [Message] => Out of range value for column 'qty' at row 1
// }
			$this->rs->execute($this->data);

		} catch(PDOException $e) {

			echo var_dump($e->getMessage());

			// Notifier::Add($e->getMessage(), Notifier::NOTIFIER_ERROR);

			return false;
		}

		return true;
	}

	// public static function Backup($file) {

	// 	shell_exec("mysqldump -u " . Connection::$user . " -p" . Connection::$pwd . " " . Connection::$database . " > " . $file);
	// }

	private function Connect() {

		if (self::$cn == null) {

			try {

				// self::$cn = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$database . ";charset=utf8", self::$user, self::$pwd);
				// self::$cn = new PDO(self::getDsn());

				// self::$cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				self::$cn = self::getInstance();

			} catch (PDOException $e) {

				var_dump($e->getMessage());
				// Notifier::Add($e->getMessage(), Notifier::NOTIFIER_ERROR);
			}
		}
	}
}
