<?php

namespace App\Controllers;

use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use App\Helpers\InputValidations;
use App\Helpers\MySQL_DB;

class MySQLController 
{

    public function getUsers(){
        $output = MySQL_DB::instance()
                ->database("mysql")
                ->run("SELECT user, host FROM mysql.user", "| awk '{print$1,$2}' | tail -n +2");
        $parsedLines = explode("\n", $output);

        $users = [];
        foreach($parsedLines as $line){
            if(!empty(trim($line))){
                $user = explode(' ', $line);
                array_push($users, [
                    "userName" => $user[0],
                    "hostName" => $user[1]
                ]);
            }
        }

		return view('components.databaseTab.mysql.mysql_user-table', [
			'userData' => $users
		]);
    }

    public function getDatabases(){
        $output = MySQL_DB::instance()
                ->database("mysql")
                ->run("SHOW DATABASES", "| awk '{print$1}' | tail -n +2");
        $parsedLines = explode("\n", $output);
        $databases = [];

        foreach($parsedLines as $line){
            if(!empty($line)){
                array_push($databases, [
                    "dbName" => $line
                ]);
            }
        }

        if(request('databaseList') != null){
            return respond($databases, 200);   
        }
		return view('components.databaseTab.mysql.mysql_db-table', [
			'databaseData' => $databases
		]);
    }
    
    public function getUserDBs(){
        $user = request('userName');
        $host = request('hostName');
        $output = MySQL_DB::instance()
                ->database("mysql")
                ->run("SELECT db FROM mysql.db WHERE User='{$user}' AND Host='{$host}'", "| awk '{print$1}' | tail -n +2");
        $userDBObj = [];

        if(!empty($output)){
            $userDBs = explode("\n", $output);
            foreach($userDBs as $userDB){
                array_push($userDBObj, [
                    'dbName' => $userDB,
                    "userName" => $user,
                    'hostName' => $host
                ]);
            }
        }

		return view('components.databaseTab.mysql.mysql_userdb-table', [
			'userDBsData' => $userDBObj
		]);
    }

    public function getDBTables(){
        $dbName = request('databaseName');
        $output = MySQL_DB::instance()
                ->database("mysql")
                ->run("SHOW TABLES FROM {$dbName}", "| awk '{print$1}' | tail -n +2");
        $parsedLines = explode("\n", $output);
        $tables = [];

        foreach($parsedLines as $line){
            if(!empty($line)){
                array_push($tables, [
                    "tableName" => $line,
                    "dbName" => $dbName
                ]);
            }
        }

		return view('components.databaseTab.mysql.mysql_dbTable-table', [
			'tableData' => $tables
		]);
    }

