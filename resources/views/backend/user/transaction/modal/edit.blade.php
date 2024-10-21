<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('transactions.update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">
	<div class="row px-2">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Date') }}</label>
				<input type="text" class="form-control datetimepicker" name="trans_date" value="{{ $transaction->getRawOriginal('trans_date') }}" required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Expense Category') }}</label>
				<select class="form-control select2-ajax auto-select" data-selected="{{ $transaction->transaction_category_id  }}" name="transaction_category_id"
				data-value="id" data-display="name" data-table="transaction_categories" data-where="3" data-title="{{ _lang('New Category') }}" data-href="{{ route('transaction_categories.create') }}" required>
					<option value="{{ $transaction->transaction_category_id }}">{{ $transaction->transaction_category->name }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Amount') }}  ({{ currency_symbol(request()->activeBusiness->currency) }})</label>
				<input type="text" class="form-control float-field" name="amount" value="{{ $transaction->amount }}" required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Payment Method') }}</label>
				<select class="form-control auto-select select2-ajax" data-selected="{{ $transaction->method }}" name="method"
				data-table="transaction_methods" data-value="name" data-display="name" data-where="8" data-title="{{ _lang('New Method') }}"
				data-href="{{ route('transaction_methods.create') }}" required>
					<option value="{{ $transaction->method }}">{{ $transaction->method }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Reference') }}</label>
				<input type="text" class="form-control" name="reference" value="{{ $transaction->reference }}">
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Employee') }}</label>
				<select class="form-control select2 auto-select" name="employee_id" data-selected="{{ old('employee_id') }}">
					<option value="">{{ _lang('Select One') }}</option>
					@foreach(\App\Models\Employee::all() as $employee)
					<option value="{{ $employee->id }}">{{ $employee->employee_id }} ({{ $employee->name }})</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Description') }}</label>
				<textarea class="form-control" name="description">{{ $transaction->description }}</textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Attachment') }}</label>
				<input type="file" class="form-control dropify" name="attachment">
			</div>
		</div>

		<div class="col-md-12 mt-2">
			<div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Update') }}</button>
		    </div>
		</div>
	</div>
</form>

