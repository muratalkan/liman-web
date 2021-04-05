<?php

namespace App\Controllers;

use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use App\Classes\Php;
use App\Classes\Module;

class DashboardController{

    public function get(){
		$nginx_ver = Command::runSudo('nginx -v 2>&1');
		$ftp_ver = Command::runSudo('pure-ftpd --help | head -1');
		$mysql_ver = Command::runSudo('mysql --version');
		$postgre_ver = Command::runSudo('psql -V');
		$php_vers =  Php::getInstalledVersions();
		$php_modules = Module::getInstalledModules();
		
		$data = [
			['name' => "Nginx ".__("Version"), 'value' => $nginx_ver],
			['name' => "Pure-FTPd ".__("Version"), 'value' => $ftp_ver],
			['name' => "MySQL ".__("Version"), 'value' => $mysql_ver],
			['name' => "PostgreSQL ".__("Version"), 'value' => $postgre_ver],
			['name' => "PHP ".__("Versions"), 'value' => implode(" ", $php_vers)],
			['name' => "PHP ".__("Modules"), 'value' => implode(" ", $php_modules)]
		];
		return view('components.dashboardTab.dashboard-table', [
			'data' => $data
		]);
    }

}