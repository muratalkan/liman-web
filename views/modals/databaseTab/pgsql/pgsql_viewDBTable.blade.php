@component('modal-component',[
    "id" => "viewPgSQLDBTableModal"
])

<div id="pgsqlDBTable-table"></div>
                
@endcomponent

<script>

    function deletePgSQLDBTable(row){
        const databaseName = row.querySelector('#dbName').innerHTML;
        const tableName = row.querySelector('#tableName').innerHTML;
        let form = new FormData();
            form.append("databaseName", databaseName);
            form.append("tableName", tableName);
        createConfirmationAlert(
            tableName,
            '{{ __("Are you sure you want to delete the PostgreSQL database table?") }}',
            form,
            'drop_pgsql_dbtable',
            'getPgSQLDBTables(row)',
            row
        );
    }

</script>