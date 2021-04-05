@component('modal-component',[
    "id" => "viewMySQLUserDBModal"
])

<div id="mysqlUserDB-table" class="table-content">
    <div class="table-body"> </div>
</div>
                
@endcomponent


<script>

function revokeMySQLDBPrivilege(line){
        var databaseName = line.querySelector('#dbName').innerHTML;
        Swal.fire({
            title: databaseName,
            text: "{{ __('Are you sure you want to revoke MySQL database privilege?') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: "{{ __('Revoke') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    const userName =  line.querySelector('#userName').innerHTML;
                    const hostName =  line.querySelector('#hostName').innerHTML;
                    formData.append("userName", userName);
                    formData.append("hostName", hostName);
                    formData.append("databaseName", databaseName);
                    request("{{API('revoke_mysql_dbprivilege')}}", formData, function(response) {
                        const output = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Revoked!') }}", text: output, type: "success", showConfirmButton: false});
                        setTimeout(function() { getMySQLUserDatabases(line); }, 1000);
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