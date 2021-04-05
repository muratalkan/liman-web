@component('modal-component',[
    "id" => "viewFtpUsersModal"
])

<div id="ftpUser-table" class="table-content">
    <div class="table-body"> </div>
</div>
                
@endcomponent

<script>

    function resetFtpUser(line){
        var ftpUser = line.querySelector('#username').innerHTML;
        Swal.fire({
            title: ftpUser,
            text: "{{ __('Enter a new password to reset and change the password for this virtual FTP user') }}.",
            input: 'password',
            inputPlaceholder: "{{__('New Password')}}",
            showCancelButton: true,
            confirmButtonText: "{{__('Apply')}}", cancelButtonText: "{{__('Cancel')}}",
            inputValidator: (password) => {
                if(password.length < 6 || password.length > 25){
                    return "{{__('Password must be a minimum of 6 and a maximum of 25 characters long')}}!";
                }
            },
            showLoaderOnConfirm: true,
              preConfirm: (password) => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    formData.append("ftpUsername", ftpUser);
                    formData.append("ftpPassword", password);
                    request("{{API('reset_ftp_user')}}", formData, function(response) {
                        const message = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Changed!') }}", text: message, type: "success", showConfirmButton: false});
                        setTimeout(function() { getFtpUsers(line); }, 1000);
                    }, function(response) {
                        const error = JSON.parse(response).message;
                        Swal.fire("{{ __('Error!') }}", error, "error");
                    });
                })
              },
              allowOutsideClick: false
        });
    }

    function deleteFtpUser(line){
        var ftpUser = line.querySelector('#username').innerHTML;
        Swal.fire({
            title: ftpUser,
            text: "{{ __('Are you sure you want to delete the FTP user?') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: "{{ __('Delete') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    const webAppName = line.querySelector('#webAppName').innerHTML;
                    formData.append("webAppName", webAppName);
                    formData.append("ftpUsername", ftpUser);
                    request("{{API('delete_ftp_user')}}", formData, function(response) {
                        const message = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Deleted!') }}", text: message, type: "success", showConfirmButton: false});
                        setTimeout(function() { getFtpUsers(line); }, 1000);
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