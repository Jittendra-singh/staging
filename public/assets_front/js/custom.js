/****************************************************************************
 * Barren v1.0
 * Simple Online Event Ticketing System Html Template by Gambolthemes
 * Copyright 2022 | Gambolthemes | https://gambolthemes.net
 * @package Gambolthemes
 ****************************************************************************/

/*----------------------------------------------
Index Of Script
------------------------------------------------

:: Tooltip
:: Bookmark Event
:: QTY JS
:: Switch Buttons
:: Payment Method Accordion
:: Add Tags
:: Initialize and add the map
:: Owl Silder
:: Count Time JS
:: Multi Dropdown JS
:: Events by Date JS

------------------------------------------------
Index Of Script
----------------------------------------------*/


/*--- Tooltip Widget ---*/
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	return new bootstrap.Tooltip(tooltipTriggerEl)
})

/*--- Bookmark Event ---*/
$(document).ready(function () {
	$('.bookmark-icon, .bookmark-button').on('click', function (e) {
		e.preventDefault();
		$(this).toggleClass('bookmarked');
		$(this).children('.bookmark-icon').toggleClass('bookmarked');
	});
});

/*--- QTY JS ---*/
function increaseCount(a, b) {
	var input = b.previousElementSibling;
	var value = parseInt(input.value, 10);
	var tid = $(b).attr('ticketid');
	var maxAllowedQty = parseInt($(b).attr('data-allowed-qty'), 10);
	if (maxAllowedQty != '' && !isNaN(maxAllowedQty)) {
		if (parseFloat($(b).attr('data-max')) > value && value < maxAllowedQty) {
			value = isNaN(value) ? 0 : value;
			value++;
			input.value = value;
		} else {
			if (value >= maxAllowedQty) {
				showError('You can only book maximum ' + maxAllowedQty + ' tickets per order.', 'error');
			} else {
				showError('Only ' + $(b).attr('data-max') + ' tickets are available at the moment.', 'error');
			}
		}
	} else {
		if (parseFloat($(b).attr('data-max')) > value) {
			value = isNaN(value) ? 0 : value;
			value++;
			input.value = value;
		} else {
			showError('Only ' + $(b).attr('data-max') + ' tickets are available at the moment.', 'error');
		}
	}

	$('#availTicketCount_' + tid).text(parseFloat($(b).attr('data-max')) - value);
	console.log(value);
}

function decreaseCount(a, b) {
	var input = b.nextElementSibling;
	var value = parseInt(input.value, 10);
	var tid = $(b).attr('ticketid');
	console.log(input, value);
	if (value > 0) {
		value = isNaN(value) ? 0 : value;
		value--;
		input.value = value;
	}
	$('#availTicketCount_' + tid).text(parseFloat($(b).attr('data-max')) - value);
}

/*--- Switch Buttons ---*/

// Early Bird Discount Switch Button

$(document).ready(function () {
	$("#bird-discount").on("change", function (e) {
		const isOn = e.currentTarget.checked;

		if (isOn) {
			$(".online-event-discount-wrapper").show();
		} else {
			$(".online-event-discount-wrapper").hide();
		}
	});
});

// Early Bird Discount Switch Button 2

$(document).ready(function () {
	$("#bird-discount2").on("change", function (e) {
		const isOn = e.currentTarget.checked;

		if (isOn) {
			$(".online-event-discount-wrapper2").show();
		} else {
			$(".online-event-discount-wrapper2").hide();
		}
	});
});

// Free Event Ticketing Switch Button

$(document).ready(function () {
	$("#free-event-ticketing").on("change", function (e) {
		const isOn = e.currentTarget.checked;

		if (isOn) {
			$(".disabled-action").hide();
		} else {
			$(".disabled-action").show();
		}
	});
});

// Booking Start Time Switch Button

$(document).ready(function () {
	$("#booking-start-time-btn").on("change", function (e) {
		const isOn = e.currentTarget.checked;

		if (isOn) {
			$(".booking-start-time-holder").hide();
		} else {
			$(".booking-start-time-holder").show();
		}
	});

	$('.generate-event-meta-data').on('click', async function (event) {
		$('#focus_keywords, #meta_title, #meta_description').val('').attr('disabled', 'disabled');
		let target = $('.generate-event-meta-data');
		$('.bx-loader').addClass('bx-spin');
		target.text('Generating...');
		await $.ajax({
			type: "POST",
			url: $(target).data('url'),
			data: {
				'event_name': $('#event_name').val(),
				'description': $('#description').val(),
				'start_datetime': $('#start_datetime').val(),
				'end_datetime': $('#end_datetime').val(),
				'venue': $('#venue').val(),
				'pincode': $('#pincode').val(),
				'event_category':$('[name="event_category_id"] option:selected').text()
			},
			success: function (response) {
				target.text('Generate By AI');
				if (response.status) {
					$('#meta_title').val(response.data.meta_title).removeAttr('disabled');
					$('#meta_description').val(response.data.meta_description).removeAttr('disabled');
					$('#focus_keywords').val(response.data.meta_keywords).removeAttr('disabled');
				}
				else {
					showError(response.message, 'error');
				}
			},
		});
		$('.bx-loader').removeClass('bx-spin');
	})
	$('.generate-focus-keywords').on('click', async function (event) {
		$('#focus_keywords').val('').attr('disabled', 'disabled');
		let target = $('.generate-focus-keywords');
		console.log($(target).data('url'));
		$('.bx-loader').addClass('bx-spin');
		await $.ajax({
			type: "POST",
			url: $(target).data('url'),
			data: {
				'meta_title': $('#meta_title').val(),
				'meta_description': $('#meta_description').val(),
			},
			success: function (response) {
				if (response.status) {
					$('#focus_keywords').val(response.data.keywords).removeAttr('disabled');
				}
				else {
					showError(response.message, 'error');
				}
			},
		});
		$('.bx-loader').removeClass('bx-spin');
	})
});

// Booking End Time Switch Button

$(document).ready(function () {
	$("#booking-end-time-btn").on("change", function (e) {
		const isOn = e.currentTarget.checked;

		if (isOn) {
			$(".booking-end-time-holder").hide();
		} else {
			$(".booking-end-time-holder").show();
		}
	});
});

// Refund Policies Holder Switch Button

$(document).ready(function () {
	$("#refund-policies-btn").on("change", function (e) {
		const isOn = e.currentTarget.checked;

		if (isOn) {
			$(".refund-policies-holder").hide();
		} else {
			$(".refund-policies-holder").show();
		}
	});
});

// Ticket Instructions Switch Button

$(document).ready(function () {
	$("#ticket-instructions-btn").on("change", function (e) {
		const isOn = e.currentTarget.checked;

		if (isOn) {
			$(".ticket-instructions-holder").hide();
		} else {
			$(".ticket-instructions-holder").show();
		}
	});
});

// Tags Switch Button

$(document).ready(function () {
	$("#tags-btn").on("change", function (e) {
		const isOn = e.currentTarget.checked;

		if (isOn) {
			$(".tags-holder").hide();
		} else {
			$(".tags-holder").show();
		}
	});
});

// Single Ticket Per Level Switch Button

$(document).ready(function () {
	$("#is-restrict-total-ticket").on("change", function (e) {
		const isOn = e.currentTarget.checked;

		if (isOn) {
			$(".total_ticket_per_level").hide();
		} else {
			$(".total_ticket_per_level").show();
		}
	});
});

// Single Ticket Per User Switch Button

$(document).ready(function () {
	$("#is-restrict-ticket-per-user").on("change", function (e) {
		const isOn = e.currentTarget.checked;

		if (isOn) {
			$(".total_ticket_per_user").hide();
		} else {
			$(".total_ticket_per_user").show();
		}
	});
});

// Group Ticket Per Level2 Switch Button

$(document).ready(function () {
	$("#is-restrict-total-ticket2").on("change", function (e) {
		const isOn = e.currentTarget.checked;

		if (isOn) {
			$(".total_ticket_per_level2").hide();
		} else {
			$(".total_ticket_per_level2").show();
		}
	});
});

// Group Ticket Per User2 Switch Button

$(document).ready(function () {
	$("#is-restrict-ticket-per-user2").on("change", function (e) {
		const isOn = e.currentTarget.checked;

		if (isOn) {
			$(".total_ticket_per_user2").hide();
		} else {
			$(".total_ticket_per_user2").show();
		}
	});
});

/*--- Payment Method Accordion ---*/
$('input[name="refund_policy_id"]').on('click', function () {
	var $value = $(this).attr('value');
	$('.refund-input-content').slideUp();
	$('[data-method="' + $value + '"]').slideDown();
});

/*--- Add tags ---*/
$(".tags-container").each(function () {

	var keywordInput = $(this).find(".tags-input");
	var keywordsList = $(this).find(".tags-list");

	// adding tags
	function addKeyword() {
		var $newKeyword = $("<span class='tag'><span class='tag-remove'></span><span class='tag-text'>" + keywordInput.val() + "</span></span>");
		keywordsList.append($newKeyword).trigger('resizeContainer');
		keywordInput.val("");
	}

	// add via enter key
	keywordInput.on('keyup', function (e) {
		if ((e.keyCode == 13) && (keywordInput.val() !== "")) {
			addKeyword();
		}
	});

	// removing tags
	$(document).on("click", ".tag-remove", function () {
		$(this).parent().addClass('tag-removed');

		function removeFromMarkup() {
			$(".tag-removed").remove();
		}
		setTimeout(removeFromMarkup, 500);
		keywordsList.css({ 'height': 'auto' }).height();
	});


	// animating container height
	keywordsList.on('resizeContainer', function () {
		var heightnow = $(this).height();
		var heightfull = $(this).css({ 'max-height': 'auto', 'height': 'auto' }).height();

		$(this).css({ 'height': heightnow }).animate({ 'height': heightfull }, 200);
	});

	$(window).on('resize', function () {
		keywordsList.css({ 'height': 'auto' }).height();
	});

	// Auto Height for tags that are pre-added
	$(window).on('load', function () {
		var keywordCount = $('.tags-list').children("span").length;

		// Enables scrollbar if more than 3 items
		if (keywordCount > 0) {
			keywordsList.css({ 'height': 'auto' }).height();

		}
	});

});

/*--- Initialize and add the map ---*/
// The following example creates a marker in Stockholm, Sweden using a DROP
// animation. Clicking on the marker will toggle the animation between a BOUNCE
// animation and no animation.
/* let marker;

function initMap() {
	const map = new google.maps.Map(document.getElementById("map"), {
		zoom: 13,
		center: { lat: 59.325, lng: 18.07 },
	});

	marker = new google.maps.Marker({
		map,
		draggable: true,
		animation: google.maps.Animation.DROP,
		position: { lat: 59.327, lng: 18.067 },
	});
	marker.addListener("click", toggleBounce);
}

function toggleBounce() {
	if (marker.getAnimation() !== null) {
		marker.setAnimation(null);
	} else {
		marker.setAnimation(google.maps.Animation.BOUNCE);
	}
}

window.initMap = initMap; */
/*--- Owl Sliders ---*/

// Engaging Online and Venue Events Slider
$('.engaging-slider').owlCarousel({
	items: 5,
	loop: true,
	margin: 20,
	nav: true,
	dots: true,
	navText: ["<i class='uil uil-angle-left'></i>", "<i class='uil uil-angle-right'></i>"],
	smartSpeed: 800,
	autoplay: true,
	autoplayTimeout: 3000,
	autoplayHoverPause: true,
	responsive: {
		0: {
			items: 1
		},
		600: {
			items: 1
		},
		1000: {
			items: 2
		},
		1200: {
			items: 3
		},
		1400: {
			items: 3
		}
	}
})

// Testimonial Slider
$('.testimonial-slider').owlCarousel({
	items: 10,
	loop: true,
	margin: 20,
	nav: false,
	dots: true,
	smartSpeed: 800,
	autoplay: true,
	autoplayTimeout: 3000,
	autoplayHoverPause: true,
	responsive: {
		0: {
			items: 1
		},
		600: {
			items: 1
		},
		1000: {
			items: 2
		},
		1200: {
			items: 2
		},
		1400: {
			items: 2
		}
	}
})

// Organisations Slider
$('.organisations-slider').owlCarousel({
	items: 7,
	loop: true,
	margin: 20,
	nav: false,
	dots: false,
	smartSpeed: 800,
	autoplay: true,
	autoplayTimeout: 3000,
	autoplayHoverPause: true,
	responsive: {
		0: {
			items: 2
		},
		600: {
			items: 2
		},
		1000: {
			items: 3
		},
		1200: {
			items: 4
		},
		1400: {
			items: 5
		}
	}
})

// More Events Slider
$('.moreEvents-slider').owlCarousel({
	items: 7,
	loop: true,
	margin: 20,
	nav: true,
	dots: false,
	navText: ["<i class='uil uil-angle-left'></i>", "<i class='uil uil-angle-right'></i>"],
	responsive: {
		0: {
			items: 1
		},
		600: {
			items: 2
		},
		800: {
			items: 2
		},
		1000: {
			items: 3
		},
		1200: {
			items: 4
		},
		1400: {
			items: 4
		}
	}
})

// More Events Slider
$('.moreEvents-slider').owlCarousel({
	items: 7,
	loop: true,
	margin: 20,
	nav: true,
	dots: false,
	navText: ["<i class='uil uil-angle-left'></i>", "<i class='uil uil-angle-right'></i>"],
	responsive: {
		0: {
			items: 1
		},
		600: {
			items: 2
		},
		800: {
			items: 2
		},
		1000: {
			items: 3
		},
		1200: {
			items: 4
		},
		1400: {
			items: 4
		}
	}
})

// Most Posts Slider
$('.most-posts-slider').owlCarousel({
	items: 1,
	loop: true,
	margin: 20,
	nav: false,
	dots: true,
	smartSpeed: 800,
	autoplay: true,
	autoplayTimeout: 3000,
	autoplayHoverPause: true,
	responsive: {
		0: {
			items: 1
		},
		600: {
			items: 1
		},
		800: {
			items: 1
		},
		1000: {
			items: 1
		},
		1200: {
			items: 1
		},
		1400: {
			items: 1
		}
	}
})

// Related Posts Slider
$('.related-posts-slider').owlCarousel({
	items: 4,
	loop: true,
	margin: 20,
	nav: true,
	dots: false,
	navText: ["<i class='uil uil-angle-left'></i>", "<i class='uil uil-angle-right'></i>"],
	responsive: {
		0: {
			items: 1
		},
		600: {
			items: 2
		},
		800: {
			items: 2
		},
		1000: {
			items: 3
		},
		1200: {
			items: 3
		},
		1400: {
			items: 4
		}
	}
})

