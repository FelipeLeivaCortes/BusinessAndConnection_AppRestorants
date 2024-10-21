@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
		<ul class="nav nav-tabs business-settings-tabs mb-4">
			 <li class="nav-item"><a class="nav-link {{ isset($_GET['tab']) ? '' : 'active'  }}" href="{{ route('customers.show', $customer->id) }}"><i class="fas fa-tools mr-2"></i><span>{{ _lang('Overview') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link {{ isset($_GET['tab']) && $_GET['tab'] == 'orders' ? 'active' : ''  }}" href="{{ route('customers.show', $customer->id) }}?tab=orders"><i class="fas fa-receipt mr-2"></i><span>{{ _lang('Orders') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link" href="{{ route('customers.edit', $customer->id) }}"><i class="far fa-edit mr-2"></i><span>{{ _lang('Edit Details') }}</span></a></li>
		</ul>

		@if(! isset($_GET['tab']))
		<div class="card">
			<div class="card-body">
				<table class="table table-bordered">
					<tr>
						<td colspan="2" class="text-center"><img class="thumb-xl rounded" src="{{ profile_picture($customer->profile_picture) }}"></td>
					</tr>
					<tr><td>{{ _lang('Name') }}</td><td>{{ $customer->name }}</td></tr>
					<tr><td>{{ _lang('Email') }}</td><td>{{ $customer->email }}</td></tr>
					<tr><td>{{ _lang('Phone') }}</td><td>{{ $customer->phone }}</td></tr>
					<tr><td>{{ _lang('City') }}</td><td>{{ $customer->city }}</td></tr>
					<tr><td>{{ _lang('State') }}</td><td>{{ $customer->state }}</td></tr>
					<tr><td>{{ _lang('Address') }}</td><td>{{ $customer->address }}</td></tr>
					<tr><td>{{ _lang('Remarks') }}</td><td>{{ $customer->remarks }}</td></tr>
				</table>
			</div>
		</div>
		@else
			<div>
				@include('backend.user.customer.tabs.'.$_GET['tab'])
			</div>
		@endif
	</div>
</div>
@endsection


