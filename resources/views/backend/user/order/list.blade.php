@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Order History') }}</span>
			</div>
			<div class="card-body">
				<table id="orders_table" class="table">
					<thead>
					    <tr>
						    <th>{{ _lang('Date') }}</th>
						    <th>{{ _lang('Order Number') }}</th>
                            <th>{{ _lang('Sub Total') }}</th>
                            <th>{{ _lang('Grand Total') }}</th>
                            <th>{{ _lang('Paid') }}</th>
                            <th>{{ _lang('Status') }}</th>
                            <th>{{ _lang('Order Type') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

@section('js-script')
<script>
(function ($) {
	"use strict";

	var orders_table = $('#orders_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: '{{ url('user/orders/get_table_data') }}',
		"columns" : [
			{ data : 'created_at', name : 'created_at' },
			{ data : 'order_number', name : 'order_number' },
			{ data : 'sub_total', name : 'sub_total' },
			{ data : 'grand_total', name : 'grand_total' },
			{ data : 'paid', name : 'paid' },
			{ data : 'status', name : 'status' },
			{ data : 'order_type', name : 'order_type' },
			{ data : "action", name : "action" },
		],
		order: [[0, 'desc']],
		responsive: true,
		"bStateSave": true,
		"bAutoWidth":false,
		"ordering": true,
		"language": {
		   "decimal":        "",
		   "emptyTable":     "{{ _lang('No Data Found') }}",
		   "info":           "{{ _lang('Showing') }} _START_ {{ _lang('to') }} _END_ {{ _lang('of') }} _TOTAL_ {{ _lang('Entries') }}",
		   "infoEmpty":      "{{ _lang('Showing 0 To 0 Of 0 Entries') }}",
		   "infoFiltered":   "(filtered from _MAX_ total entries)",
		   "infoPostFix":    "",
		   "thousands":      ",",
		   "lengthMenu":     "{{ _lang('Show') }} _MENU_ {{ _lang('Entries') }}",
		   "loadingRecords": "{{ _lang('Loading...') }}",
		   "processing":     "{{ _lang('Processing...') }}",
		   "search":         "{{ _lang('Search') }}",
		   "zeroRecords":    "{{ _lang('No matching records found') }}",
		   "paginate": {
			  "first":      "{{ _lang('First') }}",
			  "last":       "{{ _lang('Last') }}",
			  "next":       "{{ _lang('Next') }}",
			  "previous":   "{{ _lang('Previous') }}"
		  }
		}
	});

	$(document).on("ajax-screen-submit", function () {
		orders_table.draw();
  	});
})(jQuery);
</script>
@endsection