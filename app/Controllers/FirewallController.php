<?php
namespace App\Controllers;

use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use App\Classes\Service;

class FirewallController
{

	public function get(){
        $isUfwInstalled = (bool) Command::runSudo("ufw status 2>/dev/null 1>/dev/null && echo 1 || echo 0"); 
		$ufwStatus = Command::runSudo("ufw status");
		
		if($isUfwInstalled){
			if (strpos($ufwStatus, 'inactive') === false) {
				return respond(__("UFW is active"), 200);
			}
		}
		return respond(__("UFW is not active!"), 201);
	}

	/*public function checkAll(){

	}*/

	public function check(){
		$service = request('service');
		$ports = Service::serviceWithUFWPorts()[$service]['PortName'];

		foreach($ports as $port){
			$check = (bool) Command::runSudo("ufw status | grep 'ALLOW' | grep 'Anywhere' | grep @{:servicePort} 2>/dev/null 1>/dev/null && echo 1 || echo 0",[
				'servicePort' => $port
			]);
	
			$result = -1; //not configured
			if($check){
				$result = 1; //allowed
			}else{
				$check = (bool) Command::runSudo("ufw status | grep 'DENY' | grep 'Anywhere' | grep @{:servicePort} 2>/dev/null 1>/dev/null && echo 1 || echo 0",[
					'servicePort' => $port
				]);
				if($check){ //denied
					$result = 0;
				}
			}
		}

		return respond($result, 200);
	}

	public function manage(){
		$action = request('action'); //allow || deny
		$service = request('service'); //service name

		if ($service != null && $action != null) {
			$serviceRules = Service::serviceWithUFWPorts()[$service]['RuleName'];
			foreach($serviceRules as $rule){
				Command::runSudo('ufw '.$action.' '.$rule);
			}
			return respond(__("Valid Operation"), 200);
		}
		else{
			return respond(__("Invalid Operation!"), 201);
		}
	}

}