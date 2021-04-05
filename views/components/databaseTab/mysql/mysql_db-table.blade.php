@include('table',[
    "value" => $databaseData,
    "title" => [
            "Database Name"
    ],
    "display" => [
           "dbName"
    ],
    "onclick" =>  "getMySQLDBTables",
    "menu" => [
            "Delete" => [
                 "target" => "deleteMySQLDatabase",
                 "icon" => " context-menu-icon-delete"
            ]
    ]
])