<?php

use App\ConfigManager\Config;
use App\DataManager\Data;
use Liman\Toolkit\Formatter;
use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use App\Classes\Php;
use App\Classes\Module;

if (!function_exists('checkPort')) {
	function checkPort($ip, $port)
	{
		restoreHandler();
		if ($port == -1) {
			return true;
		}
		$fp = @fsockopen($ip, $port, $errno, $errstr, 0.1);
		setHandler();
		if (!$fp) {
			return false;
		} else {
			fclose($fp);
			return true;
		}
	}
}

function updateWebAppConfig()
{
	Command::bindDefaultEngine();
	$config = new Config();
	$data = Data::instance()
		->file('/etc/nginx/__webapp.conf')
		->read(true);
	$config
		->folder('/etc/nginx/sites-available')
		->file($data['web_app_name'])
		->template('nginx_conf')
		->data(
			array_merge(
				[
					'web' => $data
				],
			)
		)
		->write(true);
	
	Command::runSudo("ln -s /etc/nginx/sites-available/".$data['web_app_name']." /etc/nginx/sites-enabled/");
	
}

function updateSSLConfig()
{
	Command::bindDefaultEngine();
	$config = new Config();
	$data = Data::instance()
		->file('/etc/nginx/ssl/__sslcertificate.conf')
		->read(true);
	$config
		->folder("/etc/nginx/ssl/".$data['web_app_name'].".ssl")
		->file($data['web_app_name'].".conf")
		->template('sslCertificate_conf')
		->data(
			array_merge(
				[
					'ssl' => $data
				],
			)
		)
		->write(true);

	Command::runSudo( //10 years certificate
		'openssl req -config /etc/nginx/ssl/{:webAppName}.ssl/{:webAppName}.conf -x509 -nodes -days 3650 -newkey rsa:2048 -keyout /etc/nginx/ssl/{:webAppName}.ssl/{:webAppName}.key -out /etc/nginx/ssl/@{:webAppName}.ssl/@{:webAppName}.crt',
		[
			'webAppName' => $data['web_app_name']
		]
	);

}

function setNginxConfs(){
	/*$verify = (bool) Command::runSudo('[ -d /etc/nginx/sites-available ] && echo 1 || echo 0');
	if(!$verify){
		Command::runSudo("mkdir -p /etc/nginx/sites-available");
		ommand::runSudo("mkdir -p /etc/nginx/sites-enabled");
	}*/

}

function readWebApps(){
	$webApps = Data::instance()
		->file('/etc/nginx/webapplist.json')
		->read(true);
	return $webApps;
}

function writeWebApps($webApps){
	Data::instance()
		->file('/etc/nginx/webapplist.json')
		->write($webApps->unique(), true);
}

function preparePhpModules($modules){
	$supportedPhps = Php::getSupportedVersions();
	$debianMods = "";
	$centosMods = "";

	foreach($supportedPhps as $php){
		$debianMods .= $php.' ';
		$centosMods .= "yum-config-manager --enable remi–".str_replace(".", "", $php)." yum-config-manager; yum install php ";
		foreach($modules as $module){
			$debianMods .= $php.'-'.$module.' ';
			$centosMods .= 'php-'.$module.' ';
		}
		$centosMods .= '-y;';
	}

	return array(
        'debianModsCmd' => $debianMods,
        'centosModsCmd' => $centosMods
    );
}

function getPhpAndModules(){
	$supportedPhps = Php::getSupportedVersions();
	$preInstalledModules = Module::getPreInstalledModules();
	$result = "";
	foreach($supportedPhps as $php){
		$result .= "'".$php."' ";
		foreach($preInstalledModules as $module){
			$result .= "'".$php."-".$module."' "; //check each php version with modules
		}
	}

	return $result;
}