@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12 d-flex justify-content-center">
		<div class="card overflow-auto">
		    <div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ $hall->name }} {{ _lang('Table Setup') }}</span>
				<div>
                    <a href="{{ route('tables.index', $hall->id) }}" class="btn btn-dark btn-xs"><i class="fas fa-list-ul"></i> {{ _lang('Table List') }}</a>
				    <a href="{{ route('tables.create', $hall->id) }}" data-title="{{ _lang('Add New Table') }}" class="btn btn-primary btn-xs ajax-modal"><i class="ti-plus"></i> {{ _lang('Add Table') }}</a>
					<a href="{{ route('tables.edit_background', $hall->id) }}" data-title="{{ _lang('Update Background Hall') }}" class="btn btn-primary btn-xs ajax-modal"><i class="fas fa-cog"></i> {{ _lang('Update Background Hall') }}</a>
			    </div>
			</div>
			<div class="card-body">
				<div id="tableData" class="text-center">
					<div id="hallView" data-id="{{ $hall->id }}" style="{{ $hall->css }}">
						@foreach($hall->tables as $table)
						<div class="restaurant-table cursor-move {{ $table->type }}" data-id="{{ $table->id }}" style="{{ $table->css }}">
							<span class="name">{{ $table->table_no }}</span>
							<div class="chair_limit">
								<span><i class="fas fa-chair"></i> {{ $table->chair_limit }}</span>
							</div>
						</div>
						@endforeach
					</div>
				</div>

				<div class="float-left mt-3">
					<button type="button" id="btn-save" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Changes') }}</button>
				</div>

				<div class="float-right mt-3">
					<button type="button" class="btn btn-danger" id="remove-bottom"><i class="fas fa-arrow-up"></i></button>
					<button type="button" class="btn btn-primary" id="add-bottom"><i class="fas fa-arrow-down"></i></button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js-script')
<script>
(function($) {
    $.fn.drags = function(opt) {

        opt = $.extend({handle:""}, opt);

        if(opt.handle === "") {
            var $el = this;
        } else {
            var $el = this.find(opt.handle);
        }

        return $el.on("mousedown", function(e) {
            if(opt.handle === "") {
                var $drag = $(this).addClass('draggable');
            } else {
                var $drag = $(this).addClass('active-handle').parent().addClass('draggable');
            }
            var z_idx = $drag.css('z-index'),
                drg_h = $drag.outerHeight(),
                drg_w = $drag.outerWidth(),
                pos_y = $drag.offset().top + drg_h - e.pageY,
                pos_x = $drag.offset().left + drg_w - e.pageX;
            $drag.parents().on("mousemove", function(e) {
				$('.draggable').offset({
                    top:e.pageY + pos_y - drg_h,
                    left:e.pageX + pos_x - drg_w
                }).on("mouseup", function() {
                    $(this).removeClass('draggable').css('z-index', z_idx);
                });
            });
            e.preventDefault(); // disable selection
        }).on("mouseup", function() {
            if(opt.handle === "") {
                $(this).removeClass('draggable');
            } else {
                $(this).removeClass('active-handle').parent().removeClass('draggable');
            }
        });

    }

	$('#hallView .restaurant-table').drags();

	if($("#hallView").css('height') === '400px'){
		$("#remove-bottom").prop('disabled', true);
	}

	$(document).on('click', '#add-bottom', function(){
		var height = parseFloat($("#hallView").css('height')) + 40;
		$("#hallView").css('height', height);

		if(height > 400){
			$("#remove-bottom").prop('disabled', false);
		}
	});

	$(document).on('click', '#remove-bottom', function(){
		var height = parseFloat($("#hallView").css('height')) - 40;
		$("#hallView").css('height', height);

		if(height <= 400){
			$("#remove-bottom").prop('disabled', true);
		}
	});

	$(document).on('click', '#btn-save', function(){
		var hallId = $('#hallView').data('id');

		var tables = [];
		
		$('.restaurant-table').each(function(index,item){
			var table = {'id': $(item).data('id'), 'css': $(item).attr('style')};
			tables.push(table);
		});

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$.ajax({
			url: _user_url + '/halls/'+ hallId +'/update_setup',
			method: 'POST',
			data: {
				'tables': JSON.stringify(tables),
				'hallView': $("#hallView").attr('style')
			},
			beforeSend: function(){
				$("#preloader").fadeIn();
			},
			success: function(data){
				$("#preloader").fadeOut();
				console.log(data);
				var json = JSON.parse(JSON.stringify(data));
				if(json['result'] == 'success'){
					Swal.fire({
						text: json['message'],
						icon: 'success',
					});
				}
			}
		});
	});

})(jQuery);
</script>
@endsection