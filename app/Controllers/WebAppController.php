<?php
namespace App\Controllers;

use App\DataManager\Data;
use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use App\Controllers\ServiceController;
use App\Controllers\FTPController;
use App\Helpers\InputValidations;

class WebAppController
{
	public function get()
	{
		$webApps = readWebApps();
		return view('components.webAppTab.webApps-table', [
			'webAppsData' => $webApps
		]);
	}

	public function set()
	{
		validate([
			'webAppName' => 'required|string',
		]);

		$webAppName = request('webAppName');
		$phpVersion = request('phpVersion');
		$webAppStatus = "enabled";
		$httpPort = "80";
		$sslConfiguration ="";

		if(!empty(request("httpsStatus")) && !empty(request("SslCertStatus"))){
			$httpPort = "443";
			$sslConfiguration = request("SslCertStatus");
		}

		if(InputValidations::isNameValid($webAppName))
		{
			if(!$this->checkWebAppExists($webAppName))
			{
				$https = "no";
				$keyPath = "";
				$crtPath = "";
				
				if($httpPort == "443") { 
					if(!empty($sslConfiguration)){
						switch($sslConfiguration){
							case "no": //create self-signed ssl
								$this->createSSLCertificate($webAppName);
								$keyPath =  "/etc/ssl/private/".$webAppName."_web.key";
								$crtPath = "/etc/ssl/certs/".$webAppName."_web.crt";
								$https = "yes";
								break;
							case "yes": //use existing certificate
								validate([
									'sslKeyPath' => 'required|string',
									'sslCrtPath' => 'required|string'
								]);
								$keyPath = request('sslKeyPath');
								$crtPath = request('sslCrtPath');
								$https = "yes";
								break;
						}
					}
				}

				Data::instance()
				->file('/etc/nginx/__webapp.conf')
				->write(
					[
						'web_app_name' => $webAppName,
						'php_version' => str_replace(' ', '', strtolower($phpVersion)),
						'http_port' => $httpPort == "443" ? "443 ssl" : "80",
						'key_path' => $keyPath,
						'crt_path' => $crtPath
					],
					true
				);
	
				Command::runSudo('mkdir -p /var/www/{:webAppName}/html && sudo touch $_/index.php',[
					'webAppName' => $webAppName
				]);
				updateWebAppConfig();
				ServiceController::restartService("nginx"); //restart service after configurations

				if(!$this->testNginxConfigurations()){
					return respond(__("Nginx configuration test failed"), 201);
				}else{
					$webApps = readWebApps();
					$webApps->push([
						'webAppName' => $webAppName,
						'domainNames' => [],
						'ftpUsers' => [],
						'phpVersion' => $phpVersion,
						'https' => $https,
						'key_path' => $keyPath,
						'crt_path' => $crtPath,
						'status' => $webAppStatus
					]);
					writeWebApps($webApps);
					
					return respond(__("The web app has been added"), 200);
				}

			}else{
				return respond(__("The web app already exists!"), 201);
			}

		}else{
			return respond($webAppName.' '.__("is invalid! (It should not contain any Turkish characters, special characters or spaces)"), 201);
		}

	}

	public function enable(){
		$webAppName = request('webAppName');

		if($webAppName != null){
			if(!$this->isWebAppEnabled($webAppName)){
				$this->changeWebAppStatus($webAppName, "enabled");
				Command::runSudo("ln -s /etc/nginx/sites-available/".$webAppName." /etc/nginx/sites-enabled/");
				return respond(__("The web app is enabled"), 200);
			}
			return respond(__("The web app is already enabled!"), 201);
		}

		return respond(__("The web app could not be enabled!"), 201);
	}

	public function disable(){
		$webAppName = request('webAppName');

		if($webAppName != null){
			if($this->isWebAppEnabled($webAppName)){
				$this->changeWebAppStatus($webAppName, "disabled");
				Command::runSudo("rm /etc/nginx/sites-enabled/".$webAppName);
				return respond(__("The web app is disabled"), 200);
			}
			return respond(__("The web app is already disabled!"), 201);
		}

		return respond(__("The web app could not be disabled!"), 201);
	}

	public function delete(){
		$webAppName = request('webAppName');

		if($webAppName != null){
			$webApps = readWebApps();
			$webApp = $webApps->first(function($value, $key) use($webAppName) {
				return $value->webAppName == $webAppName;
			});

			removeDirectory('/var/www/'.$webAppName);
			removeFile('/etc/nginx/sites-available/'.$webAppName);
			removeFile('/etc/nginx/sites-enabled/'.$webAppName);
			removeFile($webApp->key_path);
			removeFile($webApp->crt_path);
			FTPController::deleteUsers($webAppName);
			
			$webApps = $webApps->reject(function ($item, $key) use($webAppName){
				return $item->webAppName == $webAppName;
			});
			writeWebApps($webApps);

			return respond(__("The web app has been deleted"), 200);
		}

		return respond(__("The web app could not be deleted!"), 201);
	}

	private function createSSLCertificate($webAppName){ 

		Data::instance()
		->file('/etc/nginx/__sslCertificate.conf')
		->write(
			[
				'web_app_name' => $webAppName,
				'country_name' => request('sslCountryName'),
				'state_name' => request('sslStateName'),
				'locality_name' => request('sslLocalName'),
				'org_name' => request('sslOrgName'),
				'org_unit_name' => request('sslOrgUnitName'),
				'common_name' => request('sslCommonName'),
				'email_address' => request('sslEmail')
			],
			true
		);

		updateSSLConfig();
	}

	private function testNginxConfigurations(){
		$testNginx = (bool) Command::runSudo('nginx -t 2>/dev/null 1>/dev/null && echo "1" || echo "0"');

		if(!$testNginx){ 
			$this->delete();
			ServiceController::restartService("nginx"); //restart nginx if it fails 
		}
		return $testNginx;
	}

	private function changeWebAppStatus($webAppName, $newStatus){
		$webApps = readWebApps();
		$webApps->map(function ($item, $key) use($webAppName, $newStatus) {
			if($item->webAppName == $webAppName){
				$item->status = $newStatus;
			}
		});
		writeWebApps($webApps);
	}

	private function isWebAppEnabled($webAppName){
		$isEnabled = (bool) Command::runSudo('[ -f /etc/nginx/sites-enabled/{:webAppName} ] && echo "1" || echo "0"',[
			'webAppName' => $webAppName
		]);
		return $isEnabled;
	}

	private function checkWebAppExists($webAppName){
		$verify1 = (bool) Command::runSudo('[ -d /var/www/{:webAppName} ] && echo "1" || echo "0"',[
			'webAppName' => $webAppName
		]);
		$verify2 = (bool) Command::runSudo('[ -f /etc/nginx/sites-available/{:webAppName} ] && echo "1" || echo "0"',[
			'webAppName' => $webAppName
		]);

		if ($verify1 || $verify2) {
			return true; //exists
		}
		return false; //does not exist 
	}

}