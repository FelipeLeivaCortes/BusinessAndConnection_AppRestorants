@extends('layouts.guest')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Select Payment Methods') }}</span>
				
				<div class="dropdown">
					<a class="btn btn-primary btn-xs" href="{{ route('membership.packages') }}">{{ _lang('All Packages') }}</a>
					@if(isset($activeBusiness->name))
					<button class="btn btn-danger btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
						{{ $activeBusiness->name }}
					</button>
					<div class="dropdown-menu">
						@foreach($businessList as $business)
						<a class="dropdown-item" href="{{ route('business.switch_business', $business->id) }}">{{ $business->name }} ({{ $business->user_id == auth()->id() ? _lang('Owner') : _lang('Invited')  }})</a>
						@endforeach
					</div>
					@endif
				</div>

			</div>
			<div class="card-body">
                <div class="row justify-content-center">
                @foreach($payment_gateways as $paymentgateway)
                    <div class="col-md-4 my-2">
						<div class="border rounded text-center">
							<div class="card-body">
								<img class="thumb-xl m-auto rounded-circle img-thumbnail" src="{{ asset('public/backend/images/gateways/'.$paymentgateway->image) }}"/>
								<h6 class="mt-3 mb-4">{{ $paymentgateway->name }}</h6>
								<a href="{{ route('membership.make_payment', $paymentgateway->slug) }}" class="btn btn-outline-primary btn-block"><i class="fas fa-long-arrow-alt-right mr-2"></i>{{ _lang('Select') }}</a>
							</div>
						</div>
					</div>
                @endforeach
                </div>
            </div>
		</div>
	</div>
</div>
@endsection