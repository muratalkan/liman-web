@include('table',[
    "value" => $userDBsData,
    "title" => [
          "Database Name", "Access Privileges", "*hidden*"
    ],
    "display" => [
           "dbName", "access", "userName:userName"
    ],
    "menu" => [
            "Revoke Privilege" => [
                 "target" => "revokePgSQLDBPrivilege",
                 "icon" => " context-menu-icon--fa fa fa-times"
            ],
            "Delete" => [
                 "target" => "deletePgSQLDatabase",
                 "icon" => " context-menu-icon-delete"
            ]
    ]
])