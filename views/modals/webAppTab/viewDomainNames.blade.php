@component('modal-component',[
    "id" => "viewDomainNamesModal"
])

<div id="domainName-table" class="table-content">
    <div class="table-body"> </div>
</div>

<small class="text-muted">*{{__("To view your websites in the browser, you must add the domain names to '/etc/hosts' file") }}.</small>
<br><small class="text-muted">*{{__("To view your HTTPS websites in the browser, you must to add 'https://' prefix") }}.</small>      

@endcomponent

<script>

    function deleteDomainName(row){
        var domainName = row.querySelector('#domainName').innerHTML;
        Swal.fire({
            title: domainName,
            text: "{{ __('Are you sure you want to delete the domain name?') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: "{{ __('Delete') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    const webAppName = row.querySelector('#webAppName').innerHTML;
                    formData.append("webAppName", webAppName);
                    formData.append("domainName", domainName);
                    request("{{API('delete_domain_name')}}", formData, function(response) {
                        const message = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Deleted!') }}", text: message, type: "success", showConfirmButton: false});
                        setTimeout(function() { getDomainNames(row); }, 1000);
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