// Role Slider
$('.role-slider').owlCarousel({
	items: 4,
	loop: false,
	margin: 20,
	nav: true,
	dots: false,
	navText: ["<i class='uil uil-angle-left'></i>", "<i class='uil uil-angle-right'></i>"],
	responsive: {
		0: {
			items: 1
		},
		600: {
			items: 2
		},
		800: {
			items: 2
		},
		1000: {
			items: 3
		},
		1200: {
			items: 3
		},
		1400: {
			items: 4
		}
	}
})

/*--- Count Time JS ---*/
function makeTimer() {
	var endTime = new Date("december  30, 2022 17:00:00 PDT");
	var endTime = (Date.parse(endTime)) / 1000;
	var now = new Date();
	var now = (Date.parse(now) / 1000);
	var timeLeft = endTime - now;
	var days = Math.floor(timeLeft / 86400);
	var hours = Math.floor((timeLeft - (days * 86400)) / 3600);
	var minutes = Math.floor((timeLeft - (days * 86400) - (hours * 3600)) / 60);
	var seconds = Math.floor((timeLeft - (days * 86400) - (hours * 3600) - (minutes * 60)));
	if (hours < "10") { hours = "0" + hours; }
	if (minutes < "10") { minutes = "0" + minutes; }
	if (seconds < "10") { seconds = "0" + seconds; }
	$("#days").html(days + "<span>Days</span>");
	$("#hours").html(hours + "<span>Hours</span>");
	$("#minutes").html(minutes + "<span>Minutes</span>");
	$("#seconds").html(seconds + "<span>Seconds</span>");
}
setInterval(function () { makeTimer(); }, 300);

/*--- Multi Dropdown JS ---*/

$(document).ready(function () {
	$('.dropdown-submenu a.submenu-item').on("click", function (e) {
		$(this).next('ul').toggle();
		e.stopPropagation();
		e.preventDefault();
	});
});

/*--- Multi Dropdown JS ---*/
$(document).ready(function () {
	$('input[type="radio"]').click(function () {
		var inputValue = $(this).attr("value");
		var targetBox = $("." + inputValue);
		$(".event-box").not(targetBox).hide();
		$(targetBox).show();
	});
});

function showError(message = '', toastType = 'danger') {
	/* if (toastType == 'danger') {
		toastType = 'error';
	}
	Swal.fire({
		icon: toastType,
		title: "",
		html: message,
	});
	return true; */

	if (toastType == 'error') {
		toastType = 'danger';
	}
	if (!message)
		return;
	var success_toast = document.getElementById("success_toast");
	var error_toast = document.getElementById("error_toast");
	if (toastType == 'danger') {
		$('#error_toast strong').html(message);
		var toast = new bootstrap.Toast(error_toast)
		toast.show()
	}
	else {
		$('#success_toast strong').html(message);
		var toast = new bootstrap.Toast(success_toast)
		toast.show()
	}
}
// for animation
document.addEventListener('DOMContentLoaded', () => {
	const elements = document.querySelectorAll('.animate-on-scroll');

	const observer = new IntersectionObserver(
		entries => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					entry.target.classList.add(entry.target.dataset.animate); // like "fade-in", etc.
					entry.target.classList.remove('animate-on-scroll');
				}
			});
		},
		{ threshold: 0.1 }
	);

	elements.forEach(el => observer.observe(el));
});


function populateOrderDetails(targetButton) {
	let volunteer = $(targetButton).data('volunteer');
	let event = $(targetButton).data('event');
	let ticket = $(targetButton).data('ticket');
	let order = $(targetButton).data('order');
	let creator = $(targetButton).data('creator');
	let registration = $(targetButton).data('registration');
	console.log(event);
	console.log(registration);
	console.log(order);
	var ticketUserName = `${volunteer.first_name} ${volunteer.last_name} (${volunteer.email_address})`;

	if (volunteer) {
		$('#registrationDetails .registration-full-name').text(`${volunteer.first_name} ${volunteer.last_name} `);
		$('#registrationDetails .registration-email-address').text(volunteer.email_address);
		$('#registrationDetails .registration-phone-number').text(volunteer.phone);
	}
	if (order) {
		$('#registrationDetails .registration-order-id').text(order.order_number ? `#${order.order_number}` : '');
		$('#registrationDetails .registration-order-total').text(order.grand_total || '');
		$('#registrationDetails .registration-ticket-quantity').text(order.total_tickets || '');
	}
	if (creator) {
		//$('#registrationDetails .registration-buyer-name').text(`${creator.first_name} ${creator.last_name} (${creator.email_address})`);
		$('#registrationDetails .registration-buyer-name').text(`${creator.first_name} ${creator.last_name}`);
	}
	if (event) {
		$('#registrationDetails .registration-event-name').text(event.event_name);
		if(event.ticket_questions && event.ticket_questions.replace(/<([^>]+)>/ig, '')!=''){
			$('.ticket_question_div').show();
			$('#registrationDetails .ticket_questions').text(event.ticket_questions ? event.ticket_questions.replace(/<([^>]+)>/ig, '') : '');
		} else {
			$('.ticket_question_div').hide();
		}
		event.waiver_form_type != null ? $('#registrationDetails .waiver-div').show() : $('#registrationDetails .waiver-div').hide();
	}
	if (ticket) {
		$('#registrationDetails .registration-ticket-type').text(ticket.ticket_type ? ticket.ticket_type.toUpperCase() : '');
		$('#registrationDetails .registration-ticket-name').text(ticket.ticket_name || '');
		$('#registrationDetails .ticket_answer').text(registration.ticket_answer || '');
	}
	if (registration) {
		$('#registrationDetails .registration-ticket-qty').text(registration.ticket_number ? '#' + registration.ticket_number : '');

		/* <newCodeBlock> */
		const money = (value) => Number(value || 0).toFixed(2);
		const hasProcessingFee =
			order &&
			order.processing_fee_per !== null &&
			Number(order.processing_fee_per) > 0;
		$('#registrationDetails .registration-ticket-subtotal').text(
			money(registration.price)
		);
		$('#registrationDetails .registration-ticket-base-price').text(
			money(registration.ticket_base_price)
		);
		$('#registrationDetails .registration-ticket-platform-fee').text(
			money(registration.ticket_platform_fee)
		);
		$('#registrationDetails .registration-ticket-organizer-payout').text(
			money(registration.tikcet_org_payout)
		);
		if (hasProcessingFee) {
			$('#registrationDetails .registration-ticket-processing-fee').text(
				money(registration.ticket_processing_fee)
			);

			$('#registrationDetails .registration-processing-fee-row').show();
		} else {
			$('#registrationDetails .registration-ticket-processing-fee').text('');
			$('#registrationDetails .registration-processing-fee-row').hide();
		}
		/* </newCodeBlock> */

		//$('#registrationDetails .waiver_filled_at').text(registration.waiver_filled_at ? registration.waiver_filled_at_formated : 'Pending');
		//$('#registrationDetails .waiver_handled_by').text(registration.waiver_handler_email != '' ? registration.waiver_handler_name + ' (' + registration.waiver_handler_email + ')' : '');
		$('#registrationDetails .waiver_filled_at').text(registration.waiver_filled_at ? registration.waiver_filled_at_formated : '-');
		$('.registration-is-under-18').text('No');
		if (registration.is_under_18 == 1) {
			ticketUserName = registration.waiver_handler_name + ' (' + registration.waiver_handler_email + ')';
			$('.registration-is-under-18').text('Yes');
		}
		$('#registrationDetails .waiver_handled_by').text(registration.waiver_filled_at ? ticketUserName : 'Not signed yet');
	}


	const children = registration && Array.isArray(registration.children)
    ? registration.children
    : [];

	const $childrenSection = $('#registrationDetails .registration-children-section');
	const $childrenList = $('#registrationDetails .registration-children-list');

	$childrenList.empty();

	if (children.length > 0) {
		children.forEach(function (child) {
			$('<li>')
				.text(`${child.child_name} — Age ${child.child_age}`)
				.appendTo($childrenList);
		});

		$childrenSection.removeClass('d-none');
	} else {
		$childrenSection.addClass('d-none');
	}

}
function populateNotesData(targetButton) {
	let ticket = $(targetButton).data('ticket');
	$('#registrationNotes .ticket-order-id').val(ticket);
	let data = $(targetButton).data('notes');
	console.log(data);
	let notesDivs = '';
	if (data) {
		data.forEach(note => {
			const addedBy = note.notes ? `<div class="note-added-by">${note.notes}</div>` : '';
			const createdAt = note.created_at ? `<div class="note-date text-primary">${note.created_at}</div>` : '';
			notesDivs += `<div class="registration-note mb-2 p-2 border-bottom rounded">
				${addedBy}
				${createdAt}
			</div>`;
		});
	}

	$('#registrationNotes .registration-notes-div').html(notesDivs);
}
function populateData(modelId, data) {
	$(`#${modelId}`).find('img').attr('src', '').addClass('d-none');
	$(`#${modelId}`).find('input,textarea,select').not('[name="_token"]').val('');
	for (index in data) {
		let item = data[index];
		if (!item)
			continue;
		if (index == 'uploaded_logo') {
			$(`#${modelId}`).find(`#${index}`).attr('src', item).removeClass('d-none');
		} else {
			$(`#${modelId}`).find(`#${index}`).val(item);
		}
	}
}

