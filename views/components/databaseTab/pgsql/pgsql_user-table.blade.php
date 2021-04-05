@include('table',[
    "value" => $userData,
    "title" => [
          "Username", "Privileges", "Member of"
    ],
    "display" => [
           "userName", "attr", "memberOf"
    ],
    "onclick" =>  "getPgSQLUserDatabases",
    "menu" => [
            "Grant Privilege" => [
                 "target" => "grantPgSQLPrivilegesModal",
                 "icon" => " context-menu-icon--fa fa fa-lock-open"
            ],
            "Revoke All Privileges" => [
                 "target" => "revokePgSQLAllPrivileges",
                 "icon" => " context-menu-icon--fa fa fa-lock"
            ],
            "Delete" => [
                 "target" => "deletePgSQLUser",
                 "icon" => " context-menu-icon-delete"
            ]
    ]
])