<?php

namespace App\Tasks;

use Liman\Toolkit\Formatter;
use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\RemoteTask\Task;
use Liman\Toolkit\Shell\Command;
use App\Classes\Package;
use App\Classes\Php;
use App\Classes\Module;

class InstallPackage extends Task
{
	protected $description = 'Installing package...';
	protected $sudoRequired = true;

	public function __construct(array $attrbs=[])
	{
		$packagesArr = Package::getPackageToInstall();
		$phpPck = preparePhpModules(Module::getPreInstalledModules());

		$this->control = Distro::debian('apt|dpkg')
			->centos('yum install')
			->get();

		$this->command = Distro::debian(
			'DEBIAN_FRONTEND=noninteractive apt-get install '.implode(' ', $packagesArr).' '.$phpPck['debianModsCmd'].' -y'
		)
			->centos(
				'
					yum install '.implode(' ', $packagesArr).' -y; 
					yum install '.$phpPck['centosModsCmd'].' -y
				'
			)
			->get();

		$this->attributes = $attrbs;
		$this->logFile = Formatter::run('/tmp/apt-install_initialPackages.txt');
	}

}