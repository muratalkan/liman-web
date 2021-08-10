@component('modal-component',[
    "id" => "viewFtpUsersModal"
])

<div id="ftpUsers-table"> </div>
                
@endcomponent

<script>

    function resetFtpUser(row){
        let ftpUser = row.querySelector('#username').innerHTML;
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
                    let form = new FormData();
                        form.append("ftpUsername", ftpUser);
                        form.append("ftpPassword", password);
                    request("{{API('reset_ftp_user')}}", form, function(response) {
                        const message = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Changed!') }}", text: message, type: "success", showConfirmButton: false});
                        setTimeout(function() { getFtpUsers(row); }, 1000);
                    }, function(response) {
                        const error = JSON.parse(response).message;
                        Swal.fire("{{ __('Error!') }}", error, "error");
                    });
                })
              },
              allowOutsideClick: false
        });
    }

    function deleteFtpUser(row){
        const webAppName = row.querySelector('#webAppName').innerHTML;
        const ftpUser = row.querySelector('#username').innerHTML;
        let form = new FormData();
            form.append("webAppName", webAppName);
            form.append("ftpUsername", ftpUser);
        createConfirmationAlert(
            ftpUser,
            '{{ __("Are you sure you want to delete the virtual FTP user?") }}',
            form,
            'delete_ftp_user',
            'getFtpUsers(row)',
            row,
        );
    }

</script>