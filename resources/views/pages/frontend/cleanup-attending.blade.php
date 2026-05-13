@extends('layouts.frontend')

@section('content')
    <div class="wrapper wrapper-body">
		<div class="dashboard-body">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="d-main-title">
							<h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>Cleanups I'm Attending</h3>
							@php
								$breadcrumbs = [
									[
										'name' => 'Cleanups I\'m Attending', 
										'url' => route('front-cleanup-attending')
									],
								]
							@endphp     
							<x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
						</div>
					</div>
					<div class="col-md-12">
                    	<x-reusables.frontend.alert />
						<div class="filter-card my-4">
							<div class="card-body p-4">
								<div class="filter-card-header mb-3">
									<i class="fa fa-filter me-2"></i>
									<span>Filters</span>
								</div>
								<form action="">
									<div class="row g-3 align-items-end">
										<div class="col-md-5">
											<label class="form-label filter-label">Search Event</label>
											<div class="filter-input-wrap">
												<i class="fa fa-search filter-icon"></i>
												<input class="form-control filter-field" type="text" value="{{ request()->search ?? '' }}" name="search" placeholder="Find an event you're attending">
											</div>
										</div>
										<div class="col-md-4">
											<label class="form-label filter-label">Event Status</label>
											<select class="selectpicker filter-field w-100" name="event_type" title="Status: All">
												<option value="">Status: All</option>
												<option value="1" @selected(request()->event_type == '1')>Upcoming</option>
												<option value="3" @selected(request()->event_type == '3')>In-progress</option>
												<option value="2" @selected(request()->event_type == '2')>Past</option>
											</select>
										</div>
										<div class="col-md-3 d-flex gap-2">
											<button class="btn btn-filter-search flex-grow-1" type="submit">
												<i class="fa fa-search me-2"></i>Search
											</button>
											<a href="{{ url()->current() }}" class="btn btn-filter-reset" title="Reset Filters">
												<i class="fa fa-rotate-left"></i>
											</a>
										</div>
									</div>
								</form>
							</div>
						</div>
						<!-- <div class="main-card mt-5">
							<div class="dashboard-wrap-content p-4">
								<h5 class="mb-4">Events ({{$attending_events->count()}})</h5>
								<form action="">
									<div class="row">
										<div class="col-md-5">
											<div class="form-group">
												<div class="relative-input position-relative">
													<input class="form-control h_40 w-100" type="text" value="{{request()->search ?? ''}}" name="search" placeholder="Find an event you're attending">
													<i class="uil uil-search"></i>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group dropdown bootstrap-select dropdown">
												<select class="selectpicker" name="event_type">
													<option value="">Select Event Status</option>
													<option value="1" @selected(request()->event_type == '1')>Upcoming</option>
													<option value="2" @selected(request()->event_type == '2')>Past</option>
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="nav custom2-tabs btn-group justify-content-start gap-2 row-gap-3" role="tablist">
												<button class="primary-btn w-fit" type="submit">Search</button>
												<a href="{{ url()->current() }}" class="primary-btn w-fit">Reset</a>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div> -->
						<div class="event-list">
							<div class="tab-content">
								<div class="tab-pane fade show active" id="all-tab" role="tabpanel">
									@forelse ($attending_events as $event)
										@php
											// check if 48hours time exeed 
											$is_48hours_exeed = 0;
											$event_start_date_time = $event->start_datetime;
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

											$icalStrData = getiCalData($event);
											$registration = $event->pivot;
											$ticket = $event->pivot->ticket;
											$volunteer = $event->pivot->volunteer; 
											$order = $event->pivot->eventOrder;
											
											/**data sets*/
											$data_event 		= $event ? $event->only('event_name', 'venue', 'start_datetime', 'end_datetime', 'waiver_form_type', 'ticket_questions') : collect([]);
											$data_event['start_datetime'] = $event->startTimeAndEndTime;
											$data_registration 	= $registration ? $registration->only('ticket_number', 'price', 'waiver_handler_name', 'waiver_handler_email', 'waiver_filled_at', 'ticket_answer','is_under_18') : collect([]);
											if(isset($registration->waiver_filled_at)){
												$data_registration['waiver_filled_at_formated'] = !empty($registration->waiver_filled_at)?$registration->waiver_filled_at->format(config('app.dateTimeFormat')):null;
											}
											$data_order 		= $order ? $order->only('order_number','grand_total','total_tickets') : collect([]); 
											$data_volunteer 	= $volunteer ? $volunteer->only('first_name','last_name','email_address','phone') : collect([]);
											$data_ticket 		= $ticket ? $ticket->only('ticket_type','ticket_name','email_address','phone') : collect([]);
											$data_creator 		= $order->creator ? $order->creator->only('first_name', 'last_name') : collect([]);
										@endphp
										<div class="main-card mt-2">
											<div class="contact-list">
												<div class="card-top event-top p-4 align-items-center top d-md-flex flex-wrap justify-content-between">
													<div class="d-md-flex align-items-center event-top-info">
														<div class="card-event-img">
															<img src="{{$event->formattedBanner()}}" alt="Event">
														</div>
														<div class="card-event-dt">
															<h5 class="m-0">{{ $event->event_name }}</h5>
															<p class="m-0">{{ $event->fullAddress }}</p>
															@if($event->is_active!=1)
															<h6 class="coupon-status">
																<span class="badge custom-badge-danger 1">Not Active</span>
															</h6>
															@endif
															@if(!empty($event->cancelled_at))
															<h6 class="coupon-status">
																<span data-bs-toggle="tooltip" data-bs-original-title="This event has been cancelled at {{ date(config('app.dateTimeFormat'),strtotime($event->cancelled_at)) }}" class="badge custom-badge-danger 1">Not Cancelled</span>
															</h6>
															@endif
														</div>
													</div>
													
													<div class="dropdown">
														<button class="option-btn" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></button>
														<div class="dropdown-menu dropdown-menu-right">	
															<a href="{{route('view-event-details',['slug' => $event->slug])}}" class="dropdown-item" target="_BLANK">
																<i class="fa-solid fa-eye me-3"></i>View Event Page  
															</a>	
															<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#registrationDetails"
																data-event='@json($data_event)' data-creator='@json($data_creator)'
																data-ticket='@json($data_ticket)'
																data-order='@json($data_order)' data-volunteer='@json($data_volunteer)'
																data-registration='@json($data_registration)'  >
																<i class="fa-solid fa-gear me-3"></i>Ticket Details
															</a>				
															@if(!empty($event->waiver_form_type) && !$registration->is_under_18 && $registration->rsvp_confirmation!==0 && $event->is_active==1)	
															<a class="dropdown-item" target="_blank" href="{{route('view-waiver-form', ['ticket' => encrypt($registration->id)])}}"><i class="fa-solid fa-link me-3"></i>
																@if(!empty($registration->waiver_filled_at))
																View Waiver
																@else
																Sign Waiver
																@endif 
															</a>	
															@endif										
															<a href="{{route('add-event-to-calendar',['id'=>encrypt($event->id)])}}" class="dropdown-item">
																<i class="fa-solid fa-calendar me-3"></i>Add to Calendar
															</a>										
															<!-- <a href="javascript:void(0)" icaldata='{!! htmlspecialchars($icalStrData, ENT_QUOTES, 'UTF-8') !!}' icaltitle="{{$event->event_name}}" onclick="downloadICS(this);" class="dropdown-item">
																<i class="fa-solid fa-calendar me-3"></i>Add to Calendar
															</a> -->								
															<a href="javascript:void(0)" class="dropdown-item download-ticket"
																data-event='@json($data_event)' data-volunteer='@json($data_volunteer)' 
																data-ticket='@json($data_ticket)' data-registration='@json($data_registration)' 
																data-order='@json($data_order)'  data-creator='@json($data_creator)'
															>
																<i class="fa-solid fa-download me-3"></i>Download Ticket
															</a>	
															@if(!empty($registration->waiver_filled_at))
															<a class="dropdown-item download-ticket" href="{{ route('view-waiver-pdf', ['ticket' => encrypt($registration->id)]) }}" target="_blank">
																<i class="fa-solid fa-download me-3"></i>Download Waiver PDF
															</a>
															@endif
															@if($event->is_completed==1)
															<a target="_blank" href="{{route('cleanup-highlights',['event'=>encrypt($event->id)])}}" class="dropdown-item">
																<i class="fa-solid fa-bar-chart me-3"></i>View Impact Report
															</a>	
															@endif
															@if(strtotime($event->start_datetime) > time() && !$registration->is_under_18 && $registration->rsvp_confirmation==1 && $registration->checkin==0)
																@if($ticket->ticket_type=='paid')
																	@if($event->allow_refund==1)
																		@if($order->creator_id == $registration->volunteer_id)
																		<a class="dropdown-item cancel-refund-ticket {{ auth()->guard('front')->user()->id }}" href="javascript:void(0);" is_48hours_exeed="{{ $is_48hours_exeed }}" price="{{ $registration->price }}" ticket_type="{{ $ticket->ticket_type }}" allow_refund="{{ $event->allow_refund }}" ticket_id="{{ encrypt($registration->id) }}"><i class="fa-solid fa-ban me-3"></i>
																			Cancel Registration
																		</a>
																		@else 
																			@if($is_48hours_exeed!=1)
																				<a class="dropdown-item cancel-refund-ticket-attendee" href="javascript:void(0);" alert-title="If you cancel now, your registration will be cancelled and the ticket amount  will be automatically refunded to the buyer and You will no longer be marked as attending this event." url="{{route('cancel-refund-paid-ticket-attendee', ['ticket' => encrypt($registration->id),'type'=>1])}}"><i class="fa-solid fa-ban me-3"></i>
																					Cancel Registration
																				</a>	
																			@else 
																				<a class="dropdown-item cancel-refund-ticket-attendee" href="javascript:void(0);" alert-title="The refund window has passed. If you cancel now, your registration will be cancelled but no refund will be issued, and the amount will go to the organizer. You will no longer be marked as attending this event." url="{{route('cancel-refund-paid-ticket-attendee', ['ticket' => encrypt($registration->id),'type'=>2])}}"><i class="fa-solid fa-ban me-3"></i>
																					Cancel Registration
																				</a>	
																			@endif 
																		@endif 
																	@else 
																		<a class="dropdown-item cancel-refund-ticket-attendee" href="javascript:void(0);" alert-title="The refund is not allowed on this event so if you cancel the ticket, your registration will be cancelled but no refund will be issued, and the amount will go to the organizer. You will no longer be marked as attending this event." url="{{route('cancel-refund-paid-ticket-attendee', ['ticket' => encrypt($registration->id),'type'=>2])}}"><i class="fa-solid fa-ban me-3"></i>
																			Cancel Registration
																		</a>	
																	@endif 
																@else 
																<a class="dropdown-item cancel-refund-ticket-attendee" href="javascript:void(0);" alert-title="If you cancel now, your registration will be cancelled and you will no longer be marked as attending this event." url="{{route('cancel-refund-paid-ticket-attendee', ['ticket' => encrypt($registration->id),'type'=>2])}}"><i class="fa-solid fa-ban me-3"></i>
																	Cancel Registration
																</a>	
																<!-- <a class="dropdown-item cancel-rsvp-action" href="javascript:void(0);" url="{{route('cancel-rsvp', ['ticket' => encrypt($registration->id)])}}"><i class="fa-solid fa-ban me-3"></i>
																	Cancel Registration
																</a>	 -->
																@endif		
															@endif			
														</div>
													</div>
												</div>
												<div class="bottom d-flex flex-wrap justify-content-between align-items-center p-4">
													
													<div class="icon-box"> 
														<span class="icon">
															<i class="fa-solid fa-calendar-days"></i>
														</span>
														<p>Event Time</p>
														<h6 class="coupon-status">{{$event->startTimeAndEndTime}}</h6>
													</div>		
													<div class="icon-box">
														<span class="icon">
															<i class="fa-solid fa-calendar-days"></i>
														</span>
														<p>Registration Date</p>
														<h6 class="coupon-status">{{$event->pivot->createdDateTime}}</h6>
													</div>										
													<div class="icon-box">
														<span class="icon">
															<i class="fa-solid fa-quote-right"></i>
														</span>
														<p>RSVP</p>
														<h6 class="coupon-status">
															@if($registration->rsvp_confirmation===0)
															<span class="badge custom-badge-danger">
																Cancelled
																@if($registration->price>0)
																	@if($registration->is_amt_donated==1)
																	, Amount Donated
																	@elseif($registration->refund_status==1)
																	, Pending Refund
																	@elseif($registration->refund_status==2)
																	, Refund Processed
																	@elseif($registration->refund_status==3)
																	, Refunded
																	@endif
																@endif
															</span>
															@elseif($registration->rsvp_confirmation==1)
															<span class="badge custom-badge-success">Active</span>
															@else
															<span class="badge custom-badge-pending">Pending</span>
															@endif 
														<!-- @if($registration->rsvp_confirmation===0)
														Declined at {{date(config('app.dateTimeFormat'),strtotime($registration->rsvp_filled_at))}}
														@elseif($registration->rsvp_confirmation==1)
														Approved at {{date(config('app.dateTimeFormat'),strtotime($registration->rsvp_filled_at))}}
														@else
														Pending
														@endif  -->
														<!-- <a title="RSVP Confirmation Form" target="_blank" href="{{route('view-waiver-form', ['ticket' => encrypt($registration->id)])}}"><i class="fa-solid fa-link ms-1"></i></a> -->
														</h6>
													</div>				
													@if(!empty($event->waiver_form_type))							
													<div class="icon-box">
														<span class="icon">
															<i class="fa-solid fa-quote-right"></i>
														</span>
														<p>Waiver</p>
														<h6 class="coupon-status">
															@if(!empty($registration->waiver_filled_at))
															Signed at {{date(config('app.dateTimeFormat'),strtotime($registration->waiver_filled_at))}}
															@else
															<span class="badge custom-badge-danger">Unsigned</span>
															@endif 
														<!-- @if(!empty($registration->waiver_filled_at))
														Signed at {{date(config('app.dateTimeFormat'),strtotime($registration->waiver_filled_at))}}
														@else
														Pending
														@endif  -->
														<!-- <a title="RSVP Confirmation Form" target="_blank" href="{{route('view-waiver-form', ['ticket' => encrypt($registration->id)])}}"><i class="fa-solid fa-link ms-1"></i></a> -->
														</h6>
													</div>	
													@endif	
													<div class="icon-box">
														<span class="icon">
															<i class="fa-solid fa-certificate"></i>
														</span>
														<p>Community Service Form</p>
														@if($registration->rsvp_confirmation==1 && $event->is_active==1)
															@if($registration->checkin==1)
																<!-- <a class="btn btn-sm" href="{{ $registration->community_form_pdf }}" download>Download PDF</a> -->
																@if($registration->community_form_status=='approved')
																<a class="btn btn-sm" href="{{ route('show-community-service-pdf', ['ticket' => encrypt($registration->id)]) }}" target="_blank">Download PDF</a>
																@else
																<h6 class="coupon-status">
																	<span class="badge custom-badge-warning">Checked-In, Certificate Pending</span>
																</h6>
																@endif
															@elseif(strtotime(date('Y-m-d H:i:s')) < strtotime($event->eventStartDateTime))
																<h6 class="coupon-status">
																	<span class="badge custom-badge-danger">Awaiting</span>
																</h6>
															@elseif($registration->checkin!=1 && $registration->community_form_status=='pending')
																<button registration_id="{{ encrypt($registration->id) }}" is_event_completed={{ $event->is_completed }} class="btn btn-sm btn-warning send-community-form-request" url="{{ route('request-community-form',[encrypt($registration->id)]) }}">Request Now</button>
															@elseif($registration->checkin!=1 && $registration->community_form_status=='requested') 
																<h6 class="coupon-status">
																	<span class="badge custom-badge-warning">Requested</span>
																</h6>
															@elseif($registration->checkin!=1 && $registration->community_form_status=='rejected')
																<h6 class="coupon-status">
																	<span class="badge custom-badge-danger">Rejected</span>
																</h6>
															@else 
																<h6 class="coupon-status">
																	<span class="badge custom-badge-danger">Awaiting</span>
																</h6>
															@endif
														@else 
															<h6 class="coupon-status">
																<span class="badge custom-badge-danger 1">Not Available</span>
															</h6>
														@endif
														<!-- <div class="form-group">
															<div class="d-flex align-items-start">
																<label class="btn-switch m-0 me-3">
																	<input type="checkbox" {{ $registration->checkin == 1 ? 'disabled checked' : '' }} class="ticket_checkin_toggle" data-ticket-id="{{ $registration->encrypted_id }}" url="{{ route('ticket-mark-checkin',[$registration->encrypted_id]) }}">
																	<span class="checkbox-slider" ></span>
																</label>
															</div>
														</div> -->
													</div>	
													<!-- <div class="icon-box ">
														<span class="icon">
															<i class="fa-solid fa-location-dot"></i>
														</span>
														<p>Ticket Status</p>
														<h6 class="coupon-status">{{ucfirst($event->pivot->ticket_status)}}</h6>
													</div> -->										
													{{-- <div class="icon-box custom2-tabs p-0 d-flex justify-content-end">
														<button class="primary-btn w-fit active" type="button">Unregister</button>
													</div>												 --}}
												</div>
												@if(!empty($event->cancelled_at) && $registration->rsvp_confirmation==0 && $registration->price>0 && $registration->is_amt_donated==0 && $registration->refund_status==1) 
												<div class="bottom d-flex flex-wrap justify-content-end align-items-center p-4">
													<a data-bs-toggle="tooltip" data-bs-original-title="Convert your ticket into a donation. If no action is taken within 48 hours from the event cancellation time, your amount will be automatically refunded." class="primary-btn w-fit btn-convert-to-donation" href="javascript:void(0);" url="{{route('front-ticket-convert-to-donation', ['id' => $registration->encrypted_id])}}">Convert to Donation</a>
												</div>
												@endif
											</div>
										</div>
									@empty
										<div class="main-card mt-2">
											<div class="contact-list">
												<div class="card-top event-top p-4 align-items-center top d-flex flex-wrap justify-content-center">
													@if(request()->anyFilled(['event_type', 'search']))
														<div class="text-center py-4">
															<div class="empty-icon-wrap mx-auto mb-3">
																<i class="fa-regular fa-image fa-2x text-muted"></i>
															</div>
															<h5 class="fw-semibold mb-2">No event found</h5>
															<p class="text-muted small mb-3">We couldn't find any event matching your<br>current filters. Try adjusting your search criteria.</p>
															<a href="{{ url()->current() }}" class="btn btn-clear-filters">
																<i class="fa fa-filter me-2"></i>Clear all filters
															</a>
														</div>
													@else
														<div class="text-center py-4">
															<div class="empty-icon-wrap mx-auto mb-3">
																<i class="fa-regular fa-image fa-2x text-muted"></i>
															</div>
															<h5 class="fw-semibold mb-2">You're not signed up for any cleanups yet.</h5>
															<p class="text-muted small mb-3">Find a cleanup event near you and start making a difference today!</p>
															<a href="{{ route('view-events') }}" class="btn btn-clear-filters">
																<i class="fa-solid fa-calendar-days me-2"></i>Start Browsing
															</a>
														</div>
													@endif
												</div>
											</div>
										</div>
										<!-- @if(isset(request()->search) || isset(request()->event_type))
										<div class="main-card mt-2">
											<div class="contact-list">
												<div class="p-4 justify-content-center">
													<h5 class="text-center">
														No event found
													</h5>
												</div>
											</div>
										</div>
										@else
										<div class="main-card mt-2">
											<div class="contact-list">
												<div class="p-4 justify-content-center pb-0">
													<h5 class="text-center">
														You're not signed up for any cleanups yet.<br>Find a cleanup event near you and start making a difference today!
													</h5>
												</div>
												<div class="card-top event-top p-4 align-items-center top d-md-flex flex-wrap justify-content-center">
													<a href="{{route('view-events')}}" class="primary-btn outlined">
														Start Browsing
													</a>
												</div>
											</div>
										</div>
										@endif -->

										{{-- <x-reusables.frontend.frontemptyresult /> --}}
									@endforelse
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<div class="modal fade" id="cancel-ticket-popup" tabindex="-1" aria-labelledby="cancelTicketPopupLabel" aria-hidden="false">
	<div class="modal-dialog modal-medium-2 modal-dialog-centered modal-sm-height modal-lg">
		<div class="modal-content modal-dialog-scrollable">
			<div class="modal-header">
				<h5 class="modal-title" id="cancelTicketPopupLabel">Cancel Ticket</h5>
				<button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
			</div>
			<form class="validateform" action="{{route('cancel-refund-paid-ticket')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="ticket[]" id="single-cancel-ticket-id" value="">
				<div class="modal-body">
					<div class="model-content main-form">
						<div class="row">
							<div class="col-lg-12 col-md-12">
								<div class="premium-select">
									<div class="row">
										<div class="col-sm-6" id="option-1-div">
											<input value="1" type="radio" name="refund_type" id="option-1">
											<label for="option-1" class="option option-1">
												<p><span class="dot m-auto"></span></p>
												<b>Cancel Registration & Donate the Amount</b>
												<span>Your registration will be cancelled, and the $<b class="ticket-cancel-amt">0</b> amount will be donated.</span>
											</label>
										</div>
										<div class="col-sm-6" id="option-2-div">
											<input value="2" type="radio" name="refund_type" id="option-2">
											<label for="option-2" class="option option-2">
												<p><span class="dot m-auto"></span></p>
												<b>Cancel Registration & Refund the Amount</b>
												<span>Your registration will be cancelled, and the $<b class="ticket-cancel-amt">0</b> amount will be refunded.</span>
											</label>
										</div>
										<div class="col-sm-6 offset-sm-3" id="option-3-div" style="display: none;">
											<input value="3" type="radio" name="refund_type" id="option-3">
											<label for="option-3" class="option option-3">
												<p><span class="dot m-auto"></span></p>
												<p class="m-0 text-black"><b>Cancel Registration</b></p>
												<span>Your registration will be cancelled, and no amount will be refunded.</span>
											</label>
										</div>
									</div>
								</div>
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
<div class="modal fade" id="request-certificate-popup" tabindex="-1" aria-labelledby="requestCertificatePopupLabel" aria-hidden="false">
	<div class="modal-dialog modal-medium-2 modal-dialog-centered modal-sm-height modal-lg">
		<div class="modal-content modal-dialog-scrollable">
			<div class="modal-header">
				<h5 class="modal-title" id="requestCertificatePopupLabel">Request Certificate</h5>
				<button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
			</div>
			<form class="validateform" action="{{route('front-submit-certificate-request')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="registration_id" id="registration_id" value="">
				<div class="modal-body">
					<div class="model-content main-form">
						<div class="row">
							<div class="col-sm-12 mt-3">
								<div class="form-group">
									<label class="form-label fs-6">Reason*</label>
									<div class="text-editor">
										<textarea required="" id="request_reason" value="" name="request_reason" class="form-control" rows="4"></textarea>
									</div>
								</div>
							</div>
							<div class="col-sm-12 mt-3">
								<div class="form-group">
									<label class="form-label fs-6">Upload Proof Images* <small class="text-muted"> (max 4 images allowed)</small></label>
									<input style="line-height: 3;" id="after_event_images" class="form-control h_50" type="file" name="proof_images[]" accept="image/*" multiple required>
									<div class="d-flex flex-wrap align-items-center mt-2" >
									<div class="d-flex flex-wrap align-items-center mt-2" id="savedImages">
									</div>
									<div class="d-flex flex-wrap align-items-center mt-2" id="newImagePreview">
									</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="primary-btn w-fit">Request Now</button>
				</div>
			</form>
		</div>
	</div>
