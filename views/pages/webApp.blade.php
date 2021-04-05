<div class="row">
    <div class="col-md-3">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <h3 class="profile-username text-center">{{__('Web Applications') }}</h3>
                <p class="text-muted text-center">{{__("You can view the available web applications on this tab. Also, you can use the 'Add Application' button to add a new application and you can left or right click on the application that you want to make operation") }}.</p>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn btn-success" data-toggle="modal" data-target="#addWebAppModal"> <i class="fas fa-plus mr-1"></i>{{ __('Add Application')}}</button>
                <br><br>
                <div id="webApp-table" class="table-content">
                    <div class="table-body"> </div>
                    <div class="overlay">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">{{__('Loading')}}...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    function getWebAppContent(){
        getWebApps();
    }

    function getWebApps() {
        request("{{API('get_web_apps')}}", new FormData(), function(response) {
            $('#webApp-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
            setBadges();
            $('#webApp-table').find('.overlay').hide();
            Swal.close();
            hideModal("addWebAppModal"); initializeWebAppModal();
            hideModal("addDomainNameModal");
            hideModal("addFtpUserModal");
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
        
    }

    function getDomainNames(line){
        var webAppName = line.querySelector('#webAppName').innerHTML;
        let formData = new FormData();
        formData.append("webAppName", webAppName);
        request("{{API('get_domain_names')}}", formData, function(response) {
            $('#domainName-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
            $('#viewDomainNamesModal').find('.modal-header').html('<h4><strong>'+webAppName+'</strong> | {{__("Domain Names")}} </h4>');
            Swal.close();
            $('#viewDomainNamesModal').modal('show');
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function getFtpUsers(line){
        var webAppName = line.querySelector('#webAppName').innerHTML;
        let formData = new FormData();
        formData.append("webAppName", webAppName);
        request("{{API('get_ftp_users')}}", formData, function(response) {
            $('#ftpUser-table').find('.table-body').html(response).find("table").DataTable(dataTablePresets('normal'));
            $('#viewFtpUsersModal').find('.modal-header').html('<h4><strong>'+webAppName+'</strong> | {{__("Virtual FTP Users")}} </h4>');
            Swal.close();
            $('#viewFtpUsersModal').modal('show');
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function setViewAlert(line){
        var webAppName = line.querySelector('#webAppName').innerHTML;
        Swal.fire({
            title: webAppName,
            text: "{{ __('Select the item you want to view') }}",
            input: 'select',
            inputOptions: {'domainName': "{{ __('Domain Names') }}",
                           'ftpUser': "{{ __('Virtual FTP Users') }}"
            },
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: "{{ __('View') }}", cancelButtonText: "{{ __('Cancel')}}", 
            showLoaderOnConfirm: true,
              preConfirm: (result) => {
                return new Promise(() => {
                  if(result == 'domainName'){
                      getDomainNames(line);
                  }else if(result == 'ftpUser'){
                      getFtpUsers(line);
                  }
                })
              }
        });
    }

    function enableWebApp(line){
        var webAppName = line.querySelector('#webAppName').innerHTML;
        Swal.fire({
            title: webAppName,
            text: "{{ __('Are you sure you want to enable the web app?') }}",
            type: 'info',
            showCancelButton: true,
            confirmButtonText: "{{ __('Enable') }}", cancelButtonText: "{{ __('Cancel')}}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise(() => {
                    let formData = new FormData();
                    formData.append("webAppName", webAppName);
                    request("{{API('enable_web_app')}}", formData, function(response) {
                        const message = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Enabled!') }}", text: message, type: "success", showConfirmButton: false});
                        setTimeout(function() { getWebApps(); }, 1000);
                    }, function(response) {
                        const error = JSON.parse(response).message;
                        Swal.fire("{{ __('Error!') }}", error, "error");
                    });
                })
              },
              allowOutsideClick: false
        });
    }

    function disableWebApp(line){
        var webAppName = line.querySelector('#webAppName').innerHTML;
        Swal.fire({
            title: webAppName,
            text: "{{ __('Are you sure you want to disable the web app?') }}",
            type: 'info',
            showCancelButton: true,
            confirmButtonText: "{{ __('Disable') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    formData.append("webAppName", webAppName);
                    request("{{API('disable_web_app')}}", formData, function(response) {
                        const message = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Disabled!') }}", text: message, type: "success", showConfirmButton: false});
                        setTimeout(function() { getWebApps(); }, 1000);
                    }, function(response) {
                        const error = JSON.parse(response).message;
                        Swal.fire("{{ __('Error!') }}", error, "error");
                    });
                })
              },
              allowOutsideClick: false
        });
    }

    function deleteWebApp(line){
        var webAppName = line.querySelector('#webAppName').innerHTML;
        Swal.fire({
            title: webAppName,
            text: "{{ __('Are you sure you want to delete the web app?') }}",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: "{{ __('Delete') }}", cancelButtonText: "{{ __('Cancel') }}", 
            showLoaderOnConfirm: true,
              preConfirm: () => {
                return new Promise((resolve) => {
                    let formData = new FormData();
                    formData.append("webAppName", webAppName);
                    request("{{API('delete_web_app')}}", formData, function(response) {
                        const message = JSON.parse(response).message;
                        Swal.fire({title:"{{ __('Deleted!') }}", text: message, type: "success", showConfirmButton: false});
                        setTimeout(function() { getWebApps(); }, 1000);
                    }, function(response) {
                        const error = JSON.parse(response).message;
                        Swal.fire("{{ __('Error!') }}", error, "error");
                    });
                })
              },
              allowOutsideClick: false
        });
     
    }

    function setBadges(){
        $('#webApp-table').find('th').eq(4).addClass("text-center");
        $('#webApp-table').find('th').eq(3).addClass("text-center");
        $('#webApp-table').find('.table-body').find("[id='https']").each(function(){
            $(this).addClass("text-center");
                if($(this).text() == "yes"){
                    $(this).html(`<small class="badge badge-success"><i class="fas fa-check-circle"></i></small>`);
                }else{
                    $(this).html(`<small class="badge badge-danger"><i class="fas fa-times-circle"></i></small>`);
                }
            });
        $('#webApp-table').find('.table-body').find("[id='status']").each(function(){
            $(this).addClass("text-center");
            if($(this).text() == "enabled"){
                $(this).html(`<small class="badge badge-primary">{{ __('Enabled')}}</small>`);
            }else{
                $(this).html(`<small class="badge badge-secondary">{{ __('Disabled')}}</small>`);
                    
            }
        });
    }
 
</script>