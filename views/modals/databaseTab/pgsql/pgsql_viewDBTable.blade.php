@component('modal-component',[
    "id" => "viewPgSQLDBTableModal"
])

<div id="pgsqlDBTable-table" class="table-content">
    <div class="table-body"> </div>
</div>
                
@endcomponent


<script>

function deletePgSQLDBTable(row){
        var tableName = row.querySelector('#tableName').innerHTML;
        Swal.fire({
            title: tableName,
            text: "{{ __('Are you sure you want to delete the PostgreSQL database table?') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: "{{ __('Sil') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData(); 
                    const databaseName = row.querySelector('#dbName').innerHTML;
                    formData.append("databaseName", databaseName);
                    formData.append("tableName", tableName);
                    request("{{API('drop_pgsql_dbtable')}}", formData, function(response) {
                        const output = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Deleted!') }}", text: output, type: "success", showConfirmButton: false});
                        setTimeout(function() { getPgSQLDBTables(row); }, 1000);
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