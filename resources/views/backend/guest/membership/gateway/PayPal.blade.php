@extends('layouts.guest')

@section('content')
<div class="row">
	<div class="col-lg-6 offset-lg-3">
		<div class="card">
			<div class="card-header bg-light">
				<h4 class="header-title text-center">{{ _lang('Pay Via PayPal') }}</h4>
			</div>
			<div class="card-body">
                <div class="row">
					<div class="col-md-12">
						<table class="table table-bordered">
							<tr>
								<td>{{ _lang('Package Name') }}</td>
								<td>{{ $package->name }}</td>
							</tr>
							<tr>
								<td>{{ _lang('Cost') }}</td>
								<td>{{ decimalPlace($package->cost, currency_symbol()) }}</td>
							</tr>
							@if($package->discount > 0)
							<tr>
								<td>{{ _lang('Discount') }}</td>
								<td>{{ $package->discount }}%</td>
							</tr>
							@endif
							<tr>
								<td>{{ _lang('Grand Total') }}</td>
								<td>{{ decimalPlace($package->cost - ($package->discount / 100) * $package->cost, currency_symbol()) }}</td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="paypal-button-container"></div>
								</td>
							</tr>					
						</table>
					</div>
                </div>
			</div>
		</div>
    </div>
</div>
@endsection

@section('js-script')
<!--PayPal Pay Now Button-->
<script src="https://www.paypal.com/sdk/js?client-id={{ $gateway->parameters->client_id }}&currency={{ currency() }}&disable-funding=credit,card"></script>

<div id="paypal-button-container"></div>

<script>
  paypal.Buttons({
	createOrder: function(data, actions) {
	  	return actions.order.create({
			purchase_units: [{
			  amount: {
				value: '{{ ($package->cost - ($package->discount / 100) * $package->cost) }}'
			  }
			}]
	 	});
	},
	onApprove: function(data, actions) {
		window.location.href = "{{ $data->callback_url }}?order_id=" + data.orderID + "&user_id={{ $data->user_id }}&slug={{ $slug }}";
	},
	onCancel: function (data) {
		alert("{{ _lang('Payment Cancelled') }}");
	}
  }).render('#paypal-button-container');

</script>
@endsection