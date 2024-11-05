<form method="post" id="place-order-form" autocomplete="off" action="{{ route('pos.place_order', $table_id) }}" enctype="multipart/form-data">
	@csrf
	<div class="row px-2">
		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Payment Method') }}</label>						
				<select class="form-control select2-ajax" name="payment_method" data-value="name" data-display="name" data-table="transaction_methods" 
					data-where="3" data-href="{{ route('transaction_methods.create') }}" data-title="{{ _lang('New Method') }}" required>
					<option value="">{{ _lang('Select One') }}</option>
					@if($orderStatus != null)
					<option value="{{ $order->payment_method }}" selected>{{ $order->payment_method }}</option>
					@endif
				</select>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Order Status') }}</label>						
				<select class="form-control auto-select" name="status" data-selected="{{ $orderStatus != null ? $orderStatus : $defaultStatus }}" required>
					<option value="">{{ _lang('Select One') }}</option>
					<option value="0">1 - {{ _lang('Pending') }}</option>
					<option value="1">2 - {{ _lang('Accepted') }}</option>
					<option value="2">3 - {{ _lang('Preparing') }}</option>
					<option value="3">4 - {{ _lang('Ready') }}</option>
					<option value="4">5 - {{ _lang('Delivered') }}</option>
					<option value="5">6 - {{ _lang('Completed') }}</option>
				</select>
			</div>
		</div>

		@if($orderType != 'table')
		<div class="col-lg-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Customer') }}</label>						
				<select class="form-control select2-ajax" data-value="id" data-display="name" data-table="customers" 
				data-where="3" data-title="{{ _lang('Add New Customer') }}" data-href="{{ route('customers.create') }}" 
				name="customer_id" required>
				@if($orderStatus != null)
                    <option value="{{ $order->customer->id }}">{{ $order->customer->name }}</option>
                @endif
				</select>
			</div>
		</div>
		@endif

		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Grand Total') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>						
				<input type="text" class="form-control" id="dueAmount" value="{{ $grandTotal }}" readonly>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Delivery Time') }}</label>						
				<input type="text" class="form-control datetimepicker" name="delivery_time" value="{{ $orderStatus != null ? $order->delivery_time : old('delivery_time', now()) }}" required>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Amount Paid') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>
				<input type="text" class="form-control float-amount" id="receivedAmount" name="amount" value="{{ old('amount', $paidAmount) }}">
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Change Amount') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>
				<input type="text" class="form-control" id="changeAmount" value="{{ $paidAmount }}" readonly>
				{{-- <input type="text" class="form-control"  value="{{ $paidAmount > 0 ? formatAmount($paidAmount - $grandTotal) : 0 }}" readonly> --}}
			</div>
		</div>

		@if($orderType == 'table')
		<div class="col-lg-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Customer') }}</label>
				<select class="form-control select2-ajax" data-value="id" data-display="name" data-table="customers" 
				data-where="3" data-title="{{ _lang('Add New Customer') }}" data-href="{{ route('customers.create') }}" 
				name="customer_id">
				</select>
			</div>
		</div>
		@endif

		<div class="col-lg-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Note') }}</label>
				<textarea class="form-control" name="note">{{ $orderStatus != null ? $order->note : old('note') }}</textarea>
			</div>
		</div>

		<div class="col-lg-12">
			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-block"><i class="ti-check-box mr-2"></i> {{ _lang('Submit') }}</button>
			</div>
		</div>
		
	</div>
</form>
