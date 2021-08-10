<script>

    function createConfirmationAlert(title, text, form, route, nextFunc, row=null, nextFunc2=null){
        Swal.fire({
            title: title,
            text: text,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ __('Confirm') }}", cancelButtonText: "{{ __('Cancel')}}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise(() => {
                    request(`{{API('${route}')}}`, form, function(response) {
                        const message = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Success') }}", text: message, type: "success", showConfirmButton: false});
                        setTimeout(function() {
                            if(nextFunc2 != null){
                                eval(nextFunc2) 
                            }
                            eval(nextFunc) 
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

</script>