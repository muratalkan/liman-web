<?php

namespace App\Tasks;

use Liman\Toolkit\Formatter;
use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\RemoteTask\Task;
use Liman\Toolkit\Shell\Command;

class InstallModule extends Task
{
	protected $description = 'Installing modules...';
	protected $sudoRequired = true;

	public function __construct(array $attrbs = [])
	{
		$result = preparePhpModules($attrbs);

		$this->control = Distro::debian('apt|dpkg')
			->centos('yum install')
			->get();

		$this->command = Distro::debian(
			'DEBIAN_FRONTEND=noninteractive apt-get install '.$result['debianModsCmd'].' -y'
		)
			->centos(
					$result['centosModsCmd']
			)
			->get();

		$this->attributes = $attrbs;
		$this->logFile = Formatter::run('/tmp/apt-installPhpModules.txt');
	}

}
