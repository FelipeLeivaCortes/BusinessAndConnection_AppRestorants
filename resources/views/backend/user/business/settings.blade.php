@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-md-12">
		<ul class="nav nav-tabs business-settings-tabs" role="tablist">
			 <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#general_settings"><i class="fas fa-tools mr-2"></i><span>{{ _lang('General Settings') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#currency_settings"><i class="fas fa-pound-sign mr-2"></i><span>{{ _lang('Currency Settings') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#pos_settings"><i class="fas fa-shopping-cart mr-2"></i><span>{{ _lang('POS Settings') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#email"><i class="fas fa-at mr-2"></i><span>{{ _lang('Email Settings') }}</span></a></li>
			 <li class="nav-item"><a class="nav-link" href="{{ route('business.edit', request()->activeBusiness->id) }}"><i class="far fa-edit mr-2"></i><span>{{ _lang('Update Business') }}</span></a></li>
		</ul>

		<div class="tab-content settings-tab-content">
			<div id="general_settings" class="tab-pane active">
				<div class="card">

					<div class="card-body">
						<form action="{{ route('business.store_general_settings', $id) }}" class="settings-submit" autocomplete="off" method="post" enctype="multipart/form-data">
							@csrf
							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Timezone') }}</label>
								<div class="col-xl-6">
									<select class="form-control select2 auto-select" data-selected="{{ get_setting($business->systemSettings, 'timezone','',$id) }}" name="timezone" required>
										<option value="">{{ _lang('Select One') }}</option>
										{{ create_timezone_option() }}
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Language') }}</label>
								<div class="col-xl-6">
									<select class="form-control select2 auto-select" name="language" data-selected="{{ get_setting($business->systemSettings, 'language','',$id) }}" required>
										<option value="">{{ _lang('Select One') }}</option>
										{{ load_language() }}
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Backend Direction') }}</label>
								<div class="col-xl-6">
									<select class="form-control auto-select" name="backend_direction" data-selected="{{ get_setting($business->systemSettings, 'backend_direction', 'ltr', $id) }}" required>
										<option value="ltr">{{ _lang('LTR') }}</option>
										<option value="rtl">{{ _lang('RTL') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Date Format') }}</label>
								<div class="col-xl-6">
									<select class="form-control auto-select" name="date_format" data-selected="{{ get_setting($business->systemSettings, 'date_format', 'Y-m-d', $id) }}" required>
										<option value="Y-m-d">{{ date('Y-m-d') }}</option>
										<option value="d-m-Y">{{ date('d-m-Y') }}</option>
										<option value="d/m/Y">{{ date('d/m/Y') }}</option>
										<option value="m-d-Y">{{ date('m-d-Y') }}</option>
										<option value="m.d.Y">{{ date('m.d.Y') }}</option>
										<option value="m/d/Y">{{ date('m/d/Y') }}</option>
										<option value="d.m.Y">{{ date('d.m.Y') }}</option>
										<option value="d/M/Y">{{ date('d/M/Y') }}</option>
										<option value="d/M/Y">{{ date('M/d/Y') }}</option>
										<option value="d M, Y">{{ date('d M, Y') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Time Format') }}</label>
								<div class="col-xl-6">
									<select class="form-control auto-select" name="time_format" data-selected="{{ get_setting($business->systemSettings, 'time_format', 24, $id) }}" required>
										<option value="24">{{ _lang('24 Hours') }}</option>
										<option value="12">{{ _lang('12 Hours') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row mt-2">
								<div class="col-xl-9 offset-lg-3">
									<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Changes') }}</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div id="currency_settings" class="tab-pane">
				<div class="card">
					<div class="card-body">
						<form method="post" class="settings-submit" autocomplete="off" action="{{ route('business.store_currency_settings', $id) }}" enctype="multipart/form-data">
							@csrf													
							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Currency Position') }}</label>						
								<div class="col-xl-6">
									<select class="form-control auto-select" data-selected="{{ get_setting($business->systemSettings, 'currency_position', 'left', $id) }}" name="currency_position" required>
										<option value="left">{{ _lang('Left') }}</option>
										<option value="right">{{ _lang('Right') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Thousand Seperator') }}</label>	
								<div class="col-xl-6">
									<input type="text" class="form-control" name="thousand_sep" value="{{ get_setting($business->systemSettings, 'thousand_sep', ',', $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Decimal Seperator') }}</label>	
								<div class="col-xl-6">
									<input type="text" class="form-control" name="decimal_sep" value="{{ get_setting($business->systemSettings, 'decimal_sep', '.', $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Decimal Places') }}</label>	
								<div class="col-xl-6">
									<input type="text" class="form-control" name="decimal_places" value="{{ get_setting($business->systemSettings, 'decimal_places', 2, $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<div class="col-xl-9 offset-xl-3">
									<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Settings') }}</button>
								</div>
							</div>								
						</form>
					</div>
				</div>
			</div>

			<div id="pos_settings" class="tab-pane">
				<div class="card">

					<div class="card-body">
						<form method="post" class="settings-submit" autocomplete="off" action="{{ route('business.store_pos_settings', $id) }}" enctype="multipart/form-data">
							@csrf					
							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Invoice Auto Increment') }}</label>	
								<div class="col-xl-6">
									<input type="number" class="form-control" name="invoice_number" value="{{ get_setting($business->systemSettings, 'invoice_number', 100001, $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Service Charge') }} (%)</label>	
								<div class="col-xl-6">
									<input type="text" class="form-control float-field" name="service_charge" value="{{ get_setting($business->systemSettings, 'service_charge', 0, $id) }}" required>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Order Delivery') }}</label>	
								<div class="col-xl-6">
									<select class="form-control auto-select" name="order_delivery_status" data-selected="{{ get_setting($business->systemSettings, 'order_delivery_status', 0, $id) }}" required>
										<option value="0">{{ _lang('Disabled') }}</option>
										<option value="1">{{ _lang('Active') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Order Takeway') }}</label>	
								<div class="col-xl-6">
									<select class="form-control auto-select" name="order_takeway_status"  data-selected="{{ get_setting($business->systemSettings, 'order_takeway_status', 0, $id) }}" required>
										<option value="0">{{ _lang('Disabled') }}</option>
										<option value="1">{{ _lang('Active') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('POS Default Order Status') }}</label>	
								<div class="col-xl-6">
									<select class="form-control auto-select" name="pos_default_status" data-selected="{{ get_setting($business->systemSettings, 'pos_default_status', 0, $id) }}" required>
										<option value="0">1 - {{ _lang('Pending') }}</option>
										<option value="1">2 - {{ _lang('Accepted') }}</option>
										<option value="2">3 - {{ _lang('Preparing') }}</option>
										<option value="3">4 - {{ _lang('Ready') }}</option>
										<option value="4">5 - {{ _lang('Delivered') }}</option>
										<option value="5">6 - {{ _lang('Closed') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Delivery Default Order Status') }}</label>	
								<div class="col-xl-6">
									<select class="form-control auto-select" name="delivery_default_status" data-selected="{{ get_setting($business->systemSettings, 'delivery_default_status', 0, $id) }}" required>
										<option value="0">1 - {{ _lang('Pending') }}</option>
										<option value="1">2 - {{ _lang('Accepted') }}</option>
										<option value="2">3 - {{ _lang('Preparing') }}</option>
										<option value="3">4 - {{ _lang('Ready') }}</option>
										<option value="4">5 - {{ _lang('Delivered') }}</option>
										<option value="5">6 - {{ _lang('Closed') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Takeway Default Order Status') }}</label>	
								<div class="col-xl-6">
									<select class="form-control auto-select" name="takeway_default_status" data-selected="{{ get_setting($business->systemSettings, 'takeway_default_status', 0, $id) }}" required>
										<option value="0">1 - {{ _lang('Pending') }}</option>
										<option value="1">2 - {{ _lang('Accepted') }}</option>
										<option value="2">3 - {{ _lang('Preparing') }}</option>
										<option value="3">4 - {{ _lang('Ready') }}</option>
										<option value="4">5 - {{ _lang('Delivered') }}</option>
										<option value="5">6 - {{ _lang('Closed') }}</option>
									</select>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-xl-3 col-form-label">{{ _lang('Receipt Footer') }}</label>	
								<div class="col-xl-6">
									<textarea class="form-control" name="receipt_footer">{{ get_setting($business->systemSettings, 'receipt_footer', '', $id) }}</textarea>
								</div>
							</div>	
							
							<div class="form-group row mt-2">
								<div class="col-xl-9 offset-xl-3">
									<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Settings') }}</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div id="email" class="tab-pane">
				<div class="row">
					<div class="col-lg-8 mb-md-4">
						<div class="card">
							<div class="card-header">
								<span>{{ _lang('Email Configuration') }}</span>
							</div>
							<div class="card-body">
								<form method="post" class="settings-submit" autocomplete="off" action="{{ route('business.store_email_settings', $id) }}" enctype="multipart/form-data">
									@csrf
									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('Mail Type') }}</label>
										<div class="col-xl-9">
											<select class="form-control auto-select" data-selected="{{ get_setting($business->systemSettings, 'mail_type', '', $id) }}" name="mail_type" id="mail_type">
												<option value="">{{ _lang('None') }}</option>
												<option value="smtp">{{ _lang('SMTP') }}</option>
												<option value="sendmail">{{ _lang('Sendmail') }}</option>
											</select>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('From Email') }}</label>
										<div class="col-xl-9">
											<input type="text" class="form-control" name="from_email" value="{{ get_setting($business->systemSettings, 'from_email', '', $id) }}">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('From Name') }}</label>
										<div class="col-xl-9">
											<input type="text" class="form-control" name="from_name" value="{{ get_setting($business->systemSettings, 'from_name', '', $id) }}">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('SMTP Host') }}</label>
										<div class="col-xl-9">
											<input type="text" class="form-control smtp" name="smtp_host" value="{{ get_setting($business->systemSettings, 'smtp_host', '', $id) }}">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('SMTP Port') }}</label>
										<div class="col-xl-9">
											<input type="text" class="form-control smtp" name="smtp_port" value="{{ get_setting($business->systemSettings, 'smtp_port', '', $id) }}">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('SMTP Username') }}</label>
										<div class="col-xl-9">
											<input type="text" class="form-control smtp" autocomplete="off" name="smtp_username" value="{{ get_setting($business->systemSettings, 'smtp_username', '', $id) }}">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('SMTP Password') }}</label>
										<div class="col-xl-9">
											<input type="password" class="form-control smtp" autocomplete="off" name="smtp_password" value="{{ get_setting($business->systemSettings, 'smtp_password', '', $id) }}">
										</div>
									</div>

									<div class="form-group row">
										<label class="col-xl-3 col-form-label">{{ _lang('SMTP Encryption') }}</label>
										<div class="col-xl-9">
											<select class="form-control smtp auto-select" data-selected="{{ get_setting($business->systemSettings, 'smtp_encryption', '', $id) }}" name="smtp_encryption">
												<option value="">{{ _lang('None') }}</option>
												<option value="ssl">{{ _lang('SSL') }}</option>
												<option value="tls">{{ _lang('TLS') }}</option>
											</select>
										</div>
									</div>

									<div class="form-group row">
										<div class="col-xl-9 offset-xl-3">
											<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Settings') }}</button>
										</div>
									</div>	
								</form>
							</div>
						</div>
					</div>

					<div class="col-lg-4">
						<div class="card">
							<div class="card-header">
								<span>{{ _lang('Send Test Email') }}</span>
							</div>
							<div class="card-body">
								<form method="post" class="settings-submit" autocomplete="off" action="{{ route('business.send_test_email', $id) }}">
									@csrf
									<div class="form-group">
										<label class="control-label">{{ _lang('Recipient Email') }}</label>
										<input type="email" class="form-control" name="recipient_email">
									</div>

									<div class="form-group">
										<label class="control-label">{{ _lang('Message') }}</label>
										<textarea class="form-control" name="message"></textarea>
									</div>

									<div class="form-group">
										<button type="submit" class="btn btn-primary btn-block"><i class="far fa-paper-plane mr-2"></i>{{ _lang('Send Test Email') }}</button>
									</div>	
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
@endsection

@section('js-script')
<script>
(function($) {
	"use strict";

	$('.nav-tabs a').on('shown.bs.tab', function(event){
		var tab = $(event.target).attr("href");
		var url = "{{ route('business.settings', request()->activeBusiness->id) }}";
		history.pushState({}, null, url + "?tab=" + tab.substring(1));
	});

	@if(isset($_GET['tab']))
	$('.nav-tabs a[href="#{{ $_GET['tab'] }}"]').tab('show');
	@endif

})(jQuery);
</script>
@endsection

