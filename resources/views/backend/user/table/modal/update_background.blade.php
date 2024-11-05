<form method="post" class="validate" autocomplete="off" action="{{ route('tables.update_background', $hallId) }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="row p-2">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Image Background') }}</label>
                <input type="file" class="form-control" name="new_url" accept=".jpg" required>
            </div>
        </div>
          
        <div class="col-md-12 mt-2">
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Save') }}</button>
            </div>
        </div>

        <small>*NOTA: Se sugiere una imagen con resolución 1920 x 1280</small>
    </div>
</form>