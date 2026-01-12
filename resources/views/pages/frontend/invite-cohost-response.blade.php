@php
$outPages = true;
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
                                @if($invitation_response==true)
								<h4>You accepted the invitation successfully.</h4>
                                @else
								<h4>You declined the invitation successfully.</h4>
                                @endif
							</div>
                            @if(isset($event) && isset($event->id))
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
									@if(isset($event->slug) && !empty($event->slug))
									<a href="{{route('view-event-details', ['slug' => $event->slug])}}" class="primary-btn mt-0"><i class="fa-solid fa-users me-1"></i>Event Detail Page</a>
										@if($invitation_response==true)
										<a href="{{route('front-post-a-event', ['id' => encrypt($event->id)])}}" class="primary-btn mt-2"><i class="fa-solid fa-users me-1"></i>Event Detail Page</a>
										@endif
									@endif
								</div>
							</div>
                            @endif 
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Body End-->
@endsection