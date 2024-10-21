<style>#main_modal .modal-lg { max-width: 400px; }</style>
<form method="post" id="discount-form" autocomplete="off" action="{{ route('pos.add_discount', $table_id) }}" enctype="multipart/form-data">
	@csrf
	<div class="row px-2">
	    <div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Discount') }} (%)</label>						
				<input type="number" class="form-control" name="discount" value="{{ old('discount', $discount) }}" required>
			</div>
		</div>

		<div class="col-md-12">
		    <div class="form-group">
			    <button type="submit" class="btn btn-primary btn-block"><i class="ti-check-box mr-2"></i> {{ _lang('Submit') }}</button>
		    </div>
		</div>
	</div>
</form>
