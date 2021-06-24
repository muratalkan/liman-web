<?php
namespace App\Classes;

class Package
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

	public static function getPreInstalledPackage(){
		return [
			"'nginx'",
			"'pure-ftpd'",
			"'default-mysql-server'",
			"'postgresql'",
			"'openssl'",
			"'lsof'"
		];
	}

	public static function getPackageToInstall(){
		return [
			"nginx",
			"pure-ftpd",
			"default-mysql-server",
			"postgresql-contrib",
			"postgresql",
			"openssl",
			"lsof"
		];
	}

}