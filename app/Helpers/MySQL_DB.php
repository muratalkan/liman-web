<?php
namespace App\Helpers;

use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Database\Capsule\Manager as Capsule;
use PDO;

class MySQL_DB
{
	protected $database;
	protected $sql;

	public function __construct($db = null){
		if ($db) {
			$this->$database = $db;
		}
	}

	public static function instance(){
		return new self();
	}

	public function getDatabase(){
		return $this->$database;
	}

	public function setDatabase($db){
		$this->$database = $db;
	}

	public function database($db){
		$this->setDatabase($db);
		return $this;
	}

	public function run($sql, $cmd) {
		return Command::runSudo("mysql -uroot -e \"USE {$this->$database}; {$sql};\" {$cmd}");
	}

	public static function getDefaultUsers(){
		return  array("root", "mysql.sys", "mysql.session", "debian-sys-maint", "phpmyadmin");
	}
	
	public static function getDefaultDatabases(){
		return array("mysql", "sys", "information_schema", "performance_schema", "phpmyadmin");
	}
    
}