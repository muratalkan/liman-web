@include('table',[
    "value" => $userData,
    "title" => [
          "Username", "Host"
    ],
    "display" => [
           "userName", "hostName"
    ],
    "onclick" =>  "getMySQLUserDatabases",
    "menu" => [
            "Grant Privilege" => [
                 "target" => "grantMySQLPrivilegesModal",
                 "icon" => " context-menu-icon--fa fa fa-lock-open"
            ],
            "Revoke All Privileges" => [
                 "target" => "revokeMySQLAllPrivileges",
                 "icon" => " context-menu-icon--fa fa fa-lock"
            ],
            "Delete" => [
                 "target" => "deleteMySQLUser",
                 "icon" => " context-menu-icon-delete"
            ]
    ]
])