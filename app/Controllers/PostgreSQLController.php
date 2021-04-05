<?php

namespace App\Controllers;

use Liman\Toolkit\OS\Distro;
use Liman\Toolkit\Shell\Command;
use App\Helpers\InputValidations;
use App\Helpers\PostgreSQL_DB;

class PostgreSQLController 
{

    public function getUsers(){
        $output = PostgreSQL_DB::instance()
                ->database('postgres')
                ->run("\du", "| tail -n +4 | head -n -1");
        $parsedLines = explode("\n", $output);
        $users = [];

        foreach($parsedLines as $line){
            if(!empty(trim($line))){
                $user = explode('|', $line);
                array_push($users, [
                    "userName" => trim($user[0]),
                    "attr" => empty(trim($user[1])) ?  "-" : trim($user[1]),
                    "memberOf" => trim($user[2])
                ]);
            }
        }
        
        return view('components.databaseTab.pgsql.pgsql_user-table', [
			'userData' =>  $users
		]);
    }

    public function getDatabases(){
        $output = PostgreSQL_DB::instance()
                ->database('postgres')
                ->run("SELECT datname, pg_get_userbyid(pg_database.datdba), pg_size_pretty(pg_database_size(pg_database.datname)) FROM pg_catalog.pg_database", "| tail -n +3 | head -n -2");
        $parsedLines = explode("\n", $output);
        $databases = [];

        foreach($parsedLines as $line){
            if(!empty(trim($line))){
                $db = explode('|', $line);
                    array_push($databases, [
                    "dbName" => trim($db[0]),
                    "owner" => trim($db[1]),
                    "size" => trim($db[2])
                ]);
            }
        }

        if(request('databaseList') != null){
            return respond($databases, 200);   
        }
		return view('components.databaseTab.pgsql.pgsql_db-table', [
			'databaseData' => $databases
		]);
    }

    public function getUserDBs(){
        $username = request('userName');
        $output = PostgreSQL_DB::instance()
                ->database('postgres')
                ->run("SELECT datname, datacl FROM pg_database", "| grep '{$username}=' | awk '{print$1,$3}'");
        $parsedLines = explode("\n", $output);
        $userDBs = [];

        foreach($parsedLines as $line){
            if(!empty(trim($line))){
                $db = explode(' ', $line);
                array_push($userDBs, [
                    "dbName" => $db[0],
                    "access" => $db[1],
                    "userName" => $username
                ]);
            }
        }

        return view('components.databaseTab.pgsql.pgsql_userdb-table', [
			'userDBsData' => $userDBs
		]);
    }

    public function getDBTables(){
        $dbName = request('databaseName');
        $output = PostgreSQL_DB::instance()
                ->database($dbName)
                ->run("\dt+ .*", "| tail -n +4 | head -n -2");
        $parsedLines = explode("\n", $output);
        $tables = [];

        foreach($parsedLines as $line){
            if(!empty(trim($line))){
                $tab = explode('|', $line);
                    array_push($tables, [
                    "tableName" => trim($tab[1]),
                    "schemaName" => trim($tab[0]),
                    "size" => trim($tab[4]),
                    "dbName" => $dbName
                ]);
            }
        }

		return view('components.databaseTab.pgsql.pgsql_dbTable-table', [
			'tableData' => $tables
		]);
    }

