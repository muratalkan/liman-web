@include('table',[
    "value" => $webAppsData,
    "title" => [
          "Application Name", "PHP Version", "HTTPS", "Status"
    ],
    "display" => [
           "webAppName", "phpVersion", "https", "status"
    ],
    "onclick" =>  "setViewAlert",
    "menu" => [
            "Add Domain Name" => [
                 "target" => "addDomainNameModal",
                 "icon" => " context-menu-icon--fa fa fa-book"
            ],
            "Create Virtual FTP User" => [
                 "target" => "addFtpUserModal",
                 "icon" => " context-menu-icon--fa fa fa-user-plus"
            ],
            "Enable" => [
                 "target" => "enableWebApp",
                 "icon" => " context-menu-icon--fa fa fa-toggle-on"
            ],
            "Disable" => [
                 "target" => "disableWebApp",
                 "icon" => " context-menu-icon--fa fa fa-toggle-off"
            ],
            "Delete" => [
                 "target" => "deleteWebApp",
                 "icon" => " context-menu-icon-delete"
            ]
    ]
])