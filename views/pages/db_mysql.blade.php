<div class="row">
    <div class="col-md-5">
        <div class="card card-primary status-card">
            <div class="card-header" style="background-color: #007bff; color: #fff;">
                <h3 class="card-title">{{ __('MySQL Users') }}</h3>
            </div>
            <div class="card-body" >
                <button type="button" class="btn btn btn-success" data-toggle="modal" data-target="#createMySQLUserModal"> <i class="fas fa-user-plus mr-1"></i>{{ __('Create User')}}</button>
                <br><br>
                <div id="mysqlUser-table">
                    @include('components.loading-effect')
                </div>
            </div>

        </div>
    </div>
    <div class="col-md-7">
        <div class="card card-primary status-card">
            <div class="card-header" style="background-color: #007bff; color: #fff;">
                <h3 class="card-title">{{ __('MySQL Databases') }}</h3>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn btn-success" data-toggle="modal" data-target="#createMySQLDBModal"> <i class="fas fa-database mr-1"></i>{{ __('Create Database')}}</button>
                <br><br>
                <div id="mysqlDB-table">
                    @include('components.loading-effect')
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    function getMySQLContent(){
        getMySQLUsers();
        getMySQLDatabases();
    }

    function getMySQLUsers(){
        request("{{API('get_mysql_users')}}", new FormData(), function(response) {
            $('#mysqlUser-table').html(response).find("table").DataTable(dataTablePresets('normal'));
            $('#mysqlUser-table').find('.overlay').hide();
            Swal.close();
            hideModal("createMySQLUserModal");
            hideModal("grantMySQLPrivilegesModal"); initializeMySQLPrivilegeModal();
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function getMySQLDatabases(){
        request("{{API('get_mysql_databases')}}", new FormData(), function(response) {
            $('#mysqlDB-table').html(response).find("table").DataTable(dataTablePresets('normal'));
            $('#mysqlDB-table').find('.overlay').hide();
            Swal.close();
            hideModal("createMySQLDBModal");
            getMySQLDatabaseSBox();
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function getMySQLUserDatabases(row){
        showSwal('{{__("Loading")}}...','info');
        var dbusername = row.querySelector('#userName').innerHTML;
        var dbhostname = row.querySelector('#hostName').innerHTML;
        let form = new FormData();
            form.append("userName", dbusername);
            form.append("hostName", dbhostname);
        request("{{API('get_mysql_user_databases')}}", form, function(response) {
            $('#mysqlUserDB-table').html(response).find("table").DataTable(dataTablePresets('normal'));
            Swal.close();
            changeModalTitle('viewMySQLUserDBModal', '<h4><strong>'+dbusername+'@'+dbhostname+'</strong> | MySQL | {{__("Authorized Databases")}} </h4>');
            $('#viewMySQLUserDBModal').modal('show');
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function getMySQLDBTables(row){
        showSwal('{{__("Loading")}}...','info');
        var databaseName = row.querySelector('#dbName').innerHTML;
        let form = new FormData();
            form.append("databaseName", databaseName);
        request("{{API('get_mysql_dbtables')}}", form, function(response) {
            $('#mysqlDBTable-table').html(response).find("table").DataTable(dataTablePresets('normal'));
            Swal.close();
            changeModalTitle('viewMySQLDBTableModal', '<h4><strong>'+databaseName+'</strong> | MySQL | {{__("Database Tables")}} </h4>');
            $('#viewMySQLDBTableModal').modal('show');
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function deleteMySQLUser(row){
        const username = row.querySelector('#userName').innerHTML;
        const hostname = row.querySelector('#hostName').innerHTML;
        let form = new FormData();
            form.append("userName", username);
            form.append("hostName", hostname);
        createConfirmationAlert(
            `${username}@${hostname}`,
            '{{ __("Are you sure you want to delete the MySQL user?") }}',
            form,
            'drop_mysql_user',
            'getMySQLUsers()'
        );
    }

    function deleteMySQLDatabase(row){
        const databaseName = row.querySelector('#dbName').innerHTML;
        const nextFunc2 = null;
        if(row.querySelector('#userName')){
            nextFunc2 = 'getMySQLUserDatabases(row)'; 
        }
        let form = new FormData();
            form.append("databaseName", databaseName);
        createConfirmationAlert(
            databaseName,
            '{{ __("Are you sure you want to delete the MySQL database?") }}',
            form,
            'drop_mysql_database',
            'getMySQLDatabases()',
            row,
            nextFunc2
        );
    }

    function revokeMySQLAllPrivileges(row){
        const username = row.querySelector('#userName').innerHTML;
        const hostname = row.querySelector('#hostName').innerHTML;
        let form = new FormData();
            form.append("userName", username);
            form.append("hostName", hostname);
        createConfirmationAlert(
            `${username}@${hostname}`,
            '{{ __("Are you sure you want to revoke all privileges of the MySQL user?") }}',
            form,
            'revoke_mysql_allprivileges',
            'getMySQLContent()'
        );
    }

</script>