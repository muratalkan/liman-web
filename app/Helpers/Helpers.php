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
	createDirectoryIfNotExist("/etc/nginx/sites-available/");
	createDirectoryIfNotExist("/etc/nginx/sites-enabled/");

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
	createDirectoryIfNotExist("/etc/ssl/certs/");
	createDirectoryIfNotExist("/etc/ssl/private/");

	Command::bindDefaultEngine();
	$config = new Config();
	$data = Data::instance()
		->file('/etc/nginx/__sslCertificate.conf')
		->read(true);
	$config
		->folder("/etc/ssl")
		->file($data['web_app_name']."_sslCert.conf")
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
		'openssl req -config /etc/ssl/{:webAppName}_sslCert.conf -x509 -nodes -days 3650 -newkey rsa:2048 -keyout /etc/ssl/private/{:webAppName}_web.key -out /etc/ssl/certs/{:webAppName}_web.crt',
		[
			'webAppName' => $data['web_app_name']
		]
	);

	removeFile("/etc/ssl/".$data['web_app_name']."_sslCert.conf");
}

function createDirectoryIfNotExist($path){
	if(!(bool)Command::runSudo('[ -d '.$path.' ] && echo 1 || echo 0'))
	{
		Command::runSudo('mkdir -p '.$path);
	}
}

function createFileIfNotExist($path){
	if(!(bool)Command::runSudo('[ -f '.$path.' ] && echo 1 || echo 0'))
	{
		Command::runSudo('touch '.$path);
	}
}

function removeDirectory($path){
	Command::runSudo('rm -rf '.$path);
}

function removeFile($path){
	Command::runSudo('rm '.$path);
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