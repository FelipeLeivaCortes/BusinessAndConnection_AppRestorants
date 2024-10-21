@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
		<div class="card">
		    <div class="card-header">
				<span class="panel-title">{{ _lang('Package Details') }}</span>
			</div>
			
			<div class="card-body">
			    <table class="table table-bordered">
				    <tr><td>{{ _lang('Pasckage Name') }}</td><td>{{ $package->name }}</td></tr>
				    <tr><td>{{ _lang('Package Type') }}</td><td>{{ ucwords($package->package_type) }}</td></tr>
					<tr><td>{{ _lang('Cost') }}</td><td>{{ decimalPlace($package->cost, currency_symbol()) }}</td></tr>
					<tr><td>{{ _lang('Status') }}</td><td>{!! xss_clean(status($package->status)) !!}</td></tr>
					<tr>
						<td>{{ _lang('Is Popular') }}</td>
						<td>
							@if($package->is_popular == 1)
							{!! xss_clean(show_status(_lang('Yes'), 'success')) !!}
							@else
							{!! xss_clean(show_status(_lang('No'), 'danger')) !!}
							@endif
						</td>
					</tr>
					<tr><td>{{ _lang('Discount') }}</td><td>{{ $package->discount }} %</td></tr>
					<tr><td>{{ _lang('Trial Days') }}</td><td>{{ $package->trial_days }}</td></tr>
					<tr><td>{{ _lang('Restaurant Limit') }}</td><td>{{ $package->business_limit != '-1' ? $package->business_limit : _lang('Unlimited') }}</td></tr>
					<tr><td>{{ _lang('System User Limit') }}</td><td>{{ $package->staff_limit != '-1' ? $package->staff_limit : _lang('Unlimited') }}</td></tr>
					<tr><td>{{ _lang('Item Limit') }}</td><td>{{ $package->item_limit != '-1' ? $package->item_limit : _lang('Unlimited') }}</td></tr>
					<tr><td>{{ _lang('Order Limit') }}</td><td>{{ $package->order_limit != '-1' ? $package->order_limit : _lang('Unlimited') }}</td></tr>
					<tr>
						<td>{{ _lang('Payroll Module') }}</td>
						<td>
							@if($package->payroll_module == 1)
							{!! xss_clean(show_status(_lang('Yes'), 'success')) !!}
							@else
							{!! xss_clean(show_status(_lang('No'), 'danger')) !!}
							@endif
						</td>
					</tr>
			    </table>
			</div>
	    </div>
	</div>
</div>
@endsection


