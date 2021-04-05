<div class="modal fade" id="addWebAppModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{__('Add Application')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addWebAppModal_form" onsubmit="return request('/extensionRun/set_web_app', this, getWebApps)">
            <div id="addWebAppModal_alert" class="alert" role="alert" hidden=""></div>
            <div class="form-group">
                <label class="col-form-label">{{__('Application Name')}}<small> | {{__('Required')}}</small></label>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">/var/www/</span>
                    </div>
                    <input type="text" name="webAppName" autocomplete="off" class="form-control" placeholder="liman" minlength="2" maxlength="30" required> 
                    <div class="input-group-append">
                        <span class="input-group-text">/html</span>
                    </div>
                </div>
                <label class="col-form-label">{{__('PHP Version')}}</label>
                    <select class="custom-select" name="phpVersion">
                    @php $phpArr = \App\Classes\Php::getSupportedVersions(); @endphp
                        @foreach($phpArr as $phpVer)
                            <option id="v_{{str_replace('.', '', $phpVer)}}" value="{{$phpVer}}">{{$phpVer}}</option>
                        @endforeach
                    </select>
                </div>
                <label class="col-form-label">{{__('HTTPS')}}</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <input type="checkbox" name="httpsStatus" value="off" onchange="setHttps(this)">
                    </span>
                </div>
                <div id ="ssl_que_area">
                    <label class="col-form-label">{{__('SSL Certificate')}}</label>
                    <div class="input-group">
                        <span> {{__('Is SSL Certificate available?')}}
                            <input type="radio" name="SslCertStatus" value="yes" onchange="setSSL(this)"> <label>{{__('Yes')}}</label>
                            <input type="radio" name="SslCertStatus" value="no" onchange="setSSL(this)"> <label>{{__('No')}}</label>
                        </span>
                    </div>
                </div>
                <div id="ssl_info_area">
                    <label class="col-form-label" id="ssl_email_label">{{__('Self-Signed SSL Certificate')}} | {{__('Email')}}<small> | {{__('Required')}}</small></label>
                        <input type="email" name="sslEmail" autocomplete="off" class="form-control" placeholder="{{__('Email Address')}} (e.g. mail@liman.dev)" minlength="7" maxlength="35"> 
                    <label class="col-form-label" id="ssl_info_label">{{__('Self-Signed SSL Certificate')}} | {{__('Information')}}<small> | {{__('Required')}}</small></label>
                    <div class="input-group">
                        <input type="text" name="sslCountryName" autocomplete="off" class="form-control" placeholder="{{__('Country Name')}} (e.g. TR)" minlength="2" maxlength="2"> 
                        <input type="text" name="sslStateName" autocomplete="off" class="form-control" placeholder="{{__('State Name')}} (e.g. Ankara)" minlength="2" maxlength="30"> 
                    </div>
                    <div class="input-group">
                        <input type="text" name="sslLocalName" autocomplete="off" class="form-control" placeholder="{{__('Local Name')}} (e.g. Çankaya)" minlength="2" maxlength="30"> 
                        <input type="text" name="sslOrgName" autocomplete="off" class="form-control" placeholder="{{__('Org. Name')}} (e.g. Havelsan)" minlength="2" maxlength="30"> 
                    </div>
                    <div class="input-group">
                        <input type="text" name="sslOrgUnitName" autocomplete="off" class="form-control" placeholder="{{__('Org. Unit Name (e.g. Software)')}}" minlength="2" maxlength="30"> 
                        <input type="text" name="sslCommonName" autocomplete="off" class="form-control" placeholder="{{__('Common Name')}} (e.g. liman.dev)" minlength="2" maxlength="30" > 
                    </div>
                </div>
                <div id="ssl_opt_area">
                    <label class="col-form-label" id="ssl_info_label">{{__('Self-Signed SSL Certificate')}} | {{__('Key and Certificate Path')}}<small> | {{__('Required')}}</small></label>
                    <input type="text" name="sslKeyPath" autocomplete="off" class="form-control" placeholder="{{__('Key Path')}} (e.g. /etc/nginx/liman.dev/liman.key)" minlength="3" maxlength="40" id="ssl_keyPath"> 
                    <input type="text" name="sslCrtPath" autocomplete="off" class="form-control" placeholder="{{__('Certificate Path')}} (e.g. /etc/nginx/liman.dev/liman.crt)" minlength="3" maxlength="40" id="ssl_crtPath"> 
                </div>
                <div class="modal-footer justify-content-right">
                    <button type="submit" class="btn btn-success">{{__('Add')}}</button>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>

    initializeWebAppModal();
    function initializeWebAppModal(){
        resetWebAppForm();
        $('#v_PHP73').attr('selected', 'selected'); //default php: 7.3
    }

    function setHttps(input){
        if(input.checked != true ){ //unchecked
            resetWebAppForm();
        }
        else{
            $("#ssl_que_area").fadeIn(0).removeAttr("disabled");
        }
    }

    function resetWebAppForm(){
        $("#ssl_que_area").fadeOut(0).attr("disabled");
        $("#ssl_info_area").fadeOut(0).attr("disabled");
        $("#ssl_opt_area").fadeOut(0).attr("disabled");
        $("#ssl_que_area input").prop("checked", false);
        $("#ssl_info_area").find("input").removeAttr("required");
        $("#ssl_opt_area").find("input").removeAttr("required");
        $("#ssl_info_area").find("input").val('');
        $("#ssl_opt_area").find("input").val('');
    }

    function setSSL(input){
        if(input.value != "no" ){ //existing certificate
            $("#ssl_info_area").fadeOut(0).attr("disabled");
            $("#ssl_opt_area").fadeIn(0).removeAttr("disabled");
            $("#ssl_info_area").find("input").attr("required", false);
            $("#ssl_opt_area").find("input").attr("required", true);
            $("#ssl_info_area").find("input").val('');
        }
        else{ //create self-signed certificate
            $("#ssl_info_area").fadeIn(0).removeAttr("disabled");
            $("#ssl_opt_area").fadeOut(0).attr("disabled");
            $("#ssl_info_area").find("input").attr("required", true);
            $("#ssl_opt_area").find("input").attr("required", false);
            $("#ssl_opt_area").find("input").val('');
        }
    }



</script>