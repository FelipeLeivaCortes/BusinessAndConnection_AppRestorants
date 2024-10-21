<form method="post" class="validate" autocomplete="off" action="{{ route('tables.update', $id) }}" enctype="multipart/form-data">
    @csrf
    <input name="_method" type="hidden" value="PATCH">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Table No') }}</label>						
                <input type="number" class="form-control" name="table_no" value="{{ $table->table_no }}" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Table Type') }}</label>						
                <select class="form-control auto-select" data-selected="{{ $table->type }}" name="type" required>
                    <option value="">{{ _lang('Select One') }}</option>
                    <option value="square">{{ _lang('Square') }}</option>
                    <option value="rectangle">{{ _lang('Rectangle') }}</option>
                    <option value="round">{{ _lang('Round') }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Chair Limit') }}</label>						
                <input type="number" class="form-control" name="chair_limit" value="{{ $table->chair_limit }}" required>
            </div>
        </div>
      
        <div class="col-md-12 mt-2">
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Update') }}</button>
            </div>
        </div>
    </div>
</form>