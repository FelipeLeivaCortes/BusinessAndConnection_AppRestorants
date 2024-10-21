<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('system_users.send_invitation') }}" enctype="multipart/form-data">
	{{ csrf_field() }}

	<div class="row p-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Email') }}</label>
				<input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
			</div>
		</div>

        <div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Business') }}</label>
				<select class="form-control auto-select" data-selected="{{ old('business_id', $businessId) }}" name="business_id" required>
					<option value="">{{ _lang('Select One') }}</option>
					@foreach(\App\Models\Business::owner()->active()->get() as $business)
                    <option value="{{ $business->id }}">{{ $business->name }}</option>
                    @endforeach
				</select>
			</div>
		</div>

        <div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('User Role') }}</label>
				<select class="form-control select2-ajax" data-href="{{ route('roles.create') }}" data-title="{{ _lang('Add New Role') }}" data-value="id" data-display="name" data-table="roles" data-where="1" id="role_id" name="role_id">
				</select>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Message') }}</label>
				<textarea class="form-control" name="message">{{ old('message') }}</textarea>
			</div>
		</div>

		<div class="col-md-12 mt-2">
			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane mr-2"></i>{{ _lang('Send Invitation') }}</button>
			</div>
		</div>
	</div>
</form>