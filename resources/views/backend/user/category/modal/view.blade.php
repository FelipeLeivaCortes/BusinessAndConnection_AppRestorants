<table class="table table-bordered">
	<tr><td>{{ _lang('Name') }}</td><td>{{ $category->name }}</td></tr>
	<tr><td>{{ _lang('Description') }}</td><td>{{ $category->description }}</td></tr>
	<tr><td>{{ _lang('Status') }}</td><td>{!! xss_clean(status($category->status)) !!}</td></tr>
</table>

