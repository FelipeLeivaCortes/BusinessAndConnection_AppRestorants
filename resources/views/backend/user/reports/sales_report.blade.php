@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Sales Report') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>

			<div class="card-body">

				<div class="report-params">
					<form class="validate" method="post" action="{{ route('reports.sales_report') }}" autocomplete="off">
						<div class="row">
              				@csrf
							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('Start Date') }}</label>
									<input type="text" class="form-control datepicker" name="date1" id="date1" value="{{ isset($date1) ? $date1 : old('date1', \Carbon\Carbon::now()->startOfMonth()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('End Date') }}</label>
									<input type="text" class="form-control datepicker" name="date2" id="date2" value="{{ isset($date2) ? $date2 : old('date2', \Carbon\Carbon::now()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-xl-2 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('Seller') }}</label>
									<select class="form-control select2 auto-select" data-selected="{{ isset($created_user_id) ? $created_user_id : old('created_user_id') }}" name="created_user_id">
										<option value="">{{ _lang('All Seller') }}</option>
										@foreach(request()->activeBusiness->users as $user)
											<option value="{{ $user->id }}">{{ $user->name }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-xl-2 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('Status') }}</label>
									<select class="form-control select2 auto-select" data-selected="{{ isset($status) ? $status : old('status') }}" name="status">
										<option value="">{{ _lang('All') }}</option>
										<option value="0">{{ _lang('Pending') }}</option>
										<option value="1">{{ _lang('Accepted') }}</option>
										<option value="2">{{ _lang('Preparing') }}</option>
										<option value="3">{{ _lang('Ready') }}</option>
										<option value="4">{{ _lang('Delivered') }}</option>
										<option value="5">{{ _lang('Completed') }}</option>
									</select>
								</div>
							</div>

							<div class="col-xl-2 col-lg-4">
								<button type="submit" class="btn btn-light btn-xs btn-block mt-26"><i class="ti-filter mr-1"></i>{{ _lang('Filter') }}</button>
							</div>
						</form>

					</div>
				</div><!--End Report param-->

				@php $date_format = get_date_format(); @endphp

				<div id="report">
					<div class="report-header">
						<h4>{{ request()->activeBusiness->name }}</h4>
						<p>{{ _lang('Sales Report') }}</p>
						<p>{{ isset($date1) ? date($date_format, strtotime($date1)).' '._lang('to').' '.date($date_format, strtotime($date2)) : '----------  '._lang('to').'  ----------' }}</p>
					</div>

					<div class="table-responsive">
						<table class="table">
							<thead>
								<th>{{ _lang('Date') }}</th>
								<th>{{ _lang('Order Number') }}</th>
								<th class="text-right">{{ _lang('Sub Total') }}</th>
								<th class="text-right">{{ _lang('Grand Total') }}</th>
								<th class="text-right">{{ _lang('Paid') }}</th>
								<th class="text-center">{{ _lang('Status') }}</th>
								<th>{{ _lang('Order Type') }}</th>
							</thead>
							<tbody>
							@if(isset($report_data))
								@php $date_format = get_date_format(); @endphp

								@foreach($report_data as $order)
									<tr>
										<td>{{ $order->created_at }}</td>
										<td>{{ $order->order_number }}</td>
										<td class="text-right">{{ formatAmount($order->sub_total, $currency_symbol) }}</td>
										<td class="text-right">{{ formatAmount($order->grand_total, $currency_symbol) }}</td>
										<td class="text-right">{{ formatAmount($order->paid, $currency_symbol) }}</td>	
										<td class="text-center">{!! xss_clean(order_status($order->status)) !!}</td>
										<td>{{ ucwords($order->order_type) }}</td>						
									</tr>
								@endforeach
							@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection