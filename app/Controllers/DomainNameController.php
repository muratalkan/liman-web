<?php

namespace App\Controllers;

use App\DataManager\Data;
use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use App\Controllers\ServiceController;
use App\Helpers\InputValidations;

class DomainNameController{

	public function get(){
		$webAppName = request("webAppName");
		
		$webApps = readWebApps();
		$webApp = $webApps->first(function($value, $key) use($webAppName) {
			return $value->webAppName == $webAppName;
		});

		$domainNamesObj=[];
		foreach($webApp->domainNames as $domainName){
			array_push($domainNamesObj, [
				'domainName' => $domainName,
				'webAppName' => $webAppName
			]);
		}

		return view('components.webAppTab.domainNames-table', [
			'domainNamesData' => $domainNamesObj
		]);
	}

	public function add(){
		$webAppName = request("webAppName");
		$domainName = request("domainName");
		
		validate([
			'domainName' => 'required|string'
		]);
		
		if($webAppName != null && $domainName != null){
			if(InputValidations::isNameValid($domainName)){
				if(!$this->checkDomainNameExists($webAppName, $domainName)){
					$domainNameArr=[];
					$webApps = readWebApps();
					foreach($webApps as $wApp){
						if($wApp->webAppName == $webAppName){
							array_push($wApp->domainNames, $domainName);
							$wApp->domainNames = array_unique($wApp->domainNames);
							$domainNameArr = $wApp->domainNames;
							break;
						}
					}
					writeWebApps($webApps);
	
					Command::runSudo('sed -i "/server_name/c server_name {:domainNames};" /etc/nginx/sites-available/{:webAppName}',[
						"domainNames" => implode(' ', $domainNameArr),
						'webAppName' => $webAppName
					]);
					ServiceController::restartService("nginx");
					return respond(__("The domain name has been added"), 200);

				}else{
					return respond(__("The domain name already exists!"), 201);
				}
			}else{
				return respond($domainName.' '.__("is invalid! (It should not contain any Turkish characters, special characters or spaces)"), 201);
			}
		}

		return respond(__("The domain name could not be added!"), 201);
	}

	public function delete(){
		$webAppName = request("webAppName");
		$domainName = request('domainName');

		if($webAppName != null && $domainName != null){
			$domainNameArr=[];
			$webApps = readWebApps();
			foreach($webApps as $wApp){
				if($wApp->webAppName == $webAppName){
					$i = array_search($domainName, $wApp->domainNames);
					if($i !== false){
						array_splice($wApp->domainNames, $i, 1);
					}
					$domainNameArr = $wApp->domainNames;
					$totalDomainNames = count($domainNameArr);
					break;
				}
			}
			writeWebApps($webApps);

			if($totalDomainNames == 0){ // default server name -> server_name _;
				Command::runSudo('sed -i "/server_name/c server_name _;" /etc/nginx/sites-available/{:webAppName}',[
					'webAppName' => $webAppName
				]);
			}
			else{
				Command::runSudo('sed -i "/server_name/c server_name {:domainNames};" /etc/nginx/sites-available/{:webAppName}',[
					"domainNames" => implode(' ', $domainNameArr),
					'webAppName' => $webAppName
				]);
			}

			return respond(__("The domain name has been deleted"), 200);
		}
		return respond(__("The domain name could not be deleted!"), 201);
	}

	private function checkDomainNameExists($webAppName, $domainName){
		$webApps= readWebApps();
		foreach($webApps as $wApp){
			$i = array_search($domainName, $wApp->domainNames);
			if($i !== false){
				return true; //exists
			}
		}
		return false; //does not exist
	}

}