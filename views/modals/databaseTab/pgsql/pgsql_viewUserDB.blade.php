@component('modal-component',[
    "id" => "viewPgSQLUserDBModal"
])

<div id="pgsqlUserDB-table" class="table-content">
    <div class="table-body"> </div>
</div>
                
@endcomponent


<script>

function revokePgSQLDBPrivilege(line){
        var databaseName = line.querySelector('#dbName').innerHTML;
        Swal.fire({
            title: databaseName,
            text: "{{ __('Are you sure you want to revoke PostgreSQL database privilege?') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: "{{ __('Revoke') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    const dbUser = line.querySelector('#userName').innerHTML;
                    formData.append("userName", dbUser);
                    formData.append("databaseName", databaseName);
                    request("{{API('revoke_pgsql_dbprivilege')}}", formData, function(response) {
                        const output = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Revoked!') }}", text: output, type: "success", showConfirmButton: false});
                        setTimeout(function() { getPgSQLUserDatabases(line); }, 1000);
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