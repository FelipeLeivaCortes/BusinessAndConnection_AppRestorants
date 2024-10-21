(function ($) {
    "use strict";

    $(".status").sortable({
        items: "li",
        connectWith: ".order-status",
        helper: "clone",
        appendTo: "#kanban-view",
        placeholder: "ui-state-highlight-task",
        revert: "invalid",
        stop: function (event, ui) {
            var status_id = ui.item.parent().data("order-status-id");
            var order_id = ui.item.data("order-id");
            var link = _user_url + '/orders/update_order_status/' + order_id;

			$.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

			$.ajax({
				method: "POST",
				url: link,
				data: {'status': status_id},
				beforeSend: function () {
					$("#preloader").css("display", "block");
				}, success: function (data) {
					$("#preloader").css("display", "none");
					var json = JSON.parse(JSON.stringify(data));
					if(json['result'] == 'success'){
						$.toast({
							text: json['message'],
							showHideTransition: 'slide',
							icon: 'success',
							position: 'top-right'
						});
					}else{
						$.toast({
							text: json['message'],
							showHideTransition: 'slide',
							icon: 'error',
							position: 'top-right'
						});
						$(".status").sortable('cancel');
					}
				}
			});
        },
    });

    //Set Kanban Width
    var status_width = $("#kanban-view").children().length * 320;
    if (status_width > $("#kanban-view").width()) {
        $("#kanban-view").css("min-width", status_width + 100);
    }


	$.fn.notify = function (message) {
		const audio = document.getElementById("myAudio");
		if (audio.paused) {
			audio.muted = false; 
			audio.play();
		}
		$.toast({
			text: message,
			showHideTransition: 'slide',
			icon: 'info',
			position: 'top-right',
			hideAfter: 5000
		});
	}

    $.fn.fetchOrders = function () {
        $.get(_user_url + "/orders/tracking/fetch", function (data, status) {
            var json = JSON.parse(JSON.stringify(data));
            var orderStatus = [
                "Pending",
                "Accepted",
                "Preparing",
                "Ready",
                "Delivered",
                "Completed",
            ];

            for (let i = 0; i < orderStatus.length; i++) {
				const orderIds = [];
                if (typeof json[orderStatus[i]] !== "undefined") {	
                    $.each(json[orderStatus[i]], function (key, orderData) {
                        if ($("#order-status-" + i).has("#order-" + orderData.id).length == 0) {
							//Add new element
							$("#order-status-" + i).append(`<li id="order-${orderData.id}" data-order-id="${orderData.id}">
																<div class="card">
																	<div class="card-body">
																		<p>${$lang_order_id}# ${orderData.order_number}</p>
																		<p>${$lang_created}# ${ orderData.created_at}</p>
																		<p>${orderData.order_type == 'table' ? orderData.table  : $lang_order_type + ': ' + orderData.order_type.toUpperCase()}</p>
																		<p>${$lang_grand_total}: ${orderData.total}</p>			
																		<div class="mt-1">
																			<a href="${orderData.print_link}" target="_blank" class="btn btn-primary btn-xs"><i class="fas fa-print"></i></a>
																			<a href="${orderData.view_link}" target="_blank" class="btn btn-dark btn-xs"><i class="far fa-eye"></i></a>
																			<a href="${orderData.edit_link}" data-title="{{ _lang('Order Details') }}" class="btn btn-warning btn-xs ajax-modal"><i class="fas fa-pencil-alt"></i></a>
																		</div>	
																	</div>	
																</div>
															</li>`);
							if(orderData.status == 0){
								//Notify New Pending Order	
								$.fn.notify($lang_new_order_placed);
							}								
						}else{
							$("#order-" + orderData.id).find('a').find('p:last-child').html(`${$lang_grand_total}: ${orderData.total}`);
						}	
						orderIds.push(orderData.id);
                    });
				}

				$.each($("#order-status-" + i + " > li"), function (key, oldData) {
					if(orderIds.indexOf($(oldData).data('order-id')) == -1) {
						$(oldData).remove();
					}
				});    
            } // End for loop
        });
    };


	if(_live_order_api == 'ably'){
		const client = new Ably.Realtime({ authUrl: '/ably/auth' });

		client.connection.on('connected', function() {
			console.log('Connected Successfully');
		});

		client.connection.on('failed', function() {
			console.log('Failed to connect');
		});

		var channel = client.channels.get('order');

		channel.subscribe('orderUpdated', function (message) {
			var json = JSON.parse(message.data);
			if(json.business_id == _business_id){
				$.fn.fetchOrders();
			}
		});
	}else{
		setInterval(function() {
			$.fn.fetchOrders();
		}, 10000); 
	}
})(jQuery);
