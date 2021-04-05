@include('table',[
    "value" => $tableData,
    "title" => [
            "Table Name", "Schema Name", "Size", "*hidden*"
    ],
    "display" => [
           "tableName", "schemaName", "size", "dbName:dbName"
    ],
    "menu" => [
            "Delete" => [
                 "target" => "deletePgSQLDBTable",
                 "icon" => " context-menu-icon-delete"
            ]
    ]
])