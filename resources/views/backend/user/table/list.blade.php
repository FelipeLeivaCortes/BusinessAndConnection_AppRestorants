@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ $hall->name }} {{ _lang('Table List') }}</span>
                <a href="{{ route('halls.show', $hall->id) }}" class="btn btn-dark btn-xs"><i class="fas fa-border-none"></i> {{ _lang('Table Setup') }}</a>
			</div>
			<div class="card-body">
				<table id="halls_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Table No') }}</th>
							<th>{{ _lang('Hall') }}</th>
							<th>{{ _lang('Chair Limit') }}</th>
							<th>{{ _lang('Type') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($tables as $table)
					    <tr data-id="row_{{ $table->id }}">
							<td>{{ $table->table_no }}</td>
							<td>{{ $table->hall->name }}</td>
							<td>{{ $table->chair_limit }}</td>
							<td>{{ ucwords($table->type) }}</td>
							<td class="text-center">
								<form action="{{ route('tables.destroy', $table['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">
									<a href="{{ route('tables.edit', $table['id']) }}" class="btn btn-warning btn-xs ajax-modal" data-title="{{ _lang('Update Table') }}"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
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