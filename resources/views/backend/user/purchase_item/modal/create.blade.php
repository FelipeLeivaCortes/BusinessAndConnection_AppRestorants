<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('purchase_items.store') }}" enctype="multipart/form-data">
	@csrf
	<div class="row px-2">
		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Name') }}</label>						
				<input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Measurement Unit') }}</label>						
				<input type="text" class="form-control" name="measurement_unit" value="{{ old('measurement_unit') }}" placeholder="EX: KG" required>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Purchase Cost') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>						
				<input type="text" class="form-control float-field" name="purchase_cost" value="{{ old('purchase_cost') }}" required>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Status') }}</label>						
				<select class="form-control auto-select" data-selected="{{ old('status', 1) }}" name="status" required>
					<option value="1">{{ _lang('Active') }}</option>
					<option value="0">{{ _lang('Disabled') }}</option>
				</select>
			</div>
		</div>

		<div class="col-lg-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Image') }}</label>						
				<input type="file" class="form-control dropify" name="image" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG">
			</div>
		</div>

		<div class="col-lg-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Descriptions') }}</label>						
				<textarea class="form-control" name="descriptions">{{ old('descriptions') }}</textarea>
			</div>
		</div>

		<div class="col-lg-12 mt-2">
			<div class="form-group">
				<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Save') }}</button>
			</div>
		</div>
	</div>
</form>
