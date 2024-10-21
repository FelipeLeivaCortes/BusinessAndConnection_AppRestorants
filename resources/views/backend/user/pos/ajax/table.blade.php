<div class="row">
	<div class="col-lg-12 my-3 d-flex justify-content-between">
		<div></div>
		<h4 class="bg-white d-inline-block py-2 px-4 rounded text-primary">{{ request()->activeBusiness->name }}</h4>
		<div></div>
	</div>

	<div class="col-lg-12 d-flex justify-content-center">
		<div class="card overflow-auto">
			<div class="card-body">
				<ul class="nav mb-2" id="hallTab" role="tablist">
					@foreach($halls as $hall)
					<li class="nav-item mr-1" role="presentation">
						<button class="btn btn-outline-primary btn-xs {{ $loop->first ? 'active' : '' }} px-3 mb-2" data-toggle="tab" data-target="#hall-{{ $hall->id }}" type="button" role="tab">{{ $hall->name }}</button>
					</li>
					@endforeach
				</ul>

				<div class="tab-content">
					@foreach($halls as $hall)
					<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="hall-{{ $hall->id }}" role="tabpanel">
						<div id="tableData">
							<div id="hallView" data-id="{{ $hall->id }}" style="{{ $hall->css }}">
								@foreach($hall->tables as $table)
								<div class="restaurant-table cursor-pointer {{ $table->type }} {{ isset($cart[$table->id]['items']) && !empty($cart[$table->id]['items']) ? 'checked-in' : '' }}" data-id="{{ $table->id }}" data-hall-id="{{ $table->hall_id }}" data-link="{{ route('pos.sell',$table->id) }}" style="{{ $table->css }}">
									<span class="name">{{ $table->table_no }}</span>
									<div class="chair_limit">
										<span><i class="fas fa-chair"></i> {{ $table->chair_limit }}</span>
									</div>
								</div>
								@endforeach
							</div>
						</div>
					</div>
					@endforeach
				</div>

				<div id="footer-details" class="d-sm-flex justify-content-between mt-3">
					<div>
						<span class="badge badge-pill badge-free">{{ _lang('Free') }}</span>
						<span class="badge badge-pill badge-checked-in">{{ _lang('Checked-In') }}</span>
					</div>
					<div>
						@if(get_business_option('order_delivery_status', 0, business_id()) == 1)
						<button type="button" data-link="{{ route('pos.sell', ['d'.rand().time(), 'delivery']) }}" class="btn btn-success btn-xs ajax-link mt-2 mt-sm-0"><i class="fas fa-biking"></i> {{ _lang('New Delivery') }}</button>
						@endif

						@if(get_business_option('order_takeway_status', 0, business_id()) == 1)
						<button type="button" data-link="{{ route('pos.sell', ['t'.rand().time(), 'takeway']) }}" class="btn btn-primary btn-xs ajax-link mt-2 mt-sm-0"><i class="fas fa-people-carry"></i> {{ _lang('New Takeway') }}</button>
						@endif
						<button type="button" data-link="{{ route('pos.active_orders') }}" class="btn btn-danger btn-xs ajax-link mt-2 mt-sm-0"><i class="fas fa-list-ul"></i> {{ _lang('Active Orders') }}</button>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>