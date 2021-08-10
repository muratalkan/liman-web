@component('modal-component',[
    "id" => "viewMySQLDBTableModal"
])

<div id="mysqlDBTable-table"></div>
                
@endcomponent

<script>

    function deleteMySQLDBTable(row){
        const databaseName = row.querySelector('#dbName').innerHTML;
        const tableName = row.querySelector('#tableName').innerHTML;
        let form = new FormData();
            form.append("databaseName", databaseName);
            form.append("tableName", tableName);
        createConfirmationAlert(
            tableName,
            '{{ __("Are you sure you want to delete the MySQL database table?") }}',
            form,
            'drop_mysql_dbtable',
            'getMySQLDBTables(row)',
            row
        );
    }

</script>