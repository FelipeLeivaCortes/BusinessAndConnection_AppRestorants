@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('User Roles') }}</span>
				<a class="btn btn-primary btn-xs ml-auto ajax-modal" data-title="{{ _lang('Create Role') }}" href="{{ route('roles.create') }}"><i class="ti-plus"></i>&nbsp;{{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="roles_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Description') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($roles as $role)
					    <tr data-id="row_{{ $role->id }}">
							<td class='name'>{{ $role->name }}</td>
							<td class='description'>{{ $role->description }}</td>
							<td class="text-center">
								<form action="{{ route('roles.destroy', $role->id) }}" method="post">
									@csrf
									<input name="_method" type="hidden" value="DELETE">
									<a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-xs ajax-modal" data-title="{{ _lang('Update Role') }}"><i class="ti-pencil-alt mr-1"></i>{{ _lang('Edit') }}</a>
									<a href="{{ route('permission.show', $role->id) }}" class="btn btn-primary btn-xs"><i class="ti-lock mr-1"></i>{{ _lang('Access Control') }}</a>
									<button class="btn-remove btn btn-danger btn-xs" type="submit"><i class="ti-trash mr-1"></i>{{ _lang('Delete') }}</button>
								</form>
							</td>
					    </tr>
					    @endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection