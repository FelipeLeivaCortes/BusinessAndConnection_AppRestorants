<div class="row p-2">
	<div class="col-lg-12">
		<table class="table table-bordered">
			<tr><td>{{ _lang('Date') }}</td><td>{{ $transaction->trans_date }}</td></tr>
			<tr><td>{{ _lang('Category') }}</td><td>{{ $transaction->transaction_category->name }}</td></tr>
			<tr><td>{{ _lang('Type') }}</td><td>{{ ucwords($transaction->type) }}</td></tr>
			<tr><td>{{ _lang('Amount') }}</td><td>{{ formatAmount($transaction->amount, currency_symbol(request()->activeBusiness->currency)) }}</td></tr>
			<tr><td>{{ _lang('Method') }}</td><td>{{ $transaction->method }}</td></tr>
			<tr><td>{{ _lang('Reference') }}</td><td>{{ $transaction->reference }}</td></tr>
			<tr><td>{{ _lang('Description') }}</td><td>{{ $transaction->description }}</td></tr>
			<tr>
				<td>{{ _lang('Attachment') }}</td>
				<td>
					@if($transaction->attachment != '')
					<a href="{{ asset('public/uploads/media/'.$transaction->attachment) }}" target="_blank">{{ $transaction->attachment }}</a>
					@endif
				</td>
			</tr>
			<tr><td>{{ _lang('Vendor') }}</td><td>{{ $transaction->vendor->name }}</td></tr>
			<tr><td>{{ _lang('Employee') }}</td><td>{{ $transaction->staff->name }}</td></tr>
		</table>
	</div>
</div>


