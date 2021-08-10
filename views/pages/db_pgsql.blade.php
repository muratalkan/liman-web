<div class="row">
    <div class="col-md-5">
        <div class="card card-primary status-card">
            <div class="card-header" style="background-color: #007bff; color: #fff;">
                <h3 class="card-title">{{ __('PostgreSQL Users') }}</h3>
            </div>
            <div class="card-body" >
                <button type="button" class="btn btn btn-success" data-toggle="modal" data-target="#createPgSQLUserModal"> <i class="fas fa-user-plus mr-1"></i>{{ __('Create User')}}</button>
                <br><br>
                <div id="pgsqlUser-table">
                    @include('components.loading-effect')
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card card-primary status-card">
            <div class="card-header" style="background-color: #007bff; color: #fff;">
                <h3 class="card-title">{{ __('PostgreSQL Databases') }}</h3>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn btn-success" data-toggle="modal" data-target="#createPgSQLDBModal"> <i class="fas fa-database mr-1"></i>{{ __('Create Database')}}</button>
                <br><br>
                <div id="pgsqlDB-table">
                    @include('components.loading-effect')
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    function getPgSQLContent(){
        getPgSQLUsers();
        getPgSQLDatabases();
    }

    function getPgSQLUsers(){
        request("{{API('get_pgsql_users')}}", new FormData(), function(response) {
            $('#pgsqlUser-table').html(response).find("table").DataTable(dataTablePresets('normal'));
            $('#pgsqlUser-table').find('.overlay').hide();
            Swal.close();
            hideModal("createPgSQLUserModal");
            hideModal("grantPgSQLPrivilegesModal"); initializePgSQLPrivilegeModal();
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function getPgSQLDatabases(){
        request("{{API('get_pgsql_databases')}}", new FormData(), function(response) {
            $('#pgsqlDB-table').html(response).find("table").DataTable(dataTablePresets('normal'));
            $('#pgsqlDB-table').find('.overlay').hide();
            Swal.close();
            hideModal("createPgSQLDBModal");
            getPgSQLDatabaseSBox();
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function getPgSQLUserDatabases(row){
        showSwal('{{__("Loading")}}...','info');
        var dbusername = row.querySelector('#userName').innerHTML;
        let formData = new FormData();
        formData.append("userName", dbusername);
        request("{{API('get_pgsql_user_databases')}}", formData, function(response) {
            $('#pgsqlUserDB-table').html(response).find("table").DataTable(dataTablePresets('normal'));
            Swal.close();
            changeModalTitle('viewPgSQLUserDBModal', '<h4><strong>'+dbusername+'</strong> | PostgreSQL | {{__("Authorized Databases")}} </h4>');
            $('#viewPgSQLUserDBModal').modal('show');
        },function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function getPgSQLDBTables(row){
        showSwal('{{__("Loading")}}...','info');
        var databaseName = row.querySelector('#dbName').innerHTML;
        let formData = new FormData();
        formData.append("databaseName", databaseName);
        request("{{API('get_pgsql_dbtables')}}", formData, function(response) {
            $('#pgsqlDBTable-table').html(response).find("table").DataTable(dataTablePresets('normal'));
            Swal.close();
            changeModalTitle('viewPgSQLDBTableModal', '<h4><strong>'+databaseName+'</strong> | PostgreSQL | {{__("Database Tables")}} </h4>');
            $('#viewPgSQLDBTableModal').modal('show');
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function deletePgSQLUser(row){
        const username = row.querySelector('#userName').innerHTML;
        let form = new FormData();
            form.append("userName", username);
        createConfirmationAlert(
            username,
            '{{ __("Are you sure you want to delete the PostgreSQL user?") }}',
            form,
            'drop_pgsql_user',
            'getPgSQLContent()'
        );
    }


    function deletePgSQLDatabase(row){
        const databaseName = row.querySelector('#dbName').innerHTML;
        const nextFunc2 = null;
        if(row.querySelector('#userName')){
            nextFunc2 = 'getPgSQLUserDatabases(row)'; 
        }
        let form = new FormData();
            form.append("databaseName", databaseName);
        createConfirmationAlert(
            databaseName,
            '{{ __("Are you sure you want to delete the PostgreSQL database?") }}',
            form,
            'drop_pgsql_database',
            'getPgSQLDatabases()',
            row,
            nextFunc2
        );
    }

    function revokePgSQLAllPrivileges(row){
        const username = row.querySelector('#userName').innerHTML;
        let form = new FormData();
            form.append("userName", username);
        createConfirmationAlert(
            username,
            '{{ __("Are you sure you want to revoke all privileges of the PostgreSQL user?") }}',
            form,
            'revoke_pgsql_privileges',
            'getPgSQLContent()'
        );
    }

</script>