$(document).ready(function () {

	if ($('.only_date_picker').length) {
		$('.only_date_picker').datetimepicker({
			format: 'Y-m-d',
			timepicker: false,
			scrollMonth: false,
			scrollInput: false,
		});
	}
	if ($('.only_datetime_picker').length) {
		$('.only_datetime_picker').datetimepicker({
			format: 'Y-m-d H:i',
			step: 30,
			scrollMonth: false,
			scrollInput: false,
		});
	}
	//if ($('#start_datetime').length && $('#site_publish_date').length && $('#ticket_sell_start_time').length) {
	if ($('#start_datetime').length && $('#site_publish_date').length) {
		$('#start_datetime').datetimepicker({
			format: 'Y-m-d, H:i',
			step: 30,
			minDate: 0,
			onShow: function (ct) {
				this.setOptions({
					//maxDate:jQuery('#end_datetime').val()?jQuery('#end_datetime').val():false
				})
			},
			onChangeDateTime: function (ct, $input) {
				/* if ($('#end_datetime').val() != '') {
					var startDate = new Date(ct);
					var endDate = new Date($('#end_datetime').val());
					var diffTime = Math.abs(endDate - startDate);
					var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
					if (diffDays >= 7) {
						$('#longerThanOneWeekText').show();
					} else {
						$('#longerThanOneWeekText').hide();
					}
				} */
				/* var startDateTimeValue = $input.val();
				if (startDateTimeValue != '') {
					$('#end_datetime').val(startDateTimeValue).trigger('change');
					$('#longerThanOneWeekText').hide();
				} */
				var startDateTimeValue = $input.val();
				if (startDateTimeValue != '') {
						// clone ct so we don't mutate the picker's internal date
						var endDateTime = new Date(ct.getTime());
						endDateTime.setHours(endDateTime.getHours() + 2);

						// format back to 'Y-m-d, H:i'
						var year = endDateTime.getFullYear();
						var month = (endDateTime.getMonth() + 1).toString().padStart(2, '0');
						var day = endDateTime.getDate().toString().padStart(2, '0');
						var hours = endDateTime.getHours().toString().padStart(2, '0');
						var minutes = endDateTime.getMinutes().toString().padStart(2, '0');

						var formattedEndDateTime = year + '-' + month + '-' + day + ', ' + hours + ':' + minutes;

						$('#end_datetime').val(formattedEndDateTime).trigger('change');
						$('#longerThanOneWeekText').hide();

						// site_depublish_date = end_datetime's date + 1 day (date only)
						var depublishDate = new Date(endDateTime.getTime());
						depublishDate.setDate(depublishDate.getDate() + 1);
						var depublishYear = depublishDate.getFullYear();
						var depublishMonth = (depublishDate.getMonth() + 1).toString().padStart(2, '0');
						var depublishDay = depublishDate.getDate().toString().padStart(2, '0');
						var formattedDepublishDate = depublishYear + '-' + depublishMonth + '-' + depublishDay;
						$('#site_depublish_date').val(formattedDepublishDate).trigger('change');
				}
			}
			/* onChangeDateTime: function (ct) {
				if (ct) {
					var dateEnd2 = new Date(ct);
					dateEnd2.setDate(dateEnd2.getDate() - 1);
					var month = (dateEnd2.getMonth() + 1).toString().padStart(2, '0');
					var day = dateEnd2.getDate().toString().padStart(2, '0');
					var year = dateEnd2.getFullYear();
					var formattedDate = year + '-' + month + '-' + day;
					$('#ticket_sell_end_time').val(formattedDate);

					var dateEnd = new Date(ct);
					dateEnd.setDate(dateEnd.getDate() - 2);
					var month = (dateEnd.getMonth() + 1).toString().padStart(2, '0');
					var day = dateEnd.getDate().toString().padStart(2, '0');
					var year = dateEnd.getFullYear();
					var formattedDate = year + '-' + month + '-' + day;
					$('#ticket_sell_start_time').val(formattedDate);
				}
			} */
		});
		$('#end_datetime').datetimepicker({
			format: 'Y-m-d, H:i',
			step: 30,
			onShow: function (ct) {
				this.setOptions({
					minDate: jQuery('#start_datetime').val() ? jQuery('#start_datetime').val() : 0
				})
			},
			onChangeDateTime: function (ct) {
				var dateEnd = new Date(ct);
				dateEnd.setDate(dateEnd.getDate() + 1);
				var month = (dateEnd.getMonth() + 1).toString().padStart(2, '0');
				var day = dateEnd.getDate().toString().padStart(2, '0');
				var year = dateEnd.getFullYear();
				var formattedDate = year + '-' + month + '-' + day;
				$('#site_depublish_date').val(formattedDate);

				// get #start_datetime value and if it's not empty then compare #end_datetime with #start_datetime and check that if date have more than one week difference, if yes then show #longerThanOneWeekText div and if no then hide it 
				/* if ($('#start_datetime').val() != '') {
					var startDate = new Date($('#start_datetime').val());
					var endDate = new Date(ct);
					var diffTime = Math.abs(endDate - startDate);
					var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
					if (diffDays >= 7) {
						$('#longerThanOneWeekText').show();
					} else {
						$('#longerThanOneWeekText').hide();
					}
				} */
				if ($('#start_datetime').val() != '') {
					var startDate = new Date($('#start_datetime').val());
					var endDate = new Date(ct);
					var diffTime = Math.abs(endDate - startDate);
					var diffHours = diffTime / (1000 * 60 * 60);
					if (diffHours > 8) {
						$('#longerThanOneWeekText').show();
					} else {
						$('#longerThanOneWeekText').hide();
					}

					// show error if start/end are the same OR start is after/equal to end
					if (startDate.getTime() == endDate.getTime() || startDate.getTime() >= endDate.getTime()) {
						$('#error_start_end_time_same').show();
					} else {
						$('#error_start_end_time_same').hide();
					}
				}
			}
		});
		$('#site_publish_date').datetimepicker({
			scrollMonth: false,
			scrollInput: false,
			format: 'Y-m-d',
			minDate: 0,
			timepicker: false,
			onShow: function (ct) {
				this.setOptions({
					maxDate: jQuery('#start_datetime').val() ? jQuery('#start_datetime').val() : false
				})
			},
			onChangeDateTime: function (ct) {
				var dateEnd = new Date(ct);
				var month = (dateEnd.getMonth() + 1).toString().padStart(2, '0');
				var day = dateEnd.getDate().toString().padStart(2, '0');
				var year = dateEnd.getFullYear();
				var formattedDate = year + '-' + month + '-' + day;
				$('.tk_sell_start_date').val(formattedDate);
			}
		});
		$('#site_depublish_date').datetimepicker({
			scrollMonth: false,
			scrollInput: false,
			format: 'Y-m-d',
			timepicker: false,
			onShow: function (ct) {
				this.setOptions({
					minDate: jQuery('#end_datetime').val() ? jQuery('#end_datetime').val() : false
				})
			},
		});
		callSellStartEndDatePicker();
		/* $('#ticket_sell_start_time').datetimepicker({
			scrollMonth: false,
			scrollInput: false,
			format: 'Y-m-d',
			timepicker: false,
			onShow: function (ct) {
				var maxDateForSellDate = false;
				if (jQuery('#start_datetime').val() != '') {
					var dateEnd = new Date(jQuery('#start_datetime').val());
					dateEnd.setDate(dateEnd.getDate() - 1);
					maxDateForSellDate = dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1) + '-' + dateEnd.getDate();
				}
				this.setOptions({
					//maxDate:jQuery('#start_datetime').val()?jQuery('#start_datetime').val():false
					maxDate: maxDateForSellDate ? maxDateForSellDate : false
				})
			},
		});
		$('#ticket_sell_end_time').datetimepicker({
			scrollMonth: false,
			scrollInput: false,
			format: 'Y-m-d',
			timepicker: false,
			onShow: function (ct) {
				var maxDateForSellDate = false;
				if (jQuery('#start_datetime').val() != '') {
					var dateEnd = new Date(jQuery('#start_datetime').val());
					dateEnd.setDate(dateEnd.getDate() - 1);
					maxDateForSellDate = dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1) + '-' + dateEnd.getDate();
				}
				this.setOptions({
					maxDate:jQuery('#start_datetime').val()?jQuery('#start_datetime').val():false,
					//maxDate: maxDateForSellDate ? maxDateForSellDate : false,
					minDate: jQuery('#ticket_sell_start_time').val() ? jQuery('#ticket_sell_start_time').val() : false
				})
			},
		}); */
	}
	/**OPEN FORM */
	$('.detailed-modal-popup').on('shown.bs.modal', function (event) {
		let target_button = event.relatedTarget;
		let modelId = event.target.id;
		if (modelId == 'registrationNotes') {
			populateNotesData(target_button);
		} else if (modelId == 'registrationDetails') {
			populateOrderDetails(target_button);
		} else if (modelId == 'sendRsvpEmail') {
			$(`#${modelId}`).find('form').attr('action', $(target_button).data('route'))
		} else {
			let Jsondata = $(target_button).parents('tr').first().find('.tr-detail').html();
			console.log(Jsondata);
			let data = JSON.parse(Jsondata);
			populateData(modelId, data);
		}
	})
	/**AJAX SETUP*/
	$.ajaxSetup({
		beforeSend: function () {
			// $('.ajax-loader').show();
			//One.loader('show')
		},
		complete: function () {
			// $('.ajax-loader').hide();
			//One.loader('hide')
		},
		headers: {
			'Accept': 'application/json',
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		statusCode: {
			422: function (responseObject, textStatus, jqXHR) {
				let responseText = JSON.parse(responseObject.responseText);
				showError(responseText.message, 'error');
			},
			405: function (responseObject, textStatus, jqXHR) {
				let responseText = JSON.parse(responseObject.responseText);
				showError(responseText.message, 'error');
			},
			500: function (responseObject, textStatus, jqXHR) {
				console.log(responseObject.responseText);
				let responseText = JSON.parse(responseObject.responseText);
				showError(responseText.message, 'error');
			}
		}
	});
	/**DELETE SETUP */
	$(document).on('click', '.delete-record', function () {
		//$('.delete-record').click(function(){
		let context = $(this);
		Swal.fire({
			title: "Are you sure?",
			text: `You want to delete this record?`,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				$.ajax({
					url: $(context).data('url'),
					method: 'DELETE',
					success: function (res) {
						if (res.status) {
							if ($(context).parents('.single-tr').length) {
								$(context).parents('.single-tr').first().remove();
							} else if ($(context).parents('.main-card').length) {
								$(context).parents('.main-card').first().remove();
							} else {
								$(context).parents('tr, .contact-list').first().remove();
							}
							showError(res.message, 'success')
						} else {
							showError(res.message, 'error')
						}
						return;
					},
				});
			}
		});
	})
	/**CREATE TICKET WIZARD */
	var ticketWizardStepper = $('#add-event-tab').steps({
		onFinish: function () {
			// alert('Wizard Completed');
		},
		onInit: function () {
		},
		onChange: async function (currentIndex, newIndex, stepDirection) {
			if (currentIndex == 5) {
				$('#show_event_end_date').text($('#end_datetime').val());
				$('.close-post-cleanup-btn').show();
				$('.go-to-next').text('Save');
				if ($('.go-to-next').attr('is_event_end') == 1) {
					$('.go-to-next').removeClass('custom-disable-btn');
					$('.go-to-next').show();
				} else {
					$('.go-to-next').addClass('custom-disable-btn');
					$('.go-to-next').hide();
				}
				if($('.go-to-next').attr('is_event_unpublish')==1){
					$('.go-to-next').hide();
				} else {
					$('.go-to-next').show();
				}
				if($('.go-to-next').attr('is_event_completed')==1){
					$('.go-to-next').hide();
				}
				$('.submit-send-wrapup-btn').show();
			} else {
				$('.close-post-cleanup-btn').hide();
				$('.go-to-next').removeClass('custom-disable-btn');
				$('.go-to-next').show();
				$('.go-to-next').text('Save & Next');
				$('.submit-send-wrapup-btn').hide();
			}
			if (currentIndex == 2) {
				var pType = $('[name="participation_type"]:checked').val();
				if (pType == 1) {
					$('#ticket_setting_overlay').hide();
				} else {
					$('#ticket_setting_overlay').show();
				}
			}
		},
	});
	$('[data-direction="prev"]').click(function () {
		$("html, body").animate({ scrollTop: 0 }, "slow");
	});
	/**FINAL EVENT WIZARD*/
	$('.go-to-next').click(async function () {
		const context = document.querySelector('.go-to-next');
		let steps_api = $('#add-event-tab').data('plugin_Steps');
		let currentIndex = steps_api.getStepIndex();
		console.log(currentIndex);
		if (context.classList.contains('processing')) {
			return false;
		}

		const currentForm = document.querySelector(`#tab_step${currentIndex} form`);
		if (!currentForm) {
			$("html, body").animate({ scrollTop: 0 }, "slow");
			$('.stepper-next-button').trigger('click');
			return true;
			showError('Form not found', 'error');
			return false;
		}
		const $form = $(currentForm);
		$form.validate({
			errorClass: 'invalid-feedback',
			errorPlacement: function (error, element) {
				console.log(element);
				if (
					element.hasClass("select2-multi-select") ||
					element.hasClass("select2-single")
				) {
					error.insertAfter(element.next('.select2-container'));
				} else if (element.hasClass("selectpicker")) {
					error.insertAfter(element.parent())
				} else if (element.hasClass("select-multiple")) {
					console.log(element);
					error.insertAfter(element.next('.ms-options-wrap'));
				} else if (element.parent(".input-group").length) {
					error.insertAfter(element.parent());
				} else if (
					element.prop("type") === "radio" &&
					element.parent(".radio-inline").length
				) {
					error.insertAfter(element.parent().parent());
				} else if (
					element.prop("type") === "checkbox" ||
					element.prop("type") === "radio"
				) {
					error.appendTo(element.parent().parent());
				} else if (element.hasClass('ckeditor')) {
					console.log(element, element.parent());
					error.insertAfter(element.parent());
				} else {
					error.insertAfter(element);
				}
			},
			highlight: function (element) {
				$(element).parent().addClass("is-invalid");
				$(element).addClass("form-control-danger");
			},
			success: function (label, element) {
				$(element).parent().removeClass("is-invalid");
				$(element).removeClass("form-control-danger");
				label.remove();
			},
		});

		if (!$form.valid()) {
			showError('Please fill all required fields', 'error');
			return false;
		}

		const formData = new FormData(currentForm);

		/* $('.ckeditor').each(function () {
			const editor = $(this).data('ckeditorInstance');
			if (editor) {
				const content = editor.getData();
				const fieldName = $(this).attr('name');
				// formData = formData.filter(item => item.name !== fieldName); // Remove old value if exists
				// formData.push({ name: fieldName, value: content });
				formData.append(fieldName, content);
			} else {
				console.warn('CKEditor instance not found for:', this.name);
			}
		}); */
		$(currentForm).find('.ckeditor').each(function () {
			const editor = $(this).data('ckeditorInstance');
			if (editor) {
				formData.set(this.name, editor.getData());
			}
		});

		try {
			// Mark as processing
			context.classList.add('processing');
			context.disabled = true;
			context.innerHTML = `
				<svg aria-hidden="true" role="status" class="inline mr-2 w-4 h-4 text-gray-200 animate-spin dark:text-gray-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"></path>
					<path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="#1C64F2"></path>
				</svg> Saving...
			`;

			const response = await fetch(currentForm.action, {
				method: 'POST',
				body: formData,
				headers: { 'Accept': 'application/json' },
			});

			if (!(response.status === 200)) {
				if (response.status === 422 || response.status === 405) {
					const errorData = await response.json();
					throw new Error(errorData.message || 'An error occurred');
				}
				throw new Error(`HTTP error! Status: ${response.status}`);
			}

			const { status, data, message } = await response.json();
			const { event_instance, redirect_href, section_html } = data || {};
			var redirect_href_ = redirect_href;

			if (status) {
				$("html, body").animate({ scrollTop: 0 }, "slow");

				$('.event_instance').val(event_instance || '')

				if (redirect_href_) {
					if (redirect_href_.includes('events.volunteercleanup.org')) {
						const urlObj = new URL(redirect_href_);
						const pathAndQuery = urlObj.pathname + urlObj.search + urlObj.hash;
						redirect_href_ = frontbase + pathAndQuery;
					}
					window.location.href = redirect_href_;
				}

				if (section_html) {
					for (const [key, value] of Object.entries(section_html)) {
						if (value) document.querySelector(`.${key}`).innerHTML = value;
					}
				}
				//history.pushState({}, '', `/post-an-event/${event_instance || ''}`);
				history.pushState({}, '', `${frontbase}/post-an-event/${event_instance || ''}`);
				document.querySelectorAll('input[type="file"]').forEach(input => input.value = '');
				console.log('Second'); // Replace with alert('Second') if needed
				console.log(currentForm.action);
				/* if(currentForm.action.includes('seo-details')){
					showError(message, 'success');
				} */
				/**TRIGGER NEXT BUTTON OF STEPPER */
				$('.stepper-next-button').trigger('click');
			} else {
				console.log('Second'); // Replace with alert('Second') if needed
				showError(message || 'Submission failed', 'error');
				return false;
			}
		} catch (error) {
			console.log('Second'); // Replace with alert('Second') if needed
			// alert("HEY FACING SOME PROBLEM IN CODE.");
			showError(`${error.message}`, 'error');
			return false; // This will now work as expected
		} finally {
			// Clean up without returning
			context.classList.remove('processing');
			context.disabled = false;
			if(currentIndex==4){
				context.innerHTML = 'Save';
			} else {
				context.innerHTML = 'Save & Next';
			}
			console.log('Third'); // Replace with alert('Third') if needed

			return false;
		}
	});

	if ($('#add-event-tab').length) {
		var open_tab = $('#add-event-tab').attr('open_tab');
		if (open_tab == 'step0') {
			$('a[href="#tab_step0"]').trigger('click');
		} else if (open_tab == 'step1') {
			$('a[href="#tab_step1"]').trigger('click');
		} else if (open_tab == 'step2') {
			$('a[href="#tab_step2"]').trigger('click');
		} else if (open_tab == 'step3') {
			$('a[href="#tab_step3"]').trigger('click');
		} else if (open_tab == 'step4') {
			$('a[href="#tab_step4"]').trigger('click');
		} else if (open_tab == 'step5') {
			$('a[href="#tab_step5"]').trigger('click');
		}
	}

	$('.send-thankyou-request-email-btn').on('click', function () {
		var control = this;
		Swal.fire({
			title: "Are you sure?",
			text: 'You want to send the wrap-up & certificate emails to all attendees. Event will be marked as completed after sending the email.',
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, send it!'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				let event_id = $(control).attr('event_id');
				let ajaxurl = $(control).attr('ajaxurl');
				$.ajax({
					url: ajaxurl,
					method: 'POST',
					data: {
						'event_id': event_id,
						'_token': $('meta[name="csrf-token"]').attr('content')
					},
					success: function (response) {
						if (response.status) {
							showError(response.message, 'success');
							$(control).after(response.data);
							$(control).hide();
						}
						else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});
	});
	$('.submit-send-wrapup-btn').on('click', function () {
		if($('#waste_collected').val()==''){
			showError('Please enter the waste collected value', 'error');
			$('#waste_collected').focus();
			return false;
		}

		if($('#post_event_notes').val()==''){
			showError('Please enter the post event notes', 'error');
			$('#post_event_notes').focus();
			return false;
		}
		var control = this;
		var url = $(this).attr('url');
		$(control).attr('disabled', 'disabled');
		$(control).text('Finalizing...');
		$.ajax({
			url: url,
			method: 'POST',
			data: {},
			success: function (response) {
				if (response.status) {
					Swal.fire({
						title: "Before You Submit",
						//text: response.data,
						html: response.data,
						icon: "warning",
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Submit Anyway'
					}).then((willDelete) => {
						if (willDelete.isConfirmed) {
							$('#send_cleanup_report_email').val(1);
							$('.go-to-next').trigger('click');
						} else {
							$(control).removeAttr('disabled');
							$(control).text('Save & Finalize Cleanup Report');
						}
					});
				}
				else {
					$(control).removeAttr('disabled');
					$(control).text('Save & Finalize Cleanup Report');
					showError(response.message, 'error');
				}
			}
		});
	});
	if($('#after_event_images').length){
		const MAX_IMAGES = 4;
		let selectedFiles = [];

		// Preview new images on select
		$('#after_event_images').on('change', function () {
			const self = this;
			const newFiles = Array.from(this.files);
			const savedCount = $('#savedImages .position-relative').length;
			const validCount = selectedFiles.filter(f => f !== null).length;
			const totalAllowed = MAX_IMAGES - savedCount - validCount;

			if (totalAllowed <= 0) {
				showError('You can only upload a maximum of 4 images.', 'error');
				$(self).val(''); // safe to clear because rebuildFileInput() will restore valid files
				rebuildFileInput(); // restore previously selected valid files back to input
				return;
			}

			let added = 0;
			let limitReached = false;

			newFiles.forEach(function (file) {
				if (added >= totalAllowed) {
					if (!limitReached) {
						showError('You can only upload a maximum of 4 images.', 'error');
						limitReached = true;
					}
					return;
				}

				const reader = new FileReader();
				const index = selectedFiles.length;
				//var index = Math.floor(10000 + Math.random() * 90000);
				selectedFiles.push(file);
				added++;

				reader.onload = function (e) {
					const wrapper = `
						<div class="position-relative me-3 mt-2" id="new-img-${index}">
							<img src="${e.target.result}" style="width:50px;height:50px;border-radius:6px;object-fit:cover;">
							<button type="button" class="remove-new-img" data-index="${index}">✕</button>
						</div>
					`;

					/* if ($('#savedImages').length === 0) {
						$('#after_event_images').closest('.form-group').append(
							'<div id="savedImages" class="d-flex flex-wrap align-items-center mt-2"></div>'
						);
					}
					$('#savedImages').append(wrapper); */
					if ($('#newImagePreview').length === 0) {
						$('#after_event_images').closest('.form-group').append(
							'<div id="newImagePreview" class="d-flex flex-wrap align-items-center mt-2"></div>'
						);
					}
					$('#newImagePreview').append(wrapper);
				};

				reader.readAsDataURL(file);
			});

			rebuildFileInput();
		});

		// Remove newly selected (unsaved) image
		$(document).on('click', '.remove-new-img', function () {
			const index = parseInt($(this).data('index'));
			selectedFiles[index] = null;
			$('#new-img-' + index).remove();
			rebuildFileInput();
		});

		// Rebuild file input after removal using DataTransfer
		function rebuildFileInput() {
			const validFiles = selectedFiles.filter(f => f !== null);
			const dataTransfer = new DataTransfer();
			validFiles.forEach(function (file) {
				dataTransfer.items.add(file);
			});
			$('#after_event_images')[0].files = dataTransfer.files;
		}
	}

	/**ADD TICKET */
	$('.add-ticket').on('click', function (event) {
		let target = $(event.target);
		let route = $(target).data('action');
		let clonedElement = $('.ticket-form-replica .ticket-form').clone();
		let nextIndex = $('.ticket-container .ticket-form').last().length ? $('.ticket-container .ticket-form').last().find('.current_counter').val() || -1 : -1;
		nextIndex = parseInt(nextIndex) + 1;
		$(clonedElement).find('.current_counter').val(nextIndex);
		$(clonedElement).find('.form-ele').each(function () {
			let name = $(this).attr('name');
			name = name.replace('counter_placeholder', nextIndex);
			$(this).attr('name', name);
		});
		$(clonedElement).find('[data-bs-target="#collapse_counter_placeholder"]').attr('aria-controls', `collapse_${nextIndex}`);
		$(clonedElement).find('[data-bs-target="#collapse_counter_placeholder"]').attr('data-bs-target', `#collapse_${nextIndex}`);
		$(clonedElement).find('#collapse_counter_placeholder').attr('id', `collapse_${nextIndex}`);
		$(clonedElement).find('#collapse_title_counter_placeholder').attr('id', `collapse_title_${nextIndex}`);
		$(clonedElement).find(`#collapse_title_${nextIndex}`).text(nextIndex + 1);
		$(clonedElement).appendTo('.ticket-container');
		fieldmasking();
		$('.only_date_picker').datetimepicker({
			format: 'Y-m-d',
			timepicker: false,
		});
		callSellStartEndDatePicker();
	})
	$(document).on('change', '#p_ticket_type_select', function () {
		let selectedValue = $(this).val();
		if (selectedValue === 'free') {
			$('#p_ticket_price').val(0).trigger('change');
			$('#p_ticket_price').prop('readonly', true);
		} else {
			$('#p_ticket_price').prop('readonly', false);
		}
		updateBreakdownTextInfo();
	});
	$(document).on('change', '.ticket_type_select', function () {
		let name = $(this).attr('name');
		let counter = name.split('[')[1].split(']')[0];
		let selectedValue = $(this).val();
		let ticketPriceInput = $('[name="ticket[' + counter + '][ticket_price]"]');
		if (selectedValue === 'free') {
			ticketPriceInput.val(0);
			ticketPriceInput.prop('readonly', true);
		} else {
			ticketPriceInput.prop('readonly', false);
		}
	});

	$(document).on('click', '.delete-ticket', function (event) {
		$(this).parents('.ticket-form').first().remove();
		// Swal.fire({
		//       title: "Are you sure?",
		//       text: `You want to delete this record?`,
		//       icon: "warning",
		//       showCancelButton: true,
		//       confirmButtonColor: '#3085d6',
		//       cancelButtonColor: '#d33',
		//       confirmButtonText: 'Yes, delete it!'
		// }).then((willDelete) => {
		//       if (willDelete.isConfirmed) {
		//       }
		// });
	});

	/**CHECK & AUTO POPULATE ATTANDEE INFORMATION */
	$('.checkout-email').change(function () {
		let control = $(this);
		let email = $(this).val();
		let parent_section = $(this).parents('.parent-container').first();
		let is_ticket = $(parent_section).data('ticket') == 1 ? 1 : 0;
		let ticket_id = $(parent_section).data('ticketid') || 0;
		$.ajax({
			url: frontbase + '/auto-populate-attandee',
			data: {
				'email_address': email,
				'is_ticket': is_ticket,
				'ticket_id': ticket_id,
			},
			statusCode: {
				408: function (responseObject, textStatus, jqXHR) {
					let responseText = JSON.parse(responseObject.responseText);
					showError(responseText.message, 'error');
					$(parent_section).find('input').not('.ticket_id').val('').removeAttr('readonly').removeAttr('placeholder');
				},
			},
			method: "POST",
			success: function (result) {
				if (!control.hasClass('billing-email')) {
					if($('#copy_buyer_info_to_attendee').length > 0 && $('#copy_buyer_info_to_attendee').is(':checked') && control.attr('name')=='ticket[1][email_address]' && $('[name="checkout_email_address"]').val()==control.val()){
						return false;
					}
					$(parent_section).find('input')
					.not('.email_address, .ticket_id, .is_under_18, .has-children-checkbox')
					.val('')
					.removeAttr('readonly')
					.attr('required', true);
					$('[name="phone"]').attr('required', false);
					if (control.hasClass('attendee-email')) {
						$(parent_section).find('.phone').attr('placeholder', '');
					}
					if (result.status) {
						if (result.data) {
							if (control.hasClass('billing-email') && $('#checkout-password').length) {
								$('#checkout-password').hide();
								$('#checkout-password').find('[name="checkout_password"]').removeAttr('required');
							}
							let user = result.data.user;
							for (let key in user) {
								if (user.hasOwnProperty(key)) {
									if (key == 'city_state_zip' && user[key] != null) {
										var cityOption = '<option value="' + user[key].id + '">' + user[key].zipcode + ' (' + user[key].city + ', ' + user[key].state + ')</option>';
										$('select.city_state_zip').html(cityOption);
										select2ajaxFunc();
									}
									else if ((key == 'first_name' && (user[key] == '' || user[key] == null)) || (key == 'last_name' && (user[key] == '' || user[key] == null)) || (key == 'pincode' && (user[key] == '' || user[key] == null)) || (key == 'phone' && (user[key] == '' || user[key] == null))) {
										if (key == 'phone') {
											$(parent_section).find(`.${key}`).attr('required', true).removeAttr('placeholder').removeAttr('readonly').val('');
										}
									} else {
										$(parent_section).find(`.${key}`)
										.not('.email_address, .ticket_id, .is_under_18, .has-children-checkbox')
										.val(user[key])
										.attr('readonly', true)
										.removeAttr('required');
										if (control.hasClass('attendee-email')) {
											$(parent_section).find('.first_name').attr('required', false).attr('readonly', true);
											$(parent_section).find('.last_name').attr('required', false).attr('readonly', true);
											if (key == 'last_name') {
												$(parent_section).find(`.${key}`).val(maskFieldValue(user[key], 1));
											}
											if (key == 'phone') {
												$(parent_section).find('.phone').attr('required', false).attr('readonly', true);
												$(parent_section).find(`.${key}`).attr('placeholder', maskFieldValue(user[key], 2)).val('');
											}
										}
									}
								}
							}
						} else if (control.hasClass('billing-email') && $('#checkout-password').length) {
							$('#checkout-password').show();
							$('#checkout-password').find('[name="checkout_password"]').attr('required', true);
						}
					}
					$(parent_section).find('.organisation_name').removeAttr('required');
					$(parent_section).find('.answer').attr('required', 'required');
				}
			},
		});
	})
	/**Open organisation if registration is for organiser */
	$('.organization_association').click(function () {
		let is_organiser = $(this).is(':checked');
		if (is_organiser)
			$('.organization_association_name').removeClass('d-none');
		else
			$('.organization_association_name').addClass('d-none');
	});

	$('.organization_association_deselect').click(function () {
		let is_organiser = $(this).is(':checked');
		if (is_organiser)
			$('.organization_association_name').addClass('d-none');
		else
			$('.organization_association_name').removeClass('d-none');
	});

	$('.fa-eye-slash').click(function () {

		let passwordField = $(this).parents('div').first().find('input');
		console.log(passwordField);
		const type = $(passwordField).attr('type') === 'password' ? 'text' : 'password';
		passwordField.attr('type', type);
	});

	$('.waiver_check_age').change(function () {

		let isChecked = $(this).is(':checked');
		let parentDiv = $(this).parents('.waiver-form-div').first();

		if (isChecked) {
			$(parentDiv).find('.waiver-guardian').removeClass('d-none');
			$(parentDiv).find('.waiver-guardian input').attr('required', true);
		} else {
			$(parentDiv).find('.waiver-guardian').addClass('d-none');
			$(parentDiv).find('.waiver-guardian input').removeAttr('required');
		}
	});

	$('.ckeditor').each(function () {
		ClassicEditor
			.create(this, {
				toolbar: [
					'heading',
					'bold', 'italic', 'underline',
					'bulletedList', 'numberedList',
					'link',
					'undo', 'redo'
				],
				link: {
					defaultProtocol: 'https://',          // <— key line
					addTargetToExternalLinks: true,       // optional
					decorators: {                         // optional: ensure rel for safety
						addNoReferrer: {
							mode: 'automatic',
							callback: url => /^https?:\/\//i.test(url),
							attributes: { rel: 'noopener noreferrer' }
						}
					}
				}
			})
			.then(editor => {
				console.log('CKEditor initialized for:', this.name);
				// Optional: Save each editor instance if needed
				$(this).data('ckeditorInstance', editor);
			})
			.catch(error => {
				console.error('Error initializing CKEditor:', error);
			});
	});
	$('.ckeditor-disabled').each(function () {
		ClassicEditor
			.create(this, {
				toolbar: [
					'heading',
					'bold', 'italic', 'underline',
					'bulletedList', 'numberedList',
					'link',
					'undo', 'redo'
				]
			})
			.then(editor => {
				editor.isReadOnly = true;
				console.log('CKEditor initialized for:', this.name);
				// Optional: Save each editor instance if needed
				$(this).data('ckeditorInstance', editor);
			})
			.catch(error => {
				console.error('Error initializing CKEditor:', error);
			});
	});

	$('.get-slug').on('change', function () {
		let context = $(this);
		let slug_target = $(this).data('slugtarget');
		let existingid = $(this).data('existingid') || 0;
		let get_slug_url = $(this).data('url');
		if ($(slug_target).val() && existingid != 0) {
			return;
		}
		$.ajax({
			url: get_slug_url,
			method: 'POST',
			data: {
				'existing_id': existingid,
				'slug_value': $(this).val(),
				'current_slug': $(slug_target).val(),
				'_token': $('meta[name="csrf-token"]').attr('content')
			},
			success: function (response) {
				if (response.status) {
					let slug = response.data.slug;
					$(`${slug_target}`).val(slug);

				}
				else {
					showError(response.message, 'error');
				}
			}
		});

	});

	$('#validate_event_slug').click(function () {
		var url = $(this).attr('ajaxurl');
		var event_id = $('.first_event_instance').val();
		var event_slug = $('.event_slug').val();
		if (!event_slug) {
			showError('Event slug is required to validate.', 'error');
			return;
		}
		var data = {
			'event_id': event_id,
			'slug': event_slug
		};
		$.ajax({
			url: url,
			method: 'POST',
			data: data,
			success: function (response) {
				if (response.status) {
					showError(response.message, 'success');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});

	$(document).on('change', '#waiver_form_type', function (event) {
		if ($(this).val() == 'text') {
			document.querySelectorAll('.waiver_form_content_editor').forEach(editor => {
				let ed = ClassicEditor
					.create(editor, {
						toolbar: [
							'heading',
							'bold', 'italic', 'underline',
							'bulletedList', 'numberedList',
							'link',
							'undo', 'redo'
						]
					})
					.then(function (editor) {
						editor.ui.view.editable.element.style.height = '50px';
					})
					.catch(function (error) {
						console.error(error);
					});
			});
		} else if ($(this).val() == 'default') {
			document.querySelectorAll('.waiver_form_content_editor').forEach(editor => {
				let ed = ClassicEditor
					.create(editor, {
						toolbar: [
							'heading',
							'bold', 'italic', 'underline',
							'bulletedList', 'numberedList',
							'link',
							'undo', 'redo'
						]
					})
					.then(function (editor) {
						editor.isReadOnly = true;
						editor.ui.view.editable.element.style.height = '50px';
					})
					.catch(function (error) {
						console.error(error);
					});
			});
		}
	});

	/**Image  */
	/* $('.change-image-preview').on('change', function () {
		const file = this.files[0];
		let context = $(this);
		if (file) {
			const reader = new FileReader();

			reader.onload = function (e) {
				$(context).parents('.image-container').first().find('.image-preview')
					.attr('src', e.target.result)
					.show();
			}

			reader.readAsDataURL(file);
		} else {
			$(context).parents('.image-container').first().find('.image-preview')
				.attr('src', '#')
				.show();
		}
	}); */

	if ($('.select2ajax').length) {
		select2ajaxFunc();
	}
	if ($('[data-bs-target="#org-profile-update-pop"]').length) {
		$('[data-bs-target="#org-profile-update-pop"]').click(function () {
			select2ajaxFunc('#org-profile-update-pop');
		});
	}
	if ($('[data-bs-target="#manageAlerts"]').length) {
		$('[data-bs-target="#manageAlerts"]').click(function () {
			select2ajaxFunc('#manageAlerts');
		});
	}
	$('#proceed_checkout_btn').click(function (el) {
		var qtys = 0;
		$('.qty').each(function (c, e) {
			qtys += parseFloat($(e).val());
		});
		if (qtys == 0) {
			el.preventDefault();
			showError('Please select at least 1 ticket to proceed.', 'error');
		}
	});
	$("#checkoutForm").on("submit", function (e) {
		if ($('#checkoutForm').attr('ticketchecked') != 1) {
			e.preventDefault();
			if ($(this).valid()) {
				var formData = $("#checkoutForm").serialize();
				$.ajax({
					url: frontbase + '/check-before-create-order',
					data: formData,
					method: "POST",
					success: function (result) {
						if (result.status) {
							$('#checkoutForm').attr('ticketchecked', 1);
							$("#checkoutForm").submit();
						} else {
							showError(result.message, 'error');
						}
					},
				});
			}
		}
	});
	$('#select_sort_by_date').change(function () {
		$('#sort_by_date').val($(this).val());
		$('#eventListForm').submit();
	});
	$('#weekly_alerts').change(function () {
		if ($(this).prop('checked')) {
			$('.register_pincode_div').show();
			$('[name="register_pincode"]').attr('required', 'required');
		} else {
			$('.register_pincode_div').hide();
			$('[name="register_pincode"]').removeAttr('required');
		}
	});
	$('#vol_avatar').change(function (event) {
		console.log('asdf');
		let input = event.target;
		if (input.files && input.files[0]) {
			let reader = new FileReader();

			reader.onload = function (e) {
				$("#vol_avatar_preview").attr("src", e.target.result).show();
			};

			reader.readAsDataURL(input.files[0]);
		}
	})
	$('#organisation_logo').change(function (event) {
		let input = event.target;
		if (input.files && input.files[0]) {
			let reader = new FileReader();

			reader.onload = function (e) {
				$("#organisation_logo_preview").attr("src", e.target.result).show();
			};

			reader.readAsDataURL(input.files[0]);
		}
	})
	if ($('.event-row-upcoming')) {
		$('[hosting-event-val]').on('click', function () {
			let tab = $(this).attr('hosting-event-val');

			let url = new URL(window.location.href);
			url.searchParams.set('tabs', tab);

			window.history.replaceState({}, '', url);
    		$('#tabsInput').val(tab);
		});

		/* var val = $('[hosting-event-val].active').attr('hosting-event-val');
		showHostingEvent(val); */
	}
	$('.select-all-ticket-checkbox').change(function () {
		if ($(this).prop('checked')) {
			$('.attendee-ticket-checkbox').prop('checked', 'checked');
		} else {
			$('.attendee-ticket-checkbox').prop('checked', false);
		}
		calcTicketPriceForPopup();
	});
	$('.attendee-ticket-checkbox').change(function () {
		calcTicketPriceForPopup();
	});
	function calcTicketPriceForPopup() {
		var amount = 0;
		$('.attendee-ticket-checkbox').each(function (c, e) {
			if ($(e).prop('checked')) {
				console.log(c, e);
				var price = $(e).attr('price');
				amount += parseFloat(price);
			}
		});
		$('.ticket-cancel-amt').text(amount);
	}
	$('#cancel-all-ticket-btn').click(function () {
		$('#cancel-all-ticket-popup').modal('show');
	});
	$('.cancel-refund-ticket-organizer').click(function () {
		var ticket_amt = $(this).attr('price');
		var ticket_id = $(this).attr('ticket_id');
		$('#single-cancel-ticket-id').val(ticket_id);
		$('[name="refund_type"]').prop('checked', false);
		$('#option-2').prop('checked', true);
		$('.ticket-cancel-amt').text(ticket_amt);
		$('#cancel-ticket-popup').modal('show');
	});
	$('.cancel-refund-ticket').click(function () {
		var ticket_type = $(this).attr('ticket_type');
		var allow_refund = $(this).attr('allow_refund');
		var ticket_amt = $(this).attr('price');
		var ticket_id = $(this).attr('ticket_id');
		var is_48hours_exeed = $(this).attr('is_48hours_exeed');
		$('#single-cancel-ticket-id').val(ticket_id);
		$('#option-1').prop('checked', true);
		if (allow_refund == '0' || is_48hours_exeed == '1') {
			$('#option-1-div').hide();
			$('#option-2-div').hide();
			$('#option-3-div').show();
			$('[name="refund_type"]').prop('checked', false);
			$('#option-3').prop('checked', true);
		} else {
			$('#option-1-div').show();
			$('#option-2-div').show();
			$('#option-3-div').hide();
			$('[name="refund_type"]').prop('checked', false);
			$('#option-1').prop('checked', true);
		}
		/* if(allow_refund=='0'){
			$('#option-2-div').hide();
			$('#option-1-div').addClass('offset-sm-3');
		} else {
			$('#option-2-div').show();
			$('#option-1-div').removeClass('offset-sm-3');
		} */
		$('.ticket-cancel-amt').text(ticket_amt);
		$('#single-cancel-ticket-id').val();
		$('#cancel-ticket-popup').modal('show');
	});
	$('.cancel-refund-ticket-attendee').click(function () {
		var url = $(this).attr('url');
		var title = $(this).attr('alert-title');
		var control = this;
		Swal.fire({
			title: "Cancel registration?",
			text: title,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, cancel it!'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				$.ajax({
					url: url,
					method: 'POST',
					data: {},
					success: function (response) {
						if (response.status) {
							showError(response.message, 'success');
							setTimeout(() => {
								window.location.reload();
							}, 350);
						}
						else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});
	});
	$('.cancel-rsvp-action').click(function () {
		var url = $(this).attr('url');
		var control = this;
		Swal.fire({
			title: "Are you sure?",
			text: `You want to cancel your registration?`,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, cancel it!'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				$.ajax({
					url: url,
					method: 'POST',
					data: {},
					success: function (response) {
						if (response.status) {
							showError(response.message, 'success');
							setTimeout(() => {
								window.location.reload();
							}, 350);
						}
						else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});

		/* var url = $(this).attr('url');
		$.ajax({
			url: url,
			method: 'GET',
			data: {},
			success: function (response) {
				if (response.status) {
					showError(response.message, 'success');
					setTimeout(() => {
						window.location.reload();
					}, 350);
				}
				else {
					showError(response.message, 'error');
				}
			}
		}); */
	});
	$('.ticket_checkin_toggle').change(function () {
		var url = $(this).attr('url');
		var control = this;
		if ($(control).prop('checked')) {
			var aStatus = true;
			//var aMessage = 'You want to mark this attendee as attended?';
			var aMessage = 'You want to mark this attendee as checked in? This will confirm their attendance for the event';
			$(control).prop('checked', false);
		} else {
			var aStatus = false;
			var aMessage = 'You want to mark this attendee as not attended?';
			$(control).prop('checked', true);
		}
		Swal.fire({
			title: "Are you sure?",
			text: aMessage,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, mark it!'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				$(control).attr('disabled', 'disabled');
				if (aStatus) {
					$(control).prop('checked', true);
				} else {
					$(control).prop('checked', false);
				}
				$.ajax({
					url: url,
					method: 'POST',
					data: {},
					success: function (response) {
						$(control).removeAttr('disabled');
						if (response.status) {
							showError(response.message, 'success');
						}
						else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});

		/* // checking toggle 
		var url = $(this).attr('url');
		var control = this;
		$(control).attr('disabled', 'disabled');
		$.ajax({
			url: url,
			method: 'POST',
			data: {},
			success: function (response) {
				$(control).removeAttr('disabled');
				if (response.status) {
					showError(response.message, 'success');
				}
				else {
					showError(response.message, 'error');
				}
			}
		}); */
	});

	$('.verify_ticket_passcode_btn').click(function () {
		var ticketid = $(this).attr('ticketid');
		var passcode = $('[name="passcode[' + ticketid + ']"]').val();
		if (passcode == '') {
			$('[name="passcode[' + ticketid + ']"]').focus();
			showError('Please enter the pass code to verify.', 'error');
			return false;
		}
		var url = $(this).attr('url');
		var control = this;
		$.ajax({
			url: url,
			method: 'POST',
			data: { passcode: passcode },
			success: function (response) {
				if (response.status) {
					showError(response.message, 'success');
					$('#passcode_div_' + ticketid).hide();
					$('#ticket_div_' + ticketid).show();
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
	$('.send-community-form-request').click(function () {
		var url = $(this).attr('url');
		var control = this;
		if($(this).attr('is_event_completed') == '1'){
			$('#registration_id').val($(this).attr('registration_id'));
			$('#request_reason').val('');
			$('#after_event_images').val('');
			$('#newImagePreview').html('');
			$('#request-certificate-popup').modal('show');
			return true;
		} else {
			Swal.fire({
				title: "Are you sure?",
				text: `You want to request a Community Service Certificate ?`,
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, request it!'
			}).then((willDelete) => {
				if (willDelete.isConfirmed) {
					$(control).attr('disabled', 'disabled');
					$.ajax({
						url: url,
						method: 'POST',
						data: {},
						success: function (response) {
							if (response.status) {
								showError(response.message, 'success');
								$(control).after('<h6 class="coupon-status"><span class="badge custom-badge-warning">Requested</span></h6>');
								$(control).remove();
							} else {
								showError(response.message, 'error');
							}
						}
					});
				}
			});
		}
	});
	$('.event_publish_toggle').change(function () {
		var eventId = $(this).data('event-id');
		var isChecked = $(this).is(':checked');
		$.ajax({
			url: frontbase + '/update-event-publish-status',
			method: 'POST',
			data: {
				event_id: eventId,
				status: isChecked ? 'published' : 'draft'
			},
			success: function (response) {
				if (response.status) {
					showError(response.message, 'success');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
	$(document).on('click', '.save_publish_event', function () {
		var url2 = $(this).attr('url2');
		var eventId = $('.first_event_instance').val();
		var url = $(this).attr('url');
		$('.save_publish_event').attr('disabled', 'disabled');
		$('.save_publish_event').text('Publishing...');
		$.ajax({
			url: url,
			method: 'POST',
			data: {
				event_id: eventId,
				status: 'published'
			},
			success: function (response) {
				if (response.status) {
					Swal.fire({
						icon: "success",
						title: "Event Published",
						text: "Your event is published. Go ahead and promote this link through your marketing and social media channels.",
						footer: '<a href="' + response.data + '">' + response.data + '</a>',
						confirmButtonText: "Copy URL & Continue",
						allowOutsideClick: false,
						allowEscapeKey: false,
					}).then((okClicked) => {
						if (okClicked.isConfirmed) {
							navigator.clipboard.writeText(response.data);
							window.location.href = url2;
						}
					});
				}
				else {
					$('.save_publish_event').removeAttr('disabled');
					$('.save_publish_event').text('Publish Event');
					showError(response.message, 'error');
				}
			}
		});
	});
	$('#open-add-co-host-popup').click(function () {
		$('#add-co-host-popup').modal('show');
	});
	$('.open-update-co-host-popup').click(function () {
		var extranotes = $(this).attr('extranotes');
		var permissions = $(this).attr('permissions');
		$('#updateCoHostPopupLabel').text('Update ' + $(this).attr('vname') + ' Permissions');
		$('#u_co_host_extra_notes').val(extranotes);
		$('#u_co_host_url').val($(this).attr('url'));
		$('input[name="u_permissions[]"]').prop('checked', false);
		permissions.split(',').forEach(function (permission) {
			$('input[name="u_permissions[]"][value="' + permission + '"]').prop('checked', true);
		});
		$('#update-co-host-popup').modal('show');
	});
	$('#add_co_host_btn').click(function () {
		var url = $(this).attr('url');
		var email = $('#co_host_email').val();
		var name = $('#co_host_name').val();
		var extra_notes = $('#co_host_extra_notes').val();
		var event_id = $('.first_event_instance').val();
		if (event_id == '') {
			showError('Event not found to add co-host.', 'error');
			return;
		} else if (email == '') {
			showError('Please enter co-host email address.', 'error');
			return;
		} else if (name == '') {
			showError('Please enter co-host name.', 'error');
			return;
		} else if ($('input[name="permissions[]"]:checked').length === 0) {
			showError('Please select at least one permission.', 'error');
			return false;
		}
		let selectedPermissions = $('input[name="permissions[]"]:checked')
			.map(function () {
				return $(this).val();
			}).get();
		$(this).attr('disabled', 'disabled');
		$(this).text('Sending Invitation...');
		var control = this;
		$.ajax({
			url: url,
			method: 'POST',
			data: {
				event_id: event_id,
				email: email,
				name: name,
				extra_notes: extra_notes,
				permission: selectedPermissions,
			},
			success: function (response) {
				if (response.status) {
					showError(response.message, 'success');
					setTimeout(() => {
						//window.location.reload();
						window.location.href = window.location.pathname + '?open_tab=step3';

					}, 350);
				}
				else {
					$(control).text('Send Invitation');
					$(control).removeAttr('disabled');
					showError(response.message, 'error');
				}
			}
		});
	});
	$('#update_co_host_btn').click(function () {
		var url = $('#u_co_host_url').val();
		var extra_notes = $('#u_co_host_extra_notes').val();
		if (url == '') {
			showError('Update co-host URL not found.', 'error');
			return false;
		}
		if ($('input[name="u_permissions[]"]:checked').length === 0) {
			showError('Please select at least one permission.', 'error');
			return false;
		}
		let selectedPermissions = $('input[name="u_permissions[]"]:checked')
			.map(function () {
				return $(this).val();
			}).get();
		$(this).attr('disabled', 'disabled');
		$.ajax({
			url: url,
			method: 'POST',
			data: {
				extra_notes: extra_notes,
				permission: selectedPermissions,
			},
			success: function (response) {
				if (response.status) {
					showError(response.message, 'success');
					setTimeout(() => {
						window.location.reload();
					}, 350);
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
	$('.reinvite-cohost').click(function () {
		var url = $(this).attr('url');
		var control = this;
		$(control).attr('disabled', 'disabled');
		$.ajax({
			url: url,
			method: 'POST',
			data: {},
			success: function (response) {
				$(control).removeAttr('disabled');
				if (response.status) {
					showError(response.message, 'success');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
	$('.remove-cohost').click(function () {
		var url = $(this).attr('url');
		var control = this;
		Swal.fire({
			title: "Are you sure?",
			text: `You want to remove this co-host from event?`,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, remove it!'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				$(control).attr('disabled', 'disabled');
				$.ajax({
					url: url,
					method: 'POST',
					data: {},
					success: function (response) {
						if (response.status) {
							showError(response.message, 'success');
							setTimeout(() => {
								//window.location.reload();
								window.location.href = window.location.pathname + '?open_tab=step3';
							}, 350);
						}
						else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});
	});
	$('[name="donation_type"]').on('change', function () {
		var val = $(this).val();
		if (val == 'custom') {
			$('[name="donation_amount"]').val('');
			$('#donation-price').show();
			$('.donation-total-div').hide();
			$('#donation-total').text('');
		} else {
			$('[name="donation_amount"]').val(val);
			$('#donation-price').hide();
			$('.donation-total-div').show();
			$('#donation-total').text(parseFloat(val).toFixed(2));
		}
		calGrandTotal();
	});
	$('[name="donation_amount"]').on('keyup', function () {
		var val = $(this).val();
		if (val != '') {
			$('.donation-total-div').show();
			$('#donation-total').text(parseFloat(val).toFixed(2));
		} else {
			$('.donation-total-div').hide();
			$('#donation-total').text('');
		}
		calGrandTotal();
	});
	$('#stripe-connect-btn').click(function () {
		var url = $(this).attr('url');
		var email = $('#stripe_email').val();
		if (email == '') {
			showError('Enter your stripe account email to connect.', 'error');
			$('#stripe_email').focus();
			return false;
		}
		$(this).text('Connecting..');
		$(this).attr('disabled', 'disabled');
		$.ajax({
			url: url,
			method: 'POST',
			data: { email: email },
			success: function (response) {
				if (response.status) {
					if (response.data.url) {
						window.location.href = response.data.url;
					} else {
						$('#stripe-connect-btn').text('Connect');
						$('#stripe-connect-btn').removeAttr('disabled');
						showError('Something went wrong. Please try again later.', 'error');
					}
				}
				else {
					$('#stripe-connect-btn').text('Connect');
					$('#stripe-connect-btn').removeAttr('disabled');
					showError(response.message, 'error');
				}
			}
		});
	})
	$('#createWidgetBtn').click(function () {
		if ($('#createWidgetForm').is(':visible')) {
			$('#createWidgetForm').hide();
		} else {
			$('#createWidgetForm').show();
		}
	});
	$('.showWidgetDetailBtn').click(function () {
		$('#showWidgetDetailLabel').text('Embedded Widget Details');
		$('#copyWidgetCode').show();
		var url = $(this).attr('url');
		$.ajax({
			url: url,
			method: 'POST',
			data: {},
			success: function (response) {
				if (response.status) {
					$('#showWidgetDetailBody').html(response.data);
					$('#showWidgetDetail').modal('show');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
	$('.showWidgetPreviewBtn').click(function () {
		$('#showWidgetDetailLabel').text('Embedded Widget Preview');
		$('#copyWidgetCode').hide();
		var url = $(this).attr('url');
		$.ajax({
			url: url,
			method: 'POST',
			data: {},
			success: function (response) {
				if (response.status) {
					$('#showWidgetDetailBody').html(response.data);
					$('#showWidgetDetail').modal('show');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
	$(document).on('click', '.widget-event-click', function (e) {
		var eventurl = $(this).attr('url');
		var ajaxurl = $(this).attr('ajaxurl');
		$.ajax({
			url: ajaxurl,
			method: 'POST',
			data: {
				'_token': $('meta[name="csrf-token"]').attr('content')
			},
			success: function (response) {
			}
		});
		window.open(eventurl, '_blank');
	});
	$(document).on('click', '#copyWidgetCode', function () {
		var widgetCode = $('#widgetCode').text();
		navigator.clipboard.writeText(widgetCode).then(function () {
			showError('Widget code copied.', 'success');
		});
	});
	$(document).on('keyup', '.ticket_base_price_f', function () {
		calcPriceBreakdown();
	});
	$(document).on('change', '.ticket_base_price_f', function () {
		calcPriceBreakdown();
	});
	$(document).on('change', '[name="charge_bear_by"]', function () {
		calcPriceBreakdown();
	})
	if ($('[name="charge_bear_by"]').length) {
		calcPriceBreakdown();
	}
	$(document).on('keyup', '#p_ticket_price', function () {
		pCalcPriceBreakdown();
	});
	$(document).on('change', '#p_ticket_price', function () {
		pCalcPriceBreakdown();
	});
	$(document).on('change', '.event_ticket_status_toggle', function () {
		var url = $(this).attr('data-url');
		$.ajax({
			url: url,
			method: 'POST',
			data: {},
			success: function (response) {
				if (response.status) {
					showError(response.message, 'success');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
	$(document).on('click', '.menu--item__has_sub_menu .menu--link', function () {
		if ($(this).parent().hasClass('menu--subitens__opened')) {
			$(this).parent().removeClass('menu--subitens__opened');
		} else {
			$(this).parent().addClass('menu--subitens__opened');
		}
	});
	$('#copy_buyer_info_to_attendee').change(function () {
		if ($(this).prop('checked')) {
			var fname = $('[name="checkout_first_name"]').val();
			var lname = $('[name="checkout_last_name"]').val();
			var email = $('[name="checkout_email_address"]').val();
			var phone = $('[name="phone"]').val();
			$('[name="ticket[1][email_address]"]').val(email);
			$('[name="ticket[1][first_name]"]').val(fname);
			$('[name="ticket[1][last_name]"]').val(lname);
			$('[name="ticket[1][phone]"]').val(phone);
			$('[name="ticket[1][email_address]"]').trigger('change');
		} else {
			var email = $('[name="checkout_email_address"]').val();
			var ticketEmail = $('[name="ticket[1][email_address]"]').val();
			if (ticketEmail == email) {
				$('[name="ticket[1][email_address]"]').val('');
				$('[name="ticket[1][first_name]"]').val('');
				$('[name="ticket[1][last_name]"]').val('');
				$('[name="ticket[1][phone]"]').val('');
			}
		}
	});
	$('.create_alert_checkbox').change(function () {
		if ($(this).prop('checked')) {
			$(this).parent('.option-card').addClass('active');
		} else {
			$(this).parent('.option-card').removeClass('active');
		}
		var checkWeeklyAlert = $('[name="weekly_alert"]').prop('checked');
		var checkNewsletterAlert = $('[name="newsletter_alert"]').prop('checked');
		if (checkWeeklyAlert) {
			$('#pincode').attr('required', 'required');
			$('.email-div').addClass('col-lg-6').removeClass('col-lg-12');
			$('.zipcode-div').show();
		} else {
			$('#pincode').removeAttr('required');
			$('.email-div').removeClass('col-lg-6').addClass('col-lg-12');
			$('.zipcode-div').hide();
		}
		if (!checkWeeklyAlert && !checkNewsletterAlert) {
			$('.email-div').addClass('col-lg-6').removeClass('col-lg-12');
			$('.zipcode-div').show();
			$('[name="weekly_alert"]').prop('checked', 'checked').trigger('change');
		}
	});
	$('[name="participation_type"]').on('change', function () {
		if ($(this).val() == 4) {
			$('#participation_type_external_link').show();
		} else {
			$('#participation_type_external_link').hide();
		}
	});
	$('#reschedule_now_btn').click(function () {
		const form = document.getElementById('rescheduleEventForm');
		if (!form.checkValidity()) {
			// This will show native browser validation messages
			form.reportValidity();
			return;
		}
		Swal.fire({
			title: "Are you sure?",
			text: `You want to reschedule this event?`,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, reschedule it!'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
                $('#reschedule_now_btn').attr('disabled', 'disabled');
				$('#rescheduleEventForm').submit();
			}
		});
	});
	$(document).on('click','.remove-event-img-btn',function(){
		var control = this;
		var url = $(control).attr('url');
		$.ajax({
			url: url,
			method: 'POST',
			data: {},
			success: function (response) {
				if (response.status) {
					if(response.data){
						$('#savedImages').html(response.data);
					} else {
						$('#savedImages').html('');
					}
					showError(response.message, 'success');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
	$(document).on('click', '.show-alert-send-waiver-to-all', function () {
		var control = this;
		var message = $(control).attr('message');
		var url = $(control).attr('url');
		var urlRegistrationList = $(control).attr('url_registration_list');
		var contentHtml = '<div class="alert-innter-ul p-4 text-center">'+
                '<b class="text-center mb-2">This will send waiver emails to all volunteers whose waiver status is Unsigned or Not Sent.</b>'+
                '<small class="text-center mb-2 mt-3 d-block">If you\'d prefer to review individual waiver statuses, resend waivers to specific volunteers, or verify signatures first, click</small>'+
				'<a href="' + urlRegistrationList + '">Review Attendees</a>'+
            '</div>';
		
		Swal.fire({
			title: "Send to All",
			//text: message,
			html: contentHtml,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Send Waivers'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				$(control).attr('disabled', 'disabled');
				$.ajax({
					url: url,
					method: 'POST',
					data: {},
					success: function (response) {
						$(control).removeAttr('disabled');
						if (response.status) {
							showError(response.message, 'success');
						}
						else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});
	});
	$(document).on('click', '.show-alert-run-url', function () {
		var control = this;
		var message = $(control).attr('message');
		var url = $(control).attr('url');
		Swal.fire({
			title: "Are you sure?",
			text: message,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				$(control).attr('disabled', 'disabled');
				$.ajax({
					url: url,
					method: 'POST',
					data: {},
					success: function (response) {
						$(control).removeAttr('disabled');
						if (response.status) {
							showError(response.message, 'success');
						}
						else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});
	});
	$(document).on('click', '.btn-resend-waiver', function () {
		var control = this;
		var url = $(control).attr('url');
		Swal.fire({
			title: "Resend waiver email?",
			text: "The attendee will receive a new email to complete the waiver.",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Resend Email'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				$(control).attr('disabled', 'disabled');
				$.ajax({
					url: url,
					method: 'POST',
					data: {},
					success: function (response) {
						$(control).removeAttr('disabled');
						if (response.status) {
							showError(response.message, 'success');
						}
						else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});
	});
	$(document).on('click', '.btn-resend-waiver-modal', function () {
		var control = this;
		var url = $(control).attr('url');
		var checkurl = $(control).attr('checkurl');
		var defaultemail = $(control).attr('defaultemail');
		$(control).attr('disabled', 'disabled');
		$(control).text('Opening..');
		$.ajax({
			url: checkurl,
			method: 'POST',
			data: {},
			success: function (response) {
				$(control).removeAttr('disabled');
				$(control).text('Resend');
				if (response.status) {
					var log = response.data.log;
					var lrwlType = '';
					var lrwlEmail = '';
					var lrwlDatetime = '';
					if(log!=''){
						lrwlType = log.link_send_type==1?'Original':'Alternative' || '';
						lrwlEmail = log.link_send_to_email || '';
						lrwlDatetime = log.createdDateTime || '';
					}
					if(defaultemail.trim()==''){
						defaultemail = 'NO EMAIL';
					}
					$('#resendWaiverEmailModal').find('form').attr('action', url);
					$('#resend_waiver_default_email').text(defaultemail);
					$('#alternative_waiver_email').val('');

					if (lrwlType.trim() != '' || lrwlEmail.trim() != '' || lrwlDatetime.trim() != '') {
						$('#resend_waiver_last_log_empty').hide();
						$('#resend_waiver_last_log_content').show();
						$('#resend_waiver_last_log_type').text(lrwlType);
						$('#resend_waiver_last_log_email').text(lrwlEmail);
						$('#resend_waiver_last_log_datetime').text(lrwlDatetime);
					} else {
						$('#resend_waiver_last_log_content').hide();
						$('#resend_waiver_last_log_empty').show();
						$('#resend_waiver_last_log_type').text('');
						$('#resend_waiver_last_log_email').text('');
						$('#resend_waiver_last_log_datetime').text('');
					}

					selectResendOption(1);
					$('#resendWaiverEmailModal').modal('show');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
	$(document).on('click', '.submit-btn-send-waiver-email', function (e) {
		var control = this;
		var checkedRadio = $('input[name="resend_waiver_type"]:checked').val();
		var alternativeEmail = $('#alternative_waiver_email').val();
		if(checkedRadio==2 && alternativeEmail==''){
			showError('Please enter alternative email address.', 'error');
			$('#alternative_waiver_email').focus();
			return false;
		}
		$(control).text('Sending...').attr('disabled', true);
		var formData = $('#resendWaiverEmailModal').find('form').serialize();
		var url = $('#resendWaiverEmailModal').find('form').attr('action');
		$.ajax({
			url: url,
			method: 'POST',
			data: formData,
			success: function (response) {
				$(control).text('Send Email').removeAttr('disabled');
				if (response.status) {
					showError(response.message, 'success');
					$('#alternative_waiver_email').val('');
					$('#resendWaiverEmailModal').modal('hide');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
	// ── APPROVE CERTIFICATE ───────────────────────────────────────────────────
	$(document).on('click', '.btn-approve-certificate', function () {
		var url  = $(this).attr('url');
		var name = $(this).attr('data-name');
		var control = this;

		Swal.fire({
			title: "Approve Certificate?",
			html: `You are approving the certificate request for <b>${name}</b>. This action cannot be undone.`,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, approve it!'
		}).then((result) => {
			if (result.isConfirmed) {
				$(control).attr('disabled', 'disabled');
				$.ajax({
					url: url,
					method: 'POST',
					data: {},
					success: function (response) {
						$(control).removeAttr('disabled');
						if (response.status) {
							showError(response.message, 'success');
							setTimeout(() => { window.location.reload(); }, 1000);
						} else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});
	});

	// ── REJECT CERTIFICATE ────────────────────────────────────────────────────
	$(document).on('click', '.btn-reject-certificate', function () {
		var url  = $(this).attr('url');
		var name = $(this).attr('data-name');
		var control = this;

		Swal.fire({
			title: "Reject Certificate Request?",
			html: `You are rejecting the certificate for <b>${name}</b>.`,
			input: "textarea",
			inputPlaceholder: "Enter reason for rejection...",
			inputAttributes: {
				rows: 4,
				style: "resize:none;"
			},
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Reject',
			cancelButtonText: 'Cancel',
			inputValidator: (value) => {
				if (!value || !value.trim()) {
					return 'Please enter a reason for rejection.';
				}
			}
		}).then((result) => {
			if (result.isConfirmed) {
				$(control).attr('disabled', 'disabled');
				$.ajax({
					url: url,
					method: 'POST',
					data: {
						reject_reason: result.value
					},
					success: function (response) {
						$(control).removeAttr('disabled');
						if (response.status) {
							showError(response.message, 'success');
							setTimeout(() => { window.location.reload(); }, 1000);
						} else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});
	});
	$(document).on('click', '.btn-cancel-event', function () {
		var cancelUrl = $(this).attr('cancel-url');
		var rescheduleUrl = $(this).attr('reschedule-url');
		Swal.fire({
			title: "What would you like to do?",
			html: 
            '<div style="display: inline-block;margin-left: auto;margin-right: auto;"><div class="text-left mb-2"><b>Cancel Event will:</b></div>'+
            '<ul class="text-left">'+
                '<li>• Notify all attendees</li>'+
                '<li>• Stop all check-ins and registrations</li>'+
                '<li>• Disable certificates</li>'+
                '<li>• Issue refunds for paid tickets</li>'+
            '</ul>'+
            '<div class="text-left mb-2 mt-3"><b>Reschedule Event will:</b></div>'+
            '<ul class="text-left">'+
                '<li>• Let you select a new date and time</li>'+
                '<li>• Keep existing registrations</li>'+
                '<li>• Send email to attendees </li>'+
            '</ul></div>',
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#34D399',
			confirmButtonText: 'Cancel Event',
			cancelButtonText: 'Reschedule Event',
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				window.location.href = cancelUrl;
			} else {
				window.open(rescheduleUrl, '_blank');
			}
		});
	});
	$(document).on('click', '.btn-convert-to-donation', function () {
		var url = $(this).attr('url');
		var control = this;
		Swal.fire({
			title: "Are you sure?",
			text: `You want to donate your ticket amount`,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, donate it!'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				$(control).attr('disabled', 'disabled');
				window.location.href = url;
			}
		});
	});
	/* $(document).on('click', '.btn-resend-waiver-logs-modal', function () {
		var logs = $(this).data('logs');

		if (typeof logs === 'string') {
			try {
				logs = JSON.parse(logs);
			} catch (e) {
				logs = [];
			}
		}

		var rows = '';

		if (logs && logs.length > 0) {
			logs.forEach(function (log) {
				rows += `
					<tr>
						<td><span class="resend-waiver-log-type">${escapeHtml(log.type || '')}</span></td>
						<td>${escapeHtml(log.email || '')}</td>
						<td>${escapeHtml(log.send_at || '')}</td>
					</tr>
				`;
			});

			$('#resendWaiverLogsTableBody').html(rows);
			$('.resend-waiver-log-empty').hide();
			$('.resend-waiver-log-table-wrap').show();
		} else {
			$('#resendWaiverLogsTableBody').html('');
			$('.resend-waiver-log-table-wrap').hide();
			$('.resend-waiver-log-empty').show();
		}
	}); */


	fieldmasking();
});
function escapeHtml(value) {
	return $('<div>').text(value || '').html();
}
function selectResendOption(n) {
	$('#resend_waiver_type1, #resend_waiver_type2').prop('checked', false);
	$('#resend_waiver_type' + n).prop('checked', true);

	$('#optionCard1, #optionCard2').removeClass('selected');
	$('#radioDot1, #radioDot2').removeClass('on');
	$('#optionCard' + n).addClass('selected');
	$('#radioDot' + n).addClass('on');

	if (n === 2) {
		$('#alternative_waiver_email').prop('disabled', false).focus();
		$('#resend_waiver_note').html(
			'<span class="resend-note-label">Override email selected — </span>' +
			'The waiver link will be sent to the address entered above instead of the registered one. ' +
			'This action will be logged. The volunteer\'s profile email will <u>not</u> be changed.'
		);
	} else {
		$('#alternative_waiver_email').prop('disabled', true).val('');
		$('#resend_waiver_note').html(
			'<span class="resend-note-label">Registered email selected — </span>' +
			'The waiver link will be resent to the volunteer\'s email on record. The volunteer\'s profile will not be modified.'
		);
	}
}

function calcPriceBreakdown() {
	/* var isOrgPayFee = $('input[name="charge_bear_by"]:checked').val();
	if ($('.ticket_base_price_f').length > 0) {
		$('.ticket_base_price_f').each(function (c, e) {
			var feePer = $(e).attr('fee_per');
			var val = $(e).val();
			var str = $(e).attr('name');
			var number = str.split('[')[1].split(']')[0];
			console.log(val, number, '[name="pb_base_price_' + number + '"]');
			if (val != '') {
				var fee = (parseFloat(val) * parseFloat(feePer)) / 100;
				var total_price = parseFloat(val) + parseFloat(fee);
				$('[name="pb_platform_fee_' + number + '"]').text(fee.toFixed(2));
				$('[name="pb_base_price_' + number + '"]').text(parseFloat(val).toFixed(2));
				if (isOrgPayFee == 1) {
					$('[name="pb_fee_text_' + number + '"]').text('Platform Fee');
					$('[name="pb_final_price_' + number + '"]').text(total_price.toFixed(2));
				} else {
					$('[name="pb_fee_text_' + number + '"]').text('Platform Fee Included');
					$('[name="pb_final_price_' + number + '"]').text(parseFloat(val).toFixed(2));
				}
			} else {
				$('[name="pb_platform_fee_' + number + '"]').text(0);
				$('[name="pb_base_price_' + number + '"]').text(0);
				$('[name="pb_final_price_' + number + '"]').text(0);
			}
		})
	} */
}
function pCalcPriceBreakdown() {
    const buyerPaysFees = Number($(document).find('#p_charge_bear_by').val()) === 1;
    const platformPercent = Number($(document).find('#p_fee_per').val()) || 0;
    const processingPercent = Number($(document).find('#p_processing_fee_per').val()) || 0;
    const basePrice = Number($(document).find('#p_ticket_price').val()) || 0;

    const platformFee = +(basePrice * platformPercent / 100).toFixed(2);
    const processingFee = +(basePrice * processingPercent / 100).toFixed(2);

    const attendeeTotal = buyerPaysFees
        ? basePrice + platformFee + processingFee
        : basePrice;

    const organizerPayout = buyerPaysFees
        ? basePrice
        : basePrice - platformFee - processingFee;

    $(document).find('#pb_base_price').text(basePrice.toFixed(2));
    $(document).find('#pb_platform_fee').text(platformFee.toFixed(2));
    $(document).find('#pb_processing_fee').text(processingFee.toFixed(2));
    $(document).find('#pb_final_price').text(attendeeTotal.toFixed(2));
    $(document).find('#pb_net_org_price').text(Math.max(0, organizerPayout).toFixed(2));

    $(document).find('#pb_fee_text').text(
        buyerPaysFees ? 'Platform Fee' : 'Platform Fee Included'
    );

    $(document).find('#pb_processing_fee_text').text(
        buyerPaysFees ? 'Processing Fee' : 'Processing Fee Included'
    );
}
/* function pCalcPriceBreakdown() {
	var isOrgPayFee = $(document).find('#p_charge_bear_by').val();
	var feePer = $(document).find('#p_fee_per').val();
	var val = $(document).find('#p_ticket_price').val();
	if (val != '') {
		var fee = (parseFloat(val) * parseFloat(feePer)) / 100;
		var est_fee = ((parseFloat(val) * 3) / 100) + 0.30;
		var total_price = parseFloat(val) + parseFloat(fee);
		$(document).find('#pb_platform_fee').text(fee.toFixed(2));
		$(document).find('#pb_est_fee').text(est_fee.toFixed(2));
		$(document).find('#pb_base_price').text(parseFloat(val).toFixed(2));
		if (isOrgPayFee == 1) {
			$(document).find('#pb_fee_text').text('Platform Fee');
			$(document).find('#pb_final_price').text(total_price.toFixed(2));
			$(document).find('#pb_net_org_price').text(parseFloat(val).toFixed(2));
		} else {
			$(document).find('#pb_fee_text').text('Platform Fee Included');
			$(document).find('#pb_final_price').text(parseFloat(val).toFixed(2));
			$(document).find('#pb_net_org_price').text((parseFloat(val) - parseFloat(fee)).toFixed(2));
		}
	} else {
		$(document).find('#pb_base_price').text(0);
		$(document).find('#pb_platform_fee').text(0);
		$(document).find('#pb_final_price').text(0);
		$(document).find('#pb_est_fee').text(0);
		$(document).find('#pb_net_org_price').text(0);
	}
} */

function disconnectStripeAccount(control) {
	var url = $(control).attr('url');
	Swal.fire({
		title: "Are you sure?",
		text: `You want to disconnect your Stripe account?`,
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, disconnect it!'
	}).then((willDelete) => {
		if (willDelete.isConfirmed) {
			$(control).text('Disconnecting...');
			$(control).attr('disabled', 'disabled');
			$.ajax({
				url: url,
				method: 'POST',
				data: {},
				success: function (response) {
					if (response.status) {
						showError(response.message, 'success');
						setTimeout(() => {
							window.location.reload();
						}, 350);
					}
					else {
						showError(response.message, 'error');
					}
				}
			});
		}
	});
}


//for alters    
let showAlert = show_alert || '';
showError(showAlert.message, showAlert.class);

/* function calGrandTotal() {
	var donation_amt = $('[name="donation_amount"]').length ? parseFloat(sanitizeDonation($('[name="donation_amount"]').val())) || 0 : 0;
	var subtotal = parseFloat($('#f-sub-total').text()) || 0;
	var grand_total = donation_amt + subtotal;
	$('#f-grand-total').text(grand_total.toFixed(2));
} */
function calGrandTotal() {
    const donationAmount = $('[name="donation_amount"]').length
        ? parseFloat(sanitizeDonation($('[name="donation_amount"]').val())) || 0
        : 0;

    const subtotal = parseFloat($('#f-sub-total').text()) || 0;

    const config = $('#checkout-fee-config');
    const processingPercent = parseFloat(config.data('processing-percent')) || 0;
    const buyerPaysFees = Number(config.data('buyer-pays')) === 1;

    const donationProcessingFee = +(donationAmount * processingPercent / 100).toFixed(2);

    const grandTotal = subtotal + donationAmount
        + (buyerPaysFees ? donationProcessingFee : 0);

    $('#donation-total').text(donationAmount.toFixed(2));
    $('#donation-processing-fee').text(donationProcessingFee.toFixed(2));

	if (donationAmount > 0) {
		$('.donation-total-div').show();
	} else {
		$('.donation-total-div').hide();
	}
	if (donationAmount > 0 && buyerPaysFees && donationProcessingFee > 0) {
		$('.donation-processing-row').show();
	} else {
		$('.donation-processing-row').hide();
	}

    $('#f-grand-total').text(grandTotal.toFixed(2));
}
function sanitizeDonation(value) {
	// Remove everything except digits and dots
	value = value.replace(/[^0-9.]/g, '');

	// Allow only one dot
	const parts = value.split('.');
	if (parts.length > 2) {
		value = parts[0] + '.' + parts.slice(1).join('');
	}

	// (Optional) limit to 2 decimal places
	const [intPart, decPart] = value.split('.');
	if (decPart !== undefined) {
		value = intPart + '.' + decPart.slice(0, 2);
	}

	return value;
}
function openTicketTypePopup(control) {
	var url = $(control).attr('url');
	var event_id = $('.first_event_instance').val();
	$.ajax({
		url: url,
		method: 'POST',
		data: {
			event_id: event_id
		},
		success: function (response) {
			if (response.status) {
				$('#manage-ticket-popup-body').html(response.data);
				$('#manage-ticket-popup').modal('show');
				fieldmasking();
				callSellStartEndDatePicker();
				pCalcPriceBreakdown();
				updateBreakdownTextInfo();
			}
			else {
				showError(response.message, 'error');
			}
		}
	});
}
function updateBreakdownTextInfo(){
	var ticketType = $('#p_ticket_type_select').val();
	if(ticketType=='free'){
		$('.breackdown_info_text').hide();
		$('.breackdown_info_text_free').show();
	} else {
		$('.breackdown_info_text').show();
		$('.breackdown_info_text_free').hide();
	}
}
function submitTicketTypeForm() {
	// Validate max_qty_allowed_per_order against available_quantity
	var availableVal = parseInt($(document).find('form#ticketTypeForm input[name="available_quantity"]').val(), 10);
	var maxQtyVal = parseInt($(document).find('form#ticketTypeForm input[name="max_qty_allowed_per_order"]').val(), 10);

	if (!isNaN(maxQtyVal) && !isNaN(availableVal) && maxQtyVal > availableVal) {
		showError('Max Tickets Per Order cannot exceed Total Tickets (' + availableVal + ').', 'error');
		return false;
	}
	
	//var url = $(document).find('form#ticketTypeForm').attr('action');
	var url = $('#save_ticket_type_url').val();
	var formData = $(document).find('form#ticketTypeForm').serialize();
	$('#save_ticket_type_btn').attr('disabled', true);
	$('#save_ticket_type_btn').text('Saving...');
	$.ajax({
		url: url,
		method: 'POST',
		data: formData,
		success: function (response) {
			if (response.status) {
				showError(response.message, 'success');
				setTimeout(() => {
					window.location.href = window.location.pathname + '?open_tab=step2';
				}, 500);
			}
			else {
				$('#save_ticket_type_btn').attr('disabled', false);
				$('#save_ticket_type_btn').text('Save');
				showError(response.message, 'error');
			}
		},
		error: function (xhr, status, error) {
			$('#save_ticket_type_btn').attr('disabled', false);
			$('#save_ticket_type_btn').text('Save');
		}
	});
}
function changeCreateAlertForm(type, control) {
	$(control).addClass('active').addClass('text-white');
	$(control).siblings().removeClass('active').removeClass('text-white');
	if (type == 'weeklyalerts') {
		$('#weekly_alert_box').show();
		$('#quarterly_newsletter_box').hide();
	} else {
		$('#weekly_alert_box').hide();
		$('#quarterly_newsletter_box').show();
	}
}
function showHostingEvent(type, control = null) {
	if (control) {
		$(control).addClass('active').addClass('text-white');
		$(control).siblings().removeClass('active').removeClass('text-white');
	}
	$('.event-row-upcoming').hide();
	$('.event-row-ended').hide();
	$('.event-row-ongoing').hide();
	$('.event-row-action_required').hide();
	$('.event-row-cancelled').hide();
	$('.event-row-' + type).show();
	$('.event-row-ended-not-found').hide();
	$('.event-row-upcoming-not-found').hide();
	$('.event-row-ongoing-not-found').hide();
	$('.event-row-action_required-not-found').hide();
	$('.event-row-cancelled-not-found').hide();
	if (type == 'cancelled' && !$('.event-row-cancelled').length > 0) {
		$('.event-row-cancelled-not-found').show();
	}
	if (type == 'ended' && !$('.event-row-ended').length > 0) {
		$('.event-row-ended-not-found').show();
	}
	if (type == 'upcoming' && !$('.event-row-upcoming').length > 0) {
		$('.event-row-upcoming-not-found').show();
	}
	if (type == 'ongoing' && !$('.event-row-ongoing').length > 0) {
		$('.event-row-ongoing-not-found').show();
	}
	if (type == 'action_required' && !$('.event-row-action_required').length > 0) {
		$('.event-row-action_required-not-found').show();
	}
}
function downloadICS(jsonData, name = 'event') {
	//var JsonP = JSON.parse($(jsonData).attr('icaldata'));
	var name = $(jsonData).attr('icaltitle');
	var JsonP = $(jsonData).attr('icaldata');
	const icsContent = JsonP.trim();
	const blob = new Blob([icsContent], { type: 'text/calendar' });
	const link = document.createElement('a');
	link.href = URL.createObjectURL(blob);
	link.download = (name || 'event') + '.ics';
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
}
function downloadICS_old(jsonData) {
	var JsonP = JSON.parse(jsonData);
	//DESCRIPTION:${JsonP.event_description}
	const icsContent = `
BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
SUMMARY:${JsonP.event_name}
DTSTART:${JsonP.event_start_datetime}
DTEND:${JsonP.event_end_datetime}
LOCATION:${JsonP.event_address}
END:VEVENT
END:VCALENDAR
  `.trim();
	const blob = new Blob([icsContent], { type: 'text/calendar' });
	const link = document.createElement('a');
	link.href = URL.createObjectURL(blob);
	link.download = (JsonP.event_name || 'event') + '.ics';
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
}
function checkEventListForm() {
	var loaction = $('#autocomplete').val();
	var latitude = $('#latitude').val();
	var longitude = $('#longitude').val();
	var distance = $('#distance').val();
	console.log(loaction, latitude, longitude);
	if (loaction != '' && latitude == '' && longitude == '') {
		showError('Please select valid google address to find the event.', 'error');
		return false;
	} else if (loaction != '' && latitude != '' && longitude != '' && distance == '') {
		showError('Please select a distance to find events near your location.', 'error');
		return false;
	}
	$('#eventListForm').submit();
}
function select2ajaxFunc(in_modal = '') {
	$('.select2ajax').each(function () {
		var datatype = $(this).attr('datatype');
		var url = '';
		if (datatype == 'organiser') {
			url = base + '/get-organizer-options';
		} else if (datatype == 'event') {
			url = base + '/get-event-options';
		} else if (datatype == 'volunteer') {
			url = base + '/get-volunteer-options';
		} else if (datatype == 'all_volunteers') {
			url = base + '/get-all-volunteer-options';
		} else if (datatype == 'pincode') {
			url = base + '/get-pincode-options';
		}
		if (in_modal != '') {
			$(this).select2({
				dropdownParent: $(in_modal),
				placeholder: 'Search for option',
				minimumInputLength: 1,
				ajax: {
					url: url,
					method: 'POST',
					data: function (params) {
						var query = {
							search: params.term,
							type: 'public'
						}
						// Query parameters will be ?search=[term]&type=public
						return query;
					}
				}
			});
		} else {
			$(this).select2({
				placeholder: 'Search for option',
				minimumInputLength: 1,
				ajax: {
					url: url,
					method: 'POST',
					data: function (params) {
						var query = {
							search: params.term,
							type: 'public'
						}
						// Query parameters will be ?search=[term]&type=public
						return query;
					}
				}
			});
		}
	});
	/* $(document).on("click", ".tag-remove", function () {
		$(this).parent().addClass('tag-removed');

		function removeFromMarkup() {
			$(".tag-removed").remove();
		}
		setTimeout(removeFromMarkup, 500);
		keywordsList.css({ 'height': 'auto' }).height();
	}); */
}
function parseDateOnlyValue(value) {
	if (!value) {
		return null;
	}

	var match = value.match(/^(\d{4})-(\d{1,2})-(\d{1,2})/);
	if (!match) {
		return null;
	}

	return new Date(parseInt(match[1], 10), parseInt(match[2], 10) - 1, parseInt(match[3], 10));
}

function toggleTicketEarlyCloseWarning() {
	var $modalBody = $('#manage-ticket-popup-body');
	var eventStartValue = $modalBody.find('.ticket_event_start_date').val() || $('#start_datetime').val();
	var sellEndValue = $modalBody.find('.tk_sell_end_date').val();

	var eventStartDate = parseDateOnlyValue(eventStartValue);
	var sellEndDate = parseDateOnlyValue(sellEndValue);

	if (!eventStartDate || !sellEndDate) {
		$modalBody.find('.ticket-early-close-warning').hide();
		return;
	}

	var diffDays = Math.floor((eventStartDate - sellEndDate) / (1000 * 60 * 60 * 24));

	if (diffDays >= 2) {
		$modalBody.find('.ticket-early-close-warning').show();
	} else {
		$modalBody.find('.ticket-early-close-warning').hide();
	}
}
$(document).on('input change', '#start_datetime, #manage-ticket-popup-body .tk_sell_end_date', function () {
	toggleTicketEarlyCloseWarning();
});
function callSellStartEndDatePicker() {
	$('.tk_sell_start_date').datetimepicker({
		scrollMonth: false,
		scrollInput: false,
		format: 'Y-m-d',
		timepicker: false,
		onShow: function (ct) {
			var minDateForSellDate = false;
			if (jQuery('#site_publish_date').val() != '') {
				var dateEnd = new Date(jQuery('#site_publish_date').val());
				minDateForSellDate = dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1) + '-' + dateEnd.getDate();
			}
			this.setOptions({
				minDate: minDateForSellDate ? minDateForSellDate : false
			})
			var maxDateForSellDate = false;
			if (jQuery('#end_datetime').val() != '') {
				var dateEnd = new Date(jQuery('#end_datetime').val());
				dateEnd.setDate(dateEnd.getDate() - 1);
				maxDateForSellDate = dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1) + '-' + dateEnd.getDate();
			}
			this.setOptions({
				maxDate: maxDateForSellDate ? maxDateForSellDate : false
			})
		},
	});
	$('.tk_sell_end_date').datetimepicker({
		scrollMonth: false,
		scrollInput: false,
		format: 'Y-m-d',
		timepicker: false,
		onShow: function (ct) {
			var minDateForSellDate = false;
			if (jQuery('#site_publish_date').val() != '') {
				var dateEnd = new Date(jQuery('#site_publish_date').val());
				minDateForSellDate = dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1) + '-' + dateEnd.getDate();
			}
			this.setOptions({
				minDate: minDateForSellDate ? minDateForSellDate : false
			})
			var maxDateForSellDate = false;
			if (jQuery('#end_datetime').val() != '') {
				var dateEnd = new Date(jQuery('#end_datetime').val());
				//dateEnd.setDate(dateEnd.getDate() - 1);
				maxDateForSellDate = dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1) + '-' + dateEnd.getDate();
			}
			this.setOptions({
				//maxDate:jQuery('#start_datetime').val()?jQuery('#start_datetime').val():false
				maxDate: maxDateForSellDate ? maxDateForSellDate : false
			})
		},
		onChangeDateTime: function () {
			setTimeout(function () {
				toggleTicketEarlyCloseWarning();
			}, 50);
		},
	});
	toggleTicketEarlyCloseWarning();
}
function fieldmasking() {
	if ($('.decimalonly').length != 0) {
		document.querySelectorAll('.decimalonly').forEach(decimalField => {
			decimalField.addEventListener('keydown', function (e) {
				if (['e', '+', '-'].includes(e.key)) {
					e.preventDefault();
				}
			});
			var decimalMask = IMask(decimalField, {
				mask: Number,
				scale: 2,                 // allow 2 decimal places
				signed: false,            // no negative
				thousandsSeparator: '',   // optional
				padFractionalZeros: false,
				normalizeZeros: true,
				radix: '.',               // decimal separator
			});
		});
	}
	if ($('.integeronly').length != 0) {
		document.querySelectorAll('.integeronly').forEach(intField => {
			intField.addEventListener('keydown', function (e) {
				if (['.', 'e', '+', '-'].includes(e.key)) {
					e.preventDefault();
				}
			});
			var intMask = IMask(intField, {
				mask: /^[0-9]*$/
			});
		});
	}
	if ($('.usphonemask').length != 0) {
		document.querySelectorAll('.usphonemask').forEach(phonefield => {
			var phoneMask = IMask(phonefield, {
				mask: '+{1} 000-000-0000'
			});
		});
	}
	if ($('.textonlymask').length != 0) {
		document.querySelectorAll('.textonlymask').forEach(textfield => {
			var textMask = IMask(textfield, {
				mask: /^[a-zA-Z\s]*$/
			});
		});
	}
	if ($('.pincodemask').length != 0) {
		document.querySelectorAll('.pincodemask').forEach(pincodefield => {
			var pincodeMask = IMask(pincodefield, {
				mask: /^[A-Za-z0-9\s]*$/
			});
		});
	}
	if ($('.onlyTextNumberSplaceMask').length != 0) {
		document.querySelectorAll('.onlyTextNumberSplaceMask').forEach(pincodefield => {
			var pincodeMask = IMask(pincodefield, {
				mask: /^[A-Za-z0-9\s]*$/
			});
		});
	}
	if ($('.slugmask').length != 0) {
		document.querySelectorAll('.slugmask').forEach(slugfield => {
			var slugMask = IMask(slugfield, {
				mask: /^[a-z0-9-]*$/,
				prepare: function (str) {
					return str.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
				}
			});
		});

	}
	if ($('.urlmask').length != 0) {
		document.querySelectorAll('.urlmask').forEach(urlfield => {
			var urlMask = IMask(urlfield, {
				mask: 'https://{rest}',
				blocks: {
					rest: {
						mask: /^[a-zA-Z0-9./?&=_-]*$/,  // allows valid URL characters
						prepare: function (str) {
							return str.replace(/\s+/g, ''); // remove spaces
						}
					}
				}
			});
		});

	}
	if ($('.specialtextonlymask').length != 0) {
		document.querySelectorAll('.specialtextonlymask').forEach(namefield => {

			var nameMask = IMask(namefield, {
				mask: /^[A-Za-z\s'-]*$/,
				prepare: function (str, masked) {
					// Remove numbers & disallowed special characters
					str = str.replace(/[^A-Za-z\s'-]/g, '');

					// Prevent multiple consecutive hyphens or apostrophes
					str = str.replace(/([-']){2,}/g, '$1');

					// Prevent starting with hyphen or apostrophe
					if (masked.value.length === 0) {
						str = str.replace(/^[-']+/g, '');
					}

					return str;
				}
			});

		});
	}
}
function redirectWithMessage(control, type = 'error') {
	var msg = $(control).attr('message');
	var url = $(control).attr('url');
	showError(msg, type);
	if (url != '') {
		setTimeout(() => {
			window.location.href = url;
		}, 700);
	}
}
function copyText(text) {
	// Create a temporary input element
	var tempInput = document.createElement("input");
	tempInput.value = text;
	document.body.appendChild(tempInput);
	tempInput.select();
	tempInput.setSelectionRange(0, 99999); // For mobile devices

	// Copy the text
	document.execCommand("copy");

	// Remove the temporary input
	document.body.removeChild(tempInput);

	// Show confirmation (optional)
	showError('Copied!', 'success');
}
function maskFieldValue(value, type) {
	if (!value) return "";

	let length = value.length;

	if (type === 1) {
		// Keep first character, mask the rest
		return value[0] + "*".repeat(length - 1);
	}
	else if (type === 2) {
		// Keep last 2 characters, mask the rest
		if (length <= 2) {
			return "*".repeat(length); // If very short, just mask all
		}
		return "*".repeat(length - 2) + value.slice(-2);
	}
	else {
		// Default: return original if type not valid
		return value;
	}
}
/* function maskFieldValue(value) {
	let arr = value.split('');
	let length = arr.length;

	// Calculate how many characters (50%) to mask
	let maskCount = Math.floor(length / 2);

	// Get random positions to mask
	let positions = [];
	while (positions.length < maskCount) {
		let pos = Math.floor(Math.random() * length);
		if (!positions.includes(pos)) {
			positions.push(pos);
		}
	}

	// Replace selected positions with '*'
	positions.forEach(pos => {
		arr[pos] = '*';
	});

	return arr.join('');
} */

document.querySelectorAll("#videoList .list-group-item").forEach(item => {
	item.addEventListener("click", function () {
		// Remove active from all
		document.querySelectorAll("#videoList .list-group-item").forEach(i => {
			i.classList.remove("active");
			let link = i.querySelector("a");
			if (link) link.classList.remove("text-white");
		});

		// Add active to clicked
		this.classList.add("active");
		let link = this.querySelector("a");
		if (link) link.classList.add("text-white");

		// Change video with autoplay
		const videoId = this.getAttribute("data-video");
		document.getElementById("mainVideo").src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
	});
});

document.querySelectorAll('.nav-item.dropdown').forEach(function (el) {
	el.addEventListener('mouseover', function () {
		if (window.innerWidth >= 992) {
			let dropdown = new bootstrap.Dropdown(el.querySelector('.dropdown-toggle'));
			dropdown.show();
		}
	});
	el.addEventListener('mouseleave', function () {
		if (window.innerWidth >= 992) {
			let dropdown = bootstrap.Dropdown.getInstance(el.querySelector('.dropdown-toggle'));
			dropdown.hide();
		}
	});
});

document.querySelectorAll('.nav-item.dropdown').forEach(function (el) {
	el.addEventListener('mouseover', function () {
		if (window.innerWidth >= 992) {
			let toggle = el.querySelector('.dropdown-toggle');
			if (toggle) {
				let dropdown = bootstrap.Dropdown.getOrCreateInstance(toggle);
				dropdown.show();
			}
		}
	});
	el.addEventListener('mouseleave', function () {
		if (window.innerWidth >= 992) {
			let toggle = el.querySelector('.dropdown-toggle');
			if (toggle) {
				let dropdown = bootstrap.Dropdown.getInstance(toggle);
				dropdown.hide();
			}
		}
	});
});

// Handle nested submenu clicks for mobile
document.querySelectorAll('.dropdown-submenu > .dropdown-toggle').forEach(function (el) {
	el.addEventListener('click', function (e) {
		if (window.innerWidth < 992) {
			e.preventDefault();
			e.stopPropagation();
			let submenu = el.nextElementSibling;
			if (submenu) {
				submenu.classList.toggle('show');
			}
		}
	});
});
