@include('table',[
    "value" => $domainNamesData,
    "title" => [
          "Domain Name", "*hidden*"
    ],
    "display" => [
           "domainName", "webAppName:webAppName"
    ],
    "menu" => [
            "Delete" => [
                 "target" => "deleteDomainName",
                 "icon" => " context-menu-icon-delete"
            ]
    ]
])