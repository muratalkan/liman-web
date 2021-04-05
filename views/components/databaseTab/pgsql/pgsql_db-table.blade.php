@include('table',[
    "value" => $databaseData,
    "title" => [
            "Database Name", "Owner", "Size"
    ],
    "display" => [
           "dbName", "owner", "size"
    ],
    "onclick" =>  "getPgSQLDBTables",
    "menu" => [
            "Delete" => [
                 "target" => "deletePgSQLDatabase",
                 "icon" => " context-menu-icon-delete"
            ]
    ]
])