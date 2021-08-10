<div class="table-responsive">
  <table class="table table-bordered table-hover dataTable dtr-inline" role="grid" >
    <thead>
      <tr role="row">
        <th rowspan="1" colspan="1" style="width: 1px;"><input data-index="0" id="btSelectAll" onclick="checkAllModules()" type="checkbox"></th>
        <th class="sorting_asc" rowspan="1" colspan="1" aria-sort="ascending" style="width: 1px;">#</th>
        <th class="sorting" rowspan="1" colspan="1" >PHP {{ __('Module Name')}}</th>
        <th owspan="1" colspan="1" style="width:100px;" class="text-center">{{ __('Status')}}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($modulesData as $key => $value)
        <tr role="row">
          <td scope="row" class="bs-checkbox">
                <input data-index="{{$key+1}}" name="btSelectItem" type="checkbox" value="{{$value}}">
          </td>
          <td scope="row" class="bs-checkbox">
              <label> {{$key+1}} </label>
          </td>
          <td>{{$value}}</td>
          <td class="text-center"> 
              <small class="badge badge-secondary">{{ __('Not Installed')}}</small> 
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>