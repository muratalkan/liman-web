<?php

return [
    
    "index" => "HomeController@index",
    "install" => "HomeController@install",
    "load" => "HomeController@load",

    "runTask" => "TaskController@runTask",
    "checkTask" => "TaskController@checkTask",
    "install_package" => "PackageController@install",

    "get_service_version" => "DashboardController@get",

    "check_service" => "ServiceController@check",
    "manage_service" => "ServiceController@manage",
    "get_service_status" => "ServiceController@get",
    "conf_service_port" => "ServiceController@configure",

    "firewall_status" => "FirewallController@get",
    "check_firewall" => "FirewallController@check",
    "manage_firewall" => "FirewallController@manage",

    "get_web_apps" => "WebAppController@get",
    "set_web_app" => "WebAppController@set",
    "enable_web_app" => "WebAppController@enable",
    "disable_web_app" => "WebAppController@disable",
    "delete_web_app" => "WebAppController@delete",

    "get_domain_names" => "DomainNameController@get",
    "add_domain_name" => "DomainNameController@add",
    "delete_domain_name" => "DomainNameController@delete",

    "get_ftp_users" => "FTPController@getUsers",
    "create_ftp_user" => "FTPController@createUser",
    "reset_ftp_user" => "FTPController@resetUser",
    "delete_ftp_user" => "FTPController@deleteUser",

    "get_mysql_databases" => "MySQLController@getDatabases",
    "create_mysql_database" => "MySQLController@createDatabase",
    "drop_mysql_database" => "MySQLController@dropDatabase",
    "get_mysql_users" => "MySQLController@getUsers",
    "create_mysql_user" => "MySQLController@createUser",
    "drop_mysql_user" => "MySQLController@dropUser",
    "grant_mysql_privileges" => "MySQLController@grantPrivileges",
    "revoke_mysql_allprivileges" => "MySQLController@revokeAllPrivileges",
    "get_mysql_user_databases" => "MySQLController@getUserDBs",
    "revoke_mysql_dbprivilege" => "MySQLController@revokeDBPrivilege",
    "get_mysql_dbtables" => "MySQLController@getDBTables",
    "drop_mysql_dbtable" => "MySQLController@dropDBTable",

    "get_pgsql_databases" => "PostgreSQLController@getDatabases",
    "create_pgsql_database" => "PostgreSQLController@createDatabase",
    "drop_pgsql_database" => "PostgreSQLController@dropDatabase",
    "get_pgsql_users" => "PostgreSQLController@getUsers",
    "create_pgsql_user" => "PostgreSQLController@createUser",
    "drop_pgsql_user" => "PostgreSQLController@dropUser",
    "grant_pgsql_privileges" => "PostgreSQLController@grantPrivileges",
    "revoke_pgsql_privileges" => "PostgreSQLController@revokeAllPrivileges",
    "get_pgsql_user_databases" => "PostgreSQLController@getUserDBs",
    "revoke_pgsql_dbprivilege" => "PostgreSQLController@revokeDBPrivilege",
    "get_pgsql_dbtables" => "PostgreSQLController@getDBTables",
    "drop_pgsql_dbtable" => "PostgreSQLController@dropDBTable",

    "install_module" => "ModuleController@install",
    "get_php_modules" => "ModuleController@getAll",
    "get_supported_phps" => "ModuleController@getSupportedPhps",
    "get_installed_phps" => "ModuleController@getInstalledPhps",
    "get_installed_modules" => "ModuleController@getInstalledPhpModules"
    
];