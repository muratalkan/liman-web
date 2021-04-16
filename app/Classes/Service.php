<?php
namespace App\Classes;

class Service
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

	public static function serviceWithPorts(){
		return [ 
			"nginx" => '80 / 443', 
			"pure-ftpd" => '21', 
			"mysql" => '3306', 
			"postgresql" => '5432', 
			"php-fpm" => '9000'
		];
	}

	public static function serviceWithProgs(){
		return [ 
			"postgres" => 'postgresql', 
			"mysqld" => 'mysql'
		];
	}
	
	public static function serviceWithUFWPorts(){
		return [ 
			"nginx" => [
				"RuleName" => ['\'Nginx Full\''],
				"PortName" => ['Nginx'] 
			],
			"pure-ftpd" => [
				"RuleName" => ['ftp', '20/tcp', '990/tcp', '30000:50000/tcp'], 
				"PortName" => ['20/tcp', '21/tcp', '990/tcp', '30000:50000/tcp'] 
			],
			"mysql" => [
				"RuleName" => ['mysql'], 
				"PortName" => ['3306'] 
			],
			"postgresql" => [
				"RuleName" => ['postgresql'], 
				"PortName" => ['5432'] 
			]
		];
	}

}