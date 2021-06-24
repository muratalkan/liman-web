<?php

use App\ConfigManager\Config;
use App\DataManager\Data;
use Liman\Toolkit\Formatter;
use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use App\Classes\Php;
use App\Classes\Module;
use App\Helpers\File;

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
	File::instance()
			->path("/etc/nginx/sites-available/")
			->createDirectory();
	File::instance()
			->path("/etc/nginx/sites-enabled/")
			->createDirectory();

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
	
	File::instance()
			->path("/etc/nginx/sites-available/".$data['web_app_name'])
			->createSymbolicLink("/etc/nginx/sites-enabled/");
}

function updateSSLConfig()
{
	File::instance()
			->path("/etc/ssl/certs/")
			->createDirectory();
	File::instance()
			->path("/etc/ssl/private/")
			->createDirectory();

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

	File::instance()
			->path("/etc/ssl/".$data['web_app_name']."_sslCert.conf")
			->removeFile();
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

function createTemplateWebPage($webAppName){
	Command::runSudo(
		"bash -c \"echo @{:fileContent} | base64 -d | tee  @{:filePath}\"",
		[
			'fileContent' => base64_encode(file_get_contents(getPath("app/Templates/limanWeb.blade.php"))),
			'filePath' => "/var/www/".$webAppName."/html/index.php"
		]
	);
}
