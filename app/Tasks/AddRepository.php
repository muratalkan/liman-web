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