    public function createUser(){
        validate([
			'userName' => 'required|string'
		]);

        $username = request('userName');
        $password = request('userPassword');
        $passwordStr = empty(trim($password)) ? "" : "WITH ENCRYPTED PASSWORD '{$password}'";

        if(InputValidations::isNameValid($username)){
            $result = (bool) PostgreSQL_DB::instance()
                    ->database('postgres')
                    ->run("CREATE USER {$username} {$passwordStr}", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
            if($result){
                return respond(__("The database user has been created"), 200);
            }
        }else{
            return respond($username .' '.__("is invalid! (It should not contain any Turkish characters, special characters or spaces)"), 201);
        }

        return respond(__("The database user could not be created!"), 201);
    }

    public function createDatabase(){
        validate([
			'databaseName' => 'required|string'
		]);

        $dbName = request('databaseName');

        if(InputValidations::isNameValid($dbName)){
            $result = (bool) PostgreSQL_DB::instance()
                    ->database('postgres')
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
        $username = request('userName');
        $checkDefaults = in_array($username, PostgreSQL_DB::getDefaultUsers());

        if($checkDefaults == false){
            $this->revokeAllPrivilegesOnUser($username);
            $result = (bool) PostgreSQL_DB::instance()
                    ->database('postgres')
                    ->run("DROP USER {$username}", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
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
        $checkDefaults = in_array($dbName, PostgreSQL_DB::getDefaultDatabases());

        if($checkDefaults == false){
            $result = (bool) PostgreSQL_DB::instance()
                    ->database('postgres')
                    ->run("DROP DATABASE {$dbName}", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
            if($result){
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
        $checkDefaults = in_array($dbName, PostgreSQL_DB::getDefaultDatabases());

        if($checkDefaults == false){
            $result = (bool) PostgreSQL_DB::instance()
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
        $privilegeType = request('privilegeType');
        $username = request('userName');
        $checkDefaults = in_array($username, PostgreSQL_DB::getDefaultUsers());

        if($checkDefaults == false){
            if($privilegeType == "db"){ //db privileges
                $database = request('databaseSelection');
                $allPrivileges = request("privilege_all");
                $privilegeStr = empty($allPrivileges) ? "" : "ALL PRIVILEGES";
        
                if($allPrivileges != "on"){
                    $privilegeStr = empty(request("privilege_connect")) ? "" : "CONNECT";
                }
        
                $result = (bool) PostgreSQL_DB::instance()
                        ->database('postgres')
                        ->run("GRANT {$privilegeStr} ON DATABASE {$database} TO {$username}", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
                if($result){
                    return respond(__("The database user has been granted privilege for the selected database"), 200);
                }
            } 
            else if($privilegeType == "user"){ //user privileges
                $privilegesArr = [];
                $superUser   = empty(request("privilege_superUser")) ? "" : array_push($privilegesArr,"SUPERUSER");
                $createDB    = empty(request("privilege_createDB")) ? "" : array_push($privilegesArr,"CREATEDB");
                $createRole  = empty(request("privilege_createRole")) ? "" : array_push($privilegesArr,"CREATEROLE");
                $replication = empty(request("privilege_replication")) ? "" : array_push($privilegesArr,"REPLICATION");
                $bypassRls   = empty(request("privilege_bypassRls")) ? "" : array_push($privilegesArr,"BYPASSRLS");
    
                $privilegeStr = implode(' ', $privilegesArr);
    
                if(!empty(trim($privilegeStr))){
                    $result = (bool) PostgreSQL_DB::instance()
                            ->database('postgres')
                            ->run("ALTER USER {$username} WITH {$privilegeStr}", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
                    if($result){
                        return respond(__("The database user has been granted privilege"), 200);
                    }
                }
            }
        }else{
            return respond(__("Default database user cannot be granted privilege!"), 201);
        }
        
        return respond(__("The database user could not be granted privilege!"), 201);
    }

    public function revokeAllPrivileges(){
        $username = request('userName');
        $roles = ["NOSUPERUSER", "NOCREATEDB", "NOCREATEROLE", "NOREPLICATION", "NOBYPASSRLS"];
        $checkDefaults = in_array($username, PostgreSQL_DB::getDefaultUsers());
        $rolesStr = implode(' ', $roles);
        if($checkDefaults == false){
            $this->revokeAllPrivilegesOnUser($username);
            $result = (bool) PostgreSQL_DB::instance()
                    ->database('postgres')
                    ->run("ALTER USER {$username} WITH {$rolesStr}", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
            if($result){
                return respond(__("The database user's privileges has been revoked"), 200);
            }
        }else{
            return respond(__("Default database user's privileges cannot be revoked!"), 201);
        }

        return respond(__("The database user's privileges could not be revoked!"), 201);
    }

    public function revokeDBPrivilege(){
        $username = request('userName');
        $dbName = request('databaseName');
        $checkDefaults = in_array($username, PostgreSQL_DB::getDefaultUsers());

        if($checkDefaults == false){
            $result = (bool) PostgreSQL_DB::instance()
                    ->database('postgres')
                    ->run("REVOKE ALL PRIVILEGES ON DATABASE {$dbName} FROM {$username}", "1>/dev/null 2>/dev/null && echo 1 || echo 0");
            if($result){
                return respond(__("The database user's database privilege has been revoked"), 200);
            }
        }else{
            return respond(__("Default database user's database privilege cannot be revoked!"), 201);
        }

        return respond(__("The database user's database privilege could not be revoked!"), 201);
    }

    private function revokeAllPrivilegesOnUser($username){
        $pgSQL = new PostgreSQL_DB("postgres");
        $output = $pgSQL->run("SELECT datname FROM pg_database", "| tail -n +3 | head -n -2");
        $allDatabases = explode("\n", $output);

        foreach($allDatabases as $db){
            $pgSQL->run("REVOKE ALL PRIVILEGES ON DATABASE {$db} FROM {$username}", "");
        }
    }

}