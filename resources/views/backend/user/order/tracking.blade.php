@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="alert alert-warning">
			<strong><i class="fas fa-info-circle"></i> {{ _lang("Orders are showing from the last 24 hour") }}</strong>
		</div>
		<div class="card">
		    <div class="card-header">
				<span class="panel-title">{{ _lang('Live Order Tracking') }}</span>
			</div>
			
			<div class="card-body overflow-auto">
				<div id="kanban-view">
					<ul class="kanban-col">
					    <li>
							<p class="kanban-title" style="background:#ff4757">{{ _lang('Pending') }}</p>
							<div class="cards">
								<ul class="status order-status" id="order-status-0" data-order-status-id="0">			    
									<!--Order List-->
									@if(isset($ordersList['Pending']))
									@foreach($ordersList['Pending'] as $pendingOrder)
									<li id="order-{{ $pendingOrder->id }}" data-order-id="{{ $pendingOrder->id }}">
										<div class="card">
											<div class="card-body">
												<p>{{ _lang('Order ID') }}# {{ $pendingOrder->order_number }}</p>
												<p>{{ _lang('Created') }}# {{ $pendingOrder->created_at }}</p>
												<p>{{ $pendingOrder->order_type == 'table' ? $pendingOrder->table : _lang('Order Type') .': '.ucwords($pendingOrder->order_type) }}</p>
												<p>{{ _lang('Grand Total') }}: {{ formatAmount($pendingOrder->grand_total, $currency_symbol) }}</p>
												<div class="mt-1">
													<a href="{{ route('pos.print_customer_receipt', $pendingOrder->id) }}" target="_blank" class="btn btn-primary btn-xs"><i class="fas fa-print"></i></a>
													<a href="{{ route('orders.show', $pendingOrder->id) }}" target="_blank" class="btn btn-dark btn-xs"><i class="far fa-eye"></i></a>
													<a href="{{ route('orders.edit', $pendingOrder->id) }}" data-title="{{ _lang('Order Details') }}" class="btn btn-warning btn-xs ajax-modal"><i class="fas fa-pencil-alt"></i></a>
												</div>	
											</div>	
										</div>
									</li>									
									@endforeach	
									@endif
								</ul>								
							</div>
						</li>
					</ul>

					<ul class="kanban-col">
					    <li>
							<p class="kanban-title" style="background:#10ac84">{{ _lang('Accepted') }}</p>
							<div class="cards">
								<ul class="status order-status" id="order-status-1" data-order-status-id="1">			    
									<!--Order List-->
									@if(isset($ordersList['Accepted']))
									@foreach($ordersList['Accepted'] as $acceptedOrder)
									<li id="order-{{ $acceptedOrder->id }}" data-order-id="{{ $acceptedOrder->id }}">
										<div class="card">
											<div class="card-body">
												<p>{{ _lang('Order ID') }}# {{ $acceptedOrder->order_number }}</p>
												<p>{{ _lang('Created') }}# {{ $acceptedOrder->created_at }}</p>
												<p>{{ $acceptedOrder->order_type == 'table' ? $acceptedOrder->table : _lang('Order Type') .': '. ucwords($acceptedOrder->order_type) }}</p>
												<p>{{ _lang('Grand Total') }}: {{ formatAmount($acceptedOrder->grand_total, $currency_symbol) }}</p>
												<div class="mt-1">
													<a href="{{ route('pos.print_customer_receipt', $acceptedOrder->id) }}" target="_blank" class="btn btn-primary btn-xs"><i class="fas fa-print"></i></a>
													<a href="{{ route('orders.show', $acceptedOrder->id) }}" target="_blank" class="btn btn-dark btn-xs"><i class="far fa-eye"></i></a>
													<a href="{{ route('orders.edit', $acceptedOrder->id) }}" data-title="{{ _lang('Order Details') }}" class="btn btn-warning btn-xs ajax-modal"><i class="fas fa-pencil-alt"></i></a>
												</div>	
											</div>	
										</div>
									</li>									
									@endforeach	
									@endif
								</ul>								
							</div>
						</li>
					</ul>

					<ul class="kanban-col">
					    <li>
							<p class="kanban-title" style="background:#5f27cd">{{ _lang('Preparing') }}</p>
							<div class="cards">
								<ul class="status order-status" id="order-status-2" data-order-status-id="2">			    
									<!--Order List-->
									@if(isset($ordersList['Preparing']))
									@foreach($ordersList['Preparing'] as $preparingOrder)
									<li id="order-{{ $preparingOrder->id }}" data-order-id="{{ $preparingOrder->id }}">
										<div class="card">
											<div class="card-body">			
												<p>{{ _lang('Order ID') }}# {{ $preparingOrder->order_number }}</p>
												<p>{{ _lang('Created') }}# {{ $preparingOrder->created_at }}</p>
												<p>{{ $preparingOrder->order_type == 'table' ? $preparingOrder->table : _lang('Order Type') .': '. ucwords($preparingOrder->order_type) }}</p>
												<p>{{ _lang('Grand Total') }}: {{ formatAmount($preparingOrder->grand_total, $currency_symbol) }}</p>
												<div class="mt-1">
													<a href="{{ route('pos.print_customer_receipt', $preparingOrder->id) }}" target="_blank" class="btn btn-primary btn-xs"><i class="fas fa-print"></i></a>
													<a href="{{ route('orders.show', $preparingOrder->id) }}" target="_blank" class="btn btn-dark btn-xs"><i class="far fa-eye"></i></a>
													<a href="{{ route('orders.edit', $preparingOrder->id) }}" data-title="{{ _lang('Order Details') }}" class="btn btn-warning btn-xs ajax-modal"><i class="fas fa-pencil-alt"></i></a>
												</div>	
											</div>	
										</div>
									</li>									
									@endforeach	
									@endif
								</ul>								
							</div>
						</li>
					</ul>

					<ul class="kanban-col">
					    <li>
							<p class="kanban-title" style="background:#341f97">{{ _lang('Ready') }}</p>
							<div class="cards">
								<ul class="status order-status" id="order-status-3" data-order-status-id="3">			    
									<!--Order List-->
									@if(isset($ordersList['Ready']))
									@foreach($ordersList['Ready'] as $readyOrder)
									<li id="order-{{ $readyOrder->id }}" data-order-id="{{ $readyOrder->id }}">
										<div class="card">
											<div class="card-body">
												<p>{{ _lang('Order ID') }}# {{ $readyOrder->order_number }}</p>
												<p>{{ _lang('Created') }}# {{ $readyOrder->created_at }}</p>
												<p>{{ $readyOrder->order_type == 'table' ? $readyOrder->table : _lang('Order Type') .': '. ucwords($readyOrder->order_type) }}</p>
												<p>{{ _lang('Grand Total') }}: {{ formatAmount($readyOrder->grand_total, $currency_symbol) }}</p>											
												<div class="mt-1">
													<a href="{{ route('pos.print_customer_receipt', $readyOrder->id) }}" target="_blank" class="btn btn-primary btn-xs"><i class="fas fa-print"></i></a>
													<a href="{{ route('orders.show', $readyOrder->id) }}" target="_blank" class="btn btn-dark btn-xs"><i class="far fa-eye"></i></a>
													<a href="{{ route('orders.edit', $readyOrder->id) }}" data-title="{{ _lang('Order Details') }}" class="btn btn-warning btn-xs ajax-modal"><i class="fas fa-pencil-alt"></i></a>
												</div>	
											</div>	
										</div>
									</li>									
									@endforeach	
									@endif
								</ul>								
							</div>
						</li>
					</ul>

					<ul class="kanban-col">
					    <li>
							<p class="kanban-title" style="background:#3742fa">{{ _lang('Delivered') }}</p>
							<div class="cards">
								<ul class="status order-status" id="order-status-4" data-order-status-id="4">			    
									<!--Order List-->
									@if(isset($ordersList['Delivered']))
									@foreach($ordersList['Delivered'] as $deliveredOrder)
									<li id="order-{{ $deliveredOrder->id }}" data-order-id="{{ $deliveredOrder->id }}">
										<div class="card">
											<div class="card-body">
												<p>{{ _lang('Order ID') }}# {{ $deliveredOrder->order_number }}</p>
												<p>{{ _lang('Created') }}# {{ $deliveredOrder->created_at }}</p>
												<p>{{ $deliveredOrder->order_type == 'table' ? $deliveredOrder->table : _lang('Order Type') .': '. ucwords($deliveredOrder->order_type) }}</p>
												<p>{{ _lang('Grand Total') }}: {{ formatAmount($deliveredOrder->grand_total, $currency_symbol) }}</p>							
												<div class="mt-1">
													<a href="{{ route('pos.print_customer_receipt', $deliveredOrder->id) }}" target="_blank" class="btn btn-primary btn-xs"><i class="fas fa-print"></i></a>
													<a href="{{ route('orders.show', $deliveredOrder->id) }}" target="_blank" class="btn btn-dark btn-xs"><i class="far fa-eye"></i></a>
													<a href="{{ route('orders.edit', $deliveredOrder->id) }}" data-title="{{ _lang('Order Details') }}" class="btn btn-warning btn-xs ajax-modal"><i class="fas fa-pencil-alt"></i></a>
												</div>	
											</div>	
										</div>
									</li>									
									@endforeach	
									@endif
								</ul>								
							</div>
						</li>
					</ul>
				</div>
				<audio id="myAudio" muted="true" src="{{ asset('public/backend/assets/notification.mp3') }}"></audio>
			</div>
	    </div>
	</div>
</div>
@endsection

@section('js-script')
<script src="https://cdn.ably.com/lib/ably.min-1.js"></script>
<script src="{{ asset('public/backend/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('public/backend/assets/js/live-order.js?v=1.2') }}"></script>
@endsection
