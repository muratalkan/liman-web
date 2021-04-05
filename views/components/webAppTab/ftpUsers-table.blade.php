@include('table',[
    "value" => $ftpUsersData,
    "title" => [
          "Username", "Path", "*hidden*"
    ],
    "display" => [
           "username", "path", "webAppName:webAppName"
    ],
    "menu" => [
            "Change Password" => [
                 "target" => "resetFtpUser",
                 "icon" => " context-menu-icon--fa fa fa-key"
            ],
            "Delete" => [
                 "target" => "deleteFtpUser",
                 "icon" => " context-menu-icon-delete"
            ]
    ]
])