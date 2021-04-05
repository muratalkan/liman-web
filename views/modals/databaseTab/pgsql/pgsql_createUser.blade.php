<div class="modal fade" id="createPgSQLUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">PostgreSQL | {{__('Create User')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="createPgSQLUserModal_form" onsubmit="return request('/extensionRun/create_pgsql_user', this, getPgSQLUsers)">
          <div id="createPgSQLUserModal_alert" class="alert" role="alert" hidden=""></div>
            <div class="form-group">
                <label class="col-form-label">{{__('Username')}}<small> | {{__('Required')}}</small></label>
                <div class="input-group">
                    <input type="text" name="userName" autocomplete="off" class="form-control" placeholder="{{__('Username')}}" minlength="2" maxlength="20" required>     
                </div>
                <label class="col-form-label">{{__('Password')}}</label>
                <div class="input-group">
                    <input type="password" name="userPassword" autocomplete="off" class="form-control" placeholder="{{__('Password')}} ({{__('Not Required')}})" minlength="6" maxlength="25" >
                </div>
            </div>
            <div class="modal-footer justify-content-right">
                <button type="submit" class="btn btn-success">{{__('Create')}}</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>