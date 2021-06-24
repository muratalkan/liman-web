<?php

namespace App\Tasks;

use Liman\Toolkit\Formatter;
use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\RemoteTask\Task;
use Liman\Toolkit\Shell\Command;

class AddRepository extends Task
{
	protected $description = 'Adding repositories...';
	protected $sudoRequired = true;

	public function __construct(array $attrbs=[])
	{
		//Temp solution for Pardus OS
			Command::runSudo("apt-get -y install apt-transport-https lsb-release ca-certificates");
			Command::runSudo("wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg");
			Command::runSudo("sh -c 'echo \"deb https://packages.sury.org/php/ stretch main\" > /etc/apt/sources.list.d/php.list'");
			Command::runSudo("apt update");

		$this->control = Distro::debian('add-apt-repository')
			->centos('yum install')
			->get();

		$this->command = Distro::debian(
			'add-apt-repository ppa:ondrej/php -y'
		)
			->centos(
				'yum install epel-release yum-utils http://rpms.remirepo.net/enterprise/remi-release-7.rpm -y'
			)
			->get();

		$this->attributes = $attrbs;
		$this->logFile = Formatter::run('/tmp/apt-add_repositories.txt');
	}

}