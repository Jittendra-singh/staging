@extends('layouts.frontend')
@section('content')
    <div class="wrapper wrapper-body">
		<div class="dashboard-body">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 d-flex align-items-center justify-content-between">
						<div class="d-main-title">
							<h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>{{ __('messages.front_event_registrations_list') }}</h3>
							<p class="mb-2">{{ __('messages.front_registrations_desc') }}</p>
							@php
								$breadcrumbs = [
										[
											'name' => __('messages.front_event_registrations_list'),
											'url' => route('front-registration-list')
										],
									]
							@endphp
							<x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
						</div>
						<button class="primary-btn w-fit download-csv" type="button" >
							<i class="fa-solid fa-download "></i>Download CSV
						</button>
					</div>
					<div class="col-md-12">
                    	<x-reusables.frontend.alert />
						<div class="filter-card mt-4">
							<div class="card-body p-4">
								<div class="filter-card-header mb-3">
									<i class="fa fa-filter me-2"></i>
									<span>Filters</span>
								</div>
								<form class="filter-form">
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label class="form-label filter-label">{{ __('messages.front_search_event_name') }}</label>
											<div class="filter-input-wrap">
												<i class="fa fa-search filter-icon"></i>
												<select class="selectpicker filter-field" name="event" data-live-search="true" title="{{ __('messages.front_find_event_by_name') }}">
                                                @if(isset($events) && $events->isNotEmpty())
													@foreach($events as $event)
														<option value="{{ $event->id }}" @selected(request()->event == $event->id)
															data-content="{{ $event->event_name }}<br/><small class='p-0'>{{ date('M d, Y h:i A', strtotime($event->start_datetime)) }} - {{ date('M d, Y h:i A', strtotime($event->end_datetime)) }}</small>">
															{{ $event->event_name }}
														</option>
													@endforeach
												@endif
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<label class="form-label filter-label">Volunteer Info</label>
											<div class="filter-input-wrap">
												<i class="fa fa-user filter-icon"></i>
												<input class="form-control filter-field" value="{{ request()->search }}" name="search" type="text" placeholder="{{ __('messages.front_name_email_phone') }}">
											</div>
										</div>
									</div>
									<div class="row g-3 align-items-end">
										<div class="col-md-3">
											<label class="form-label filter-label">{{ __('messages.front_event_status') }}</label>
											<select class="selectpicker filter-field w-100" name="event_type" title="{{ __('messages.front_status_all') }}">
												<option value="">{{ __('messages.front_status_all') }}</option>
												<option value="1" @selected(request()->event_type == '1')>{{ __('messages.front_upcoming') }}</option>
												<option value="3" @selected(request()->event_type == '3')>{{ __('messages.front_in_progress') }}</option>
												<option value="2" @selected(request()->event_type == '2')>{{ __('messages.front_past') }}</option>
											</select>
										</div>
										<div class="col-md-3">
											<label class="form-label filter-label">RSVP Status</label>
											<select class="selectpicker filter-field w-100" name="status" title="RSVP: All">
												<option value="">RSVP: All</option>
												<option value="active" @selected(request()->status == 'active')>Active RSVP</option>
												<option value="cancelled" @selected(request()->status == 'cancelled')>Cancelled RSVP</option>
											</select>
										</div>
										<div class="col-md-3">
											<label class="form-label filter-label">Sort By</label>
											<select class="selectpicker filter-field w-100" name="filter">
												<option value="newest" @selected(request()->filter == 'newest' || !request()->filter)>Newest First</option>
												<option value="oldest" @selected(request()->filter == 'oldest')>Oldest First</option>
											</select>
										</div>
										<div class="col-md-3 d-flex gap-2">
											<button class="btn btn-filter-search flex-grow-1" type="submit">
												<i class="fa fa-search me-2"></i>{{ __('messages.front_search') }}
											</button>
											<a href="{{ url()->current() }}" class="btn btn-filter-reset" title="{{ __('messages.front_reset_filters') }}">
												<i class="fa fa-rotate-left"></i>
											</a>
										</div>
									</div>
								</form>
							</div>
						</div>
						<!-- <div class="main-card mt-5">
							<div class="dashboard-wrap-content p-4">
								<form class="filter-form">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group dropdown bootstrap-select dropdown">
												<select class="selectpicker" name="event" data-live-search="true">
													<option disabled selected>Select Event</option>
													@foreach($events as $event)
														<option value="{{ $event->id }}" @selected(request()->event == $event->id) data-content="{{ $event->event_name }}<br/><small class='p-0'>{{date('M d, Y h:i A', strtotime($event->start_datetime))}} - {{date('M d, Y h:i A', strtotime($event->end_datetime))}}</small>">
															{{ $event->event_name }}
														</option>
													@endforeach
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group dropdown bootstrap-select dropdown">
												<select class="selectpicker" name="event_type">
													<option value="">Select Event Status</option>
													<option value="1" @selected(request()->event_type == '1')>Upcoming</option>
													<option value="2" @selected(request()->event_type == '2')>Past</option>
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<input class="form-control h_40 w-100" value="{{request()->search}}" name="search" type="text" placeholder="Type text and press enter">
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group dropdown bootstrap-select dropdown">
												<select class="selectpicker" name="status">
													<option value="">All RSVP Status</option>
													<option value="active" @selected(request()->status == 'active')>Active RSVP</option>
													<option value="cancelled" @selected(request()->status == 'cancelled')>Cancelled RSVP</option>
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group dropdown bootstrap-select dropdown">
												<select class="selectpicker" name="filter">
													<option value="newest" @selected(request()->filter == 'newest' || !request()->filter)>Newest first</option>
													<option value="oldest" @selected(request()->filter == 'oldest')>Oldest first</option>
												</select>
											</div>
										</div>
										<div class="col-md-1 ps-0">
											<div class="rs mt_r4 ps-3">
												<div class="nav custom2-tabs btn-group justify-content-start gap-2 row-gap-3" >
													<button class="primary-btn w-fit" type="submit" ><i class="fa fa-search"></i></button>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div> -->
						<div class="d-flex flex-wrap gap-2 my-4">
							<div class="d-flex align-items-center bg-white rounded-3 px-3 py-3 flex-grow-1 border" style="min-width:160px;">
								<span class="me-3 d-flex align-items-center justify-content-center rounded-circle" style="background:#e3f0ff;width:48px;height:48px;">
									<i class="fa-solid fa-users fa-xl text-primary"></i>
								</span>
								<div>
									<div class="fw-bold fs-3">{{$reports['registration']}}</div>
									<div class="small text-muted">{{ __('messages.front_total_registration') }}</div>
								</div>
							</div>
							<div class="d-flex align-items-center bg-white rounded-3 px-3 py-3 flex-grow-1 border" style="min-width:160px;">
								<span class="me-3 d-flex align-items-center justify-content-center rounded-circle" style="background:#fff8e1;width:48px;height:48px;">
									<i class="fa-solid fa-user-plus fa-xl" style="color:#d4af37;"></i>
								</span>
								<div>
									<div class="fw-bold fs-3">{{$reports['new']}}</div>
									<div class="small text-muted">New</div>
								</div>
							</div>
							<div class="d-flex align-items-center bg-white rounded-3 px-3 py-3 flex-grow-1 border" style="min-width:160px;">
								<span class="me-3 d-flex align-items-center justify-content-center rounded-circle" style="background:#ffeaea;width:48px;height:48px;">
									<i class="fa-solid fa-user-xmark fa-xl text-danger"></i>
								</span>
								<div>
									<div class="fw-bold fs-3">{{$reports['cancelled']}}</div>
									<div class="small text-muted">Cancelled</div>
								</div>
							</div>
							<div class="d-flex align-items-center bg-white rounded-3 px-3 py-3 flex-grow-1 border" style="min-width:160px;">
								<span class="me-3 d-flex align-items-center justify-content-center rounded-circle" style="background:#fff3e6;width:48px;height:48px;">
									<i class="fa-solid fa-ticket fa-xl" style="color:#ff7f32;"></i>
								</span>
								<div>
									<div class="fw-bold fs-3">{{$reports['approved']}}</div>
									<div class="small text-muted">Total Sold Tickets</div>
								</div>
							</div>
							<div class="d-flex align-items-center bg-white rounded-3 px-3 py-3 flex-grow-1 border" style="min-width:160px;">
								<span class="me-3 d-flex align-items-center justify-content-center rounded-circle" style="background:#e3f0ff;width:48px;height:48px;">
									<i class="fa-solid fa-money-bill-wave fa-xl text-primary"></i>
								</span>
								<div>
									<div class="fw-bold fs-3">{{$reports['paid']}}</div>
									<div class="small text-muted">Paid Tickets</div>
								</div>
							</div>
							<div class="d-flex align-items-center bg-white rounded-3 px-3 py-3 flex-grow-1 border" style="min-width:160px;">
								<span class="me-3 d-flex align-items-center justify-content-center rounded-circle" style="background:#eafaf1;width:48px;height:48px;">
									<i class="fa-solid fa-gift fa-xl text-success"></i>
								</span>
								<div>
									<div class="fw-bold fs-3">{{$reports['free']}}</div>
									<div class="small text-muted">Free Tickets</div>
								</div>
							</div>

						</div>
						<div class="event-list">
							<div class="tab-content">
								<div class="tab-pane fade show active" id="all-tab" role="tabpanel">
									@forelse($registrations as $registration)
										@php
											$event = $registration->event;
											$volunteer = $registration->volunteer;
											$ticket = $registration->ticket;
											$order = $registration->eventOrder()->with('creator')->first();
											/**data sets*/
											$data_event 		= $event ? $event->only('event_name', 'venue', 'start_datetime', 'end_datetime', 'waiver_form_type', 'ticket_questions') : collect([]);
											$data_event['start_datetime'] = $event->startTimeAndEndTime;
											$data_registration 	= $registration ? $registration->only('ticket_number', 'price', 'waiver_handler_email', 'waiver_filled_at', 'ticket_answer') : collect([]);
											$data_order 		= $order ? $order->only('order_number','grand_total','total_tickets') : collect([]);
											$data_volunteer 	= $volunteer ? $volunteer->only('first_name','last_name','email_address','phone') : collect([]);
											$data_ticket 		= $ticket ? $ticket->only('ticket_type','ticket_name','email_address','phone') : collect([]);
											$data_creator 		= $order->creator ? $order->creator->only('first_name', 'last_name') : collect([]);
											if(isset($registration) && isset($registration->waiver_filled_at) && !empty($registration->waiver_filled_at)){
												//$registration->waiver_filled_at_formated=date(config('app.dateTimeFormat'),strtotime($registration->waiver_filled_at));
												$formateddate = new DateTime($registration->waiver_filled_at);
												$registration->waiver_filled_at_formated = $formateddate->format('d M, Y g:iA');
											}
											$resendWaiverLogs = $registration->resendWaiverLogs()->latest('created_at')->get()->map(function($log) {
												return [
													'type' => $log->link_send_type == 1 ? 'Original Email' : 'Alternative Email',
													'email' => $log->link_send_to_email,
													'send_at' => $log->createdDateTime,
												];
											});

										@endphp
										<div class="main-card mt-4">
											<div class="contact-list">
												<div class="card-top event-top p-4 align-items-center top d-md-flex flex-wrap justify-content-between">
													<div class="d-md-flex align-items-top event-top-info">
														<div class="card-event-img">
															<img src="{{$volunteer->formattedImage('profile_picture')}}" alt="{{$volunteer->name}}">
														</div>
														<div class="card-event-dt">
															<h5 class="m-0">{{$volunteer->name}}</h5>
															<small>{{$volunteer->email_address}}</small>
															<p class="mt-1 mb-0">{{$event->event_name}}</p>
															@if($registration->checkin!=1 && $registration->community_form_status=='requested')
															<span class="badge custom-badge-warning mt-1">{{ __('messages.front_certificate_requested') }}</span>
															@endif
														</div>
													</div>
													<div class="dropdown">
														<button class="option-btn" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></button>
														<div class="dropdown-menu dropdown-menu-right">
															<a href="javascript:void(0)" class="dropdown-item download-ticket"
																data-event='@json($data_event)' data-volunteer='@json($data_volunteer)'
																data-ticket='@json($data_ticket)' data-registration='@json($data_registration)'
																data-order='@json($data_order)'  data-creator='@json($data_creator)'
															>
																<i class="fa-solid fa-download me-3"></i>Download Ticket
															</a>
															@if(!empty($registration->waiver_filled_at))
															<a class="dropdown-item download-ticket" href="{{ route('view-waiver-pdf', ['ticket' => encrypt($registration->id)]) }}" target="_blank">
																<i class="fa-solid fa-download me-3"></i>{{ __('messages.front_download_waiver_pdf') }}
															</a>
															@endif
															@if($ticket->ticket_status)
																<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editRegistraionStatus">
																	<i class="fa-solid fa-gear me-3"></i>Edit Status
																</a>
															@endif

															<a class="dropdown-item registration-notes"
																data-notes='@json($registration->notes)' data-ticket="{{$registration->id}}"
																data-bs-toggle="modal" data-bs-target="#registrationNotes" >
																<i class="fa-solid fa-note-sticky me-3" ></i> {{ __('messages.front_notes') }}
															</a>
															<!-- <a href="{{ route('front-registration-calendar',['slug'=>$event->slug]) }}" class="dropdown-item">
																<i class="fa-solid fa-envelope me-3"></i>Download iCal
															</a> -->
															<a class="dropdown-item" data-event='@json($event)' data-volunteer='@json($volunteer)'
															data-ticket='@json($ticket)' data-registration='@json($registration)' data-order='@json($order)'
																data-bs-toggle="modal" data-bs-target="#registrationDetails">
																<i class="fa-solid fa-info-circle me-3"></i>{{ __('messages.front_details') }}
															</a>
															<a ref="javascript:void(0);" class="dropdown-item send-attendee-email" data-route="{{ route('front-send-custom-email-attendee', ['id' => encrypt($registration->id)]) }}" data-bs-toggle="modal" data-bs-target="#sendAttendeeEmailModal">
																<i class="fa-solid fa-envelope me-3"></i>{{ __('messages.front_send_email') }}
															</a>
															<a href="javascript:void(0);"
																class="dropdown-item btn-resend-waiver-logs-modal"
																data-logs='@json($resendWaiverLogs)'
																data-bs-toggle="modal"
																data-bs-target="#resendWaiverLogsModal">
																<i class="fa-solid fa-clock-rotate-left me-3"></i>Resend Waiver Logs
															</a>
															@if($registration->rsvp_confirmation==1 && $registration->checkin==0 && $event->is_completed!=1)
																@if($registration->price>0)
																	<a class="dropdown-item cancel-refund-ticket-organizer {{ auth()->guard('front')->user()->id }}" href="javascript:void(0);" price="{{ $registration->price }}" ticket_id="{{ encrypt($registration->id) }}"><i class="fa-solid fa-ban me-3"></i>
																		{{ __('messages.front_cancel_registration') }}
																	</a>
																@else
																<a class="dropdown-item cancel-refund-ticket-attendee" href="javascript:void(0);" alert-title="If you proceed, this ticket will be cancelled and the attendee will no longer be marked as attending this event. This ticket has no amount associated with it, so no refund will be processed." url="{{route('cancel-refund-paid-ticket-attendee', ['ticket' => encrypt($registration->id),'type'=>2])}}"><i class="fa-solid fa-ban me-3"></i>
																	{{ __('messages.front_cancel_registration') }}
																</a>
																@endif
															@endif
														</div>
													</div>
												</div>
												<div class="reports-box bottom d-flex flex-wrap justify-content-between align-items-center p-4">
													<div class="icon-box ">
														<span class="icon">
															{{-- <i class="fa-solid fa-ticket"></i> --}}
															<i class="fa-solid fa-clock-rotate-left"></i>
														</span>
														<p>Event Time</p>
														<h6 class="coupon-status">{{$event->startTimeAndEndTime}}</h6>
													</div>
													<div class="icon-box">
														<span class="icon">
															<i class="fa-solid fa-calendar-days"></i>
														</span>
														<p>{{ __('messages.front_ticket_registration_date') }}</p>
														<h6 class="coupon-status">{{$ticket->ticket_name}} - {{$registration->price>0 ? '$'.$registration->price : __('messages.front_free')}} <br/>{{$registration->createdDateTime}}</h6>
													</div>
													<!-- <div class="icon-box">
														<span class="icon">
															<i class="fa-solid fa-ticket"></i>
														</span>
														<p>Ticket</p>
														<h6 class="coupon-status">{{$ticket->ticket_name}}/ {{$ticket->ticket_price ? '$'.$ticket->ticket_price : 'Free'}}</h6>
													</div>	 -->
													<div class="icon-box">
														<span class="icon">
															<i class="fa-solid fa-quote-right"></i>
														</span>
														<p>RSVP</p>
														<h6 class="coupon-status">
															@if($registration->rsvp_confirmation===0)
															<span class="badge custom-badge-danger">
																{{ __('messages.front_cancelled') }}
																@if($registration->price>0)
																	@if($registration->is_amt_donated==1)
																	, {{ __('messages.front_amount_donated') }}
																	@elseif($registration->refund_status==1)
																	, {{ __('messages.front_pending_refund') }}
																	@elseif($registration->refund_status==2)
																	, {{ __('messages.front_refund_processed') }}
																	@elseif($registration->refund_status==3)
																	, {{ __('messages.front_refunded') }}
																	@endif
																@endif
															</span>
															@elseif($registration->rsvp_confirmation==1)
															<span class="badge custom-badge-success">{{ __('messages.front_active') }}</span>
															@else
															<span class="badge custom-badge-pending">Pending</span>
															@endif
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
															{{ __('messages.front_signed_at', ['date' => date(config('app.dateTimeFormat'),strtotime($registration->waiver_filled_at))]) }} <!-- <a href="{{ route('view-waiver-pdf', ['ticket' => encrypt($registration->id)]) }}" data-bs-toggle="tooltip" data-bs-original-title="Download Waiver PDF" target="_blank" class="ms-1"><span class="badge custom-badge-success"><i class="fa fa-download"></i></span></a> -->
															@else
															<span class="badge custom-badge-danger">{{ __('messages.front_unsigned') }}</span>
															<a href="{{ route('view-waiver-form', ['ticket' => encrypt($registration->id)]) }}" data-bs-toggle="tooltip" data-bs-original-title="Waiver Page Link" target="_blank"><span class="badge custom-badge-warning"><i class="fa fa-external-link"></i></span></a><br/>
															<!-- <button class="btn btn-sm btn-outline-primary mt-2 btn-resend-waiver border-radius-30" url="{{ route('resend-waiver', ['ticket' => encrypt($registration->id)]) }}">Resend</button> -->
															<button class="btn btn-sm btn-outline-primary mt-2 border-radius-30 btn-resend-waiver-modal" defaultemail="{{ $registration->is_under_18==1?$registration->waiver_handler_email:$registration->volunteer->email_address }}" url="{{ route('resend-waiver', ['ticket' => encrypt($registration->id)]) }}" checkurl="{{ route('check-resend-waiver', ['ticket' => encrypt($registration->id)]) }}">{{ __('messages.front_resend') }}</button>
															@endif
														</h6>
													</div>
													@endif
													@if(strtotime(date('Y-m-d 00:00:0')) >= strtotime(date('Y-m-d',strtotime($event->start_datetime)).' 00:00:0'))
													<div class="icon-box">
														<span class="icon">
															<i class="fa-solid fa-check"></i>
														</span>
														<p>Check-in</p>
														<div class="form-group">
															<div class="d-flex align-items-start">
																@if($registration->rsvp_confirmation!=1)
																<label class="btn-switch m-0 me-3">
																	<input type="checkbox" disabled {{ $registration->checkin == 1 ? 'checked' : '' }} class="ticket_checkin_toggle">
																	<span class="checkbox-slider" url="" onclick="redirectWithMessage(this)" message="{{'You can\'t check-in before RSVP confirmation.'}}"></span>
																</label>
																@elseif(empty($registration->waiver_filled_at))
																<!-- <label class="btn-switch m-0 me-3">
																	<input type="checkbox" disabled {{ $registration->checkin == 1 ? 'checked' : '' }} class="ticket_checkin_toggle">
																	<span class="checkbox-slider" url="" onclick="redirectWithMessage(this)" message="{{'You can\'t check-in before waiver signing.'}}"></span>
																</label> -->
																<label class="btn-switch m-0 me-3">
																	<input type="checkbox" disabled {{ $registration->checkin == 1 ? 'checked' : '' }} class="ticket_checkin_toggle">
																	<span
																		class="checkbox-slider btn-manual-waiver-checkin"
																		data-route="{{ route('front-sign-waiver-mark-checking-manually', ['id' => $registration->encrypted_id]) }}"
																		data-volunteer-name="{{ $volunteer->name }}"
																		data-event-name="{{ $event->event_name }}"
																		data-bs-toggle="modal"
																		data-bs-target="#manualWaiverCheckinModal">
																	</span>
																</label>
																@else
																<label class="btn-switch m-0 me-3">
																	<input type="checkbox" {{ $registration->checkin == 1 ? 'checked' : '' }} class="ticket_checkin_toggle" data-ticket-id="{{ $registration->encrypted_id }}" url="{{ route('ticket-mark-checkin',[$registration->encrypted_id]) }}">
																	<span class="checkbox-slider" ></span>
																</label>
																@endif
															</div>
														</div>
													</div>
													@endif
													<!-- <div class="icon-box">
														<span class="icon">
															<i class="fa-solid fa-quote-right"></i>
														</span>
														<p>Ticket Status</p>
														<h6 class="coupon-status">{{ucfirst($registration->ticket_status)}}</h6>
													</div>	 -->
												</div>
											</div>
										</div>
									@empty
										<div class="main-card mt-2">
											<div class="contact-list">
												<div class="card-top event-top p-4 align-items-center top d-flex flex-wrap justify-content-center">
													@if(request()->anyFilled(['event', 'event_type', 'search', 'status', 'filter']))
														<div class="text-center py-4">
															<div class="empty-icon-wrap mx-auto mb-3">
																<i class="fa-regular fa-image fa-2x text-muted"></i>
															</div>
															<h5 class="fw-semibold mb-2">{{ __('messages.front_no_registrations_found') }}</h5>
															<p class="text-muted small mb-3">{{ __('messages.front_no_registrations_filter_desc') }}</p>
															<a href="{{ url()->current() }}" class="btn btn-clear-filters">
																<i class="fa fa-filter me-2"></i>{{ __('messages.front_clear_all_filters') }}
															</a>
														</div>
													@else
														<div class="text-center py-4">
															<div class="empty-icon-wrap mx-auto mb-3">
																<i class="fa-regular fa-image fa-2x text-muted"></i>
															</div>
															<h5 class="fw-semibold mb-2">{{ __('messages.front_no_registrations_found') }}</h5>
															<p class="text-muted small mb-3">{{ __('messages.front_no_registrations_empty_desc') }}</p>
														</div>
													@endif
												</div>
											</div>
										</div>
										<!-- <div class="main-card mt-2">
											<div class="contact-list">
												<div class="card-top event-top p-4 align-items-center top d-md-flex flex-wrap justify-content-between">
													<h4 class="text-center">{{ __('messages.front_no_registrations_empty_desc') }}</h4>
												</div>
											</div>
										</div> -->
									@endforelse
								</div>
							</div>
						</div>
                        <div class="mt-2" data-aos="fade-up" data-aos-duration="800">
                            {{ $registrations->links() }}
                        </div>
					</div>
				</div>
			</div>
		</div>
	</div>

    <x-reusables.frontend.registration-popups/>