    public function createUser(){
        validate([
			'userName' => 'required|string'
		]);

        $user = request('userName');
        $host = request('hostName');

        if(InputValidations::isNameValid($user)){
            if(!empty($host) && !InputValidations::isNameValid($host)){
                return respond($host.' '.__("is invalid! (It should not contain any Turkish characters, special characters or spaces)"), 201);
            }
            $hostStr = empty(trim($host)) ? "%" : $host;
            $password = request('userPassword');
            $passwordStr = empty(trim($password)) ? "" : "IDENTIFIED BY '{$password}'";
            
            $result = (bool) MySQL_DB::instance()
                    ->database("mysql")
                    ->run("CREATE USER '{$user}'@'{$hostStr}' {$passwordStr}", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
            if($result){
                return respond(__("The database user has been created"), 200);
            }
        }else{
            return respond($user.' '.__("is invalid! (It should not contain any Turkish characters, special characters or spaces)"), 201);
        }

        return respond(__("The database user could not be created!"), 201);
    }

    public function createDatabase(){
        validate([
			'databaseName' => 'required|string'
		]);
        $dbName = request('databaseName');

        if(InputValidations::isNameValid($dbName)){
            $result = (bool) MySQL_DB::instance()
                    ->database("mysql")
                    ->run("CREATE DATABASE {$dbName}", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
            if($result){
                return respond(__("The database has been created"), 200);
            }
        }else{
            return respond($dbName .' '.__("is invalid! (It should not contain any Turkish characters, special characters or spaces)"), 201);
        }

        return respond(__("The database could not be created!"), 201);
    }

    public function dropUser(){
        $user = request('userName');
        $host = request('hostName');

        $checkDefaults = in_array($user, MySQL_DB::getDefaultUsers());
        
        if($checkDefaults == false){
            $result = (bool) MySQL_DB::instance()
                    ->database("mysql")
                    ->run("DROP USER '{$user}'@'{$host}'", '1>/dev/null 2>/dev/null && echo 1 || echo 0');
            if($result){
                return respond(__("The database user has been deleted"), 200);
            }
        }else{
            return respond(__("Default database user cannot be deleted!"), 201);
        }

        return respond(__("The database user could not be deleted!"), 201);
    }

    public function dropDatabase(){
        $dbName = request('databaseName');
        $checkDefaults = in_array($dbName, MySQL_DB::getDefaultDatabases());

        if($checkDefaults == false){
            $result = (bool) MySQL_DB::instance()
                    ->database("mysql")
                    ->run("DROP DATABASE {$dbName}", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
            if($result){
                $this->revokeAllPrivilegesOnDB($dbName);
                return respond(__("The database has been deleted"), 200);
            }
        }else{
            return respond(__("Default database cannot be deleted!"), 201);
        }

        return respond(__("The database could not be deleted!"), 201);
    }

    public function dropDBTable(){
        $dbName = request('databaseName');
        $tableName = request('tableName');
        $checkDefaults = in_array($dbName, MySQL_DB::getDefaultDatabases());

        if($checkDefaults == false){
            $result = (bool) MySQL_DB::instance()
                    ->database($dbName)
                    ->run("DROP TABLE {$tableName}", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
            if($result){
                return respond(__("The database table has been deleted"), 200);
            }
        }else{
            return respond(__("Default database table cannot be deleted!"), 201);
        }

        return respond(__("The database table could not be deleted!"), 201);
    }

    public function grantPrivileges(){
        $user = request('userName');
        $host = request('hostName');

        $checkDefaults = in_array($user, MySQL_DB::getDefaultUsers());

        if($checkDefaults == false){
            $database = request('databaseSelection');
            $allPrivileges = request("privilege_all");
            $databaseStr = $database != "-All-" ? "{$database}.*" : "*.*";
            $privilegeStr = empty($allPrivileges) ? "USAGE" : "ALL PRIVILEGES"; //usage -> no privileges

            if($allPrivileges != "on"){
                $privilegesArr = [];
                $create      = empty(request("privilege_create")) ? "" : array_push($privilegesArr,"CONNECT");
                $drop        = empty(request("privilege_drop"))   ? "" : array_push($privilegesArr,"DROP");
                $delete      = empty(request("privilege_delete")) ? "" : array_push($privilegesArr,"DELETE");
                $insert      = empty(request("privilege_insert")) ? "" : array_push($privilegesArr,"INSERT");
                $select      = empty(request("privilege_select")) ? "" : array_push($privilegesArr,"SELECT");
                $update      = empty(request("privilege_update")) ? "" : array_push($privilegesArr,"UPDATE");
                $grantOption = empty(request("privilege_grant"))  ? "" : array_push($privilegesArr,"GRANT OPTION");

                $privilegeStr = implode(',', $privilegesArr);
            }

            $result = (bool) MySQL_DB::instance()
                    ->database("mysql")
                    ->run("GRANT {$privilegeStr} ON {$databaseStr} TO '{$user}'@'{$host}'", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
            if($result){
                MySQL_DB::instance()
                    ->database("mysql")
                    ->run("FLUSH PRIVILEGES","");
                return respond(__("The database user has been granted privilege for the selected database"), 200);
            }
        }else{
            return respond(__("Default database user cannot be granted privilege!"), 201);
        }

        return respond(__("The database user could not be granted privilege for the selected database!"), 201);
    }
    
    public function revokeAllPrivileges(){
        $user = request('userName');
        $host = request('hostName');
        $checkDefaults = in_array($user, MySQL_DB::getDefaultUsers());

        if($checkDefaults == false){
            $result = (bool) MySQL_DB::instance()
                    ->database("mysql")
                    ->run("REVOKE ALL PRIVILEGES, GRANT OPTION FROM '{$user}'@'{$host}'", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
            if($result){
                return respond(__("The database user's privileges has been revoked"), 200);
            }
        }else{
            return respond(__("Default database user's privileges cannot be revoked!"), 201);
        }

        return respond(__("The database user's privileges could not be revoked!"), 201);
    }

    public function revokeDBPrivilege(){
        $user = request('userName');
        $host = request('hostName');
        $dbName = request('databaseName');
        $checkDefaults = in_array($user, MySQL_DB::getDefaultUsers());

        if($checkDefaults == false){
            $result = (bool) MySQL_DB::instance()
                    ->database("mysql")
                    ->run("REVOKE ALL PRIVILEGES ON {$dbName}.* FROM '{$user}'@'{$host}'", '1>/dev/null 2>/dev/null && echo 1 || echo 0');
            if($result){
                return respond(__("The database user's database privilege has been revoked"), 200);
            }
        }else{
            return respond(__("Default database user's database privilege cannot be revoked!"), 201);
        }

        return respond(__("The database user's database privilege could not be revoked!"), 201);
    }

    private function revokeAllPrivilegesOnDB($database){
        $mySQL = new MySQL_DB("mysql");
        $output = $mySQL->run("SELECT user, host FROM mysql.user", "| awk '{print$1,$2}' | tail -n +2");
        $parsedLines = explode("\n", $output);

        foreach($parsedLines as $line){
            if(!empty(trim($line))){
                $user = explode(' ', $line);
                $mySQL->run("REVOKE ALL PRIVILEGES ON {$database}.* FROM '{$user[0]}'@'{$user[1]}'", "");
            }
        }
    }
}