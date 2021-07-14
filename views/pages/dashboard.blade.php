<div class="row">
    <div class="col-md-4">

        <div class="card card-primary status-card" id="service_area">
            <div class="card-header" style="background-color: #007bff; color: #fff;">
                <h3 class="card-title">{{__('Service Status')}}</h3>
            </div>
            <div class="card-body">
                <div id="service_rows">

                </div>
                <small>*{{__('All services must be active')}}</small>
            </div>
        </div>

        <div class="card card-primary status-card collapsed collapsed-card" id="firewall_area">
            <div class="card-header" style="background-color: #007bff; color: #fff;">
                <h3 class="card-title">{{__('Firewall Status')}}</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"> <i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div id="firewall_rows">

                </div>
                <small>*{{__('All services must be allowed')}}</small>
            </div>
            <div class="overlay">
                <div class="spinner-border" role="status">
                    <span class="sr-only">{{__('Loading')}}...</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-primary nodes-card"  id="dashboardCard">
            <div class="card-header" style="background-color: #007bff; color: #fff;">
                <h3 class="card-title">{{__('Service Version')}}</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
                </div>
            </div>
            <div class="card-body" id="dashboard-table"> 

            </div>
            <div class="overlay">
                <div class="spinner-border" role="status">
                    <span class="sr-only">{{__('Loading')}}...</span>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    var dashboardServices = ["Nginx", "Pure-FTPd", "MySQL", "PostgreSQL", "PHP-FPM"];
    var firewallServices = ["Nginx", "Pure-FTPd", "MySQL", "PostgreSQL"];

    $(function() {
        dashboardServices.forEach(function (service) {
            $('#service_area').find('#service_rows').append(`
            <dl class="row">
                <dt class="col-4">${service}</dt>
                    <dd class="col-3">
                        <small class="${service.toLowerCase()}-status-badge badge">
                            <a></a>
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                        </small>
                    </dd>
                    <dd class="col-4">
                        <button type="button" onClick="manageService('${service.toLowerCase()}', 'start')" class="btn btn-primary btn-xs" data-toggle="tooltip" title="{{ __('Start') }}"><i class="fas fa-play"></i></button>
                        <button type="button" onClick="manageService('${service.toLowerCase()}', 'stop')" class="btn btn-primary btn-xs" data-toggle="tooltip" title="{{ __('Stop') }}"><i class="fas fa-stop"></i></button>
                        <button type="button" onClick="manageService('${service.toLowerCase()}', 'restart')" class="btn btn-primary btn-xs" data-toggle="tooltip" title="{{ __('Restart') }}"><i class="fas fa-sync"></i></button>
                        <button type="button" onClick="getServiceStatus('${service.toLowerCase()}')" class="btn btn-secondary btn-xs" data-toggle="tooltip" title="{{ __('See Details') }}"><i class="fas fa-info"></i></button>
                    </dd>
                </dl>
            `);
        }); 

        firewallServices.forEach(function (service) {
            $('#firewall_area').find('#firewall_rows').append(`
            <dl class="row">
                <dt class="col-4">${service}</dt>
                    <dd class="col-4">   
                        <small class="${service.toLowerCase()}-status-badge badge">
                            <a></a>
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                        </small>
                    </dd>
                    <dd class="col-3">
                        <button type="button" onClick="manageFirewall('${service.toLowerCase()}', 'allow')" class="btn btn-primary btn-xs" data-toggle="tooltip" title="{{ __('Allow') }}"><i class="fas fa-check"></i></button>
                        <button type="button" onClick="manageFirewall('${service.toLowerCase()}', 'deny')" class="btn btn-primary btn-xs" data-toggle="tooltip" title="{{ __('Deny') }}"><i class="fas fa-ban"></i></button>
                    </dd>
                </dl>
            `);
        }); 
    });

    function getDashboardContent(){
        firewallStatus();
        checkAllServices();
        getServiceVersions();
    }

    function getServiceVersions(){
        $('#dashboardCard').find('.overlay').show();
        request(API('get_service_version'), new FormData(), function(response) {
            $('#dashboard-table').html(response);
            $('#dashboardCard').find('.overlay').hide();
        }, function(response) {
            $('#dashboard-table').html("{{__('An error occurred')}}!");
            $('#dashboardCard').find('.overlay').hide();
        });
    }

    //////////////////////////////////////////////// Services ////////////////////////////////////////////////
    function checkAllServices(){
        dashboardServices.forEach(function (service) {
            checkService(service.toLowerCase());
        }); 
    }
    
    function checkService(service){
        $('#service_area').find("."+service+"-status-badge").find("a").html('');
        $('#service_area').find("."+service+"-status-badge").find(".spinner-grow").show();
        let data = new FormData();
        data.append('service', service);
        request("{{API('check_service')}}", data, function(response) {
            const service = JSON.parse(response).message;
            $('#service_area').find("."+service+"-status-badge").find("a").text('{{__('Active')}}');
            $('#service_area').find("."+service+"-status-badge").removeClass('badge-secondary').addClass('badge-success');
            $('#service_area').find("."+service+"-status-badge").find(".spinner-grow").hide();
        }, function(response) {
            const service = JSON.parse(response).message;
            $('#service_area').find("."+service+"-status-badge").find("a").text('{{__('Inactive')}}');
            $('#service_area').find("."+service+"-status-badge").removeClass('badge-success').addClass('badge-secondary');
            $('#service_area').find("."+service+"-status-badge").find(".spinner-grow").hide();
        });
    }

    function manageService(service, action){
        $('#service_area').find("."+service+"-status-badge").find("a").html('');
        $('#service_area').find("."+service+"-status-badge").find(".spinner-grow").show();
        let data = new FormData();
        data.append('service', service);
        data.append('action', action);
        request("{{API('manage_service')}}", data, function(response) {
            checkService(service);
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'info', 2000);
        });
    }

    function getServiceStatus(serviceName){
        showSwal('{{__("Reading")}}...','info');
        let data = new FormData();
        data.append('service', serviceName);
        request("{{API('get_service_status')}}", data, function (response) {
            const outputArr = JSON.parse(response).message;
            let serviceViewSBox = $("#viewServicePart1").find("#serviceSBox");
            let serviceStatusDiv = $("#viewServicePart2").find("#serviceViewStatus");
            let serviceLogDiv = $("#viewServicePart2").find("#serviceViewDetails");
            let servicePortDiv = $("#viewServicePart1").find("#serviceViewPort");
            serviceViewSBox.html(""); serviceStatusDiv.html(""); serviceLogDiv.html(""); servicePortDiv.html("");
            outputArr.forEach(function (output) {
                serviceViewSBox.append("<option value='"+output.service.split('.').join('')+"'>"+output.service+"</option>");
                const serviceName = output.service.split('.').join('');
                serviceStatusDiv.append("<pre id='viewService_"+serviceName+"' style='color:white;' hidden>"+output.status+"</pre>");
                let color = "black";
                if(output.status.includes("Active: active")) {  color="#28a745"; }
                else if(output.status.includes("Active: inactive")) { color="#868e96";}
                else if(output.status.includes("Active: failed")) { color="#dc3545"; }
                $("#viewService_"+serviceName+"").css("background-color", color);
                $("#viewService_"+serviceName+"").parent().css( "background-color", color);
                serviceLogDiv.append("<pre id='viewService_"+serviceName+"' style='color:lime;' hidden>"+output.log+"</pre>");
                if(output.program !== ""){
                    servicePortDiv.append("<p id='viewService_"+serviceName+"' hidden><strong>"+output.port+"</strong> {{__('port number is used by')}} <strong>"+output.program+"</strong> </p>");
                    $("#viewServiceModal").find(".modal-footer").find("button").hide();
                    if(output.program === output.service){
                        servicePortDiv.find("#viewService_"+serviceName).find("strong:eq(1)").css( "color", "green");
                        color="green";
                    } else{
                        if(output.program === "N/A"){
                            color="black";
                        } else{ //if is is used by another program
                            $("#viewServiceModal").find(".modal-footer").find("button").show();
                            color="red";
                        }
                    }
                    servicePortDiv.find("#viewService_"+serviceName).find("strong:eq(1)").css( "color", color);
                } else{
                    $("#viewServiceModal").find(".modal-footer").find("button").hide();
                } 
            });
            serviceStatusDiv.find("pre:first").removeAttr("hidden");
            serviceLogDiv.find("pre:first").removeAttr("hidden");
            servicePortDiv.find("p:first").removeAttr("hidden");
            $("#viewServiceModal").modal('show');
            $("#viewServicePart2").find(".overlay").hide();
            Swal.close();
        }, function(response){
            const error = JSON.parse(response).message;
            showSwal(error,'error',2000);
        })
    }


    //////////////////////////////////////////////// Firewall - UFW ////////////////////////////////////////////////
    function firewallStatus(){
        $('#firewall_area').find('.overlay').show();
        request("{{API('firewall_status')}}", new FormData(), function(response) {
            checkAllFirewalls();
        }, function(response) {
            const error = JSON.parse(response).message;
            $('#firewall_area').find(".card-body").html("<span class='status-badge badge badge-secondary'>"+error+"</span>");
            $('#firewall_area').find(".overlay").hide();
        });
    }

    function checkAllFirewalls(){
        firewallServices.forEach(function (service){
            checkFirewall(service.toLowerCase());
        });
    }

    function checkFirewall(service){
        let data = new FormData();
        data.append('service', service);
        request("{{API('check_firewall')}}", data, function(response) {
            const status = JSON.parse(response).message;
            let badge = ['{{__("Not configured")}}','secondary','success','danger'];
            if(status === 1){ badge = ['{{__("Allowed")}}','success','danger','secondary'];}
            else if(status === 0){ badge = ['{{__("Denied")}}','danger','success','secondary'];}
            $('#firewall_area').find("."+service+"-status-badge").find("a").text(badge[0]);
            $('#firewall_area').find("."+service+"-status-badge").removeClass('badge badge-'+badge[2]).removeClass('badge badge-'+badge[3]);
            $('#firewall_area').find("."+service+"-status-badge").addClass('badge badge-'+badge[1]);
            $('#firewall_area').removeClass("collapsed collapsed-card");
            $('#firewall_area').find(".card-tools").find(".fas").removeClass("fa-plus").addClass("fa-minus");
            $('#firewall_area').find("."+service+"-status-badge").find(".spinner-grow").hide();
            $('#firewall_area').find('.overlay').hide();
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

    function manageFirewall(service, action){
        $('#firewall_area').find("."+service+"-status-badge").find("a").html('');
        $('#firewall_area').find("."+service+"-status-badge").find(".spinner-grow").show();
        let data = new FormData();
        data.append('action', action);
        data.append('service', service);
        request("{{API('manage_firewall')}}", data, function(response) {
            checkFirewall(service);
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

</script>