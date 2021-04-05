<div class="modal fade" id="grantMySQLPrivilegesModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">MySQL | {{__('Grant Privilege')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="grantMySQLPrivilegesModal_form" onsubmit="return request('/extensionRun/grant_mysql_privileges', this, getMySQLUsers)">
            <div id="grantMySQLPrivilegesModal_alert" class="alert" role="alert" hidden=""></div>
            <div class="form-group">
                <label class="col-form-label">{{__('User')}}</label>
                <div class="input-group">
                    <input type="text" name="userName" class="form-control" readonly>  
                    <div class="input-group-append"> <span class="input-group-text">@</span></div>   
                    <input type="text" name="hostName" class="form-control" readonly>  
                </div>
                <br>
                <label class="col-form-label">{{__('Database')}}</label>
                    <select class="custom-select" name="databaseSelection" id="mysql_databaseSBox"></select>
                <br><br>
                <label class="col-form-label">{{__('Privileges')}}</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="privilege_all" onchange="controlMySQLPrivileges(this)">
                    <label class="form-check-label">{{__('All Privileges')}}</label>
                </div>
                <div class="form-group col-12"><hr></div>
                    <div class="row" id="privilege_mysqlDB_area2">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="privilege_create">
                                    <label class="form-check-label">{{__('Create')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="privilege_drop">
                                    <label class="form-check-label">{{__('Drop')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="privilege_delete">
                                    <label class="form-check-label">{{__('Delete')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="privilege_insert">
                                    <label class="form-check-label">{{__('Insert')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="privilege_select">
                                    <label class="form-check-label">{{__('Select')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="privilege_update">
                                    <label class="form-check-label">{{__('Update')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="privilege_grant">
                                    <label class="form-check-label">{{__('Grant Option')}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-right">
                    <button type="submit" class="btn btn-success">{{__('Grant Privilege')}}</button>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>

    initializeMySQLPrivilegeModal();
    function initializeMySQLPrivilegeModal(){
        $("#privilege_mysqlDB_area2").find("input").removeAttr("disabled");
    }

    getMySQLDatabaseSBox();
    function getMySQLDatabaseSBox(){
        let data = new FormData();
        data.append('databaseList', "mysql");
        request("{{API('get_mysql_databases')}}",  data, function(response) {
            let dbList = JSON.parse(response).message;
            $("#mysql_databaseSBox").html("<option value='-All-'>{{__('Tümü')}}</option>");
            dbList.forEach(function(db){
                $("#mysql_databaseSBox").append("<option value='"+db['dbName']+"'>"+db['dbName']+"</option>");
            });
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 2000);
        });
    }

    function controlMySQLPrivileges(input){
        if(input.checked){
            $("#privilege_mysqlDB_area2").find("input").prop('checked', false);
            $("#privilege_mysqlDB_area2").find("input").attr("disabled", true);
        } else{
            $("#privilege_mysqlDB_area2").find("input").removeAttr("disabled");
        }
    }

</script>