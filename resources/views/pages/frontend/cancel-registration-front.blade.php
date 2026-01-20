@php
$outPages = true;
@endphp
@extends('layouts.frontend')
@push('style')
<style>
    .danger-btn {
        background: #dc3545 !important;
        border-color: #dc3545 !important;
    }
    .danger-btn:hover {
        background: white !important;
        color: #dc3545 !important;
    }
</style>
@endpush
@section('content')
<!-- Body Start-->
<div class="wrapper" style="background-color: #f5f5f5cf">
	<div class="event-dt-block p-80">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xl-5 col-lg-7 col-md-10">
					<div class="booking-confirmed-content">
						<div class="main-card">
                            @if($registration->rsvp_confirmation==1)
                                <div class="booking-confirmed-top p_30 pb-0">
                                    <h4>Confirm Registration Cancellation</h4>
                                    <p>Please review the details below before processing with your cancellation.</p>
                                    <x-reusables.frontend.alert />
                                </div>
                                <!-- <div class="booking-confirmed-top text-center p_30">
                                    <div class="booking-confirmed-img mt-4">
                                        <div class="calendar-icon">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                    </div>
                                    <h4>Are you sure, you want to unsubscribe this alert?</h4>
                                    @if(isset($alert) && isset($alert->id))
                                    <div class="social-share mt-4 mb-0">
                                        <span class="share-label">Alert Name: {{ $alert->alert_name }}</span>
                                        <span class="share-label mb-0">Pincode: {{ $alert->zipcode }}</span>
                                    </div>
                                    @endif 
                                </div> -->
                                <div class="booking-confirmed-bottom">
                                    <div class="booking-confirmed-bottom-bg p_30 m-4 mb-3">
                                        <div class="event-order-dt">
                                            <div class="event-thumbnail-img">
                                                <img src="{{ $event->formattedBanner() }}" alt="{{$event->event_name}}">
                                            </div>
                                            <div class="event-order-dt-content">
                                                <h6 class="text-primary">EVENT DETAILS</h6>
                                                <h5>{{$event->event_name}}</h5>
                                            </div>
                                        </div>
                                        <div class="event-order-dt mt-3">
                                            <div class="event-order-dt-content m-0">
                                                <div class="booking-total-tickets mb-2">
                                                    <i class="fa-solid fa-user me-1"></i>
                                                    Volunteer: {{$registration->volunteer->first_name}} {{$registration->volunteer->last_name}}
                                                </div>
                                                <div class="booking-total-tickets mb-2">
                                                    <i class="fa-solid fa-calendar me-1"></i>
                                                    {{$event->startTimeAndEndTime}}
                                                </div>
                                                <div class="booking-total-tickets">
                                                    <i class="fa-solid fa-map-marker me-1"></i>
                                                    {{$event->full_address}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="booking-confirmed-top mx-4 mb-4">
                                    @if(strtotime($event->start_datetime) > time() && $registration->rsvp_confirmation==1 && $registration->checkin==0)
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
                                        $refund_type = 2;
                                        @endphp 
                                        @if($registration->ticket->ticket_type=='paid')
                                            @if($event->allow_refund==1)
                                                @if($is_48hours_exeed!=1)
                                                    @php 
                                                        $refund_type = 1;
                                                    @endphp 
                                                @else 
                                                @endif 
                                            @else 
                                            @endif 
                                        @else 
                                        @endif 
                                        <div class="alert alert-danger mb-3" role="alert">
                                            <i class="fa fa-exclamation-triangle me-1"></i>
                                            <b>This action cannot be undone.</b><br/>
                                            Are you sure you want to cancel this registration? You will lose your spot in this event.
                                        </div>
                                        <div class="d-flex gap-2 mt-4">
                                            <a href="{{route('view-events')}}" style="font-size: 12px;" class="primary-btn bg-white text-primary w-100">Back to Events Page</a>
                                            <a href="{{route('confirm-cancel-registration-front',[encrypt($registration->id),$refund_type])}}" style="font-size: 12px;" class="primary-btn danger-btn w-100">Confirm Cancellation</a>
                                        </div>
                                    @else 
                                    <div class="alert alert-danger mb-3" role="alert">
                                        <i class="fa fa-exclamation-triangle me-1"></i>
                                        <b>You cannot cancel this registration.</b><br/>
                                        @if(strtotime($event->start_datetime) <= time())
                                        The event has ended, so this registration cannot be cancelled.
                                        @elseif($registration->rsvp_confirmation!=1)
                                        The registration has already been cancelled.
                                        @elseif($registration->checkin==1)
                                        The volunteer has already been checked in, so this registration cannot be cancelled.
                                        @else 
                                        Cancellation is not allowed.
                                        @endif  
                                    </div>
                                    @endif
                                </div>
                            @else
                                <div class="booking-confirmed-top text-center p_30">
                                    <div class="booking-confirmed-img mt-4">
                                        <div class="calendar-icon">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                    </div>
                                    <h4>This registration has been successfully cancelled.</h4>
                                    <p>A confirmation email has been sent. Any applicable refunds will be processed automatically.</p>
                                </div>
                                <div class="booking-confirmed-top text-center p_30 pt-0">
                                        <a href="{{config('app.frontsite')}}" class="primary-btn w-100 mb-2">Return to VolunteerCleanup.org</a>
                                        <a href="{{route('view-events')}}" class="primary-btn bg-white text-primary w-100">Browse Upcoming Cleanups</a>
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