<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12 px-4 bg-white py-3 d-md-flex align-items-center">	
			<a href="{{ route('dashboard.index') }}">
				<h4 class="pos-restaurant-name mb-2 mb-md-0">{{ request()->activeBusiness->name }}</h4>
			</a>
			
			<div class="mb-2 mb-md-0 flex-fill">
				<div class="search-box-container">
					<input type="text" class="pos-search-box" placeholder="{{ _lang('Search Product') }}...">
				</div>
			</div>

			<div class="mb-2 mb-md-0 flex-fill d-md-flex justify-content-end">
				<span class="dropdown">
					<button class="btn btn-primary dropdown-toggle btn-xs  mr-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-list-ul"></i> {{ _lang('Orders') }}
					</button>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
						<a href="{{ route('pos.table') }}" class="dropdown-item ajax-link select-table"><i class="fas fa-hotel mr-2"></i>{{ _lang('Select Table') }}</a>
						@if(get_business_option('order_delivery_status', 0, business_id()) == 1)
						<a href="{{ route('pos.sell', ['d'.rand().time(), 'delivery']) }}" class="dropdown-item ajax-link"><i class="fas fa-biking mr-2"></i>{{ _lang('New Delivery') }}</a>
						@endif

						@if(get_business_option('order_takeway_status', 0, business_id()) == 1)
						<a href="{{ route('pos.sell', ['t'.rand().time(), 'takeway']) }}" class="dropdown-item ajax-link"><i class="fas fa-people-carry mr-2"></i>{{ _lang('New Takeway') }}</a>
						@endif
						<a href="{{ route('pos.active_orders') }}" class="dropdown-item ajax-link"><i class="fas fa-list-ul mr-2"></i>{{ _lang('Active Orders') }}</a>
					</div>
				</span>

				<a href="{{ route('logout') }}" class="btn btn-danger btn-xs"><i class="fas fa-sign-out-alt"></i> {{ _lang('Logout') }}</a>
			</div>
		</div>
	</div>


	<div class="row mt-4">
		<div class="col-xl-9 col-lg-8 col-md-6 px-4">	
			<div id="pos-item-category" class="sticky">
				<ul>
					<li><a href="#" data-id="" data-class="" class="active">{{ _lang('All Items') }}</a></li>
					@foreach($categories as $category)
					<li><a href="#" data-id="{{ $category->id }}" data-class=".category-{{ $category->id }}">{{ $category->name }}</a></li>
					@endforeach
				</ul>
			</div>

			<div id="pos-items" class="mt-4">
				<div class="row">
				@foreach($products as $product)
					<div class="col-lg-3 col-sm-6 category-{{ $product->category_id }}">
						<a href="{{ route('pos.product', $product->id) }}?table_id={{ $tableId }}" class="ajax-modal" data-title="{{ $product->name }}">
							<div class="item" data-id="{{ $product->id }}">
								<p class="category-name">{{ $product->category->name }}</p>
								<img src="{{ asset('public/uploads/media/' . $product->image) }}">
								<p class="item-name">{{ $product->name }}</p>
								<p class="item-price">		
									@if($product->special_price > 0)
									<span class="text-secondary"><del>{{ formatAmount($product->price, currency_symbol(request()->activeBusiness->currency)) }}</del></span>
									{{ formatAmount($product->special_price, currency_symbol(request()->activeBusiness->currency)) }}
									@else
									{{ formatAmount($product->price, currency_symbol(request()->activeBusiness->currency)) }}
									@endif
								</p>
					
							</div>
						</a>
					</div>
				@endforeach
				</div>
			</div>
		</div>

		<div class="col-xl-3 col-lg-4 col-md-6 px-4">	
			<div id="need-update" class="alert alert-warning {{ $needUpdate == false ? 'd-none' : '' }}">
				<strong><i class="fas fa-info-circle"></i> {{ _lang('You need to update the order !') }}</strong>
			</div>
			<div id="pos-cart" class="sticky">
				<div class="cart-heading d-flex align-items-center justify-content-between">
					@if($type == 'table')
					<h5>{{ $table->hall->name }} - {{ _lang('Table').' '.$table->table_no }}</h5>
					@elseif($type == 'delivery')
					<h5>{{ _lang('Delivery Order') }}</h5>
					@elseif($type == 'takeway')
					<h5>{{ _lang('Takeway Order') }}</h5>
					@endif
					<span class="dropdown">
						<button class="btn btn-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-tasks"></i>
						</button>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
							<a href="{{ route('pos.add_discount', $tableId) }}" data-title="{{ _lang('Add Discount') }}" class="dropdown-item ajax-modal"><i class="fas fa-percentage mr-1"></i> {{ _lang('Add Discount') }}</a>
							@if($orderStatus != null)
							<a href="{{ route('pos.print_kitchen_receipt', $orderId) }}" target="_blank" class="dropdown-item"><i class="fas fa-receipt mr-1"></i> {{ _lang('Kitchen Receipt') }}</a>
							<a href="{{ route('pos.print_customer_receipt', $orderId) }}" target="_blank" class="dropdown-item"><i class="fas fa-file-invoice-dollar mr-1"></i> {{ _lang('Customer Receipt') }}</a>
							@endif
						</div>
					</span>
				</div>
				<div class="cart-content">
					@if($cartItems->count() > 0)
					@foreach($cartItems as $cartId => $cartItem)
					<div class="cart-item justify-content-between">
						<div class="d-flex">
							<div class="flex-grow-1">
								<span class="item-name">{{ $cartItem['name'] }}</span>
							</div>
							<div class="action ml-2">
								<a href="{{ route('pos.remove_cart', [$cartId, $tableId]) }}"><i class="fas fa-times-circle"></i></a>
							</div>
						</div>
						<div class="d-flex align-items-center justify-content-between">
							<span class="item-price flex-grow-1"><b>{{ $cartItem['unit_price'] }}</b></span>
							<span class="item-quantity">
								<input type="text" name="quantity" class="quantity" min="0" value="{{ $cartItem['quantity'] }}" placeholder="{{ _lang('Quantity') }}" data-cart-id="{{ $cartId }}" data-table-id="{{ $cartItem['table_id'] }}" readonly="readonly"> 
								<button type="button" class="btn-plus">+</button> 
								<button type="button" class="btn-minus">-</button>
							</span>

						</div>
					</div>
					@endforeach
					@else
					<div class="d-flex justify-content-center align-items-center h-100">
						<h5 class="text-center">{{ _lang('No items available !') }}</h5>
					</div>
					@endif
				</div>
				<div class="cart-footer">
					<div class="d-flex fs">
						<span class="flex-grow-1">{{ _lang('Sub Total') }}</span>
						<span id="subTotal">{{ formatAmount($subTotal, currency_symbol(request()->activeBusiness->currency)) }}</span>
					</div>

					<div id="taxes">
					@foreach($taxes as $tax)
					<div class="d-flex fs">
						<span class="flex-grow-1">{{ $tax['name'] }} ({{ $tax['rate'] }}%)</span>
						<span>{{ formatAmount($tax['rawAmount'], currency_symbol(request()->activeBusiness->currency)) }}</span>
					</div>
					@endforeach
					</div>

					@if($serviceCharge['percentage'] > 0)
					<div class="d-flex fs">
						<span class="flex-grow-1">{{ _lang('Service Charge') }} (<span id="s-percentage">{{ $serviceCharge['percentage'] }}</span>%)</span>
						<span id="serviceCharge">{{ formatAmount($serviceCharge['rawAmount'], currency_symbol(request()->activeBusiness->currency)) }}</span>
					</div>
					@endif

					<div class="d-flex fs">
						<span class="flex-grow-1">{{ _lang('Discount') }} (<span id="percentage">{{ $discount['percentage'] }}</span>%)</span>
						<span id="discount">{{ formatAmount($discount['rawAmount'], currency_symbol(request()->activeBusiness->currency)) }}</span>
					</div>

					<div class="d-flex fs grand-total">
						<span class="flex-grow-1">{{ _lang('Grand Total') }}</span>
						<span id="grandTotal">{{ formatAmount($grandTotal, currency_symbol(request()->activeBusiness->currency)) }}</span>
					</div>

					<div class="row mt-2">
						@if($orderStatus == null)
						<div class="col-12 mb-2">
							<select name="taxes[]" class="multi-selector select_taxes auto-multiple-select" data-selected="{{ $taxes->pluck('id') }}" data-table-id="{{ $tableId }}" data-placeholder="{{ _lang('Select Taxes') }}" multiple>
								@foreach(\App\Models\Tax::active()->get() as $tax)
								<option value="{{ $tax->id }}" data-tax-rate="{{ $tax->rate }}" data-tax-name="{{ $tax->name }} ({{ $tax->rate }} %)">{{ $tax->name }} ({{ $tax->rate }} %)</option>
								@endforeach
							</select>
						</div>
						<div class="col-12">
							<a href="{{ route('pos.place_order', $tableId) }}" data-title="{{ _lang('Place Order') }}" class="btn btn-primary btn-block ajax-modal md-mb-0"><i class="fas fa-check-circle"></i> {{ _lang('Place Order') }}</a>
						</div>
						@else
						<div class="col-12">
							<a href="{{ route('pos.place_order', $tableId) }}" data-title="{{ _lang('Update Order') }}" class="btn btn-danger btn-block ajax-modal md-mb-0"><i class="fas fa-pen-alt"></i> {{ _lang('Update Order') }}</a>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>