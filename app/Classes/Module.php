<?php
namespace App\Classes;

use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;

class Module
{
	public $name;

    function __construct($name) {
        $this->name = $name;
    }

	function setName($name){
        $this->name = $name;
    }

    function getName(){
        return $this->name;
    }
	
	
	public static function getModulesList(){
	   return [
			"apc", "bcmath", "bz2", "calendar", "Core", "ctype", "date", "dba", "dom", 
			"exif", "fileinfo", "filter", "ftp", "gd", "gettext", "gmp", "hash", "iconv", "ldap", "json", "libxml", 
			"mcrypt", "mongodb", "mysqli", "openssl", "odbc", "pcntl", "pcre", "PDO", "pdo_mysql", 
			"Phar", "pgsql", "pspell", "readline", "Reflection", "session", "shmop", "SimpleXML","snmp", "soap", 
			"sockets", "SPL", "sqlite3", "standard", "tidy", "tokenizer", "xml", "xmlreader", "xmlwriter", "xsl", "zip"
		];
   }

	public static function getPreInstalledModules(){
		// Laravel -> "bcmath", "ctype", "fileinfo", "fpm", "ftp", "json", "mbstring", "mysql", "openSSL", "pdo", "pgsql", "tokenizer", "xml"
		return [
			"common", // 'bz2', 'calendar', 'Core', 'ctype', 'curl', 'date', 'ereg', 'exif', 'fileinfo', 'filter', 'ftp', 'gettext', 'gmp', 'hash', 'iconv', 'json', 'libxml', 'openssl', 'pcre', 'Phar', 'Reflection', 'session', 'shmop', 'SimpleXML', 'sockets', 'SPL', 'standard', 'tokenizer', 'zip', 'zlib'
			"fpm", 
			"mysql", 
			"pgsql"
		];
	}

	public static function getInstalledModules(){
		//apt-cache search --names-only ^php7
		$installedModules = Command::runSudo("php -m | tail -n +2 | sed '/^$/q' | head -n -2");
		$phpModules = explode("\n", $installedModules);
		return $phpModules;
	}

}
