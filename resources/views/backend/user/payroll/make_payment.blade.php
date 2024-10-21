@extends('layouts.app')

@section('content')
<div class="row">
	@if(!isset($payslips))
	<div class="col-lg-4 offset-lg-4">
		<div class="card">
			<div class="card-header text-center">
				<span class="panel-title">{{ _lang('Make Payment') }}</span>
			</div>
			<div class="card-body">
			    <form method="post" class="validate" autocomplete="off" action="{{ route('payslips.make_payment') }}" enctype="multipart/form-data">
					@csrf
					<div class="row">

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Month') }}</label>						
								<select type="text" class="form-control auto-select" name="month" data-selected="{{ old('month', date('m')) }}" required>
									@for($m = 1; $m <=12; $m++)
									<option value="{{ date('m', mktime(0, 0, 0, $m, 10)) }}">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
									@endfor
								</select>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Year') }}</label>						
								<select type="text" class="form-control auto-select" name="year" data-selected="{{ old('year', date('Y')) }}" required>
									@for($y = 2020; $y <=date('Y'); $y++)
									<option value="{{ $y }}">{{ $y }}</option>
									@endfor
								</select>
							</div>
						</div>
			
						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-block">{{ _lang('Next') }}</button>
							</div>
						</div>
					</div>
			    </form>
			</div>
		</div>
    </div>
	@else
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header text-center">
				<span class="panel-title">{{ _lang('Make Payment') }}</span>
			</div>
			<div class="card-body">
			    <form method="post" class="validate" autocomplete="off" action="{{ route('payslips.store_payment') }}">
					@csrf
					<div class="row">
						<div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th>{{ _lang('Employee ID') }}</th>
                                        <th>{{ _lang('Name') }}</th>
                                        <th>{{ _lang('Month') }}</th>
                                        <th>{{ _lang('Year') }}</th>
                                        <th class="text-right">{{ _lang('Net Salary') }}</th>
                                        <th class="text-center">{{ _lang('Status') }}</th>
                                        <th class="text-center">{{ _lang('Pay') }}</th>
                                    </thead>
                                    <tbody>
                                        @foreach($payslips as $payslip)
                                        <tr>
                                            <td>{{ $payslip->staff->employee_id }}</td>
                                            <td>{{ $payslip->staff->name }}</td>
                                            <td>{{ date('F', mktime(0, 0, 0, $payslip->month, 10)) }}</td>
                                            <td>{{ $payslip->year }}</td>
                                            <td class="text-right">{{ formatAmount($payslip->net_salary, $currency_symbol) }}</td>
                                            <td class="text-center">{!! xss_clean(payroll_status($payslip->status)) !!}</td>
                                            <td class="text-center">
                                                <label class="switch float-none mb-0">
                                                    <input type="checkbox" class="primary product_addon" name="payslip_ids[]" value="{{ $payslip->id }}" checked>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
						</div>
				
						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Make Payment') }}</button>
							</div>
						</div>
					</div>
			    </form>
			</div>
		</div>
    </div>
	@endif
</div>
@endsection