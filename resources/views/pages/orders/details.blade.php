@php
    $pageName = 'Orders Details';
    $parentRoute = route('index-orders');
    $parentPageName = $module;
@endphp
@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $parentPageName, 'parentRoute' => $parentRoute,'breadcrumb' => [
    ['name' => 'Orders', 'route' => route('index-orders')],
    ['name' => 'Order Details', 'route' => ''],
]])
@section('content')
<style>
    
.premium-select {
    margin-top: 20px;
}

.premium-select .option {
    margin-top: 1.5rem;
    text-align: center;
    display: block;
    justify-content: space-evenly;
    border-radius: 5px;
    cursor: pointer;
    padding: 40px;
    border: 2px solid #efefef;
    background-color: #ffffff;
    width: 100%;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.premium-select .option .dot {
    height: 20px;
    width: 20px;
    background: #d9d9d9;
    border-radius: 50%;
    position: relative;
    display: inline-block;
}

.premium-select .option .dot::before {
    position: absolute;
    content: "";
    top: 4px;
    left: 4px;
    width: 12px;
    height: 12px;
    background: #fff;
    border-radius: 50%;
    opacity: 0;
    transform: scale(1.5);
    transition: all 0.3s ease;
}

.premium-select input[type="radio"] {
    display: none;
}

#option-1:checked:checked~.option-1,
#option-2:checked:checked~.option-2 {
    border: 2px solid #6ac045;
    background: #fff;
}

#option-1:checked:checked~.option-1 .dot,
#option-2:checked:checked~.option-2 .dot {
    background: #6ac045;
}

#option-1:checked:checked~.option-1 .dot::before,
#option-2:checked:checked~.option-2 .dot::before {
    opacity: 1;
    transform: scale(1);
}

.wrapper .option span {
    font-size: 18px;
    font-weight: 500;
    color: #000;
    display: block;
    margin: 13px 0 10px;
}

.wrapper .option span:last-child {
    font-size: 16px;
    font-weight: 400;
    color: #717171;
    display: block;
    margin-bottom: 0;
}

#option-1:checked:checked~.option-1 span,
#option-2:checked:checked~.option-2 span {
    color: #000;
}

#option-1:checked:checked~.option-1 span:last-child,
#option-2:checked:checked~.option-2 span:last-child {
    color: #717171;
    display: block;
}
.contact-list .bottom .icon-box{
    width: 20%!important;
}

.border-radius-30{
    border-radius: 30px;
}


#option-1:checked:checked~.option-1, #option-2:checked:checked~.option-2, #option-3:checked:checked~.option-3{
    border-color: var(--bs-primary)!important;
}
#option-1:checked:checked~.option-1 .dot, #option-2:checked:checked~.option-2 .dot, #option-3:checked:checked~.option-3 .dot{
    background: var(--bs-primary)!important;;
}
#option-1:checked:checked~.option-1 .dot::before, #option-2:checked:checked~.option-2 .dot::before, #option-3:checked:checked~.option-3 .dot::before{
    opacity: 1;
    transform: scale(1);
}
</style>
    <div class="">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Order #{{ $order->order_number }}</strong>
                <span class="text-muted">Created At: <b>{{ $order->createdDateTime }}</b></span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8 mb-2">
                        <div class="mb-3">
                            <table class="table table-borderless mb-0"> 
                                <tbody>
                                    <tr>
                                        <th class="fw-bold" style="width: 30%;">Event:</th>
                                        <td>{{ $order->event->event_name ?? '-' }}</td>
                                    </tr> 
                                    <tr>
                                        <th class="fw-bold">Payment Type:</th>
                                        <td>
                                            <span class="badge bg-info text-dark">{{ ucfirst($order->payment_type) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="fw-bold">Transaction Id:</th>
                                        <td>
                                            {{ ucfirst($order->payment_transaction) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="fw-bold">Payment Status:</th>
                                        <td>
                                            <span class="badge 
                                                @if($order->payment_status == 'paid') bg-success
                                                @elseif($order->payment_status == 'pending') bg-warning text-dark
                                                @else bg-secondary
                                                @endif
                                            ">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <!-- <tr>
                                        <th class="fw-bold">Extra Notes</th>
                                        <td>
                                            {{ "Extra Notes Extra Notes" }}
                                        </td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <table class="table table-sm mb-0 table-bordered">
                            <tbody>
                                <tr>
                                    <th class="text-end p-2">Total Tickets</th>
                                    <td class="text-end p-2">{{ $order->total_tickets }}</td>
                                </tr>
                                <tr>
                                    <th class="text-end p-2">Subtotal</th>
                                    <td class="text-end p-2 text-primary">${{ number_format($order->sub_total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-end p-2">Donation</th>
                                    <td class="text-end p-2 text-primary">${{ number_format($order->donation_amount, 2) }}</td>
                                </tr>
                                <!-- <tr>
                                    <th class="text-end p-2">Discount ({{ $order->discount_pert }}%)</th>
                                    <td class="text-end p-2 text-danger">-${{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-end p-2">Tax ({{ $order->tax_pert }}%)</th>
                                    <td class="text-end p-2 text-primary">${{ number_format($order->tax_amount, 2) }}</td>
                                </tr> -->
                                <tr class="border-top">
                                    <th class="text-end p-2">Grand Total</th>
                                    <td class="text-end p-2 fw-bold text-success">${{ number_format($order->grand_total, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @php 
            $totalTickets = count($order->tickets);
            $cancelledTickets = 0;
            foreach ($order->tickets as $key => $ticket){
                if($ticket->rsvp_confirmation!=1){
                    $cancelledTickets++;
                }
            }
            $showCancelBtn = $cancelledTickets!=$totalTickets?true:false;
            if(strtotime($order->event->start_datetime) <= time()){
                $showCancelBtn = false;
            }
        @endphp 
        <h4 class="mb-3 font-size-18 d-flex justify-content-between">
            <span>Tickets</span>
            @if($showCancelBtn && $order->event->is_completed==0)
                <button id="cancel-all-ticket-btn" class="btn btn-primary">Cancel Ticket/Registration</button>
            @endif
        </h4>
        <div class="card">
            <div class="card-body p-0"></div>
                <table class="table table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Ticket Number</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Volunteer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <!-- <th>Extra Notes</th> -->
                            <th>Created At</th>
                            <th>RSVP Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->tickets as $index => $ticket)
                            <tr>
                                <td>{{ $index + 1 }}
                                    <div class="d-none tr-detail">
                                        @json($ticket)
                                    </div>
                                </td>
                                <td>#{{ $ticket->ticket_number }}
                                    @if ($ticket->checkin == 1 ) 
                                        <i class="mdi mdi-sticker-check-outline text-primary" title="Checked In"></i>
                                    @endif
                                </td>
                                <td>{{ $ticket->ticket->ticket_type }}</td>
                                <td>${{ $ticket->price }}</td>
                                <td>{{ $ticket->volunteer->name ?? '-' }}</td>
                                <td>{{ $ticket->volunteer->email_address ?? '-' }}</td>
                                <td>{{ $ticket->volunteer->phone ?? '-' }}</td>
                                <!-- <td>{{ $ticket->extra_notes }}</td> -->
                                <td>{{ $ticket->created_at }}</td>
                                <td>
                                    @if($ticket->rsvp_confirmation===0 && $ticket->is_amt_donated==1)
                                    Cancelled & Amt Donated
                                    @elseif($ticket->rsvp_confirmation===0)
                                    Cancelled
                                    @elseif($ticket->rsvp_confirmation==1)
                                    Registered
                                    @else
                                    Pending
                                    @endif 
                                </td>
                                <td>    
                                    <div class="d-flex gap-2 justify-content-start action-button">
                                        <a target="_blank" href="{{ route('index-volunteers','name='.$ticket->volunteer->email_address) }}">
                                            <i class="fs-5 mdi mdi-eye" title="View Details"></i>
                                        </a>
                                    </div>
                                </td>
                                <!-- <td>    
                                    <div class="d-flex gap-2 justify-content-start action-button">
                                        <a class="detailed-modal-popup" 
                                            data-bs-toggle="modal" data-bs-target="#ticketDetails"
                                            data-name="">
                                            <i class="fs-5 mdi mdi-arrow-expand" title="View Details"></i>
                                        </a>
                                    </div>
                                </td> -->
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No tickets found for this order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- modal detail popup --}}
    <div class="modal fade detailed-modal-popup" id="ticketDetails" tabindex="-1" aria-labelledby="ticketDetailsLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketDetailsLabel">Ticket Details</h5>
                    <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i
                            class="uil uil-multiply"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mt-3">
                        <label for="ticketNumber" class="form-label">Extra Notes </label>
                        <input type="text" class="form-control extra_notes " name="extra_notes" id="extra_notes" value="" readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="ticketNumber" class="form-label">
                            {{$order->event->ticket_questions}}    
                        </label>
                        <input type="text" class="form-control ticket_answer " name="ticket_answer" id="ticket_answer" value="" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-sm-12 mt-4 text-center">
                            <button type="button" class="btn btn-light waves-effect me-3"
                                data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="cancel-all-ticket-popup" tabindex="-1" aria-labelledby="cancelAllTicketPopupLabel" aria-hidden="false">
	<div class="modal-dialog modal-medium-2 modal-dialog-centered modal-sm-height modal-lg">
		<div class="modal-content modal-dialog-scrollable">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelAllTicketPopupLabel">Cancel Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
			<form class="" action="{{route('cancel-refund-paid-ticket-admin')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="model-content main-form">
						<div class="row">
							<div class="col-lg-12 col-md-12">
								<div class="premium-select mt-0 mb-3">
									<div class="row">
										<div class="col-sm-6" id="option-3-div">
											<input value="3" checked type="radio" name="refund_type" id="option-3">
											<label for="option-3" class="option option-3">
												<p><span class="dot m-auto"></span></p>
												<b>Cancel Registration & No Amount Refund</b>
												<span>Your registration will be cancelled, and no amount will be refunded.</span>
											</label>
										</div>
										<div class="col-sm-6" id="option-2-div">
											<input {{ $order->event->id }} value="2" type="radio" name="refund_type" id="option-2">
											<label for="option-2" class="option option-2">
												<p><span class="dot m-auto"></span></p>
												<b>Cancel Registration & Refund the Amount</b>
												<span>Your registration will be cancelled, and the $<b class="ticket-cancel-amt">0</b> amount will be refunded.</span>
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-12 col-md-12">
								<table class="table table-ordered">
									 <caption>Choose the attendees whose tickets should be cancelled.</caption>
									<tr>
										<th><input class="select-all-ticket-checkbox" type="checkbox"></th>
										<th><b>Attendee Name</b></th>
										<th><b>Email Address</b></th>
										<th><b>Phone Number</b></th>
										<th><b>Ticket Price</b></th>
									</tr>
									@if(isset($order->tickets[0]))
										@foreach ($order->tickets as $key => $ticket)
											@php
											$showCheckbox = false;
											$dMsg = '';
											if($ticket->rsvp_confirmation==1 && $ticket->checkin==0){
												$showCheckbox = true;
											}
											if($ticket->rsvp_confirmation!=1 && $ticket->is_amt_donated==1){
												$dMsg = 'Cancelled & Amt Donated';
											} else if($ticket->rsvp_confirmation!=1){
												$dMsg = 'Cancelled';
											} else if($ticket->checkin==1){
												$dMsg = 'Checked-in';
											}
											@endphp 
											<tr>
												<td>
													@if($showCheckbox)	
													<input value="{{ encrypt($ticket->id) }}" name="ticket[]" price="{{ $ticket->price }}" class="attendee-ticket-checkbox" type="checkbox">
													@else 
													<span class="badge primary-btn">{{$dMsg}}</span>
													@endif
												</td>
												<td>{{$ticket->volunteer->name}}</td>
												<td>{{$ticket->volunteer->email_address}}</td>
												<td>{{$ticket->volunteer->phone}}</td>
												<td>{{config('app.currency').formatAmt($ticket->price)}}</td>
											</tr>
										@endforeach
									@else 
									@endif 
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary waves-effect waves-light">Submit Now</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
