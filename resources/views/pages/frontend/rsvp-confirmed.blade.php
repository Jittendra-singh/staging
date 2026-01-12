@php
$outPages = true;
$icalStrData = getiCalData($event);
@endphp
@extends('layouts.frontend')

@section('content')
<!-- Body Start-->
<div class="wrapper" style="background-color: #f5f5f5cf">
	<div class="event-dt-block p-80">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-5 col-lg-7 col-md-10">
					<div class="booking-confirmed-content">
						<div class="main-card">
							<div class="booking-confirmed-top text-center p_30">
								<div class="booking-confirmed-img mt-4">
									<div class="calendar-icon">
										<i class="fas fa-calendar-check"></i>
									</div>
								</div>
								@if(!empty($event->waiver_form_type))
								<h4>Thank You,<br> Waiver signed successfully.</h4>
								@else 
								<h4>Thank You,<br> Status updated successfully.</h4>
								@endif 
								<div class="social-share mt-4 mb-0">
									<span class="share-label">Share this event:</span>
									<div class="social-buttons">
										<a class="social-btn facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('view-event-details', ['slug' => $event->slug])) }}" target="_blank" rel="noopener">
											<i class="fab fa-facebook-f"></i>
											<span>Facebook</span>
										</a>
										<a class="social-btn linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('view-event-details', ['slug' => $event->slug])) }}" target="_blank" rel="noopener">
											<i class="fab fa-linkedin-in"></i>
											<span>LinkedIn</span>
										</a>
										<a class="social-btn linkedin" href="https://twitter.com/intent/tweet?url={{ urlencode(route('view-event-details', ['slug' => $event->slug])) }}" target="_blank" rel="noopener">
											<i class="">𝕏</i>
											<span>X</span>
										</a>
									</div>
								</div>
							</div>
							<div class="booking-confirmed-bottom">
								<div class="booking-confirmed-bottom-bg p_30">
									<div class="event-order-dt" style="margin-bottom: 40px;">
										<div class="event-thumbnail-img">
											<img src="{{$event->formattedBanner()}}" alt="">
										</div>
										<div class="event-order-dt-content">
											<h5>{{$event->event_name}}</h5>
											<div class="booking-total-tickets mb-1">
												<span class="booking-count-tickets mt-0">{{$event->fullAddress}}</span>
											</div>
											<small>
												{{$event->startTimeAndEndTime}}
											</small>
										</div>
									</div>
									<a href="{{route('view-event-details', ['slug' => $event->slug])}}" class="primary-btn mt-0"><i class="fa-solid fa-users me-1"></i>Event Detail Page</a>
									<!-- <a href="{{route('front-cleanup-attending')}}" class="primary-btn mt-2 bg-white text-primary"><i class="fa-solid fa-ticket rotate-icon me-1"></i>View My Ticket</a> -->
									<a href="{{route('add-event-to-calendar',['id'=>encrypt($event->id)])}}" class="secondary-btn w-100 mt-2">
										<svg class="lucide lucide-download" fill="none" height="20" stroke="currentColor"
											stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
											<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
											<polyline points="7 10 12 15 17 10"></polyline>
											<line x1="12" x2="12" y1="15" y2="3"></line>
										</svg>
										Add to Calendar
									</a>
									<!-- <a href="javascript:void(0);" icaldata='{!! htmlspecialchars($icalStrData, ENT_QUOTES, 'UTF-8') !!}' icaltitle="{{$event->event_name}}" onclick="downloadICS(this);" class="secondary-btn w-100 mt-2">
										<svg class="lucide lucide-download" fill="none" height="20" stroke="currentColor"
											stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
											<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
											<polyline points="7 10 12 15 17 10"></polyline>
											<line x1="12" x2="12" y1="15" y2="3"></line>
										</svg>
										Add to Calendar
									</a> -->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Body End-->
@endsection