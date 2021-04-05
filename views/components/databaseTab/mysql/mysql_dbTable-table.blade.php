@include('table',[
    "value" => $tableData,
    "title" => [
            "Table Name", "*hidden*"
    ],
    "display" => [
           "tableName", "dbName:dbName"
    ],
    "menu" => [
            "Delete" => [
                 "target" => "deleteMySQLDBTable",
                 "icon" => " context-menu-icon-delete"
            ]
    ]
])