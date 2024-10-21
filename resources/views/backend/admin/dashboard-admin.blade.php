@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Total User') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ $total_user }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="fas fa-users bg-success text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Total Owner') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ $total_owner }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="fas fa-user-tie bg-primary text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Trial User') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ $trial_users }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="far fa-user bg-info text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Expired Users') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ $expired_users }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="fas fa-user-times bg-danger text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-8 col-sm-7 mb-4">
		<div class="card h-100">
			<div class="card-header d-flex align-items-center">
				<span>{{ _lang('Subscription Payments').' - '.date('Y')  }}</span>
			</div>
			<div class="card-body">
				<h5 class="text-center loading-chart"><i class="fas fa-spinner fa-spin"></i> {{ _lang('Loading Chart') }}</h5>
				<canvas id="revenueAnalysis"></canvas>
			</div>
		</div>
	</div>

	<div class="col-md-4 col-sm-5 mb-4">
		<div class="card h-100">
			<div class="card-header d-flex align-items-center">
				<span>{{ _lang('Package Wise Subscribed') }}</span>
			</div>
			<div class="card-body">
				<h5 class="text-center loading-chart"><i class="fas fa-spinner fa-spin"></i> {{ _lang('Loading Chart') }}</h5>
				<canvas id="packageOverview"></canvas>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="card mb-4">
			<div class="card-header">
				{{ _lang('New Registered Users') }}
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table">
					<thead>
						<tr>
                            <th>{{ _lang('Name') }}</th>
                            <th>{{ _lang('Package') }}</th>
                            <th>{{ _lang('Membership') }}</th>
                            <th>{{ _lang('Status') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
					</thead>
					<tbody>
						@foreach($newUsers as $user)
						<tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->package->name }}</td>
                            <td>{{ ucwords($user->membership_type) }}</td>
                            <td>{!! xss_clean(status($user->status)) !!}</td>
                            <td class="text-center">
								<div class="dropdown text-center">
									<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}</button>
									<div class="dropdown-menu">
										<a class="dropdown-item" href="{{ route('users.edit', $user->id) }}"><i class="ti-pencil-alt mr-2"></i>{{ _lang('Edit') }}</a>
										<a class="dropdown-item" href="{{ route('users.show', $user->id) }}"><i class="ti-eye mr-2"></i>{{ _lang('View') }}</a>
										<a class="dropdown-item" href="{{ route('users.login_as_user', $user->id) }}"><i class="ti-user mr-2"></i>{{ _lang('Login as User') }}</a>
										<form action="{{ route('users.destroy', $user->id) }}" method="post">
											@csrf
											<input name="_method" type="hidden" value="DELETE">
											<button class="dropdown-item btn-remove" type="submit"><i class="ti-trash mr-2"></i>{{ _lang('Delete') }}</button>
										</form>
									</div>
								</div>
							</td>
                        </tr>
						@endforeach
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="card mb-4">
			<div class="card-header">
				{{ _lang('Recent Subscription Payments') }}
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table">
					<thead>
						<tr>
						    <th>{{ _lang('User') }}</th>
							<th>{{ _lang('Order ID') }}</th>
							<th>{{ _lang('Method') }}</th>
							<th>{{ _lang('Package') }}</th>
							<th class="text-right">{{ _lang('Amount') }}</th>
							<th class="text-center">{{ _lang('Status') }}</th>
					    </tr>
					</thead>
					<tbody>
						@foreach($recentPayments as $recentPayment)
						<tr>
						    <td>{{ $recentPayment->user->name }}</td>
							<td>{{ $recentPayment->order_id }}</td>
							<td>{{ $recentPayment->payment_method }}</td>
							<td>{{ $recentPayment->package->name }}</td>
							<td class="text-right">{{ decimalPlace($recentPayment->amount, currency_symbol()) }}</td>
							<td class="text-center">
							@if($recentPayment->status == 0)
								{!! xss_clean(show_status(_lang('Pending'), 'warning')) !!}
							@elseif ($recentPayment->status == 1)
								{!! xss_clean(show_status(_lang('Completed'), 'success')) !!}
							@elseif ($recentPayment->status == 2)
								{!! xss_clean(show_status(_lang('Hold'), 'primary')) !!}
							@elseif ($recentPayment->status == 3)
								{!! xss_clean(show_status(_lang('Refund'), 'info')) !!}
							@elseif ($recentPayment->status == 4)
								{!! xss_clean(show_status(_lang('Cancelled'), 'danger')) !!}
							@endif
							</td>
					    </tr>
						@endforeach
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js-script')
<script src="{{ asset('public/backend/plugins/chartJs/chart.min.js') }}"></script>
<script src="{{ asset('public/backend/assets/js/dashboard-admin.js?v=1.1') }}"></script>
@endsection
