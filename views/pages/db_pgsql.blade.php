<div class="row">
    <div class="col-md-5">
        <div class="card card-primary status-card">
            <div class="card-header" style="background-color: #007bff; color: #fff;">
                <h3 class="card-title">{{ __('PostgreSQL Users') }}</h3>
            </div>
            <div class="card-body" >
                <button type="button" class="btn btn btn-success" data-toggle="modal" data-target="#createPgSQLUserModal"> <i class="fas fa-user-plus mr-1"></i>{{ __('Create User')}}</button>
                <br><br>
                <div id="pgsqlUser-table" class="table-content">
                    <div class="table-body">
                    
                    </div>
                    <div class="overlay">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">{{ __('Loading')}}...</span>
                        </div>
                    </div>
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
                <div id="pgsqlDB-table" class="table-content">
                    <div class="table-body">

                    </div>
                    <div class="overlay">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">{{ __('Loading')}}...</span>
                        </div>
                    </div>
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
            $('#pgsqlUser-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
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
            $('#pgsqlDB-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
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
            $('#pgsqlUserDB-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
            Swal.close();
            $('#viewPgSQLUserDBModal').find('.modal-header').html('<h4><strong>'+dbusername+'</strong> | PostgreSQL | {{__("Authorized Databases")}} </h4>');
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
            $('#pgsqlDBTable-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
            Swal.close();
            $('#viewPgSQLDBTableModal').find('.modal-header').html('<h4><strong>'+databaseName+'</strong> | PostgreSQL | {{__("Database Tables")}} </h4>');
            $('#viewPgSQLDBTableModal').modal('show');
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function deletePgSQLUser(row){
        var username = row.querySelector('#userName').innerHTML;
        Swal.fire({
            title: username,
            text: "{{ __('Are you sure you want to delete the PostgreSQL user?') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: "{{ __('Delete') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    formData.append("userName", username);
                    request("{{API('drop_pgsql_user')}}", formData, function(response) {
                        const output = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Deleted!') }}", text: output, type: "success", showConfirmButton: false});
                        setTimeout(function() { getPgSQLContent();  }, 1000);
                    }, function(response) {
                        const error = JSON.parse(response).message;
                        Swal.fire("{{ __('Error!') }}", error, "error");
                    });
                })
              },
              allowOutsideClick: false
        });
    }


    function deletePgSQLDatabase(row){
        var databaseName = row.querySelector('#dbName').innerHTML;
        Swal.fire({
            title: databaseName,
            text: "{{ __('Are you sure you want to delete the PostgreSQL database?') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: "{{ __('Delete') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    formData.append("databaseName", databaseName);
                    request("{{API('drop_pgsql_database')}}", formData, function(response) {
                        const output = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Deleted!') }}", text: output, type: "success", showConfirmButton: false});
                        setTimeout(function() { 
                            if(row.querySelector('#userName')){
                                getPgSQLUserDatabases(row); 
                            }
                            getPgSQLDatabases(); 
                        }, 1000);
                    }, function(response) {
                        const error = JSON.parse(response).message;
                        Swal.fire("{{ __('Error!') }}", error, "error");
                    });
                })
              },
              allowOutsideClick: false
        });
    }

    function revokePgSQLAllPrivileges(row){
        var username = row.querySelector('#userName').innerHTML;
        Swal.fire({
            title: username,
            text: "{{ __('Are you sure you want to revoke all privileges of the PostgreSQL user?') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: "{{ __('Revoke') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    formData.append("userName", username);
                    request("{{API('revoke_pgsql_privileges')}}", formData, function(response) {
                        const output = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Revoked!') }}", text: output, type: "success", showConfirmButton: false});
                        setTimeout(function() { getPgSQLContent(); }, 1000);
                    }, function(response) {
                        const error = JSON.parse(response).message;
                        Swal.fire("{{ __('Error!') }}", error, "error");
                    });
                })
              },
              allowOutsideClick: false
        });
    }

</script>