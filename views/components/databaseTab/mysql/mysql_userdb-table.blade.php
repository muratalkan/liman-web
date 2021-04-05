@include('table',[
    "value" => $userDBsData,
    "title" => [
          "Database Name", "*hidden*", "*hidden*"
    ],
    "display" => [
           "dbName", "userName:userName", "hostName:hostName"
    ],
    "menu" => [
            "Revoke Privilege" => [
                 "target" => "revokeMySQLDBPrivilege",
                 "icon" => " context-menu-icon--fa fa fa-times"
            ],
            "Delete" => [
                 "target" => "deleteMySQLDatabase",
                 "icon" => " context-menu-icon-delete"
            ]
    ]
])