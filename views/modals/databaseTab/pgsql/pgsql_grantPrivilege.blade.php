<div class="modal fade" id="grantPgSQLPrivilegesModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">PostgreSQL | {{__('Grant Privilege')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="grantPgSQLPrivilegesModal_form" onsubmit="return request(API('grant_pgsql_privileges'), this, getPgSQLContent)">
        <div id="grantPgSQLPrivilegesModal_alert" class="alert" role="alert" hidden=""></div>
        <div class="form-group">
            <label class="col-form-label">{{__('User')}}</label>
            <div class="input-group">
                <input type="text" name="userName" class="form-control" readonly>
            </div>
            <br>
            <div class="row">
                <div class="col-sm-12">
                    <div class="input-group">
                        <span> {{__('Select Privilege Type')}}:
                            <input type="radio" name="privilegeType" value="db" onchange="setPrivilegeType(this)" checked>
                            <label>{{__('Database')}}</label>
                            <input type="radio" name="privilegeType" value="user" onchange="setPrivilegeType(this)">
                            <label>{{__('User')}}</label>
                        </span>
                    </div>
                </div>
            </div>
            <div id ="privilege_pgsqlDB_area">
                <label class="col-form-label">{{__('Database')}}</label>
                <select class="custom-select" name="databaseSelection" id="pgsql_databaseSBox"> </select>
                <br><br>
                <label class="col-form-label">{{__('Database Privileges')}}</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="privilege_all" onchange="controlPgSQLPrivileges(this)">
                    <label class="form-check-label">{{__('All Privileges')}}</label>
                </div>
                <div class="form-group col-12"><hr></div>
                <div class="row" id="privilege_pgsqlDB_area2">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="privilege_connect">
                                <label class="form-check-label">{{__('Connect')}}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id ="privilege_pgsqlUser_area">
                <label class="col-form-label">{{__('User Privileges')}}</label>
                <div class="form-group col-12"><hr></div>
                <div class="row">
                    <div class="col-sm-5">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="privilege_superUser">
                                <label class="form-check-label">{{__('Superuser')}}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="privilege_createDB">
                                <label class="form-check-label">{{__('Create Database')}}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="privilege_createRole">
                                <label class="form-check-label">{{__('Create Role')}}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5" >
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="privilege_replication">
                                <label class="form-check-label">{{__('Replication')}}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="privilege_bypassRls">
                                <label class="form-check-label">{{__('Bypass RLS')}}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer justify-content-right">
            <button type="submit" class="btn btn-success">{{__('Grant Privilege')}}</button>
        </div>
        </form>
      </div>
    </div>
  </div>
</div>


<script>

    initializePgSQLPrivilegeModal();
    function initializePgSQLPrivilegeModal(){
        $("#privilege_pgsqlUser_area").fadeOut(0);
        $("#privilege_pgsqlDB_area").fadeIn(0).removeAttr("disabled");
        $("#privilege_pgsqlDB_area2").find("input").removeAttr("disabled");
    }
    
    getPgSQLDatabaseSBox();
    function getPgSQLDatabaseSBox(){
        let data = new FormData();
        data.append('databaseList', "pgsql");
        request("{{API('get_pgsql_databases')}}",  data, function(response) {
            let dbList = JSON.parse(response).message;
            $("#pgsql_databaseSBox").html("");
            dbList.forEach(function(db){
                $("#pgsql_databaseSBox").append("<option value='"+db['dbName']+"'>"+db['dbName']+"</option>");
            });
        }, function(response) {
            let error = JSON.parse(response);
            showSwal(error.message, 'error', 2000);
        });
    }

    function controlPgSQLPrivileges(input){
        if(input.checked){ //all
            $("#privilege_pgsqlDB_area2").find("input").prop('checked', false);
            $("#privilege_pgsqlDB_area2").find("input").attr("disabled", true);
        } else{
            $("#privilege_pgsqlDB_area2").find("input").removeAttr("disabled");
        }
    }

    function setPrivilegeType(input){
        if(input.value != "user" ){ //db
            $("#privilege_pgsqlUser_area").fadeOut(0).attr("disabled", true);
            $("#privilege_pgsqlUser_area").find("input").prop('checked', false);
            $("#privilege_pgsqlDB_area").fadeIn(0).removeAttr("disabled");
        }
        else{ //user
            $("#privilege_pgsqlDB_area").fadeOut(0).attr("disabled", true);
            $("#privilege_pgsqlDB_area").find("input").prop('checked', false);
            $("#privilege_pgsqlUser_area").fadeIn(0).removeAttr("disabled");
        }
    }

</script>