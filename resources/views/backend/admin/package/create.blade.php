@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header">
				<span class="panel-title">{{ _lang('Add New Package') }}</span>
			</div>
			<div class="card-body">
			    <form method="post" class="validate" autocomplete="off" action="{{ route('packages.store') }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-lg-12">
							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Package Name') }}</label>
								<div class="col-xl-9">
									<input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Package Type') }}</label>
								<div class="col-xl-9">
									<select class="form-control auto-select" data-selected="{{ old('package_type') }}" name="package_type" required>
										<option value="">{{ _lang('Select One') }}</option>
										<option value="trimestral">{{ _lang('Quarterly') }}</option>
										<option value="semestral">{{ _lang('Biannual') }}</option>
										<option value="anual">{{ _lang('Yearly') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Cost') }} ({{ currency_symbol() }})</label>
								<div class="col-xl-9">
									<input type="text" class="form-control float-field" name="cost" value="{{ old('cost') }}" required>
								</div>
							</div>
					
							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Discount') }} (%)</label>						
								<div class="col-xl-9">
									<input type="text" class="form-control float-field" name="discount" value="{{ old('discount', 0) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Trial Days') }}</label>						
								<div class="col-xl-9">
									<input type="number" class="form-control" name="trial_days" value="{{ old('trial_days', 0) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Status') }}</label>						
								<div class="col-xl-9">
									<select class="form-control auto-select" data-selected="{{ old('status', 1) }}" name="status" required>
										<option value="1">{{ _lang('Active') }}</option>
										<option value="0">{{ _lang('Disabled') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Is Popular') }}</label>						
								<div class="col-xl-9">
									<select class="form-control auto-select" data-selected="{{ old('is_popular', 0) }}" name="is_popular" required>
										<option value="0">{{ _lang('No') }}</option>
										<option value="1">{{ _lang('Yes') }}</option>
									</select>
								</div>
							</div>
						
							<hr>
							<div class="form-group row">					
								<div class="col-xl-9 offset-xl-3">
									<h5 class="text-info"><strong>{{ _lang('Manage Package Features') }}</strong></h5>
								</div>
							</div>			
							<hr>

							<div class="form-group row align-items-center">
								<label class="col-xl-3 col-form-label">{{ _lang('Restaurant Limit') }}</label>						
								<div class="col-xl-7">
									<input type="number" class="form-control" name="business_limit" value="{{ old('business_limit') != '-1' ? old('business_limit') : '' }}" placeholder="5">
								</div>

								<div class="col-xl-2">
									<div class="form-check">
										<label class="form-check-label text-danger">
											<input type="checkbox" class="form-check-input no-msg" name="business_limit" value="-1" {{ old('business_limit') == '-1' ? 'checked' : '' }}>{{ _lang('UNLIMITED') }}
										</label>
									</div>
								</div>
							</div>

							<div class="form-group row align-items-center">
								<label class="col-xl-3 col-form-label">{{ _lang('System User Limit') }}</label>						
								<div class="col-xl-7">
									<input type="number" class="form-control" name="staff_limit" value="{{ old('staff_limit') != '-1' ? old('staff_limit') : '' }}" placeholder="5">
								</div>

								<div class="col-xl-2">
									<div class="form-check">
										<label class="form-check-label text-danger">
											<input type="checkbox" class="form-check-input no-msg" name="staff_limit" value="-1" {{ old('staff_limit') == '-1' ? 'checked' : '' }}>{{ _lang('UNLIMITED') }}
										</label>
									</div>
								</div>
							</div>

							<div class="form-group row align-items-center">
								<label class="col-xl-3 col-form-label">{{ _lang('Item Limit') }}</label>						
								<div class="col-xl-7">
									<input type="number" class="form-control" name="item_limit" value="{{ old('item_limit') != '-1' ? old('item_limit') : '' }}" placeholder="100">
								</div>

								<div class="col-xl-2">
									<div class="form-check">
										<label class="form-check-label text-danger">
											<input type="checkbox" class="form-check-input no-msg" name="item_limit" value="-1" {{ old('item_limit') == '-1' ? 'checked' : '' }}>{{ _lang('UNLIMITED') }}
										</label>
									</div>
								</div>
							</div>

							<div class="form-group row align-items-center">
								<label class="col-xl-3 col-form-label">{{ _lang('Order Limit') }}</label>						
								<div class="col-xl-7">
									<input type="number" class="form-control" name="order_limit" value="{{ old('order_limit') != '-1' ? old('order_limit') : '' }}" placeholder="150">
								</div>

								<div class="col-xl-2">
									<div class="form-check">
										<label class="form-check-label text-danger">
											<input type="checkbox" class="form-check-input no-msg" name="order_limit" value="-1" {{ old('order_limit') == '-1' ? 'checked' : '' }}>{{ _lang('UNLIMITED') }}
										</label>
									</div>
								</div>
							</div>

							<div class="form-group row align-items-center">
								<label class="col-xl-3 col-form-label">{{ _lang('Payroll Module') }}</label>						
								<div class="col-xl-7">
									<select class="form-control auto-select" data-selected="{{ old('payroll_module', 0) }}" name="payroll_module" required>
										<option value="0">{{ _lang('No') }}</option>
										<option value="1">{{ _lang('Yes') }}</option>
									</select>
								</div>
							</div>
						
							<div class="form-group row mt-2">
								<div class="col-xl-9 offset-xl-3">
									<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Changes') }}</button>
								</div>
							</div>
						</div>
					</div>
			    </form>
			</div>
		</div>
    </div>
</div>
@endsection


