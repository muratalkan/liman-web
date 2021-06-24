<?php
namespace App\Controllers;

use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use Liman\Toolkit\Validator;
use Liman\Toolkit\Formatter;
use App\DataManager\Data;
use App\Controllers\ServiceController;
use App\Controllers\FTPController;
use App\Helpers\InputValidations;
use App\Helpers\File;

class WebAppController
{
	public function get()
	{
		$webApps = readWebApps();
		/*File::instance()
			->path($path)
			->checkDirectoryExists()*/
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
	
				File::instance()
						->path("/var/www/".$webAppName."/html")
						->createDirectory();
				File::instance()
						->path("/var/www/".$webAppName."/html/index.php")
						->createFile();
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

					//Template index.php file
					createTemplateWebPage($webAppName);
					
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
				File::instance()
						->path("/etc/nginx/sites-available/".$webAppName)
						->createSymbolicLink("/etc/nginx/sites-enabled/");
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
				File::instance()
						->path("/etc/nginx/sites-enabled/".$webAppName)
						->removeFile();
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

			File::instance()
					->path("/var/www/".$webAppName)
					->removeDirectory();
			File::instance()
					->path("/etc/nginx/sites-available/".$webAppName)
					->removeFile();
			File::instance()
					->path("/etc/nginx/sites-enabled/".$webAppName)
					->removeFile();
			File::instance()
					->path($webApp->key_path)
					->removeFile();
			File::instance()
					->path($webApp->crt_path)
					->removeFile();
			FTPController::deleteUsers($webAppName);
			
			$webApps = $webApps->reject(function ($item, $key) use($webAppName){
				return $item->webAppName == $webAppName;
			});
			writeWebApps($webApps);
			ServiceController::restartService("nginx");
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
		ServiceController::restartService("nginx");
		$webApps = readWebApps();
		$webApps->map(function ($item, $key) use($webAppName, $newStatus) {
			if($item->webAppName == $webAppName){
				$item->status = $newStatus;
			}
		});
		writeWebApps($webApps);
	}

	private function isWebAppEnabled($webAppName){
		return File::instance()
						->path("/etc/nginx/sites-enabled/".$webAppName)
						->checkFileExists();
	}

	private function checkWebAppExists($webAppName){
		$verify1 = File::instance()
						->path("/var/www/".$webAppName)
						->checkDirectoryExists();
		$verify2 = File::instance()
						->path("/etc/nginx/sites-available/".$webAppName)
						->checkFileExists();
		if ($verify1 || $verify2) {
			return true; //exists
		}
		return false; //does not exist 
	}

}