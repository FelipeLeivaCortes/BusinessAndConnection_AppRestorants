@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-6">
		<div class="card">
		    <div class="card-header">
				<span class="panel-title">{{ _lang('Order Details') }}</span>
			</div>
			
			<div class="card-body">
			    <table class="table table-bordered">
				    <tr><td>{{ _lang('Order Number') }}</td><td>{{ $order->order_number }}</td></tr>
					<tr><td>{{ _lang('Status') }}</td><td>{!! xss_clean(order_status($order->status)) !!}</td></tr>
					<tr><td>{{ _lang('Sub Total') }}</td><td>{{ formatAmount($order->sub_total, currency_symbol(request()->activeBusiness->currency)) }}</td></tr>
					<tr><td>{{ _lang('Discount') }}</td><td>{{ formatAmount($order->discount, currency_symbol(request()->activeBusiness->currency)) }}</td></tr>
					<tr><td>{{ _lang('Service Charge') }}</td><td>{{ formatAmount($order->service_charge, currency_symbol(request()->activeBusiness->currency)) }}</td></tr>
					<tr><td>{{ _lang('Grand Total') }}</td><td>{{ formatAmount($order->grand_total, currency_symbol(request()->activeBusiness->currency)) }}</td></tr>
					<tr><td>{{ _lang('Paid') }}</td><td>{{ formatAmount($order->paid, currency_symbol(request()->activeBusiness->currency)) }}</td></tr>
					@if($order->paid > $order->grand_total)
                    <tr><td>{{ _lang('Change') }}</td><td>{{ formatAmount($order->paid - $order->grand_total, currency_symbol(request()->activeBusiness->currency)) }}</td></tr>
					@endif
                    <tr><td>{{ _lang('Note') }}</td><td>{{ $order->note }}</td></tr>
					<tr><td>{{ _lang('Payment Method') }}</td><td>{{ str_replace('_',' ',$order->payment_method) }}</td></tr>
					<tr><td>{{ _lang('Order Type') }}</td><td>{{ ucwords($order->order_type) }}</td></tr>
					<tr><td>{{ _lang('Delivery Time') }}</td><td>{{ $order->delivery_time }}</td></tr>

					@if($order->order_type == 'table')
					<tr><td>{{ _lang('Table') }}</td><td>{{ $order->table }}</td></tr>
					@endif

					@if($order->customer_id != null)
					<tr><td>{{ _lang('Customer Name') }}</td><td>{{ $order->customer_name }}</td></tr>
					<tr><td>{{ _lang('Customer Email') }}</td><td>{{ $order->customer_email }}</td></tr>
					<tr><td>{{ _lang('Customer Phone') }}</td><td>{{ $order->customer_phone }}</td></tr>
					<tr><td>{{ _lang('Customer City') }}</td><td>{{ $order->customer_city }}</td></tr>
					<tr><td>{{ _lang('Customer State') }}</td><td>{{ $order->customer_state }}</td></tr>
					<tr><td>{{ _lang('Customer Address') }}</td><td>{{ $order->customer_address }}</td></tr>
					@endif
					<tr><td>{{ _lang('Created By') }}</td><td>{{ $order->created_by->name }}</td></tr>
			    </table>
			</div>
	    </div>
	</div>

	@php $currency_sumbol = currency_symbol(request()->activeBusiness->currency); @endphp

	<div class="col-lg-6">
		<div class="card">
		    <div class="card-header">
				<span class="panel-title">{{ _lang('Order Items') }}</span>
			</div>
			
			<div class="card-body">
				<table class="table border">
					<thead class="bg-white">
						<th class="text-dark">{{ _lang('Product') }}</th>
						<th class="text-dark text-center">{{ _lang('Qty') }}</th>
						<th class="text-dark text-right">{{ _lang('Price') }}</th>
						<th class="text-dark text-right">{{ _lang('Total') }}</th>
					</thead>
					<tbody>
						@foreach($order->items as $item)
						<tr>
							<td>{{ $item->product_name }}</td>
							<td class="text-center">{{ $item->quantity }}</td>
							<td class="text-right text-nowrap">{{ formatAmount($item->unit_cost, $currency_sumbol) }}</td>
							<td class="text-right text-nowrap">{{ formatAmount($item->sub_total, $currency_sumbol) }}</td>
						</tr>
						@endforeach
						<tr>
							<td class="border-top pt-2" colspan="3"><b>{{ _lang('Sub Total') }}:</b></td>
							<td class="text-right border-top pt-2 text-nowrap"><b>{{ formatAmount($order->sub_total) }}</b></td>
						</tr>

						@foreach($order->taxes as $tax)
						<tr>
							<td colspan="3">{{ $tax->name }} ({{ $tax->rate }}%):</td>
							<td class="text-right text-nowrap">+ {{ formatAmount($tax->amount, $currency_sumbol) }}</td>
						</tr>
						@endforeach

						@if($order->service_charge > 0)
						<tr>
							<td colspan="3">{{ _lang('Service Charge') }}:</td>
							<td class="text-right text-nowrap">+ {{ formatAmount($order->service_charge, $currency_sumbol) }}</td>
						</tr>
						@endif

						@if($order->discount > 0)
						<tr>
							<td colspan="3">{{ _lang('Discount') }} ({{ $order->discount_percentage }}%):</td>
							<td class="text-right text-nowrap">- {{ formatAmount($order->discount, $currency_sumbol) }}</td>
						</div>
						@endif

						<tr>
							<td class="border-top pt-2" colspan="3"><b>{{ _lang('Grand Total') }}:</b></td>
							<td class="text-right border-top pt-2 text-nowrap"><b>{{ formatAmount($order->grand_total, $currency_sumbol) }}</b></td>
						</tr>

						<tr>
							<td colspan="3"><b>{{ _lang('Paid') }}:</b></td>
							<td class="text-right text-nowrap"><b>{{ formatAmount($order->paid, $currency_sumbol) }}</b></td>
						</tr>

						@if($order->paid > $order->grand_total)
						<tr>
							<td colspan="3">{{ _lang('Change') }}:</td>
							<td class="text-right text-nowrap">{{ formatAmount($order->paid - $order->grand_total, $currency_sumbol) }}</td>
						</tr>
						@endif

						@if($order->paid > 0)
						<tr>
							<td colspan="3">{{ _lang('Pay Mode') }}:</td>
							<td class="text-right">{{ str_replace('_', ' ', $order->payment_method) }}</td>
						</tr>
						@endif
					</tbody>
				</table>
			</div>
	    </div>
	</div>
</div>
@endsection
