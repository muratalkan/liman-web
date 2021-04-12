<?php

namespace App\Controllers;

use App\DataManager\Data;
use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use App\Controllers\ServiceController;
use App\Helpers\InputValidations;

class FTPController{

	public function getUsers(){
		$webAppName = request("webAppName");
		/*$output = Command::runSudo("pure-pw list | grep '/var/www/{:webApp}' | awk '{print$1}'",[
			'webApp' => $webAppName
		]);
		$ftpUsers = explode("\n", $output);*/
		
		$webApps = readWebApps();
		$webApp = $webApps->first(function($value, $key) use($webAppName) {
			return $value->webAppName == $webAppName;
		});

		$ftpUsersObj =[];
		foreach($webApp->ftpUsers as $ftpUser){
			array_push($ftpUsersObj, [
				'username' => $ftpUser,
				'path' => "/var/www/".$webAppName,
				'webAppName' => $webAppName
			]);
		}

		return view('components.webAppTab.ftpUsers-table', [
			'ftpUsersData' => $ftpUsersObj
		]);
	}

	public function createUser(){
		validate([
			'ftpUsername' => 'required|string',
		]);
		
		$webAppName = request("webAppName");
		$ftpUsername = request("ftpUsername");
		$ftpPassword = request("ftpPassword");
		
		if(InputValidations::isNameValid($ftpUsername)){
			if(!$this->checkFTPUserExists($ftpUsername)){
				//add group if it does not exist
				$check = (bool) Command::runSudo('groupadd pureFtpdUsrGroup 2>/dev/null 1>/dev/null && echo 1 || echo 0');
				if($check){
					Command::runSudo('useradd -g pureFtpdUsrGroup -d /dev/null -s /etc pureFtpdUsr');
				}
			
				//reconfigure permissions
				Command::runSudo('chgrp pureFtpdUsrGroup -R /var/www');
				Command::runSudo('chmod g+rw /var/www -R');

				$result = (bool) Command::runSudo('printf "{:passwd}\n{:passwd}\n" | sudo pure-pw useradd {:username} -u pureFtpdUsr -d /var/www/{:webApp} 2>/dev/null 1>/dev/null && echo 1 || echo 0',[
					'passwd' => $ftpPassword,
					'username' => $ftpUsername,
					'webApp' => $webAppName
				]);
				if($result){
					Command::runSudo("pure-pw mkdb");
					$verify1 = (bool) Command::runSudo('[ -f /etc/pure-ftpd/conf/PureDB ] && echo 1 || echo 0');
					$verify2 = (bool) Command::runSudo('[ -f /etc/pure-ftpd/auth/PureDB ] && echo 1 || echo 0');
					if($verify1){
						if(!$verify2){
							Command::runSudo('ln -s /etc/pure-ftpd/conf/PureDB /etc/pure-ftpd/auth/PureDB');
							ServiceController::restartService("pure-ftpd");
						}

						$webApps = readWebApps();
						$webApps->map(function ($item, $key) use($webAppName, $ftpUsername) {
							if($item->webAppName == $webAppName){
								array_push($item->ftpUsers, $ftpUsername);
								$item->ftpUsers = array_unique($item->ftpUsers);
							}
						});
						writeWebApps($webApps);
					
						return respond(__("The virtual FTP user has been created"), 200);
					}
				}
			}else{
				return respond(__("The virtual FTP user already exists!"), 201);
			}
			
		}else{
			return respond($ftpUsername.' '.__("is invalid! (It should not contain any Turkish characters, special characters or spaces)"), 201);
		}
		
		return respond(__("The virtual FTP user could not be created!"), 201);
	}
	
	public function resetUser(){
		$ftpUsername = request("ftpUsername");
		$ftpPassword = request("ftpPassword");

		if($ftpUsername != null && $ftpPassword != null){
			$result = (bool) Command::runSudo('printf "{:passwd}\n{:passwd}\n" | sudo pure-pw passwd {:username} 2>/dev/null 1>/dev/null && echo 1 || echo 0',[
				'passwd' => $ftpPassword,
				'username' => $ftpUsername
			]);

			if($result){
				Command::runSudo("pure-pw mkdb");
				return respond(__("The virtual FTP user's password has been changed"), 200);
			}
		}

		return respond(__("The virtual FTP user's password could not be changed!"), 201);
	}
		
	public function deleteUser(){
		$webAppName = request("webAppName");
		$ftpUsername = request("ftpUsername");

		if($webAppName != null && $ftpUsername != null){
			Command::runSudo('pure-pw userdel {:username}',[
				'username' => $ftpUsername,
			]);

			$webApps = readWebApps();
			$webApps->map(function ($item, $key) use($webAppName, $ftpUsername) {
				if($item->webAppName == $webAppName){
					$i = array_search($ftpUsername, $item->ftpUsers);
					if($i !== false){
						array_splice($item->ftpUsers, $i, 1);
					}
				}
			});
			writeWebApps($webApps);

			return respond(__("The virtual FTP user has been deleted"), 200);
		}
		return respond(__("The virtual FTP user could not be deleted!"), 201);
	}

	private function checkFTPUserExists($ftpUser){
		$output = Command::runSudo("pure-pw list | awk '{print$1}'");
		$ftpUsers = explode("\n", $output);

		if(array_search($ftpUser, $ftpUsers) !== false){
			return true; //exists
		}
		return false; //does not exist
	}

	public static function deleteUsers($webAppName){
		$output = Command::runSudo("pure-pw list | grep '/var/www/{:webApp}' | awk '{print$1}'",[
			'webApp' => $webAppName
		]);
		$ftpUsers = explode("\n", $output); //delete virtual ftp users related with the given webApp

		foreach($ftpUsers as $usr){
			Command::runSudo('pure-pw userdel {:username}',[
				'username' => $usr,
			]);
		}

	}

}