<?php
namespace App\Controllers;

use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use App\Classes\Service;
use App\Classes\Php;

class ServiceController
{
	public function get(){
		$service = request('service');
		$serviceArr = [];
		
		if($service != null){
			if($service == "php-fpm"){
				$portNumber = Service::serviceWithPorts()[$service];
				$listeningPort = $this->checkServicePort($portNumber);
				$supportedPhps = Php::getSupportedVersions();
				foreach($supportedPhps as $php){
					$status = Command::runSudo('service '.strtolower($php).'-fpm status');
					$log = Command::runSudo('journalctl -u '.strtolower($php).'-fpm -b | tail -50');
					array_push($serviceArr, [
						"service" => $php,
						"status" => $status,
						"log" => $log,
						"program" => empty(Service::serviceWithPorts()[$listeningPort]) ? $listeningPort : Service::serviceWithPorts()[$listeningPort],
						"port" => $portNumber
					]);
				}
			} else{
				$status = Command::runSudo('service '.$service.' status');
				$log = Command::runSudo('journalctl -u '.$service.' -b | tail -50');
				$portNumber = Service::serviceWithPorts()[$service];
				$listeningPort = $this->checkServicePort($portNumber);
				array_push($serviceArr, [
					"service" => $service,
					"status" => $status,
					"log" => $log,
					"program" => empty(Service::serviceWithProgs()[$listeningPort]) ? $listeningPort : Service::serviceWithProgs()[$listeningPort],
					"port" => $portNumber
				]);
			}
			return respond($serviceArr, 200);
		}

		return respond(__("Invalid Operation!"), 201);
	}

	/*public function checkAll(){
		
	}*/
	
	public function check() {
		$check1 = false; $check2 = true;
		$service = request('service');

		if($service != null){
			if($service == "php-fpm"){
				$supportedPhps = Php::getSupportedVersions();
				foreach($supportedPhps as $php){
					$check1 = $this->isServiceActive(strtolower($php)."-fpm");
					if(!$check1){
						$check2 = false;
					}
				}
			}else{
				$check1 = $this->isServiceActive($service);
			}
		}

		if ($check1 && $check2) {
			return respond($service, 200);
		}
		return respond($service, 201);
	}

	public function manage(){
		$check1 = false; $check2 = true;
		$service = request('service'); //service name
		$action = request('action'); //start-stop-restart

		if($service != null && $action != null){
			if($service == "php-fpm"){
				$supportedPhps = Php::getSupportedVersions();
				foreach($supportedPhps as $php){
					$check1 = $this->manageService(strtolower($php)."-fpm", $action);
					if(!$check1){ $check2 = false; }
				}
			}else{
				$check1 = $this->manageService($service, $action);
			}
		}

		if ($check1 && $check2) {
			return respond(__("Valid Operation"), 200);
		}
		return respond(__("Invalid Operation!"), 201);
	}

	public function configure(){
		$service = request('service');
		$program = request('program');
		$result = (bool) Command::runSudo('service --status-all | grep @{:program} 1>/dev/null 2>/dev/null && echo "1" || echo "0"',[
			'program' => $program 
		]);

		if($result){ //if it is service 
			$check = $this->manageService($program, "stop"); //stop and disable
			$check = $this->manageService($service, "start"); //start and enable
		}else{ 
			$check = $this->killProcess($program); 
		}

		if($check){
			return respond(__("The service port has been configured"), 200);
		}
		return respond(__("The service port could not be configured!"), 201);
	}

	public static function restartService($service){
		$result = (bool) Command::runSudo('service {:serviceName} restart 1>/dev/null 2>/dev/null && echo "1" || echo "0"', [
			'serviceName' => $service
		]);
		return $result;
	}

	private function manageService($service, $action){
		$result = (bool) Command::runSudo('service {:serviceName} {:action} 1>/dev/null 2>/dev/null && echo "1" || echo "0"', [
			'serviceName' => $service,
			'action' => $action //start-stop-restart
		]);

			//on system-startup
			if($action == "start" && !$this->isServiceEnabled($service)){
				$result = (bool) Command::runSudo('systemctl enable {:serviceName} 1>/dev/null 2>/dev/null && echo "1" || echo "0"', [
					'serviceName' => $service
				]);
			}else if($action == "stop" && $this->isServiceEnabled($service)){
				$result = (bool) Command::runSudo('systemctl disable {:serviceName} 1>/dev/null 2>/dev/null && echo "1" || echo "0"', [
					'serviceName' => $service
				]);
			}

		if($result){
			return true;
		}
		return false;
	}

	private function checkServicePort($servicePortNum){
		$program = Command::runSudo("lsof -i:{:servicePort} | grep LISTEN | awk '{print $1}' | sort -u", [
			'servicePort' => $servicePortNum,
		]);

		if(empty($program)){
			$program = "N/A";
		}

		return $program;
	}

	private function isServiceActive($service){
		$result = Command::runSudo('systemctl is-active '.$service);
		if(trim($result) != "active"){
			return false;
		}
		return true;
	}
	
	private function isServiceEnabled($service){
		$result = Command::runSudo("systemctl is-enabled ".$service);
		if(trim($result) != "enabled"){
			return false;
		}
		return true;
	}

	private function killProcess($process){
		$result = (bool) Command::runSudo('killall {:process} 1>/dev/null 2>/dev/null && echo "1" || echo "0"', [
			'process' => $process
		]);
		return $result;
	}

}