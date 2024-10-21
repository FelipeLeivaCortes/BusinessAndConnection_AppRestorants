<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('customers.store') }}" enctype="multipart/form-data">
	@csrf
	<div class="row px-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Name') }}</label>						
				<input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Email') }}</label>						
				<input type="text" class="form-control" name="email" value="{{ old('email') }}" required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Phone') }}</label>						
				<input type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
			</div>
		</div>


		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('City') }}</label>						
				<input type="text" class="form-control" name="city" value="{{ old('city') }}">
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('State') }}</label>						
				<input type="text" class="form-control" name="state" value="{{ old('state') }}">
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Address') }}</label>						
				<textarea class="form-control" name="address">{{ old('address') }}</textarea>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Remarks') }}</label>						
				<textarea class="form-control" name="remarks">{{ old('remarks') }}</textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Profile Picture') }}</label>						
				<input type="file" class="form-control py-2" name="profile_picture">
			</div>
		</div>
			
		<div class="col-md-12 mt-2">
			<div class="form-group">
				<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Changes') }}</button>
			</div>
		</div>
	</div>
</form>
