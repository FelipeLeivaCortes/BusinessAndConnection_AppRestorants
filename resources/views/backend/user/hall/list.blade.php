@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Hall List') }}</span>
				<a class="btn btn-primary btn-xs ml-auto ajax-modal" data-title="{{ _lang('Add New Hall') }}" href="{{ route('halls.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="halls_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Capacity') }}</th>
							<th>{{ _lang('Description') }}</th>
							<th>{{ _lang('Status') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($halls as $hall)
					    <tr data-id="row_{{ $hall->id }}">
							<td class='name'>{{ $hall->name }}</td>
							<td class='capacity'>{{ $hall->capacity }}</td>
							<td class='description'>{{ $hall->description }}</td>
							<td class='status'>{!! xss_clean(status($hall->status)) !!}</td>
							
							<td class="text-center">	
								<form action="{{ route('halls.destroy', $hall['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">
									<a href="{{ route('halls.show', $hall['id']) }}" class="btn btn-primary btn-xs"><i class="fas fa-border-none"></i> {{ _lang('Table Setup') }}</a>
									<a href="{{ route('halls.edit', $hall['id']) }}" class="btn btn-warning btn-xs ajax-modal" data-title="{{ _lang('Update Hall') }}"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
									<button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="fas fa-trash-alt"></i> {{ _lang('Delete') }}</button>								
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