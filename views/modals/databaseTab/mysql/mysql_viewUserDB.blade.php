@component('modal-component',[
    "id" => "viewMySQLUserDBModal"
])

<div id="mysqlUserDB-table"></div>
                
@endcomponent

<script>

    function revokeMySQLDBPrivilege(row){
        const databaseName = row.querySelector('#dbName').innerHTML;
        const userName = row.querySelector('#userName').innerHTML;
        const hostName = row.querySelector('#hostName').innerHTML;
        let form = new FormData();
            form.append("userName", userName);
            form.append("hostName", hostName);
            form.append("databaseName", databaseName);
        createConfirmationAlert(
            databaseName,
            '{{ __("Are you sure you want to revoke MySQL database privilege?") }}',
            form,
            'revoke_mysql_dbprivilege',
            'getMySQLUserDatabases(row)',
            row
        );
    }

</script>