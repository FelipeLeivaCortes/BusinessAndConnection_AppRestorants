<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('product_addons.store', $productId) }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="row p-2">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Name') }}</label>						
                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Price') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>						
                <input type="text" class="form-control float-field" name="price" value="{{ old('price') }}" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Description') }}</label>						
                <textarea class="form-control" name="description">{{ old('description') }}</textarea>
            </div>
        </div>
            
        <div class="col-md-12 mt-2">
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Save') }}</button>
            </div>
        </div>
    </div>
</form>