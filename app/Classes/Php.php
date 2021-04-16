<?php
namespace App\Classes;

use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;

class Php
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

    public static function getSupportedVersions(){
		return [
			"PHP7.0", 
			"PHP7.1", 
			"PHP7.2", 
			"PHP7.3", 
			"PHP7.4"
		];
	}

	public static function getInstalledVersions(){

		$installedPhps = Distro::debian(
			"dpkg --get-selections | grep -v deinstall | awk '{print $1}' | grep -i 'php[0-9]\.[0-9]' | awk '!/-/'"
		)
			->centos(
				"rpm -qa | grep -i '^php-[0-9]\.[0-9]'"
			)
			->runSudo();
		$phpVersions = explode("\n", $installedPhps);
		return array_unique($phpVersions);
	}

}