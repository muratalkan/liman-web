@component('modal-component',[
    "id" => "viewPgSQLUserDBModal"
])

<div id="pgsqlUserDB-table"></div>
                
@endcomponent

<script>

    function revokePgSQLDBPrivilege(row){
        const databaseName = row.querySelector('#dbName').innerHTML;
        const dbUser = row.querySelector('#userName').innerHTML;
        let form = new FormData();
            form.append("userName", dbUser);
            form.append("databaseName", databaseName);
        createConfirmationAlert(
            databaseName,
            '{{ __("Are you sure you want to revoke PostgreSQL database privilege?") }}',
            form,
            'revoke_pgsql_dbprivilege',
            'getPgSQLUserDatabases(row)',
            row
        );
    }

</script>