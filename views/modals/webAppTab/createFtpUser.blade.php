<div class="modal fade" id="addFtpUserModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{__('Create Virtual FTP User')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addFtpUserModal_form" onsubmit="return request('/extensionRun/create_ftp_user', this, getWebApps)">
            <div id="addFtpUserModal_alert" class="alert" role="alert" hidden=""></div>
            <div class="form-group" id="ftpUserArea">
              <div class = "col-md-6 mx-auto text-center">
                <label class="col-form-label">{{__('Application')}}</label>
                <div class="input-group">
                    <input type="text" name="webAppName" class="form-control text-center" readonly>
                </div>
              </div>
              <label class="col-form-label">{{__('Username')}}<small> | {{__('Required')}}</small></label>
              <div class="input-group mb-3" >
                  <input type="text" name="ftpUsername" autocomplete="off" class="form-control" placeholder="{{__('Username')}}" minlength="3" maxlength="25" required>     
              </div>
              <label class="col-form-label">{{__('Password')}}<small> | {{__('Required')}}</small></label>
              <div class="input-group mb-3" >
                  <input type="password" name="ftpPassword" autocomplete="off" class="form-control" placeholder="{{__('Password')}}" minlength="6" maxlength="25" required>     
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