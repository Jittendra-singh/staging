@extends('layouts.frontend')
@section('content')
    <div class="wrapper wrapper-body">
		<div class="dashboard-body">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12 d-flex align-items-center justify-content-between">
						<div class="d-main-title">
							<h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>Event Registrations List</h3>
							@php						
								$breadcrumbs = [
										[
											'name' => 'Event Registrations List', 
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
						<div class="main-card mt-5">
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
													<!-- Add sorting options here -->
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
										<!-- <div class="col-md-3">
											<div class="rs mt_r4 ps-3">
												<div class="nav custom2-tabs btn-group justify-content-start gap-2 row-gap-3" >
													<button class="primary-btn w-fit" type="submit" >Search</button>
													<a href="{{ url()->current() }}" class="primary-btn w-fit">Reset</a>
													<button class="primary-btn w-fit download-csv" type="button" >
														<i class="fa-solid fa-download "></i>Download CSV
													</button>
												</div>
											</div>
										</div> -->
									</div>
								</form>
							</div>
						</div>
						<div class="d-flex flex-wrap gap-2 my-4">
							<div class="d-flex align-items-center bg-white rounded-3 px-3 py-3 flex-grow-1 border" style="min-width:160px;">
								<span class="me-3 d-flex align-items-center justify-content-center rounded-circle" style="background:#e3f0ff;width:48px;height:48px;">
									<i class="fa-solid fa-users fa-xl text-primary"></i>
								</span>
								<div>
									<div class="fw-bold fs-3">{{$reports['registration']}}</div>
									<div class="small text-muted">Total Registration</div>
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
															<span class="badge custom-badge-warning mt-1">Certificate Requested</span>
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
															@if($ticket->ticket_status)
																<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editRegistraionStatus">
																	<i class="fa-solid fa-gear me-3"></i>Edit Status
																</a>
															@endif

															<a class="dropdown-item registration-notes" 
																data-notes='@json($registration->notes)' data-ticket="{{$registration->id}}"
																data-bs-toggle="modal" data-bs-target="#registrationNotes" >
																<i class="fa-solid fa-note-sticky me-3" ></i> Notes
															</a>
															<!-- <a href="{{ route('front-registration-calendar',['slug'=>$event->slug]) }}" class="dropdown-item">
																<i class="fa-solid fa-envelope me-3"></i>Download iCal
															</a> -->
															<a class="dropdown-item" data-event='@json($event)' data-volunteer='@json($volunteer)' 
															data-ticket='@json($ticket)' data-registration='@json($registration)' data-order='@json($order)'  
																data-bs-toggle="modal" data-bs-target="#registrationDetails">
																<i class="fa-solid fa-info-circle me-3"></i>Details
															</a>
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
														<p>Ticket / Registration Date</p>
														<h6 class="coupon-status">{{$ticket->ticket_name}} - {{$ticket->ticket_price ? '$'.$ticket->ticket_price : 'Free'}} <br/>{{$registration->createdDateTime}}</h6>
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
															<span class="badge custom-badge-danger">Cancelled</span>
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
															<a href="{{ route('view-waiver-form', ['ticket' => encrypt($registration->id)]) }}" data-bs-toggle="tooltip" data-bs-original-title="Waiver Page Link" target="_blank"><span class="badge custom-badge-warning"><i class="fa fa-external-link"></i></span></a><br/>
															<button class="btn btn-sm btn-outline-primary mt-2 show-alert-run-url border-radius-30" url="{{ route('resend-waiver', ['ticket' => encrypt($registration->id)]) }}" message="You want to resend email for waiver">Resend</button>
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
																@if(empty($registration->waiver_filled_at) || $registration->rsvp_confirmation!=1)
																<label class="btn-switch m-0 me-3">
																	<input type="checkbox" disabled {{ $registration->checkin == 1 ? 'checked' : '' }} class="ticket_checkin_toggle">
																	<span class="checkbox-slider" url="" onclick="redirectWithMessage(this)" message="{{$registration->rsvp_confirmation!=1?'You can\'t check-in before RSVP confirmation.':'You can\'t check-in before waiver signing.'}}"></span>
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
												<div class="card-top event-top p-4 align-items-center top d-md-flex flex-wrap justify-content-between">
													<h4 class="text-center">New registrations will appear here once volunteers sign up for your events.</h4>
												</div>
											</div>
										</div>
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
		.border-radius-30{
			border-radius: 30px;
		}
	</style>
	@endstyle
@endsection