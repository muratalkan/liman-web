<div class="alert alert-info" role="alert">
  <i class="fas fa-info-circle mr-2"></i>{{__("In order to use this extension, you must install the necessary packages on the server. You can install it by clicking on 'Install Packages' button below")}}.
</div>

<button id="installPackageButton" class="btn btn-secondary" onclick="installPackages()">{{__("Install Packages")}}</button>

@component('modal-component',[
    "id" => "packageInstallerModal",
    "title" => "Package Installer"
])@endcomponent

<script>

    function installPackages(){
      showSwal('{{__("Loading")}}...','info',2000);
      request(API('install_package'), new FormData(), function (response) {
        const output = JSON.parse(response).message;
        $("#installPackageButton").attr("disabled","true");
        $('#packageInstallerModal').modal({backdrop: 'static', keyboard: false})
        $('#packageInstallerModal').find('.modal-body').html(output);
        $('#packageInstallerModal').modal("show"); 
      }, function(response){
          const error = JSON.parse(response).message;
          showSwal(error,'error',2000);
      })
    }
    

    $('#packageInstallerModal').on('hidden.bs.modal', function () {
        $(this).find('.modal-body').html("");
    })

    function onTaskSuccess(){
        showSwal('{{__("Your request has been successfully completed")}}', 'success', 2000);
        setTimeout(function(){
          $('#packageInstallerModal').modal("hide"); 
        }, 2000);
        window.location.href = 'index';
    }

    function onTaskFail(){
        showSwal('{{__("An error occurred while processing your request")}}!', 'error', 2000);
    }

</script>