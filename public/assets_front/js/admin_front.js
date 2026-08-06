
// on opoen of form
$('.detailed-modal-popup').on('shown.bs.modal', function (event) {
	let target_button = event.relatedTarget;
	let modelId = event.target.id;
	if(modelId == 'registrationNotes'){
		populateNotesData(target_button);
	}else if(modelId == 'registrationDetails'){
		populateOrderDetails(target_button);
	}else{
		let Jsondata = $(target_button).parents('tr').first().find('.tr-detail').html();
		console.log(Jsondata);
		let data = JSON.parse(Jsondata);
		populateData(modelId, data);	
	}
})

function populateOrderDetails(targetButton){
	let volunteer = $(targetButton).data('volunteer');
	let event = $(targetButton).data('event');
	let ticket = $(targetButton).data('ticket');
	let order = $(targetButton).data('order');
	let registration = $(targetButton).data('registration');
	
	if(volunteer){
		$('#registrationDetails .registration-full-name').text(`${volunteer.first_name} ${volunteer.last_name} `);
		$('#registrationDetails .registration-email-address').text(volunteer.email_address);
		$('#registrationDetails .registration-phone-number').text(volunteer.phone);
	}
	if (order) {
		$('#registrationDetails .registration-order-id').text(order.order_number ? `#${order.order_number}` :  '');
		$('#registrationDetails .registration-order-total').text(order.grand_total || '');
		$('#registrationDetails .registration-ticket-quantity').text(order.total_tickets || '');
		$('#registrationDetails .registration-buyer-name').text(`${order.creator?.first_name} ${order.creator?.last_name}`);
	}
	if (event) {
		$('#registrationDetails .registration-event-name').text(event.event_name);
	}
	if (ticket) {
		$('#registrationDetails .registration-ticket-type').text(ticket.ticket_type || '');
		$('#registrationDetails .registration-ticket-name').text(ticket.ticket_name || '');
	}
	if(registration){
		$('#registrationDetails .registration-ticket-qty').text(registration.ticket_number ? '#'+registration.ticket_number : '');
		//$('#registrationDetails .registration-ticket-subtotal').text(registration.price ? registration.price : '');
		
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
	}

}

function populateNotesData(targetButton){
	let ticket = $(targetButton).data('ticket');
	$('#registrationNotes .ticket-order-id').val(ticket);
	let data = $(targetButton).data('notes');
	console.log(data);
	let notesDivs = '';
	if(data){
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
	$(`#${modelId}`).find('img').attr('src' , '').addClass('d-none');
	$(`#${modelId}`).find('input,textarea,select').not('[name="_token"]').val('');
	for(index in data){
		let item = data[index];
		if(!item)
				continue;
		if(index == 'uploaded_logo'){
				$(`#${modelId}`).find(`#${index}`).attr('src', item).removeClass('d-none');
		}else{
				$(`#${modelId}`).find(`#${index}`).val(item); 
		}
	} 
}