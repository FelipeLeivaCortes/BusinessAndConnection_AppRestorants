@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div class="flex-grow-1">
						<h5>{{ _lang('Current Month Sales') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ formatAmount($current_month_sales, currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="fas fa-funnel-dollar bg-success text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div class="flex-grow-1">
						<h5>{{ _lang('Current Day Sales') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ formatAmount($current_day_sales, currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="fas fa-dollar-sign bg-primary text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div class="flex-grow-1">
						<h5>{{ _lang('Current Month Expense') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ formatAmount($current_month_expenses, currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="fas fa-wallet bg-danger text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div class="flex-grow-1">
						<h5>{{ _lang('Current Month Orders') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ $current_month_orders }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="fas fa-chart-line bg-dark text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12 mb-4">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<span>{{ _lang('Cash Flow').' - '._lang('Year of').' '.date('Y')  }}</span>
			</div>
			<div class="card-body">
				<h5 class="text-center loading-chart"><i class="fas fa-spinner fa-spin"></i> {{ _lang('Loading Chart') }}</h5>
				<canvas id="cashFlow" style="height: 400px"></canvas>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6 mb-4">
		<div class="card h-100">
			<div class="card-header d-flex align-items-center">
				<span>{{ _lang('Sales By Category').' - '._lang('Year of').' '.date('Y') }}</span>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-8 offset-md-2 col-sm-10 offset-sm-1">
						<h5 class="text-center loading-chart"><i class="fas fa-spinner fa-spin"></i> {{ _lang('Loading Chart') }}</h5>
						<canvas id="salesOverview"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6 mb-4">
		<div class="card h-100">
			<div class="card-header d-flex align-items-center">
				<span>{{ _lang('Expense By Category').' - '._lang('Year of').' '.date('Y') }}</span>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-8 offset-md-2 col-sm-10 offset-sm-1">
						<h5 class="text-center loading-chart"><i class="fas fa-spinner fa-spin"></i> {{ _lang('Loading Chart') }}</h5>
						<canvas id="expenseOverview"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js-script')
<script src="{{ asset('public/backend/plugins/chartJs/chart.min.js') }}"></script>
<script src="{{ asset('public/backend/assets/js/dashboard.js?v=1.1') }}" defer></script>
@endsection
