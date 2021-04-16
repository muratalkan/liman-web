<div class="modal fade" id="createPgSQLDBModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">PostgreSQL | {{__('Create Database')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="createPgSQLDBModal_form" onsubmit="return request(API('create_pgsql_database'), this, getPgSQLDatabases)">
            <div id="createPgSQLDBModal_alert" class="alert" role="alert" hidden=""></div>
            <div class="form-group">
                <label class="col-form-label">{{__('Database Name')}}<small> | {{__('Required')}}</small></label>
                <div class="input-group mb-3">
                    <input type="text" name="databaseName" autocomplete="off" placeholder="{{__('Enter the Database Name')}}" class="form-control " required>   
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