</div>
    <x-reusables.frontend.registration-popups />
	@push('script')
		<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
		<script>
			const opt = {
                margin: 1,
                filename: 'output.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, scrollX: 0, scrollY: 0 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

			$('.download-ticket').click(function(){
				/*populate data with this*/
				const eventData = $(this).data('event');
				const volunteerData = $(this).data('volunteer');
				const ticketData = $(this).data('ticket');
				const registrationData = $(this).data('registration');
				const orderData = $(this).data('order');

				// Populate ticket details
				$('#event-name').text(eventData.event_name || 'N/A');
				$('#event-venue').text(eventData.venue || 'N/A');
				$('#event-date').text(eventData.start_datetime || 'N/A');
				$('#event-time').text(eventData.time || '');
				$('#volunteer-name').text(volunteerData ? `${volunteerData.first_name} ${volunteerData.last_name}` : 'N/A');
				$('#ticket-type').text(ticketData.type || 'N/A');
				$('#registration-id').text(registrationData.ticket_number || 'N/A');
				$('#order-id').text(orderData ? `#${orderData.order_number}` : 'N/A');
				const element = document.getElementById('contentop');
				html2pdf().set(opt).from(element).toPdf().save('output.pdf');
			});

			$('.download-csv').click(function(event){
				
				event.preventDefault();
				$('.filter-form').attr('action', '{{route("front-export-registration")}}')
				$('.filter-form').submit();
			})

		</script>
	@endpush
	@style
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
		#option-1:checked:checked~.option-1 .dot::before, #option-2:checked:checked~.option-2 .dot::before, #option-3:checked:checked~.option-3 .dot::before{
			opacity: 1;
    		transform: scale(1);
		}


		.remove-new-img {
			position: absolute;
			top: -5px;
			right: -5px;
			background: #dc3545;
			color: white;
			border: none;
			border-radius: 50%;
			width: 18px;
			height: 18px;
			font-size: 10px;
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 0;
			line-height: 1;
		}

		.remove-new-img:hover {
			background: #b02a37;
		}
	</style>
	<link rel="stylesheet" href="{{ asset('assets_front/css/filter-css.css') }} ">
	@endstyle
@endsection