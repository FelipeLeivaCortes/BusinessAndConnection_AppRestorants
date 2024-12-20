<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('purchases.add_payment', $purchase->id) }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<div class="row px-2">
	    <div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Date') }}</label>
				<input type="text" class="form-control datetimepicker" name="trans_date" value="{{ old('trans_date', now()) }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Due Amount') }}</label>
				<input type="text" class="form-control float-field" id="due_amount" value="{{ formatAmount($purchase->grand_total - $purchase->paid, currency_symbol($purchase->business->currency), $purchase->business_id) }}" readonly>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Amount') }}</label>
				<input type="text" class="form-control float-field" name="amount" value="{{ old('amount', $purchase->grand_total - $purchase->paid) }}" required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Payment Method') }}</label>
				<select class="form-control auto-select select2-ajax" data-selected="{{ old('method') }}" name="method"
				data-table="transaction_methods" data-value="name" data-display="name" data-where="8" data-title="{{ _lang('New Method') }}"
				data-href="{{ route('transaction_methods.create') }}" required>
					<option value="">{{ _lang('Select One') }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Reference') }}</label>
				<input type="text" class="form-control" name="reference" value="{{ old('reference') }}">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Description') }}</label>
				<textarea class="form-control" name="description">{{ old('description') }}</textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Attachment') }}</label></br>
				<input type="file" class="dropify" name="attachment">
			</div>
		</div>

		<div class="col-md-12 mt-2">
		    <div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Submit') }}</button>
		    </div>
		</div>
	</div>
</form>


<script>
$(document).on('change','#account_id', function(){
	var account_id = $(this).val();
	if(account_id != ''){
		$.ajax({
			url: _url + `/user/accounts/${account_id}/{{ $purchase->grand_total - $purchase->paid }}/convert_due_amount`,
			success: function(data){
				var json = JSON.parse(JSON.stringify(data));
				$("#due_amount").val(json['formatAmount']);
			}
		});
	}
});
</script>
