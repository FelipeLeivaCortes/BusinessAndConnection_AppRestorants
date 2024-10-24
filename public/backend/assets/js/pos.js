(function ($) {
    "use strict";
    
    var hallId, tableID;
    /*====================
    *    Preloader         *
    ======================*/
    var preloader = $("#preloader");
    $(window).on("load", function () {
        setTimeout(function () {
            preloader.fadeOut("slow");
        }, 300);
    });

    $(document).on("click", "#pos-item-category li a", function (event) {
        event.preventDefault();
        $("#pos-item-category li a").removeClass("active");
        $(this).addClass("active");
        if ($(this).data("class") != "") {
            $("#pos-items > .row > .col-lg-3").addClass("d-none");
            $("#pos-items > .row " + $(this).data("class")).removeClass(
                "d-none"
            );
        } else {
            $("#pos-items > .row > .col-lg-3").removeClass("d-none");
        }
    });

    //Search Products
    $(document).on("keyup", ".pos-search-box", function (event) {
        $("#pos-item-category li a").removeClass("active");
        $("#pos-item-category li a:first").addClass("active");
        $("#pos-items > .row > .col-lg-3").addClass("d-none");

        var search = $(this).val();
        if(search != ''){
            $("#pos-items > .row > .col-lg-3").each(function(index, elem){
                if($(elem).find('.item-name').html().toLowerCase().startsWith(search)){
                    $(elem).removeClass("d-none");
                }
            });
        }else{
            $("#pos-items > .row > .col-lg-3").removeClass("d-none");
        }
    });

    $(document).on("click", ".btn-plus", function (event) {
        var value = parseInt($(this).parent().find("input").val());
        $(this)
            .parent()
            .find("input")
            .val(value + 1);
        $(this).parent().find("input").trigger("change");
    });

    $(document).on("click", ".btn-minus", function (event) {
        var value = parseInt($(this).parent().find("input").val());
        if (value > 1) {
            $(this)
                .parent()
                .find("input")
                .val(value - 1);
            $(this).parent().find("input").trigger("change");
        }
    });

    $(window).bind("popstate", function () {
        var link = location.href;

        if (link != "") {
            $("#pos-content").empty();
            $("#preloader").fadeIn();
            $("#pos-content").load(link, function () {
                $("#preloader").fadeOut();
                $(document).trigger("dPageLoaded", { link: link });
            });
        }
    });

    $(document).on("click", ".restaurant-table", function () {
        var link = $(this).data("link");
        hallId = $(this).data("hall-id");
        tableID = $(this).data("id");

        $("#preloader").fadeIn();
        $("#pos-content").load(link, function () {
            history.pushState({}, "", link);
            $("#preloader").fadeOut();
            $(document).trigger("dPageLoaded", { link: link });
        });
    });

    $(document).on("click", ".ajax-link", function () {
        var link = $(this).data("link");
        if (typeof link == "undefined") {
            link = $(this).attr("href");
        }

        $("#preloader").fadeIn();
        $("#pos-content").load(link, function (responseTxt, status, xhr) {
            if (status == "success") {          
                history.pushState({}, "", link);
                $(document).trigger("dPageLoaded", { link: link });
            }else{
                alert(JSON.parse(responseTxt));
            }
            $("#preloader").fadeOut();
        });
    });

    $(document).on("dPageLoaded", function (event, data) {
        var page = data.link.replace(/^.*[\\\/]/, "");
        if (page == "table") {
            if (hallId != null) {
                $('#hallTab button[data-target="#hall-' + hallId + '"]').tab(
                    "show"
                );
            }
        }

        if ($(".auto-multiple-select").length) {
            $(".auto-multiple-select").each(function (i, obj) {
                var values = $(this).data("selected");
                $(this).val(values);
            });
        }

        //Multi Select
        if ($(".multi-selector").length) {
            $(".multi-selector").each(function (i, obj) {
                var dropdonwValues = "";
                var selectedText = "";

                $($(this).find("option")).each(function (index, option) {
                    if ($(this).is(":selected")) {
                        selectedText += ", " + option.text;
                        dropdonwValues += `<a class="dropdown-item" href="javascript: void(0);"><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="${option.value}" data-text="${option.text}" checked><span>${option.text}</span></label></a>`;
                    } else {
                        dropdonwValues += `<a class="dropdown-item" href="javascript: void(0);"><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="${option.value}" data-text="${option.text}"><span>${option.text}</span></label></a>`;
                    }
                });

                if (selectedText == "") {
                    selectedText = $(this).data("placeholder");
                } else {
                    selectedText = selectedText.split(" ").slice(1).join(" ");
                }

                $(this).after(`<div class="dropdown multi-select-box">
                    <button class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                        ${selectedText}
                    </button>
                    <div class="dropdown-menu">
                    ${dropdonwValues}
                    </div>
                </div>`);
            });
        }
    });

    //Multi Select
    if ($(".multi-selector").length) {
        $(".multi-selector").each(function (i, obj) {
            var dropdonwValues = "";
            var selectedText = "";

            $($(this).find("option")).each(function (index, option) {
                if ($(this).is(":selected")) {
                    selectedText += ", " + option.text;
                    dropdonwValues += `<a class="dropdown-item" href="javascript: void(0);"><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="${option.value}" data-text="${option.text}" checked><span>${option.text}</span></label></a>`;
                } else {
                    dropdonwValues += `<a class="dropdown-item" href="javascript: void(0);"><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="${option.value}" data-text="${option.text}"><span>${option.text}</span></label></a>`;
                }
            });

            if (selectedText == "") {
                selectedText = $(this).data("placeholder");
            } else {
                selectedText = selectedText.split(" ").slice(1).join(" ");
            }

            $(this).after(`<div class="dropdown multi-select-box">
				<button class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
					${selectedText}
				</button>
				<div class="dropdown-menu">
				${dropdonwValues}
				</div>
			</div>`);
        });
    }

    $(document).on(
        "change",
        ".multi-select-box .dropdown-item input",
        function () {
            var selectedText = "";
            var selectedValues = [];
            $($(this).closest(".dropdown-menu").find("input")).each(function (
                value,
                option
            ) {
                if ($(this).is(":checked")) {
                    selectedText += ", " + $(this).data("text");
                    selectedValues.push($(this).val());
                }
            });

            $(this)
                .closest(".multi-select-box")
                .prev()
                .val(selectedValues)
                .trigger("change");

            if (selectedText == "") {
                selectedText = $(this)
                    .closest(".multi-select-box")
                    .prev()
                    .data("placeholder");
            } else {
                selectedText = selectedText.split(" ").slice(1).join(" ");
            }

            $(this)
                .closest(".multi-select-box")
                .find(".dropdown-toggle")
                .html(selectedText);
        }
    );

    $(document).on("click", ".multi-select-box.dropdown", function (e) {
        e.stopPropagation();
    });

    //Ajax Modal Function
    var previous_select;
	var target_select;
    $(document).on("click", ".ajax-modal", function () {
        var link = $(this).attr("href");
        var title = $(this).data("title");
        var reload = $(this).data("reload");

        $.ajax({
            url: link,
            beforeSend: function () {
                $("#preloader").css("display", "block");
            },
            success: function (data) {
                $("#preloader").css("display", "none");

                if ($.isPlainObject(data)) {
                    var json = JSON.parse(JSON.stringify(data));
                    if(json['result'] == false){
                        $.toast({
                            text: json['message'],
                            showHideTransition: 'slide',
                            icon: 'info',
                            position: 'top-right'
                        });
                        $('.select-table').trigger("click");
                    }
                    return;
                }

                $("#main_modal .modal-title").html(title);
                $("#main_modal .modal-body").html(data);
                $("#main_modal .alert-primary").addClass("d-none");
                $("#main_modal .alert-danger").addClass("d-none");
                $("#main_modal").modal("show");

                if (reload == false) {
					target_select.select2('close');
					$("#main_modal .ajax-submit, #main_modal .ajax-screen-submit").attr('data-reload', false);
				}

                $(".float-field").keypress(function (event) {
                    if ((event.which != 46 || $(this).val().indexOf(".") != -1) && (event.which < 48 || event.which > 57)) {
                        event.preventDefault();
                    }
                });

                $(".int-field").keypress(function (event) {
                    if (event.which < 48 || event.which > 57) {
                        event.preventDefault();
                    }
                });

                //Select2
				$("#main_modal select.select2").select2({
					theme: "classic",
					dropdownParent: $("#main_modal .modal-content")
				});

				//Ajax Select2
				if ($("#main_modal .select2-ajax").length) {
					$('#main_modal .select2-ajax').each(function (i, obj) {

						var display2 = "";
						var divider = "";
						if (typeof $(this).data('display2') !== "undefined") {
							display2 = "&display2=" + $(this).data('display2');
						}

						if (typeof $(this).data('divider') !== "undefined") {
							divider = "&divider=" + $(this).data('divider');
						}

						$(this).select2({
							theme: "classic",
							placeholder: $lang_select_one,
							ajax: {
								url: _url + '/ajax/get_table_data?table=' + $(this).data('table') + '&value=' + $(this).data('value') + '&display=' + $(this).data('display') + display2 + divider + '&where=' + $(this).data('where'),
								processResults: function (data) {
									return {
										results: data
									};
								}
							},
							dropdownParent: $("#main_modal .modal-content")
						}).on('select2:open', () => {
							if(target_select != null && previous_select == null){
								previous_select = target_select;
							}
							target_select = $(this); // 2nd level		
							
							$(".select2-results:not(:has(a))").append('<p class="border-top m-0 p-2"><a class="ajax-modal-2" href="'+ $(this).data('href') +'" data-title="'+ $(this).data('title') +'" data-reload="false"><i class="fas fa-plus-circle mr-1"></i>'+ $lang_add_new +'</a></p>');
						});;

					});
				}

                //Auto Selected
                if ($(".auto-select").length) {
                    $(".auto-select").each(function (i, obj) {
                        $(this).val($(this).data("selected")).trigger("change");
                    });
                }

                /** Init DateTimepicker **/
                $(".datetimepicker").daterangepicker({
                    timePicker: true,
                    timePicker24Hour: true,
                    singleDatePicker: true,
                    showDropdowns: true,
                    locale: {
                        format: "YYYY-MM-DD HH:mm",
                    },
                });

                // INITIALIZATION REQUIRED FIELDS SIGN
                $(
                    "#main_modal form input:required, #main_modal form select:required, #main_modal form textarea:required"
                )
                    .closest(".form-group")
                    .find(
                        "label.form-label, label.col-form-label, label.control-label"
                    )
                    .append("<span class='required'> *</span>");
            },
            error: function (request, status, error) {
                console.log(request.responseText);
            },
        });

        return false;
    });

    //Ajax Secondary Modal Function
	$(document).on("click", ".ajax-modal-2", function () {
		var link = $(this).attr("href");
		var title = $(this).data("title");
        var reload = $(this).data("reload");

		$.ajax({
			url: link,
			beforeSend: function () {
				$("#preloader").css("display", "block");
			}, success: function (data) {
				$("#preloader").css("display", "none");
				$('#secondary_modal .modal-title').html(title);
				$('#secondary_modal .modal-body').html(data);
				$("#secondary_modal .alert-primary").addClass('d-none');
				$("#secondary_modal .alert-danger").addClass('d-none');
				$('#secondary_modal').modal('show');

                if (reload == false) {
					target_select.select2('close');
					$("#secondary_modal .ajax-submit, #secondary_modal .ajax-screen-submit").attr('data-reload', false);
				}
				
				//init Essention jQuery Library
				$("#secondary_modal select.select2").select2({
					theme: "classic",
					dropdownParent: $("#secondary_modal .modal-content")
				});

				$(".float-field").on('keypress', function (event) {
					if ((event.which != 46 || $(this).val().indexOf('.') != -1) &&
						(event.which < 48 || event.which > 57)) {
						event.preventDefault();
					}
				});

				$(".int-field").on('keypress', function (event) {
					if ((event.which < 48 || event.which > 57)) {
						event.preventDefault();
					}
				});
				
				$("#secondary_modal input:required, #secondary_modal select:required, #secondary_modal textarea:required")
					.closest(".form-group")
					.find("label.form-label, label.col-form-label, label.control-label")
					.append("<span class='required'> *</span>");
			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}
		});

		return false;
	});

	$("#secondary_modal").on('show.bs.modal', function () {
		$('#secondary_modal').css("overflow-y", "hidden");
	});

	$("#secondary_modal").on('shown.bs.modal', function () {
		$('#secondary_modal').css("overflow-y", "auto");
	});

    //Ajax Modal Submit without loading
	$(document).on("submit", ".ajax-screen-submit", function () {
		var link = $(this).attr("action");
		var reload = $(this).data('reload');
		var current_modal = $(this).closest('.modal');

		var elem = $(this);
		$(elem).find("button[type=submit]").prop("disabled", true);

		$.ajax({
			method: "POST",
			url: link,
			data: new FormData(this),
			mimeType: "multipart/form-data",
			contentType: false,
			cache: false,
			processData: false,
			beforeSend: function () {
				$("#preloader").css("display", "block");
			}, success: function (data) {
				$(elem).find("button[type=submit]").attr("disabled", false);
				$("#preloader").css("display", "none");
				var json = JSON.parse(data);
				if (json['result'] == "success") {

					$(document).trigger('ajax-screen-submit');

					$.toast({
						text: json['message'],
						showHideTransition: 'slide',
						icon: 'success',
						position: 'top-right'
					});

					var table = json['table'];

					if (json['action'] == "update") {

						$(table + ' tr[data-id="row_' + json['data']['id'] + '"]').find('td').each(function () {
							if (typeof $(this).attr("class") != "undefined") {
								$(this).html(json['data'][$(this).attr("class").split(' ')[0]]);
							}
						});

					} else if (json['action'] == "store") {
						$(elem)[0].reset();
						var new_row = $(table).find('tbody').find('tr:eq(0)').clone();

						$(new_row).attr("data-id", "row_" + json['data']['id']);


						$(new_row).find('td').each(function () {
							if ($(this).attr("class") == "dataTables_empty") {
								window.location.reload();
							}
							if (typeof $(this).attr("class") != "undefined") {
								$(this).html(json['data'][$(this).attr("class").split(' ')[0]]);
							}
						});


						$(new_row).find('form').attr("action", link + "/" + json['data']['id']);
						$(new_row).find('.dropdown-edit').attr("data-href", link + "/" + json['data']['id'] + "/edit");
						$(new_row).find('.dropdown-view').attr("data-href", link + "/" + json['data']['id']);

						$(table).prepend(new_row);

						if (reload == false) {				
							var select_value = json['data'][target_select.data('value')];
							var select_display = json['data'][target_select.data('display')];

							var newOption = new Option(select_display, select_value, true, true);
							target_select.append(newOption).trigger('change');

							if(previous_select != null){
								var newOption = new Option(select_display, select_value, true, true);
								previous_select.append(newOption).trigger('change');					
							}
							$(current_modal).modal('hide');
						}

					}
					$(current_modal).modal('hide');
					$(current_modal).find(".alert-primary").addClass('d-none');
					$(current_modal).find(".alert-danger").addClass('d-none');

				} else if (json['result'] == "error") {
					$(current_modal).find(".alert-danger").html("");
					if (Array.isArray(json['message'])) {
						jQuery.each(json['message'], function (i, val) {
							$(current_modal).find(".alert-danger").append("<span>" + val + "</span>");
						});
						$(current_modal).find(".alert-primary").addClass('d-none');
						$(current_modal).find(".alert-danger").removeClass('d-none');
					} else {
						$(current_modal).find(".alert-danger").html("<span>" + json['message'] + "</span>");
						$(current_modal).find(".alert-primary").addClass('d-none');
						$(current_modal).find(".alert-danger").removeClass('d-none');
					}
				} else {
					$.toast({
						text: data.replace(/(<([^>]+)>)/ig, ""),
						showHideTransition: 'slide',
						icon: 'error',
						position: 'top-right'
					});
				}
			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}
		});

		return false;
	});

    $(document).on("click", ".btn-remove", function () {
        var elem = this;
        if (confirm($lang_alert_message) == true) {
            $(elem).closest("form").submit();
        } else {
            return false;
        }
    });

    $(document).on("change",".select_product_option, .product_addon", function () {
        if ($(this).val() != "") {
            $.ajax({
                url: $("#product-variation-form").attr("action"),
                method: "POST",
                data: $("#product-variation-form").serialize(),
                beforeSend: function () {
                    $("#price-value").html(
                        '<i class="fas fa-spinner fa-spin"></i>'
                    );
                },
                success: function (data) {
                    var result = JSON.parse(data);

                    if (result.result == true) {
                        if (result.is_available == false) {
                            alert($lang_item_not_available);
                            $(".select_product_option")
                                .prop("selectedIndex", 0)
                                .change();
                            return;
                        }
                        $("#price-value").html(result.price);
                    }
                },
            });
        }
    });

    $(document).on("submit", "#add-to-cart-form", function (event) {
        event.preventDefault();
        var buttonText = $(".btn-cart").html();
        $(".btn-cart").attr("disabled", true);

        if ($("#quantity").val() != "") {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
            });

            $.ajax({
                url: $("#add-to-cart-form").attr("action"),
                method: "POST",
                data: $(
                    "#product-variation-form, #add-to-cart-form"
                ).serialize(),
                beforeSend: function () {
                    $(".btn-cart").html(
                        '<i class="fas fa-spinner fa-spin"></i>'
                    );
                },
                success: function (data) {
                    $(".btn-cart").attr("disabled", false);
                    $(".btn-cart").html(buttonText);
                    var json = JSON.parse(JSON.stringify(data));
                    if (json["result"] == true) {
                        $("#pos-cart .cart-content").html("");

                        jQuery.each(
                            json["cartItems"],
                            function (index, cartItem) {
                                var _cart_remove_url =
                                    _user_url +
                                    "/pos/" +
                                    index +
                                    "/" +
                                    cartItem.table_id +
                                    "/remove_cart";

                                $("#pos-cart .cart-content")
                                    .append(`<div class="cart-item justify-content-between">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <span class="item-name">${cartItem.name}</span>
                                        </div>
                                        <div class="action ml-2">
                                            <a href="${_cart_remove_url}"><i class="fas fa-times-circle"></i></a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="item-price flex-grow-1"><b>${cartItem.unit_price}</b></span>
                                        <span class="item-quantity">
                                        <input type="text" class="quantity" name="quantity" min="1" value="${cartItem.quantity}" placeholder="${$lang_quantity}" data-cart-id="${index}" data-table-id="${cartItem.table_id}" readonly="readonly"> 
                                            <button type="button" class="btn-plus">+</button> 
                                            <button type="button" class="btn-minus">-</button>
                                        </span>
                                    </div>
                                </div>`);
                            }
                        );

                        $("#subTotal").html(json["subTotal"]);
                        $("#percentage").html(json["discount"]["percentage"]);
                        $("#discount").html(json["discount"]["amount"]);
                        $("#s-percentage").html(
                            json["serviceCharge"]["percentage"]
                        );
                        $("#serviceCharge").html(
                            json["serviceCharge"]["amount"]
                        );
                        $("#grandTotal").html(json["grandTotal"]);
                        $("#taxes").html("");
                        jQuery.each(json["taxes"], function (j, tax) {
                            $("#taxes").append(`<div class="d-flex fs">
                                <span class="flex-grow-1">${tax["name"]} (${tax["rate"]}%)</span>
                                <span>${tax["amount"]}</span>
                            </div>`);
                        });
                        if(json["needUpdate"] == true){
                            $("#need-update").removeClass('d-none');
                        }
                        $("#main_modal").modal("hide");
                        $.toast({
                            text: $lang_cart_item_added,
                            showHideTransition: "slide",
                            icon: "success",
                            position: "top-right",
                        });
                    } else {
                        if (Array.isArray(json["message"])) {
                            jQuery.each(json["message"], function (i, val) {
                                $(current_modal)
                                    .find(".alert-danger")
                                    .append("<span>" + val + "</span>");
                            });
                            $(current_modal)
                                .find(".alert-primary")
                                .addClass("d-none");
                            $(current_modal)
                                .find(".alert-danger")
                                .removeClass("d-none");
                        } else {
                            $(current_modal)
                                .find(".alert-danger")
                                .html("<span>" + json["message"] + "</span>");
                            $(current_modal)
                                .find(".alert-primary")
                                .addClass("d-none");
                            $(current_modal)
                                .find(".alert-danger")
                                .removeClass("d-none");
                        }
                    }
                },
            });
        }
    });

    $(document).on(
        "change",
        "#pos-cart .cart-content .quantity",
        function (event) {
            var elem = $(this);
            if ($(elem).val() != "") {
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                });

                $.ajax({
                    url:
                        _user_url +
                        "/pos/" +
                        $(elem).data("cart-id") +
                        "/" +
                        $(elem).data("table-id") +
                        "/update_cart",
                    method: "POST",
                    data: { quantity: $(elem).val() },
                    beforeSend: function () {
                        $("#preloader").fadeIn();
                    },
                    success: function (data) {
                        $("#preloader").fadeOut();
                        var json = JSON.parse(JSON.stringify(data));

                        if (json["result"] == true) {
                            $("#subTotal").html(json["subTotal"]);
                            $("#percentage").html(
                                json["discount"]["percentage"]
                            );
                            $("#discount").html(json["discount"]["amount"]);
                            $("#s-percentage").html(
                                json["serviceCharge"]["percentage"]
                            );
                            $("#serviceCharge").html(
                                json["serviceCharge"]["amount"]
                            );
                            $("#grandTotal").html(json["grandTotal"]);
                            $("#taxes").html("");
                            jQuery.each(json["taxes"], function (j, tax) {
                                    $("#taxes").append(`<div class="d-flex fs">
                                    <span class="flex-grow-1">${tax["name"]} (${tax["rate"]}%)</span>
                                    <span>${tax["amount"]}</span>
                                </div>`);
                            });
                            if(json["needUpdate"] == true){
                                $("#need-update").removeClass('d-none');
                            }
                            $.toast({
                                text: $lang_cart_updated,
                                showHideTransition: "slide",
                                icon: "success",
                                position: "top-right",
                            });
                        }
                    },
                });
            }
        }
    );

    $(document).on("submit", "#discount-form", function (event) {
        event.preventDefault();
        $.ajax({
            url: $(this).attr("action"),
            method: "POST",
            data: $(this).serialize(),
            beforeSend: function () {
                $("#preloader").fadeIn();
            },
            success: function (data) {
                $("#preloader").fadeOut();
                var json = JSON.parse(JSON.stringify(data));

                if (json["result"] == true) {
                    $("#percentage").html(json["discount"]["percentage"]);
                    $("#discount").html(json["discount"]["amount"]);
                    $("#s-percentage").html(
                        json["serviceCharge"]["percentage"]
                    );
                    $("#serviceCharge").html(json["serviceCharge"]["amount"]);
                    $("#subTotal").html(json["subTotal"]);
                    $("#grandTotal").html(json["grandTotal"]);
                    $("#taxes").html("");
                    jQuery.each(json["taxes"], function (j, tax) {
                        $("#taxes").append(`<div class="d-flex fs">
                            <span class="flex-grow-1">${tax["name"]} (${tax["rate"]}%)</span>
                            <span>${tax["amount"]}</span>
                        </div>`);
                    });
                    if(json["needUpdate"] == true){
                        $("#need-update").removeClass('d-none');
                    }
                    $("#main_modal").modal("hide");
                } else {
                    $.toast({
                        text: json["message"],
                        showHideTransition: "slide",
                        icon: "error",
                        position: "top-right",
                    });
                }
            },
        });
    });

    //Select Taxes
    $(document).on("change", ".select_taxes", function (event) {
        var elem = $(this);
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.ajax({
            url: _user_url + "/pos/" + $(elem).data("table-id") + "/apply_tax",
            method: "POST",
            data: { "taxes[]": $(elem).val() },
            beforeSend: function () {
                $("#preloader").fadeIn();
            },
            success: function (data) {
                $("#preloader").fadeOut();
                var json = JSON.parse(JSON.stringify(data));
                console.log(data);

                if (json["result"] == true) {
                    $("#percentage").html(json["discount"]["percentage"]);
                    $("#discount").html(json["discount"]["amount"]);
                    $("#s-percentage").html(
                        json["serviceCharge"]["percentage"]
                    );
                    $("#serviceCharge").html(json["serviceCharge"]["amount"]);
                    $("#subTotal").html(json["subTotal"]);
                    $("#grandTotal").html(json["grandTotal"]);
                    $("#taxes").html("");
                    jQuery.each(json["taxes"], function (j, tax) {
                        $("#taxes").append(`<div class="d-flex fs">
                            <span class="flex-grow-1">${tax["name"]} (${tax["rate"]}%)</span>
                            <span>${tax["amount"]}</span>
                        </div>`);
                    });
                    if(json["needUpdate"] == true){
                        $("#need-update").removeClass('d-none');
                    }
                } else {
                    $.toast({
                        text: json["message"],
                        showHideTransition: "slide",
                        icon: "error",
                        position: "top-right",
                    });
                }
            },
        });
    });

    $(document).on(
        "click",
        "#pos-cart .cart-content .action > a",
        function (event) {
            event.preventDefault();
            var elem = $(this);
            $.ajax({
                url: $(elem).attr("href"),
                data: $(
                    "#product-variation-form, #add-to-cart-form"
                ).serialize(),
                beforeSend: function () {
                    $("#preloader").fadeIn();
                },
                success: function (data) {
                    $("#preloader").fadeOut();
                    var json = JSON.parse(JSON.stringify(data));
                    if (json["result"] == true) {
                        $(elem).parent().parent().parent().remove();
                        $("#subTotal").html(json["subTotal"]);
                        $("#percentage").html(json["discount"]["percentage"]);
                        $("#discount").html(json["discount"]["amount"]);
                        $("#s-percentage").html(
                            json["serviceCharge"]["percentage"]
                        );
                        $("#serviceCharge").html(
                            json["serviceCharge"]["amount"]
                        );
                        $("#grandTotal").html(json["grandTotal"]);
                        $("#taxes").html("");
                        jQuery.each(json["taxes"], function (j, tax) {
                            $("#taxes").append(`<div class="d-flex fs">
                            <span class="flex-grow-1">${tax["name"]} (${tax["rate"]}%)</span>
                            <span>${tax["amount"]}</span>
                        </div>`);
                        });
                        if(json["needUpdate"] == true){
                            $("#need-update").removeClass('d-none');
                        }
                    }
                },
            });
        }
    );

    $(document).on("keyup", "#receivedAmount", function () {
        var dueAmount = parseFloat($("#dueAmount").val());
        var receivedAmount = parseFloat($(this).val());

        if (receivedAmount >= dueAmount) {
            var changeAmount = receivedAmount - dueAmount;
            $("#changeAmount").val(changeAmount.toFixed(2));
        } else {
            $("#changeAmount").val(0);
        }
    });

    //Place Order
    $(document).on("submit", "#place-order-form", function (event) {
        event.preventDefault();
        var elem = $(this);
        var buttonText = $(elem).find(":submit").html();
        $(elem).find(":submit").prop("disabled", true);

        $.ajax({
            url: $(elem).attr("action"),
            method: "POST",
            data: $(elem).serialize(),
            beforeSend: function () {
                $("#preloader").fadeIn();
                $(elem)
                    .find(":submit")
                    .html('<i class="fas fa-spinner fa-spin"></i>');
            },
            success: function (data) {
                $(elem).find("button[type=submit]").attr("disabled", false);
                $(elem).find(":submit").html(buttonText);
                var json = JSON.parse(JSON.stringify(data));

                if (json["result"] == true) {
                    $.toast({
                        text: json["message"],
                        showHideTransition: "slide",
                        icon: "success",
                        position: "top-right",
                    });
                    $("#main_modal").modal("hide");

                    var link = json["link"];
                    $("#pos-content").load(link, function () {
                        history.pushState({}, "", link);
                        $("#preloader").fadeOut();
                        $(document).trigger("dPageLoaded", { link: link });

                        //If Order Completed
                        if (json["orderStatus"] == "5") {
                            $("#preloader").fadeOut();
                            var printLink = json["printLink"];
                            window.open(printLink, "_blank");
                        }
                    });
                } else {
                    if (Array.isArray(json["message"])) {
                        $("#main_modal").find(".alert-danger").html("");
                        jQuery.each(json["message"], function (i, val) {
                            $("#main_modal")
                                .find(".alert-danger")
                                .append("<span>" + val + "</span>");
                        });
                        $("#main_modal")
                            .find(".alert-primary")
                            .addClass("d-none");
                        $("#main_modal")
                            .find(".alert-danger")
                            .removeClass("d-none");
                    } else {
                        $("#main_modal")
                            .find(".alert-danger")
                            .html("<span>" + json["message"] + "</span>");
                        $("#main_modal")
                            .find(".alert-primary")
                            .addClass("d-none");
                        $("#main_modal")
                            .find(".alert-danger")
                            .removeClass("d-none");
                    }
                    $("#preloader").fadeOut();
                }
            },
        });
    });

    
})(jQuery);
