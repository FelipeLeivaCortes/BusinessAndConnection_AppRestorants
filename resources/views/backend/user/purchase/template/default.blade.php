@php $type = isset($type) ? $type : 'preview'; @endphp
<!-- Default Invoice template -->
<div id="invoice" class="{{ $type }}">	
	<div class="default-invoice">			
		<div class="invoice-header">
			<div class="row">
				<div class="col-6 float-left left-header">
					<h2 class="title">{{ $purchase->title }}</h2>
					@if($type == 'pdf')
					<img class="logo" src="{{ public_path('uploads/media/' . $purchase->business->logo) }}">
					@else
					<img class="logo" src="{{ asset('public/uploads/media/' . $purchase->business->logo) }}">
					@endif
				</div>
				<div class="col-6 float-right right-header">
					<h4 class="company-name">{{ $purchase->business->name }}</h4>
					<p>{{ $purchase->business->address }}</p>
					<p>{{ $purchase->business->phone }}</p>
					<p>{{ $purchase->business->email }}</p>
					<p>{{ $purchase->business->country }}</p>
				</div>
				<div class="clear"></div>
			</div>
		</div>

		<div class="invoice-details">
			<div class="row align-items-bottom">
				<div class="col-6 float-left">
					<h5 class="bill-to-heading">{{ _lang('BILLING DETAILS') }}</h5>

					<h4 class="bill-to">{{ $purchase->vendor->name }}</h4>
					<p>{{ $purchase->vendor->address }}</<p>
					<p>{{ $purchase->vendor->mobile }}</<p>
					<p>{{ $purchase->vendor->city }}</<p>
					<p>{{ $purchase->vendor->zip }}</<p>
					<p>{{ $purchase->vendor->country }}</p>
				</div>
				<div class="col-6 text-right float-right">
					<h5 class="mb-2">{{ _lang('Bill No') }}#: {{ $purchase->bill_no }}</h4>
					<p>{{ _lang('Purchase Date') }}: {{ $purchase->purchase_date }}</p>
					<p class="mb-2">{{ _lang('Due Date') }}: {{ $purchase->due_date }}</p>
					<p><strong>{{ _lang('Grand Total') }}: {{ formatAmount($purchase->grand_total, currency_symbol($purchase->business->currency), $purchase->business_id) }}</strong></p>
					<p><strong>{{ _lang('Due Amount') }}: {{ formatAmount($purchase->grand_total - $purchase->paid, currency_symbol($purchase->business->currency), $purchase->business_id) }}</strong></p>
				</div>
				<div class="clear"></div>
			</div>
		</div>

		@php $invoiceColumns = json_decode(get_business_option('invoice_column', null, $purchase->business_id)); @endphp
							
		<div class="invoice-body">
			<div class="table-responsive-sm">
				<table class="table">
					<thead>
						<tr>
							<th>{{ _lang('Name') }}</th>
							<th class="text-center">{{ _lang('Quantity') }}</th>
							<th class="text-right">{{ _lang('Price') }}</th>
							<th class="text-right">{{ _lang('Amount') }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($purchase->items as $item)
						<tr>	
							<td class="product-name">
								<p>{{ $item->product_name }}</p>			
								<p>{{ $item->description }}</p>
							</td>
							<td class="text-center">{{ $item->quantity.' '.$item->product->measurement_unit }}</td>
							<td class="text-right text-nowrap">{{ formatAmount($item->unit_cost, currency_symbol($purchase->business->currency), $purchase->business_id) }}</td>
							<td class="text-right text-nowrap">{{ formatAmount($item->sub_total, currency_symbol($purchase->business->currency), $purchase->business_id) }}</td>	
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

		<div class="invoice-summary">
			<div class="row">
				<div class="col-xl-7 col-lg-6 float-left">
					<div class="invoice-note">
						<p><b>{{ _lang('Notes / Terms') }}:</b> {!! xss_clean($purchase->note) !!}</p>
					</div>
				</div>
				<div class="col-xl-5 col-lg-6 float-right">
					<table class="table text-right m-0">
						<tr>
							<td>{{ _lang('Sub Total') }}</td>
							<td class="text-nowrap">{{ formatAmount($purchase->sub_total, currency_symbol($purchase->business->currency), $purchase->business_id) }}</td>
						</tr>
						@foreach($purchase->taxes as $tax)
						<tr>
							<td>{{ $tax->name }}</td>						
							<td class="text-nowrap">+ {{ formatAmount($tax->amount, currency_symbol($purchase->business->currency), $purchase->business_id) }}</td>
						</tr>
						@endforeach	
						<tr>
							<td>{{ _lang('Discount') }}</td>
							<td class="text-nowrap">- {{ formatAmount($purchase->discount, currency_symbol($purchase->business->currency), $purchase->business_id) }}</td>
						</tr>
						<tr>
							<td><b>{{ _lang('Grand Total') }}</b></td>
							<td class="text-nowrap"><b>{{ formatAmount($purchase->grand_total, currency_symbol($purchase->business->currency), $purchase->business_id) }}</b></td>
						</tr>
					</table>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>

	<div class="invoice-footer">
		<p>{!! xss_clean($purchase->footer) !!}</p>
	</div>

</div>

