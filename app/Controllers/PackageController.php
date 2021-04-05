<?php

namespace App\Controllers;

use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use App\Classes\Package;

class PackageController
{

	public static function verify() {

		$packageArr = Package::getPreInstalledPackage();
		$phpStr = getPhpAndModules();

		$check = (bool) Distro::debian(
			"dpkg -s ".implode(' ', $packageArr)." ".$phpStr." 2>/dev/null 1>/dev/null && echo 1 || echo 0"
		)
			->centos(
				"rpm -q ".implode(' ', $packageArr)." ".$phpStr." 2>/dev/null 1>/dev/null && echo 1 || echo 0"
			)
			->runSudo();

		if ($check) {
			return true;
		} 
		return false;
	}

	public function install() {

		return respond(
			view('components.task', [
				'onFail' => 'onTaskFail',
				'onSuccess' => 'onTaskSuccess',
				'tasks' => [
					0 => [
						'name' => 'AddRepository',
						'attributes' => []
					],
					1 => [
						'name' => 'InstallPackage',
						'attributes' => []
					]
				]
			]),
			200
		);
	}

}