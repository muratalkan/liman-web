@component('modal-component',[
    "id" => "viewServiceModal",
    "title" => "Service Information",
    "footer" => [
        "text" => __("Configure Port"),
        "class" => "btn-primary",
        "onclick" => "configureServicePort()"
    ]
])

<div class="row" id="viewServicePart1">
    <div class="col-md-6 mx-auto" >
        <div class="card card-secondary">
            <div class="card-header" >
                <div> 
                    <select class='custom-select' id='serviceSBox' onchange='showServiceStatusView()'></select>
                </div>
            </div>
            <div class="card-body mx-auto collapsed p-0">
                <div id="serviceViewPort" style="margin-top:15px;"> </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="viewServicePart2">
    <div class="col-md-6">
        <div class="card card-secondary">
            <div class="card-header" data-card-widget="collapse">
                <h3 class="card-title">{{ __('Service Status') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool">
                        <i style="color:black" class="fas fa-plus"></i>
                    </button>
                 </div>
            </div>
            <div class="card-body collapsed p-0">
                <div style="height:250px; overflow-y: auto;" id="serviceViewStatus"> </div>
            </div>
            <div class="overlay">
                <div class="spinner-border" role="status">
                    <span class="sr-only">{{__('Loading')}}...</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-secondary">
            <div class="card-header" data-card-widget="collapse">
                <h3 class="card-title">{{ __('Service Logs') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool">
                        <i style="color:black" class="fas fa-plus"></i>
                    </button>
                 </div>
            </div>
            <div class="card-body collapsed p-0">
                <div style="height:250px; overflow-y: auto; background:black;" id="serviceViewDetails"> </div>
            </div>
            <div class="overlay">
                <div class="spinner-border" role="status">
                    <span class="sr-only">{{__('Loading')}}...</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endcomponent


<script>

    function showServiceStatusView(){
        $("#viewServicePart2").find(".overlay").show();
        $("#viewServicePart1").find("p").attr("hidden", true);
        $("#viewServicePart2").find("pre").attr("hidden", true);
        
        const service = $("#viewServicePart1").find("#serviceSBox option:selected").val();
        $("#viewServiceModal").find("[id='viewService_"+service+"']").removeAttr("hidden");
        setTimeout(function() { $("#viewServicePart2").find(".overlay").hide();  }, 500);
    }

    function configureServicePort(){
        showSwal("{{__('Configuring')}}...", 'info');
        const service = $("#viewServicePart1").find("#serviceSBox option:selected").val();
        const program = $("#viewServicePart1").find("#serviceViewPort").find("strong:eq(1)").text();
        let formData = new FormData();
        formData.append('service', service);
        formData.append('program', program);
        request("{{API('conf_service_port')}}", formData, function(response) {
            const output = JSON.parse(response).message;
            Swal.close();
            showSwal(output, 'info', 2000);
            $("#viewServiceModal").modal('hide');
            checkAllServices();
        }, function(response) {
            const error = JSON.parse(response).message;
            showSwal(error, 'error', 2000);
        });
    }

</script>