@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Expense Report') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>
			<div class="card-body">
				<div class="report-params">
					<form class="validate" method="post" action="{{ route('reports.expense_report') }}">
						<div class="row">
              				{{ csrf_field() }}

							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('Start Date') }}</label>
									<input type="text" class="form-control datepicker" name="date1" id="date1" value="{{ isset($date1) ? $date1 : old('date1', \Carbon\Carbon::now()->startOfMonth()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('End Date') }}</label>
									<input type="text" class="form-control datepicker" name="date2" id="date2" value="{{ isset($date2) ? $date2 : old('date2', \Carbon\Carbon::now()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('Category') }}</label>
									<select class="form-control select2 auto-select" data-selected="{{ isset($transaction_category_id) ? $transaction_category_id : old('transaction_category_id') }}" name="transaction_category_id">
										<option value="">{{ _lang('All Category') }}</option>
										@foreach(\App\Models\TransactionCategory::expense()->get() as $category)
											<option value="{{ $category->id }}">{{ $category->name }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-xl-2 col-lg-12">
								<button type="submit" class="btn btn-light btn-xs btn-block mt-26"><i class="ti-filter"></i>&nbsp;{{ _lang('Filter') }}</button>
							</div>
						</form>

					</div>
				</div><!--End Report param-->

				@php $date_format = get_date_format(); @endphp

				<div id="report">
					<div class="report-header">
						<h4>{{ request()->activeBusiness->name }}</h4>
						<p>{{ _lang('Expense Report') }}</p>
						<p>{{ isset($date1) ? date($date_format, strtotime($date1)).' '._lang('to').' '.date($date_format, strtotime($date2)) : '----------  '._lang('to').'  ----------' }}</p>
					</div>

					<div class="table-responsive">
						<table class="table">
							<thead>
								<th>{{ _lang('Date') }}</th>
								<th>{{ _lang('Category') }}</th>
								<th>{{ _lang('Type') }}</th>
								<th>{{ _lang('Method') }}</th>
								<th class="text-right">{{ _lang('Amount') }}</th>
							</thead>
							<tbody>
							@if(isset($report_data))
								@foreach($report_data as $transaction)
									@php
									$symbol = $transaction->dr_cr == 'dr' ? '-' : '+';
									$class  = $transaction->dr_cr == 'dr' ? 'text-danger' : 'text-success';
									@endphp
									<tr>
										<td>{{ $transaction->trans_date }}</td>
										<td>{{ $transaction->transaction_category->name }}</td>				
										<td>{{ ucwords($transaction->type) }}</td>
										<td>{{ $transaction->method }}</td>
										<td class="text-right"><span class="{{ $class }}">{{ $symbol.' '.formatAmount($transaction->amount, $currency_symbol) }}</span></td>
									</tr>
								@endforeach
							@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection