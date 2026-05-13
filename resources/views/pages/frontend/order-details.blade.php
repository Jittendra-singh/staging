@extends('layouts.frontend')
@section('content')
<style>
	.contact-list .bottom .icon-box{
		width: 20%!important;
	}
	#option-1:checked:checked~.option-1, #option-2:checked:checked~.option-2, #option-3:checked:checked~.option-3{
		border-color: var(--linear-gradient-2)!important;
	}
	#option-1:checked:checked~.option-1 .dot, #option-2:checked:checked~.option-2 .dot, #option-3:checked:checked~.option-3 .dot{
		background: var(--linear-gradient-2)!important;;
	}
</style>
<div class="wrapper wrapper-body">
	<div class="dashboard-body event-listing-detail">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 d-flex align-items-center justify-content-between">
					<div class="d-main-title d-flex align-items-center justify-content-between gap-2 w-100">
						<div>
							<h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>Order Details</h3>
							@php						
								$breadcrumbs = [
										[
											'name' => 'Orders', 
											'url' => route('front-orders')
										],
										[
											'name' => 'Order Details', 
											'url' => ''
										],
									]
							@endphp     
							<x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
						</div>
						
						<a href="{{route('front-orders')}}" class="m-0 p-0">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<polyline points="15 18 9 12 15 6" />
							</svg>
							Back to Orders
						</a>
					</div>

				</div>

				<!-- Main Event Detail Card -->
                <x-reusables.frontend.alert />
				<div class="detail-card mb-4">
					<!-- Event Header -->
					<div class="event-header p-4">
						<div class="row align-items-center justify-content-between w-100">
							<div class="col-md-8">
								<h3 class="mb-1"><a href="{{ route('view-event-details', $order->event->slug) }}">{{$order->event->event_name}}</a></h3>
								<p class="mb-0 opacity-75">Order #{{$order->order_number}}</p>
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
							@if($showCancelBtn && $order->event->is_completed==0)
							<div class="col-md-4">
								<button id="cancel-all-ticket-btn" class="primary-btn w-fit float-end">Cancel Ticket/Registration</button>
							</div>
							@endif 
						</div>
					</div>

					<div class="p-4">
						<div class="row">
							<!-- Event Information -->
							<div class="col-md-4 mb-4">
								<h4 class="section-title">
									Event Information
								</h4>

								<div class="info-label">Event Name</div>
								<div class="info-value">{{$order->event->event_name}}</div>

								<div class="info-label">Event Start Date</div>
								<div class="info-value">{{$order->event->start_datetime->format(config('app.dateTimeFormat'))}}</div>

								<div class="info-label">Event End Date</div>
								<div class="info-value">{{$order->event->end_datetime->format(config('app.dateTimeFormat'))}}</div>

								<div class="info-label">Location</div>
								<div class="info-value">{{$order->event->full_address}}</div>
							</div>

							<!-- Organizer Information -->
							<div class="col-md-4 mb-4">
								<h4 class="section-title">
									Organizer Information
								</h4>

								<div class="info-label">Organizer Name</div>
								<div class="info-value">{{$order->event->creator->name}}</div>

								<div class="info-label">Contact Email</div>
								<div class="info-value">{{$order->event->creator->email_address}}</div>

								<div class="info-label">Contact Phone</div>
								<div class="info-value">{{$order->event->creator->phone}}</div>

                                @if($order->event->creator->organisation_website)
								<div class="info-label">Website</div>
								<div class="info-value">
									<a href="{{$order->event->creator->organisation_website}}" class="text-decoration-none">{{$order->event->creator->organisation_website}}</a>
								</div>
                                @endif
							</div>
							<!-- Payment Information -->
							<div class="col-md-4 mb-4">
								<h4 class="section-title">
									Payment Information
								</h4>
								<table class="table table-bordered">
									<tr>
										<th>Transaction</th>
										<td><small>{{$order->payment_intent}}</small></td>
									</tr>
									<tr>
										<th>Payment Status</th>
										<td>
											@if($order->payment_status == 'pending')
												<span class="badge bg-warning">Pending</span>
											@elseif($order->payment_status == 'paid')
												<span class="badge bg-success">Paid</span>
											@else
												<span class="badge bg-danger">N/A</span>
											@endif
										</td>
									</tr>
									<tr>
										<th>Sub Total</th>
										<td>{{config('app.currency').formatAmt($order->sub_total)}}</td>
									</tr>
									@if(!empty($order->donation_amount) && $order->donation_amount > 0)
									<tr>
										<th>Donation</th>
										<td>{{config('app.currency').formatAmt($order->donation_amount)}}</td>
									</tr>
									@endif 
									<tr>
										<th>Grand Total</th>
										<td>{{config('app.currency').formatAmt($order->grand_total)}}</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>

				<!-- Ticket Information Card -->
				<div class="detail-card mb-4">
					<div class="event-header p-4">
						<h4 class="section-title mb-0">
							Ticket Information
						</h4>
					</div>
					<div class="p-4">
						<div class="row">
                            @foreach ($order->tickets as $key => $ticket)
							<div class="col-md-6">
								<div class="ticket-card">
									<div class="ticket-header">
										Ticket #{{$key+1}}
										@if($ticket->rsvp_confirmation!=1 && $ticket->is_amt_donated==1)
										<span class="badge bg-danger float-end">Cancelled & Amt Donated</span>
										@elseif($ticket->rsvp_confirmation!=1)
										<span class="badge bg-danger float-end">Cancelled</span>
										@endif 
									</div>
									<div class="info-label">Attendee Name</div>
									<div class="info-value">{{ $ticket->volunteer->name }}</div>

									<div class="info-label">Email Address</div>
									<div class="info-value">{{ $ticket->volunteer->email_address }}</div>

									<div class="info-label">Phone Number</div>
									<div class="info-value">{{ $ticket->volunteer->phone }}</div>

									<div class="info-label">Ticket Price</div>
									<div class="info-value">{{config('app.currency').formatAmt($ticket->price)}}</div>

									<div class="info-label">Age Category</div>
									<div class="info-value">
                                        @if($ticket->is_under_18)
										<span class="badge bg-warning">Minor (Under 18)</span>
                                        @else 
										<span class="badge primary-btn">Adult (18+)</span>
                                        @endif
									</div>

                                    @if($ticket->is_under_18)
									<div class="info-label">Guardian Details</div>
									<div class="info-value">{{ $ticket->waiver_handler_name }} ({{ $ticket->waiver_handler_email }})</div>
                                    @endif
								</div>
							</div>
                            @endforeach
						</div>
					</div>
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
				<button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
			</div>
			<form class="validateform" action="{{route('cancel-refund-paid-ticket')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="model-content main-form">
						<div class="row">
							<div class="col-lg-12 col-md-12">
								<div class="premium-select">
									<div class="row">
										@php 
										// check if 48hours time exeed 
										$is_48hours_exeed = 0;
										$event_start_date_time = $order->event->start_datetime;
										$now = new DateTime(); 
										$eventTime = new DateTime($event_start_date_time);
										// Difference in seconds
										$diffInSeconds = $eventTime->getTimestamp() - $now->getTimestamp();
										// 48 hours in seconds
										$hours48 = 48 * 60 * 60;
										// If event is within next 48 hours OR already started
										if ($diffInSeconds <= $hours48) {
											$is_48hours_exeed = 1;
										}			
										@endphp 
										@if($order->event->allow_refund!=1 || $is_48hours_exeed==1)
										<div class="col-sm-6 offset-sm-3" id="option-3-div">
											<input value="3" checked type="radio" name="refund_type" id="option-3">
											<label for="option-3" class="option option-3">
												<p><span class="dot m-auto"></span></p>
												<p class="m-0 text-black"><b>Cancel Registration</b></p>
												<span>Your registration will be cancelled, and no amount will be refunded.</span>
											</label>
										</div>
										@else 
										<div class="col-sm-6" id="option-1-div">
											<input value="1" type="radio" name="refund_type" id="option-1" checked>
											<label for="option-1" class="option option-1">
												<p><span class="dot m-auto"></span></p>
												<b>Cancel Registration & Donate the Amount</b>
												<span>Your registration will be cancelled, and the $<b class="ticket-cancel-amt">0</b> amount will be donated.</span>
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
										@endif
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
					<button type="submit" class="primary-btn w-fit">Submit Now</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection