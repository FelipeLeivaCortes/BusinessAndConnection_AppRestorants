<form method="post" id="place-order-form" autocomplete="off" action="{{ route('pos.place_order', $table_id) }}" enctype="multipart/form-data">
	@csrf
	<div class="row px-2">
		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Grand Total') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>
				<input type="text" class="form-control" id="dueAmount" value="{{ $grandTotal }}" readonly>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Way To Pay') }}</label>
				@if($orderStatus != null)
					<input type="text" class="form-control" value="{{ _lang($order->way_payment) }}" readonly>
				@else
					<select class="form-control" id="selectWay2Pay" name="way_to_pay">
						<option value="">{{ _lang('Select One') }}</option>
						<option value="total_pay">{{ _lang('Total Pay') }}</option>
						<option value="partial_pay">{{ _lang('Partial Pay') }}</option>
					</select>
				@endif
			</div>
		</div>

		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Payment Method') }}</label>

				@if($orderStatus != null)
					<input type="text" class="form-control" value="{{ str_replace('|', ' - ', $order->payment_method) }}" readonly>
				@else
					<select class="form-control select2-ajax selectPaymentMethods" id="selectPaymentMethods" name="payment_method" data-value="name" data-display="name" data-table="transaction_methods"
						data-where="3" data-href="{{ route('transaction_methods.create') }}" data-title="{{ _lang('New Method') }}">
						<option value="">{{ _lang('Select One') }}</option>
						@if($orderStatus != null)
							<option value="{{ $order->payment_method }}" selected>{{ $order->payment_method }}</option>
						@endif
					</select>
				@endif
			</div>
		</div>

		<div class="col-lg-12 d-none" id="containerAllPartialPays">
			<div class="form-group">
				<label class="control-label">{{ _lang('All Method To Pay') }}</label>
				<table class="table table-striped" id="tableMethodsSelected">
					<thead>
						<tr>
							<th>#</th>
							<th>Método</th>
							<th>Cantidad</th>
							<th>Acción</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>

		<div class="col-lg-6 d-none" id="containerAmount">
			<div class="form-group">
				<label class="control-label">{{ _lang('Amount Paid') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>
				<input type="text" class="form-control float-amount" id="receivedAmount" name="amount" value="{{ old('amount', $paidAmount) }}">
			</div>
		</div>

		<div class="col-lg-6 d-none" id="containerChangeAmount">
			<div class="form-group">
				<label class="control-label">{{ _lang('Change Amount') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>
				<input type="text" class="form-control" id="changeAmount" value="{{ $paidAmount }}" readonly>
				{{-- <input type="text" class="form-control"  value="{{ $paidAmount > 0 ? formatAmount($paidAmount - $grandTotal) : 0 }}" readonly> --}}
			</div>
		</div>

		@if (isset($orderStatus) && $orderStatus != null)
			<div class="col-lg-6">
				<div class="form-group">
					<label class="control-label">Monto Pagado: ({{ currency_symbol(request()->activeBusiness->currency) }})</label>
					<input type="text" class="form-control" name="total_paid" value="{{ $grandTotal }}" readonly required>
				</div>
			</div>
		@endif

		<div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Order Status') }}</label>
				<select id="selectOrderStatus" class="form-control auto-select" name="status" data-selected="{{ $orderStatus != null ? $orderStatus : $defaultStatus }}" required>
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
				<label class="control-label">{{ _lang('Delivery Time') }}</label>
				<input type="text" class="form-control datetimepicker" name="delivery_time" value="{{ $orderStatus != null ? $order->delivery_time : old('delivery_time', now()) }}" required>
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

<script type="text/javascript" defer>
	$(document).ready(function(){
		let totalToPay	= <?php echo json_encode($grandTotal); ?>;
		totalToPay		= totalToPay.split('.')[0];
		totalToPay		= totalToPay.replace(/\,/g, '');

		$('#selectWay2Pay').on('change', function(){
			const value	= $(this).val();

			if (value !== 'partial_pay') {
				$('#selectPaymentMethods').attr('required');
				$('#containerAllPartialPays').addClass('d-none');
				$('#containerAmount').removeClass('d-none');
				$('#selectPaymentMethods').val('');

			} else {
				$('#selectPaymentMethods').off();
				$('#selectPaymentMethods').removeAttr('required');
				$('#selectPaymentMethods').change(function(){
					const paymentMethod	= $(this).val();

					if (paymentMethod) {
						const rowCount	= $('#tableMethodsSelected tbody tr').length + 1;
						const newRow	= `<tr>
							<td>${rowCount}</td>
							<td>
								<input type="text" class="form-control-plaintext" value="${paymentMethod}" readonly>
								<input type="hidden" name="methodPayment[]" value="${paymentMethod}">
							</td>
							<td><input class="form-control" type="number" name="amountPayment[]" required"></td>
							<td><button type="button" class="btn btn-danger btn-xs deleteBtn">Retirar</button></td>
						</tr>`;
						$('#tableMethodsSelected tbody').append(newRow);
						$(this).val('');
					}
				});

				$('#tableMethodsSelected').on('click', '.deleteBtn', function() {
					const row		= $(this).closest('tr');
					const itemValue	= row.find('td:nth-child(2)').text();

					$('#selectPaymentMethods').find(`option[value="${itemValue}"]`).show();
					row.remove();
					updateRowNumbers();
				});

				function updateRowNumbers() {
					$('#tableMethodsSelected tbody tr').each(function(index) {
						$(this).find('td:first').text(index + 1);
					});
				}
				
				$('#containerAllPartialPays').removeClass('d-none');
				$('#containerAmount').addClass('d-none');
				$('#selectPaymentMethods').val('');
			}
		});

		$('#place-order-form').off();
		$('#place-order-form').on('submit', function(event) {
			event.preventDefault();

			if ($('#selectOrderStatus').val() == '0') {
				if ($('#selectWay2Pay').val() === 'partial_pay') {
					const formData			= {};
					const methodsPayment	= [];
					const partialPays		= [];
		
					$('input[name="methodPayment[]"]').each(function() {
						methodsPayment.push($(this).val());
					});
		
					$('input[name="amountPayment[]"]').each(function() {
						partialPays.push(parseFloat($(this).val()) || 0);
					});
		
					let partialSum = 0;
					for (let i = 0; i < partialPays.length; i++) {
						partialSum += partialPays[i];
					}
		
					if (partialSum != 0 && partialSum !== parseInt(totalToPay)) {
						alert('Los montos pagados no coinciden con el total del pedido');
						return false;
					}

				} else {
					const amountPaid	= $('#receivedAmount').val();

					if (parseInt(amountPaid) != parseInt(totalToPay)) {
						alert('El monto pagado no coincide con el total del pedido');
						return false;
					}
				}
			}

			this.submit();
		});
	});
</script>
