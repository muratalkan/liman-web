<div class="modal fade" id="createMySQLUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">MySQL | {{__('Create User')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="createMySQLUserModal_form" onsubmit="return request('/extensionRun/create_mysql_user', this, getMySQLUsers)">
        <div id="createMySQLUserModal_alert" class="alert" role="alert" hidden=""></div>
            <div class="form-group">
                <label class="col-form-label">{{__('Username')}}</label>
                <div class="input-group">
                    <input type="text" name="userName" autocomplete="off" class="form-control" placeholder="{{__('Username')}}" minlength="2" maxlength="20" required>     
                    <div class="input-group-append"> <span class="input-group-text">@</span> </div>
                    <input type="text" name="hostName" autocomplete="off" class="form-control" placeholder="{{__('Host')}} ({{__('Not Required')}})" minlength="1" maxlength="20">  
                </div>
                <label class="col-form-label">{{__('Password')}}</label>
                <div class="input-group">
                    <input type="password" name="userPassword" autocomplete="off" class="form-control"  placeholder="{{__('Password')}} ({{__('Not Required')}})" minlength="6" maxlength="25">
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