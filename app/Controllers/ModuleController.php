<?php
namespace App\Controllers;

use App\DataManager\Data;
use Liman\Toolkit\Shell\Command;
use App\Classes\Php;
use App\Classes\Module;
use Liman\Toolkit\OS\Distro;

class ModuleController
{

	public function getAll(){
		$modulesToInstall = Module::getModulesList();
		$installedPhpModules = Module::getInstalledModules();
		$notInstalledModules=array_diff($modulesToInstall, $installedPhpModules);
		
		return view('components.moduleTab.module-table', [
			"modulesData" => array_values($notInstalledModules)
		]);
	}

	public function getInstalledPhpModules(){
		$installedPhpModules = Module::getInstalledModules();
		return respond($installedPhpModules, 200);
	}

	public function getSupportedPhps(){
		$supportedPhps = Php::getSupportedVersions();
		return respond($supportedPhps, 200);
	}

	public function getInstalledPhps(){
		$installedPhps = Php::getInstalledVersions();
		return respond($installedPhps, 200);
	}

	public function install()
	{
		$moduleList = explode(",", request('moduleList'));
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
						'name' => 'InstallModule',
						'attributes' => $moduleList
					]
				]
			]),
			200
		);

	}

}