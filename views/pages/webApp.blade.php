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
                <div id="webApp-table">
                    @include('components.loading-effect')
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
            $('#webApp-table').html(response).find("table").DataTable(dataTablePresets('normal'));
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

    function getDomainNames(row){
        let webAppName = row.querySelector('#webAppName').innerHTML;
        let formData = new FormData();
        formData.append("webAppName", webAppName);
        request("{{API('get_domain_names')}}", formData, function(response) {
            $('#domainNames-table').html(response).find("table").DataTable(dataTablePresets('normal'));
            changeModalTitle('viewDomainNamesModal', '<h4><strong>'+webAppName+'</strong> | {{__("Domain Names")}} </h4>');
            Swal.close();
            $('#viewDomainNamesModal').modal('show');
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function getFtpUsers(row){
        let webAppName = row.querySelector('#webAppName').innerHTML;
        let formData = new FormData();
        formData.append("webAppName", webAppName);
        request("{{API('get_ftp_users')}}", formData, function(response) {
            $('#ftpUsers-table').html(response).find("table").DataTable(dataTablePresets('normal'));
            changeModalTitle('viewFtpUsersModal', '<h4><strong>'+webAppName+'</strong> | {{__("Virtual FTP Users")}} </h4>');
            Swal.close();
            $('#viewFtpUsersModal').modal('show');
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function setViewAlert(row){
        let webAppName = row.querySelector('#webAppName').innerHTML;
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
                    switch(result){
                        case 'domainName':
                            getDomainNames(row);
                            break;
                        case 'ftpUser':
                            getFtpUsers(row);
                            break;
                    }
                })
              }
        });
    }

    function enableWebApp(row){
        const webAppName = row.querySelector('#webAppName').innerHTML;
        let form = new FormData();
            form.append("webAppName", webAppName);
        createConfirmationAlert(
            webAppName,
            '{{ __("Are you sure you want to enable the web app?") }}',
            form,
            'enable_web_app',
            'getWebApps()'
        );
    }

    function disableWebApp(row){
        const webAppName = row.querySelector('#webAppName').innerHTML;
        let form = new FormData();
            form.append("webAppName", webAppName);
        createConfirmationAlert(
            webAppName,
            '{{ __("Are you sure you want to disable the web app?") }}',
            form,
            'disable_web_app',
            'getWebApps()'
        );
    }

    function deleteWebApp(row){
        const webAppName = row.querySelector('#webAppName').innerHTML;
        let form = new FormData();
            form.append("webAppName", webAppName);
        createConfirmationAlert(
            webAppName,
            '{{ __("Are you sure you want to delete the web app?") }}',
            form,
            'delete_web_app',
            'getWebApps()'
        );
    }

    function setBadges(){
        $('#webApp-table').find('th').eq(4).addClass("text-center");
        $('#webApp-table').find('th').eq(3).addClass("text-center");
        $('#webApp-table').find('table').find("td[id='https']").each(function(){
            $(this).addClass("text-center");
                if($(this).text() == "yes"){
                    $(this).html(`<small class="badge badge-success"><i class="fas fa-check-circle"></i></small>`);
                }else{
                    $(this).html(`<small class="badge badge-danger"><i class="fas fa-times-circle"></i></small>`);
                }
            });
        $('#webApp-table').find('table').find("td[id='status']").each(function(){
            $(this).addClass("text-center");
            if($(this).text() == "enabled"){
                $(this).html(`<small class="badge badge-primary">{{ __('Enabled')}}</small>`);
            }else{
                $(this).html(`<small class="badge badge-secondary">{{ __('Disabled')}}</small>`);
                    
            }
        });
    }
 
</script>