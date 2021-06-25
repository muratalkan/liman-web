<div class="row">
    <div class="col-md-5">
        <div class="card card-primary status-card">
            <div class="card-header" style="background-color: #007bff; color: #fff;">
                <h3 class="card-title">{{ __('MySQL Users') }}</h3>
            </div>
            <div class="card-body" >
                <button type="button" class="btn btn btn-success" data-toggle="modal" data-target="#createMySQLUserModal"> <i class="fas fa-user-plus mr-1"></i>{{ __('Create User')}}</button>
                <br><br>
                <div id="mysqlUser-table" class="table-content">
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
                <h3 class="card-title">{{ __('MySQL Databases') }}</h3>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn btn-success" data-toggle="modal" data-target="#createMySQLDBModal"> <i class="fas fa-database mr-1"></i>{{ __('Create Database')}}</button>
                <br><br>
                <div id="mysqlDB-table" class="table-content">
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

    function getMySQLContent(){
        getMySQLUsers();
        getMySQLDatabases();
    }

    function getMySQLUsers(){
        request("{{API('get_mysql_users')}}", new FormData(), function(response) {
            $('#mysqlUser-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
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
            $('#mysqlDB-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
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
        let formData = new FormData();
        formData.append("userName", dbusername);
        formData.append("hostName", dbhostname);
        request("{{API('get_mysql_user_databases')}}", formData, function(response) {
            $('#mysqlUserDB-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
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
        let formData = new FormData();
        formData.append("databaseName", databaseName);
        request("{{API('get_mysql_dbtables')}}", formData, function(response) {
            $('#mysqlDBTable-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
            Swal.close();
            changeModalTitle('viewMySQLDBTableModal', '<h4><strong>'+databaseName+'</strong> | MySQL | {{__("Database Tables")}} </h4>');
            $('#viewMySQLDBTableModal').modal('show');
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function deleteMySQLUser(row){
        var username = row.querySelector('#userName').innerHTML;
        var hostname = row.querySelector('#hostName').innerHTML;
        Swal.fire({
            title: `${username}@${hostname}`,
            text: "{{ __('Are you sure you want to delete the MySQL user?') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: "{{ __('Delete') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    formData.append("userName", username);
                    formData.append("hostName", hostname);
                    request("{{API('drop_mysql_user')}}", formData, function(response) {
                        const output = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Deleted!') }}", text: output, type: "success", showConfirmButton: false});
                        setTimeout(function() { getMySQLUsers(); }, 1000);
                    }, function(response) {
                        const error = JSON.parse(response).message;
                        Swal.fire("{{ __('Error!') }}", error, "error");
                    });
                })
              },
              allowOutsideClick: false
        });
    }

    function deleteMySQLDatabase(row){
        var databaseName = row.querySelector('#dbName').innerHTML;
        Swal.fire({
            title: databaseName,
            text: "{{ __('Are you sure you want to delete the MySQL database?') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: "{{ __('Delete') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    formData.append("databaseName", databaseName);
                    request("{{API('drop_mysql_database')}}", formData, function(response) {
                        const output = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Deleted!') }}", text: output, type: "success", showConfirmButton: false});
                        setTimeout(function() { 
                            if(row.querySelector('#userName')){
                                getMySQLUserDatabases(row); 
                            }
                            getMySQLDatabases(); 
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

    function revokeMySQLAllPrivileges(row){
        var username = row.querySelector('#userName').innerHTML;
        var hostname = row.querySelector('#hostName').innerHTML;
        Swal.fire({
            title: `${username}@${hostname}`,
            text: "{{ __('Are you sure you want to revoke all privileges of the MySQL user?') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: "{{ __('Revoke') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    formData.append("userName", username);
                    formData.append("hostName", hostname);
                    request("{{API('revoke_mysql_allprivileges')}}", formData, function(response) {
                        const output = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Revoked!') }}", text: output, type: "success", showConfirmButton: false});
                        setTimeout(function() { getMySQLContent(); }, 1000);
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