<div class="modal fade" id="addDomainNameModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{__('Add Domain Name')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addDomainNameModal_form" onsubmit="return request(API('add_domain_name'), this, getWebApps)">
            <div id="addDomainNameModal_alert" class="alert" role="alert" hidden=""></div>
            <div class="form-group">
              <div class = "col-md-6 mx-auto text-center">
                <label class="col-form-label">{{__('Application')}}</label>
                <div class="input-group">
                    <input type="text" name="webAppName" class="form-control text-center" readonly>
                </div>
              </div>
              <label class="col-form-label">{{__('Domain Name')}}</label>
              <div class="input-group mb-3" >
                  <input type="text" name="domainName" autocomplete="off" class="form-control" placeholder="liman.dev" minlength="6" maxlength="30" required>     
              </div>
            </div>
            <div class="modal-footer justify-content-right">
                <button type="submit" class="btn btn-success">{{__('Add')}}</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>