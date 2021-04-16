<?php
namespace App\Helpers;

use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use Illuminate\Database\Events\StatementPrepared;
use Illuminate\Database\Capsule\Manager as Capsule;
use PDO;

class PostgreSQL_DB 
{
	protected $database;

	public function __construct($db = null){
		if ($db) {
			$this->database = $db;
		}
	}

	public static function instance(){
		return new self();
	}

	public function getDatabase(){
		return $this->database;
	}

	public function setDatabase($db){
		$this->database = $db;
	}

	public function database($db){
		$this->setDatabase($db);
		return $this;
	}

	public function run($sql, $cmd){
		return Command::runSudo("-u postgres psql -d {$this->database} -c \"{$sql};\" {$cmd}");
	}

	public static function getDefaultUsers(){
		return array("postgres");
	}

	public static function getDefaultDatabases(){
		return array("postgres", "template0", "template1");
	}

}