<div class="modal fade" id="sendAttendeeEmailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="sendAttendeeEmailForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary">Send Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
					<div class="model-content main-form pt-4">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group mb-3">
									<label>Subject*</label>
									<input type="text" name="subject" class="form-control" required>
								</div>

								<div class="form-group mb-3">
									<label>Content*</label>
									<textarea name="content" id="description" class="form-control ckeditor" rows="8"></textarea>
								</div>
							</div>
						</div>
					</div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="primary-btn w-fit btn-send-custom-email">Send Email</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="resendWaiverEmailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="resendWaiverEmailForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary">Resend Waiver Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
				<div class="modal-body p-4">

					{{-- Hidden radio inputs (keep original names/ids for JS serialize) --}}
					<input type="radio" name="resend_waiver_type" id="resend_waiver_type1" value="1" checked hidden>
					<input type="radio" name="resend_waiver_type" id="resend_waiver_type2" value="2" hidden>

					{{-- Option 1: Original Email --}}
					<div class="resend-opt-card selected" id="optionCard1" onclick="selectResendOption(1)">
						<div class="d-flex align-items-start gap-3">
							<div class="resend-radio-dot on" id="radioDot1"></div>
							<div class="flex-grow-1">
								<p class="resend-opt-title">Send to the original email address</p>
								<p class="resend-opt-desc">Resend the waiver link to the volunteer's email that's currently on file.</p>
								<span class="resend-email-badge">
									<i class="fa fa-envelope"></i>
									<span id="resend_waiver_default_email">example@gmail.com</span>
								</span>
							</div>
						</div>
					</div>

					{{-- Option 2: Alternative Email --}}
					<div class="resend-opt-card" id="optionCard2" onclick="selectResendOption(2)">
						<div class="d-flex align-items-start gap-3">
							<div class="resend-radio-dot" id="radioDot2"></div>
							<div class="flex-grow-1">
								<p class="resend-opt-title">Send to an alternative email address</p>
								<p class="resend-opt-desc">Use this if the original email is incorrect, bouncing, or inaccessible to the volunteer.</p>
								<input type="email"
									name="alternative_waiver_email"
									id="alternative_waiver_email"
									class="form-control form-control-sm resend-alt-input mt-1"
									placeholder="Enter alternative email address"
									disabled>
							</div>
						</div>
					</div>

					{{-- Contextual Note --}}
					<div class="resend-note">
						<i class="ti ti-info-circle resend-note-icon"></i>
						<p class="mb-0" id="resend_waiver_note">
							<span class="resend-note-label">Original email selected — </span>
							The waiver link will be resent to the volunteer's registered address. This does not change their profile email.
						</p>
					</div>
					{{-- Last Resend Waiver Log --}}
					<div class="resend-last-log-box" id="resend_waiver_last_log_box">
						<div class="resend-last-log-header">
							<i class="fa fa-clock-rotate-left"></i>
							<span>Last Resend Record</span>
						</div>

						<div class="resend-last-log-empty" id="resend_waiver_last_log_empty">
							No resend waiver email record found.
						</div>

						<div class="resend-last-log-content" id="resend_waiver_last_log_content" style="display:none;">
							<div class="resend-last-log-row">
								<span class="resend-last-log-label">Type</span>
								<span class="resend-last-log-value" id="resend_waiver_last_log_type"></span>
							</div>
							<div class="resend-last-log-row">
								<span class="resend-last-log-label">Email</span>
								<span class="resend-last-log-value" id="resend_waiver_last_log_email"></span>
							</div>
							<div class="resend-last-log-row mb-0">
								<span class="resend-last-log-label">Sent At</span>
								<span class="resend-last-log-value" id="resend_waiver_last_log_datetime"></span>
							</div>
						</div>
					</div>

				</div>
                <!-- <div class="modal-body">
					<div class="model-content main-form pt-4">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group mb-3">
									<input type="radio" name="resend_waiver_type" value="1" id="resend_waiver_type1" checked>
									<label for="resend_waiver_type1">Resend the waiver email on <b id="resend_waiver_default_email">example@gmail.com</b></label>
								</div>
								<div class="form-group mb-3">
									<input type="radio" name="resend_waiver_type" value="2" id="resend_waiver_type2">
									<label for="resend_waiver_type2">Resend the waiver email on alternative email</label>
									<input type="text" name="alternative_waiver_email" id="alternative_waiver_email" class="form-control" placeholder="Enter alternative email">
								</div>
							</div>
						</div>
					</div>
                </div> -->

                <div class="modal-footer">
                    <button type="button" class="primary-btn w-fit submit-btn-send-waiver-email">Send Email</button>
                </div>
            </div>
        </form>
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
										<div class="col-sm-6" id="option-2-div">
											<input value="2" type="radio" name="refund_type" id="option-2">
											<label for="option-2" class="option option-2">
												<p><span class="dot m-auto"></span></p>
												<b>Cancel Registration & Refund the Amount</b>
												<span>This registration will be cancelled, and the $<b class="ticket-cancel-amt">0</b> amount will be refunded.</span>
											</label>
										</div>
										<div class="col-sm-6" id="option-3-div">
											<input value="3" type="radio" name="refund_type" id="option-3">
											<label for="option-3" class="option option-3">
												<p><span class="dot m-auto"></span></p>
												<b>Cancel Registration & No Amount Refund</b>
												<span>This registration will be cancelled, and no amount will be refunded.</span>
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
<div class="modal fade" id="resendWaiverLogsModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-primary">Resend Waiver Logs</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body p-4">
				<div class="resend-waiver-log-empty text-center py-4" style="display:none;">
					<i class="fa-solid fa-clock-rotate-left fa-2x text-muted mb-3"></i>
					<h6 class="mb-1">No logs found</h6>
					<p class="text-muted small mb-0">No waiver resend email has been sent yet.</p>
				</div>

				<div class="table-responsive resend-waiver-log-table-wrap">
					<table class="table table-bordered table-hover align-middle resend-waiver-log-table mb-0">
						<thead>
							<tr>
								<th>Type</th>
								<th>Email</th>
								<th>Send At</th>
							</tr>
						</thead>
						<tbody id="resendWaiverLogsTableBody"></tbody>
					</table>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="primary-btn w-fit" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="manualWaiverCheckinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="manualWaiverCheckinForm" enctype="multipart/form-data">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary">Verify Paper Waiver & Check-In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
					<div class="model-content main-form pt-4">
						<div class="manual-waiver-attendee-box mb-4">
							<div class="manual-waiver-avatar">
								<i class="fa-solid fa-user-check"></i>
							</div>
							<div>
								<h6 class="manual-waiver-attendee-name mb-1" id="manual_waiver_volunteer_name">Volunteer</h6>
								<p class="manual-waiver-event-name mb-0" id="manual_waiver_event_name">Event</p>
							</div>
						</div>

						<div class="form-group mb-3">
							<label class="manual-waiver-label">Upload Signed Waiver Document</label>

							<div class="manual-waiver-mobile-actions" style="display:none;">
								<button type="button" class="manual-waiver-action-btn" id="manual_waiver_capture_btn">
									<i class="fa-solid fa-camera"></i>
									Capture Image
								</button>

								<button type="button" class="manual-waiver-action-btn" id="manual_waiver_gallery_btn">
									<i class="fa-solid fa-image"></i>
									Choose Image
								</button>
							</div>

							<label class="manual-waiver-upload-box" id="manual_waiver_desktop_upload_box" for="waiver_signed_external_document">
								<span class="manual-waiver-upload-icon">
									<i class="fa-solid fa-cloud-arrow-up"></i>
								</span>
								<span class="manual-waiver-upload-content">
									<strong id="manual_waiver_file_text">Choose file to upload</strong>
									<small>Supported formats: PDF, JPG, PNG. Maximum file size: 5 MB</small>
								</span>
								<span class="manual-waiver-upload-btn">Browse</span>
							</label>

							<div class="manual-waiver-preview" id="manual_waiver_preview" style="display:none;">
								<img id="manual_waiver_preview_image" src="" alt="Selected waiver image">
							</div>

							<input
								type="file"
								name="waiver_signed_external_document"
								id="waiver_signed_external_document"
								class="d-none"
								accept=".pdf,.jpg,.jpeg,.png">
						</div>

						<div class="manual-waiver-confirm-box">
							<div class="form-check mb-2">
								<input class="form-check-input" type="checkbox" name="mark_as_signed" value="1" id="mark_as_signed" required>
								<label class="form-check-label fw-semibold m-0 ms-2" for="mark_as_signed">
									Mark As Signed
								</label>
							</div>
							<p class="mb-0">
								I confirm that a signed paper waiver has been collected from this volunteer.
							</p>
						</div>
					</div>
				</div>

                <div class="modal-footer">
                    <button type="button" class="secondary-btn w-fit" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="primary-btn w-fit btn-submit-manual-waiver-checkin">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
	@push('script')
        <script src="{{ asset('assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
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
			$('.send-attendee-email').click(function () {
				$('#sendAttendeeEmailForm').attr('action', $(this).data('route'));
				$('#sendAttendeeEmailForm')[0].reset();
				$('#description').val('');
			});
			$('.btn-send-custom-email').click(function(e){
				e.preventDefault();
				$(this).text('Sending...').attr('disabled', true);
				$('#sendAttendeeEmailForm').submit();
			});

			/*$('.btn-manual-waiver-checkin').click(function () {
				$('#manualWaiverCheckinForm').attr('action', $(this).data('route'));
				$('#manualWaiverCheckinForm')[0].reset();

				$('#manual_waiver_volunteer_name').text($(this).data('volunteer-name') || 'Volunteer');
				$('#manual_waiver_event_name').text($(this).data('event-name') || 'Event');
				$('#manual_waiver_file_text').text('Choose file to upload');

				$('.btn-submit-manual-waiver-checkin').text('Submit').attr('disabled', false);
			});

			$('#waiver_signed_external_document').change(function () {
				const fileName = this.files && this.files.length ? this.files[0].name : 'Choose file to upload';
				$('#manual_waiver_file_text').text(fileName);
			});*/
			function isMobileWaiverDevice() {
				return window.matchMedia('(max-width: 767px)').matches
					|| /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
			}

			function setManualWaiverUploadMode() {
				if (isMobileWaiverDevice()) {
					$('.manual-waiver-mobile-actions').show();
					$('#manual_waiver_desktop_upload_box').hide();
				} else {
					$('.manual-waiver-mobile-actions').hide();
					$('#manual_waiver_desktop_upload_box').show();

					$('#waiver_signed_external_document')
						.attr('accept', '.pdf,.jpg,.jpeg,.png')
						.removeAttr('capture');
				}
			}

			function resetManualWaiverPreview() {
				$('#manual_waiver_preview').hide();
				$('#manual_waiver_preview_image').attr('src', '');
			}

			$('.btn-manual-waiver-checkin').click(function () {
				$('#manualWaiverCheckinForm').attr('action', $(this).data('route'));
				$('#manualWaiverCheckinForm')[0].reset();

				$('#manual_waiver_volunteer_name').text($(this).data('volunteer-name') || 'Volunteer');
				$('#manual_waiver_event_name').text($(this).data('event-name') || 'Event');
				$('#manual_waiver_file_text').text('Choose file to upload');

				resetManualWaiverPreview();
				setManualWaiverUploadMode();

				$('.btn-submit-manual-waiver-checkin').text('Submit').attr('disabled', false);
			});

			$('#manual_waiver_capture_btn').click(function () {
				$('#waiver_signed_external_document')
					.attr('accept', 'image/*')
					.attr('capture', 'environment')
					.trigger('click');
			});

			$('#manual_waiver_gallery_btn').click(function () {
				$('#waiver_signed_external_document')
					.attr('accept', 'image/*')
					.removeAttr('capture')
					.trigger('click');
			});

			$('#waiver_signed_external_document').change(function () {
				const file = this.files && this.files.length ? this.files[0] : null;

				if (!file) {
					$('#manual_waiver_file_text').text('Choose file to upload');
					resetManualWaiverPreview();
					return;
				}

				$('#manual_waiver_file_text').text(file.name);

				if (file.type && file.type.startsWith('image/')) {
					const reader = new FileReader();

					reader.onload = function (e) {
						$('#manual_waiver_preview_image').attr('src', e.target.result);
						$('#manual_waiver_preview').show();
					};

					reader.readAsDataURL(file);
				} else {
					resetManualWaiverPreview();
				}
			});

			$(window).on('resize', setManualWaiverUploadMode);
		</script>
	@endpush
	@push('style')
	<style>
		.manual-waiver-attendee-box {
			display: flex;
			align-items: center;
			gap: 14px;
			padding: 14px 16px;
			border: 1px solid #e5eaf1;
			border-radius: 8px;
			background: #f8fafc;
		}

		.manual-waiver-avatar {
			width: 44px;
			height: 44px;
			border-radius: 50%;
			background: #e6f1fb;
			color: var(--bs-primary);
			display: flex;
			align-items: center;
			justify-content: center;
			flex: 0 0 auto;
			font-size: 18px;
		}

		.manual-waiver-attendee-name {
			font-size: 16px;
			font-weight: 700;
			color: var(--bs-body-color);
		}

		.manual-waiver-event-name {
			color: #6c757d;
			font-size: 14px;
		}

		.manual-waiver-label {
			font-weight: 600;
			color: var(--bs-body-color);
			margin-bottom: 8px;
		}

		.manual-waiver-upload-box {
			display: flex;
			align-items: center;
			gap: 14px;
			width: 100%;
			padding: 16px;
			border: 1.5px dashed #b8c4d4;
			border-radius: 8px;
			background: #fff;
			cursor: pointer;
			transition: .15s ease;
		}

		.manual-waiver-upload-box:hover {
			border-color: var(--bs-primary);
			background: #f8fbff;
		}

		.manual-waiver-upload-icon {
			width: 42px;
			height: 42px;
			border-radius: 8px;
			background: #eef6ff;
			color: var(--bs-primary);
			display: flex;
			align-items: center;
			justify-content: center;
			flex: 0 0 auto;
			font-size: 18px;
		}

		.manual-waiver-upload-content {
			display: flex;
			flex-direction: column;
			min-width: 0;
			flex: 1;
		}

		.manual-waiver-upload-content strong {
			color: var(--bs-body-color);
			font-weight: 700;
			overflow-wrap: anywhere;
		}

		.manual-waiver-upload-content small {
			color: #6c757d;
			margin-top: 2px;
		}

		.manual-waiver-upload-btn {
			padding: 8px 14px;
			border-radius: 6px;
			background: #eef2f7;
			color: #27364a;
			font-weight: 700;
			font-size: 13px;
			flex: 0 0 auto;
		}

		.manual-waiver-confirm-box {
			padding: 14px 16px;
			border: 1px solid #dce8f7;
			border-radius: 8px;
			background: #f6fbff;
		}

		.manual-waiver-confirm-box p {
			color: #6c757d;
			font-size: 13px;
			line-height: 1.5;
		}

		.manual-waiver-confirm-box .form-check-input:checked {
			background-color: var(--bs-primary) !important;
			border-color: var(--bs-primary) !important;
			outline: var(--bs-primary) !important;
			box-shadow: none !important;
		}

		@media (max-width: 575px) {
			.manual-waiver-upload-box {
				align-items: flex-start;
			}

			.manual-waiver-upload-btn {
				display: none;
			}
		}
	</style>
	<style>
		.contact-list .bottom .icon-box{
			width: 20%!important;
		}
		.border-radius-30{
			border-radius: 30px;
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
	</style>
	<link rel="stylesheet" href="{{ asset('assets_front/css/filter-css.css') }} ">
	<style>
		.resend-opt-card {
			border: 1.5px solid #dee2e6;
			border-radius: 10px;
			padding: 14px 16px;
			cursor: pointer;
			background: #f8f9fa;
			transition: border-color 0.15s, background 0.15s;
			margin-bottom: 10px;
		}
		.resend-opt-card.selected {
			border-color: #185FA5;
			background: #E6F1FB;
		}
		.resend-opt-card:hover:not(.selected) {
			border-color: #adb5bd;
			background: #f1f3f5;
		}
		.resend-radio-dot {
			width: 18px;
			height: 18px;
			border-radius: 50%;
			border: 2px solid #adb5bd;
			flex-shrink: 0;
			margin-top: 2px;
			position: relative;
			transition: border-color 0.15s, background 0.15s;
		}
		.resend-radio-dot.on {
			border-color: #185FA5;
			background: #185FA5;
		}
		.resend-radio-dot.on::after {
			content: '';
			display: block;
			width: 6px;
			height: 6px;
			border-radius: 50%;
			background: white;
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
		}
		.resend-opt-title {
			margin: 0 0 3px;
			font-size: 14px;
			font-weight: 600;
			color: #212529;
		}
		.resend-opt-desc {
			margin: 0 0 10px;
			font-size: 12px;
			color: #6c757d;
			line-height: 1.5;
		}
		.resend-email-badge {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 4px 10px;
			border-radius: 6px;
			background: white;
			border: 1px solid #B5D4F4;
			font-size: 12px;
			font-weight: 500;
			color: #185FA5;
		}
		.resend-email-badge .ti { font-size: 13px; }
		.resend-alt-input {
			font-size: 13px !important;
			transition: opacity 0.15s;
		}
		.resend-alt-input:disabled {
			opacity: 0.55;
			cursor: not-allowed;
			background: #f8f9fa !important;
		}
		.resend-note {
			display: flex;
			align-items: flex-start;
			gap: 8px;
			padding: 10px 12px;
			background: #f0f7ff;
			border-left: 3px solid #378ADD;
			border-radius: 0 6px 6px 0;
			margin-top: 4px;
			font-size: 12px;
			color: #6c757d;
			line-height: 1.6;
		}
		.resend-note-icon {
			font-size: 15px;
			color: #185FA5;
			flex-shrink: 0;
			margin-top: 1px;
		}
		.resend-note-label {
			font-weight: 600;
			color: #212529;
		}

		/* resend last log box */
		.resend-last-log-box {
			margin-top: 12px;
			padding: 12px 14px;
			border: 1px solid #D7E6F5;
			border-radius: 10px;
			background: #fbfdff;
		}
		.resend-last-log-header {
			display: flex;
			align-items: center;
			gap: 8px;
			margin-bottom: 10px;
			font-size: 13px;
			font-weight: 700;
			color: #185FA5;
		}
		.resend-last-log-empty {
			font-size: 12px;
			color: #6c757d;
			background: #f8f9fa;
			border-radius: 6px;
			padding: 9px 10px;
		}
		.resend-last-log-row {
			display: flex;
			justify-content: space-between;
			gap: 12px;
			margin-bottom: 8px;
			font-size: 12px;
		}
		.resend-last-log-label {
			color: #6c757d;
			font-weight: 600;
			min-width: 70px;
		}
		.resend-last-log-value {
			color: #212529;
			font-weight: 600;
			text-align: right;
			word-break: break-word;
		}
		/* resend waiver log table  */
		.resend-waiver-log-table {
	font-size: 13px;
	border-color: #D7E6F5;
}
.resend-waiver-log-table thead th {
	background: #E6F1FB;
	color: #185FA5;
	font-weight: 700;
	border-color: #D7E6F5;
	white-space: nowrap;
}
.resend-waiver-log-table tbody td {
	color: #212529;
	border-color: #E6EEF7;
	vertical-align: middle;
}
.resend-waiver-log-table tbody tr:hover {
	background: #fbfdff;
}
.resend-waiver-log-type {
	display: inline-flex;
	align-items: center;
	padding: 4px 10px;
	border-radius: 6px;
	background: #f0f7ff;
	color: #185FA5;
	font-weight: 600;
	font-size: 12px;
}



.manual-waiver-mobile-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 12px;
}

.manual-waiver-action-btn {
    border: 1px solid #d7e2ef;
    background: #fff;
    color: var(--bs-body-color);
    border-radius: 8px;
    padding: 12px 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.manual-waiver-action-btn i {
    color: var(--bs-primary);
}

.manual-waiver-preview {
    margin-top: 12px;
}

.manual-waiver-preview img {
    width: 88px;
    height: 88px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #d7e2ef;
}
	</style>
	@endpush
@endsection
