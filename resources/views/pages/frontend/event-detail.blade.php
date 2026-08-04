{{-- https://volunteercleanup.org/event/south-beach-cleanup-10th-ocean-lets-make-waves-for-a-cleaner-shore/ --}}
@php
$outPages = true;
$ticket_session = session()->get('ticket_booking', []);
/*$icalData = [
'event_name'=>$event->event_name,
//'event_description'=>$event->description,
'event_address'=>$event->venue.' '.$event->pincode,
'event_start_datetime'=>$event->start_datetime->format('Ymd\THis\Z'),
'event_end_datetime'=>$event->end_datetime->format('Ymd\THis\Z'),
];*/
$icalStrData = getiCalData($event);
@endphp
@extends('layouts.frontend')
@section('content')
<!-- Body Start-->
<style>
.passcodewidth{
    width:80px!important;
}    
.parent-price-item{
    background: #f8fafc;
    border-radius: 10px;
}
.price-item:hover {
  background: inherit !important;
  box-shadow: none !important;
  transform: none !important;
}
</style>
<div class="wrapper event-detail" style="background-color: #f5f5f5cf">
    <main class="main">
        <div class="container">
            <div class="row mt-4">
                <div class="col-sm-12 mb-4">
                    <a href="{{route('view-events')}}" class="back-link fw-bold">Events</a>
                    <span class="fw-bold px-2">/</span>
                    <b><a href="{{route('view-events')}}" class="back-link text-secondary fw-bold">{{$event->event_name}}</a></b> 

                    @if(auth()->guard('front')->user() && auth()->guard('front')->user()->id == $event->creator_id)
                    <a href="{{route('front-post-a-event', encrypt($event->id))}}" class="primary-btn float-end">Edit Event</a>
                    @endif 
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-sm-8 py-0 content-description">
                    <!-- <img src="{{ $event->formattedBanner() }}" alt="{{$event->event_name}}" class="w-100 fade-in" style="height: 400px;object-fit: cover; border-radius: 15px; border-radius: 5px;"> -->
                    <img src="{{ $event->formattedBanner() }}" alt="{{$event->event_name}}" class="w-100 fade-in" style="height: 400px;object-fit: cover; border-radius: 15px; border-radius: 5px; width:100%; height:auto; display:block; max-height:80vh; object-fit:contain;">
                    <h1 class="fw-bold mt-4">{{$event->event_name}}</h1>
                    <p>{!! $event->description !!}</p>
                    <div class="row">
                        @if($event->bringTools->count() || $event->providedTools->count() || $event->parking_info_details || $event->meeting_spot_details)
                            @if($event->bringTools->count() || $event->providedTools->count())
                            <div class="col-sm-12 mb-4">
                                <div class="card shadow-sm rounded-4 border-0">
                                    <div class="card-body p-4">
                                        <div class="row">
                                            @if($event->bringTools->count())
                                            <div class="col-sm-12 col-md-12 col-lg-6 d-flex align-items-stretch">
                                                <div class="info--item">
                                                    <h3>
                                                        <svg class="h-6 w-6 text-[var(--primary-600)]" fill="none" stroke="currentColor"
                                                            stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M6 6v12a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V6" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                            <path d="M17.5 6.5a2.5 2.5 0 1 1-5 0" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </svg>
                                                        What Should Volunteers Bring?
                                                    </h3>
                                                    <ul>
                                                        @foreach($event->bringTools as $tool)
                                                        <li>{{$tool->name}}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                            @endif
                                            @if($event->providedTools->count())
                                            <div class="col-sm-12 col-md-12 col-lg-6 d-flex align-items-stretch"">
                                                <div class=" info--item">
                                                    <h3>
                                                        <svg class="h-6 w-6 text-[var(--primary-600)]" fill="none" stroke="currentColor"
                                                            stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="m12 2 7 7-7 7-7-7 7-7Z" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                            <path d="M2.2 12h19.6" stroke-linecap="round" stroke-linejoin="round">
                                                            </path>
                                                            <path d="m12 22 7-7-7-7-7 7 7 7Z" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </svg>
                                                        What Will Be Provided?
                                                    </h3>
                                                    <ul>
                                                        @foreach($event->providedTools as $tool)
                                                        <li>{{$tool->name}}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($event->parking_info_details || $event->meeting_spot_details)
                            <div class="col-sm-12 mb-4">
                                <div class="card shadow-sm rounded-4 border-0">
                                    <div class="card-body p-4">
                                        <div class="row">
                                            @if($event->parking_info_details)
                                            <div class="col-sm-12 col-md-12 col-lg-6 d-flex align-items-stretch">
                                                <div class="info--item w-100">
                                                    <h3>
                                                        <svg class="h-6 w-6 text-[var(--primary-600)]" fill="none" stroke="currentColor"
                                                            stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M10 10H8v4h2"></path>
                                                            <path d="M16 12h-4a2 2 0 1 0 0-4h4v4"></path>
                                                            <circle cx="12" cy="12" r="10"></circle>
                                                        </svg>
                                                        Parking Information
                                                    </h3>
                                                    <span>{!! ($event->parking_info_details) !!}</span>
                                                </div>
                                            </div>
                                            @endif
                                            @if($event->meeting_spot_details)
                                            <div class="col-sm-12 col-md-12 col-lg-6 d-flex align-items-stretch">
                                                <div class="info--item w-100">
                                                    <h3>
                                                        <svg class="h-6 w-6 text-[var(--primary-600)]" fill="none" stroke="currentColor"
                                                            stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M21.05 10.05a9 9 0 0 0-18.1 0" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                            <path d="m12 19-7-7" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            <path d="m12 19 7-7" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                        Meeting Spot Details
                                                    </h3>
                                                    <span>{!! ($event->meeting_spot_details) !!}</span>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endif
                        <div class="col-sm-12">
                            <div class="card shadow-sm rounded-4 border-0">
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-12 d-flex align-items-stretch">
                                            <div class="info--item w-100">
                                                <h3>
                                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M6 2c-1.10457 0-2 .89543-2 2v4c0 .55228.44772 1 1 1s1-.44772 1-1V4h12v7h-2c-.5523 0-1 .4477-1 1v2h-1c-.5523 0-1 .4477-1 1s.4477 1 1 1h5c.5523 0 1-.4477 1-1V3.85714C20 2.98529 19.3667 2 18.268 2H6Z"/>
                                                    <path d="M6 11.5C6 9.567 7.567 8 9.5 8S13 9.567 13 11.5 11.433 15 9.5 15 6 13.433 6 11.5ZM4 20c0-2.2091 1.79086-4 4-4h3c2.2091 0 4 1.7909 4 4 0 1.1046-.8954 2-2 2H6c-1.10457 0-2-.8954-2-2Z"/>
                                                    </svg>
                                                   Organizer Information
                                                </h3>
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-4 row">
                                                    <div class="d-flex align-items-center gap-3 flex-wrap gap-2">
                                                        <img src="{{ @$event->creator->formattedImage('organisation_logo') }}" alt="logo"
                                                            style="width: 128px;height: 128px;border-radius: 100px;" />
                                                        <div class="col-6">
                                                            <h2 class="section-title m-0 text-dark">{{ @$event->creator->organisation_name }}</h2>
                                                            <!-- <h2 class="section-title m-0 text-dark">Test user</h2> -->
                                                            <!-- <p class="description m-0 font-bold">{{ @$event->creator->name }}, {{ @$event->creator->email_address }}</p> -->
                                                            <p class="description m-0 font-bold">{{ @$event->creator->name }}</p>


                                                            <div class="social-links mt-2">
                                                                @if (@$event->creator->organisation_website)
                                                                    <a href="{{ @$event->creator->organisation_website }}" target="_BLANK"
                                                                        class="organizer-link">
                                                                        <i class="fa fa-link"></i>
                                                                    </a>
                                                                @endif
                                                                @if (@$event->creator->organisation_instagram)
                                                                    <a href="{{ @$event->creator->organisation_instagram }}" target="_BLANK"
                                                                        class="organizer-link">
                                                                        <i class="fab fa-instagram"></i>
                                                                    </a>
                                                                @endif
                                                                @if (@$event->creator->organisation_facebook)
                                                                    <a href="{{ @$event->creator->organisation_facebook }}" target="_BLANK"
                                                                        class="organizer-link">
                                                                        <i class="fab fa-facebook-square"></i>
                                                                    </a>
                                                                @endif
                                                                @if (@$event->creator->organisation_linkedin)
                                                                    <a href="{{ @$event->creator->organisation_linkedin }}" target="_BLANK"
                                                                        class="organizer-link">
                                                                        <i class="fab fa-linkedin-in"></i>
                                                                    </a>
                                                                @endif
                                                                @if (@$event->creator->organisation_twitter)
                                                                    <a href="{{ @$event->creator->organisation_twitter }}" target="_BLANK"
                                                                        class="organizer-link">
                                                                        <i class="fab"> 𝕏</i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="organizer-actions">
                                                                <a href="{{route('organizer-detail', ['slug' => @$event->creator->organisation_landing_slug])}}" class="primary-btn w-100">
                                                                    Visit Profile
                                                                </a>
                                                                <button class="secondary-btn w-100" data-bs-toggle="modal" data-bs-target="#contactOrgraniser">
                                                                    Contact
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="col-sm-4">
                <div class="event-sidebar">
                    <div class="sidebar-card shadow-sm">
                        <!-- <div class="d-flex flex-column align-items-start gap-2 mb-3">
                            <a href="#" class="primary-btn w-100">Register Now</a>
                            <a href="#" class="secondary-btn w-100">
                                <svg class="lucide lucide-download" fill="none" height="20" stroke="currentColor"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" x2="12" y1="15" y2="3"></line>
                                </svg>
                                Download Event iCal
                            </a>
                        </div>

                        <hr /> -->

                        <h3>Event Details</h3>

                        <div class="event-details-item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                                <path d="M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M8 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>

                            <div class="detail-content">
                                <b class="text-secondary">Start Date</b>
                                <p class="text-body-tertiary">{{$event->eventStartDateTime}}</p>
                            </div>
                        </div>
                        <div class="event-details-item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                                <path d="M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M8 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>

                            <div class="detail-content">
                                <b class="text-secondary">End Date</b>
                                <p class="text-body-tertiary">{{$event->eventEndDateTime}}</p>
                            </div>
                        </div>
                        <div class="event-details-item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C8.13401 2 5 5.13401 5 9C5 13.25 12 22 12 22C12 22 19 13.25 19 9C19 5.13401 15.866 2 12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="12" cy="9" r="2.5" fill="currentColor" />
                            </svg>

                            <div class="detail-content">
                                <b class="text-secondary">Location</b>
                                <p class="text-body-tertiary">{{$event->fullAddress}}</p>
                            </div>
                        </div>
                        <div class="event-details-item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 4H9C9.53043 4 10.0391 4.21071 10.4142 4.58579L20 14.1716C20.781 14.9526 20.781 16.219 20 17L17 20C16.219 20.781 14.9526 20.781 14.1716 20L4.58579 10.4142C4.21071 10.0391 4 9.53043 4 9V4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="7.5" cy="7.5" r="2" fill="currentColor" />
                            </svg>

                            <div class="detail-content">
                                <b class="text-secondary">Category</b>
                                <p class="text-body-tertiary mb-0">{{@$event->category->name}}</p>
                            </div>
                        </div>
                        
                        @php
                        if(!empty($event->creator->stripe_email) && !empty($event->creator->stripe_account_id)){
                            $evenTickets = $event->eventActiveTickets;
                            $eventAllTickets = $event->eventTickets()->where('is_active',1)->get();
                        } else {
                            $evenTickets = $event->eventActiveFreeTickets;
                            $eventAllTickets = $event->eventTickets()->where('ticket_type' , 'free')->where('is_active',1)->get();
                        }
                        
                        $totalTicketCount = $evenTickets->count();
                        $allTicketCount = $eventAllTickets->count();
                        $isEventEnded = false;
                        $ticketIds = $eventAllTickets->pluck('id')->toArray();
                        if(strtotime($event->end_datetime) < strtotime(date('Y-m-d H:i:s'))){
                            $totalTicketCount = 0;
                            $allTicketCount = 0;
                            $isEventEnded = true;
                        }
                        if($event->participation_type!=1){
                            $totalTicketCount = 0;
                            $allTicketCount = 0;
                        }
                        if(!empty($event->cancelled_at)){
                            $totalTicketCount = 0;
                            $allTicketCount = 0;
                        }
                        @endphp 
                        <!-- if($event->sellTicketBooking() && $event->eventTickets->count() > 0) -->
                        @if($allTicketCount > 0)
                        @else 
                        <a href="{{route('add-event-to-calendar',['id'=>encrypt($event->id)])}}"  class="secondary-btn w-100 my-4">
                            <svg class="lucide lucide-download" fill="none" height="20" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" x2="12" y1="15" y2="3"></line>
                            </svg>
                            Download Event iCal
                        </a>
                        <!-- <a href="javascript:void(0);" icaldata='{!! htmlspecialchars($icalStrData, ENT_QUOTES, 'UTF-8') !!}' icaltitle="{{$event->event_name}}" onclick="downloadICS(this);" class="secondary-btn w-100 my-4">
                            <svg class="lucide lucide-download" fill="none" height="20" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" x2="12" y1="15" y2="3"></line>
                            </svg>
                            Download Event iCal
                        </a> -->
                        @endif

                        <hr />

                        {{-- Apply Ticket Conditions --}}
                        <!-- if($event->sellTicketBooking() && $event->eventTickets->count() > 0) -->
                        <x-reusables.frontend.alert />
                        @if($allTicketCount>0)
                        <form action="{{route('land-to-checkout')}}" method="post">
                            @csrf
                            <input type="hidden" name="event" value="{{$event->encrypted_id}}">
                            <div class="">
                                <div class="pricing-section">
                                    <h3>Ticket Information</h3>
                                    @foreach ($eventAllTickets as $eticket) <!-- evenTickets -->
                                    @php
                                    $session_ticket = collect($ticket_session)->firstWhere('id', $eticket->id);
                                    $booked_count = $session_ticket ? $session_ticket['count'] : 0;
                                    //$soldTickets = $eticket->soldTickets->count();
                                    $soldTickets = $eticket->approvedSold->count();
                                    $allAvailTickets = $eticket->available_quantity;
                                    $curAvailTickets = $allAvailTickets-$soldTickets;
                                    $allowedPerOrder = $eticket->max_qty_allowed_per_order;
                                    if((empty($event->creator->stripe_email) || empty($event->creator->stripe_account_id)) && $eticket->ticket_type=='paid'){
                                        continue;
                                    }
                                    $upcomingDate = '';
                                    if(strtotime($eticket->sell_start_time) > strtotime(date('Y-m-d'))){
                                        $upcomingDate = \Carbon\Carbon::parse($eticket->sell_start_time)->format(config('app.dateFormat'));
                                    }
                                    @endphp
                                    <div class="parent-price-item mb-3 pb-3 ">
                                        <div class="col-sm-12 px-3 pt-3">
                                            <div class="row">
                                                <div class="col-sm-9">
                                                    <p class="m-0"><b>{{ $eticket->ticket_name }}</b></p>
                                                </div>
                                                <div class="col-sm-3 ps-0 text-end">
                                                    <h5 class="m-0"><b>{{ $eticket->payable_price ? '$'.formatAmt($eticket->payable_price) : 'Free'}}</b></h5>
                                                </div>
                                            </div>
                                        </div>
                                        @if($event->show_ticket_description==1 && !empty($eticket->ticket_description))
                                        <div class="col-sm-12 px-3 mt-1">
                                            <small>{{ $eticket->ticket_description }}</small>
                                        </div>
                                        @endif 
                                        <div class="price-item gap-4 mb-0 pb-0">
                                            <div class="price-div">
                                                <!-- <div class="price">
                                                    {{ $eticket->payable_price ? '$'.$eticket->payable_price : 'Free'}}
                                                </div> -->
                                                @if(!empty($upcomingDate))
                                                <span class="price-label">
                                                    <span class="badge badge-primary bg-primary"><span>Available from {{ $upcomingDate }}</span></span>
                                                </span>
                                                @elseif($event->show_remaining_tickets)
                                                <span class="price-label">
                                                    <span class="badge badge-primary bg-primary"><span id="availTicketCount_{{ $eticket->id }}">{{ $curAvailTickets-$booked_count }}</span> tickets available</span>
                                                </span>
                                                @endif
                                                <!-- <div class="price-label">{{$eticket->ticket_name}}</div> -->
                                            </div>
                                            @if(empty($upcomingDate))
                                            @if($event->sell_tickets_individually)
                                            <div class=" w-auto">
                                                <label for="order_ticket_{{$eticket->id}}">
                                                    <input id="order_ticket_{{$eticket->id}}" class="checkout-ticket-checkbox"
                                                        onchange="$(this).is(':checked') ? $('.checkout-ticket-checkbox').not(this).prop('checked', false): true"
                                                        name="order_ticket[{{$eticket->id}}]" value="1" type="checkbox" @checked($booked_count)>
                                                    Book Ticket
                                                </label>
                                            </div>
                                            @else
                                                @if(!empty($eticket->pass_code))
                                                    <div class="text-center" id="passcode_div_{{ $eticket->id }}">
                                                        <div class="quantity-control">
                                                            <input placeholder="Pass Code" type="text" class="qty passcodewidth" name="passcode[{{$eticket->id}}]" value="">
                                                            <button url="{{ route('verify-ticket-passcode',['ticket'=>encrypt($eticket->id)]) }}" ticketid="{{ $eticket->id }}" type="button" class="qty-btn plus ms-auto verify_ticket_passcode_btn"><i class="fa fa-check"></i></button>
                                                        </div>
                                                        <small>Verify Pass Code</small>
                                                    </div>
                                                @endif
                                                <div class="text-center" id="ticket_div_{{ $eticket->id }}" style="{{ !empty($eticket->pass_code)?'display:none;':'' }}">
                                                    <div class="quantity-control">
                                                        <button type="button" ticketid="{{ $eticket->id }}" class="qty-btn minus" data-max="{{ $curAvailTickets }}" data-allowed-qty="{{ $allowedPerOrder }}" onClick='decreaseCount(event, this)'>-</button>
                                                        <input type="text" class="qty" name="order_ticket[{{$eticket->id}}]" value="{{$booked_count}}">
                                                        <button type="button" class="qty-btn plus" data-max="{{ $curAvailTickets }}" data-allowed-qty="{{ $allowedPerOrder }}" ticketid="{{ $eticket->id }}" onClick='increaseCount(event, this)'>+</button>
                                                    </div>
                                                    <!-- @if($event->show_remaining_tickets)
                                                    <small ><span id="availTicketCount_{{ $eticket->id }}">{{ $curAvailTickets-$booked_count }}</span> tickets available</small>
                                                    @endif -->
                                                </div>
                                            @endif
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach

                                    @if(!empty($upcomingDate) && $totalTicketCount <=0)
                                        @if(auth()->guard('front')->user() && isset(auth()->guard('front')->user()->id))
                                            @php 
                                            
                                            $now = \Carbon\Carbon::now();
                                            $upcomingTickets = $eventAllTickets->filter(function ($ticket) use ($now) {
                                                return $ticket->sell_start_time > $now;
                                            });
                                            $nextTicket = $upcomingTickets->sortBy('sell_start_time')->first();

                                            $ticketNotifications = auth()->guard('front')->user()->ticketNotifications()->where('is_sent', 0)->get();
                                            @endphp 
                                            @if($ticketNotifications->contains('ticket_id', $nextTicket->id))
                                                <p class="m-0"><i style="font-style: italic;" class="text-primary">An email notification will be sent once tickets are available.</i></p>
                                            @else
                                                <a class="primary-btn w-100" href="{{route('save-ticket-notify', encrypt($nextTicket->id))}}">
                                                    Notify Me for upcoming ticket
                                                </a>
                                            @endif 
                                        @else 
                                        <a href="{{ route('front-login',['redirect_to'=>request()->fullUrl()]) }}" class="primary-btn w-100">
                                            Notify Me for upcoming ticket
                                        </a>
                                        @endif 
                                    @else 
                                    <button class="primary-btn w-100" id="proceed_checkout_btn" type="submit">
                                        Get Tickets
                                    </button>
                                    @endif
                                    <a href="{{route('add-event-to-calendar',['id'=>encrypt($event->id)])}}" class="secondary-btn w-100 my-4">
                                        <svg class="lucide lucide-download" fill="none" height="20" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" x2="12" y1="15" y2="3"></line>
                                        </svg>
                                        Download Event iCal
                                    </a>
                                    <!-- <a href="javascript:void(0);" icaldata='{!! htmlspecialchars($icalStrData, ENT_QUOTES, 'UTF-8') !!}' icaltitle="{{$event->event_name}}" onclick="downloadICS(this);" class="secondary-btn w-100 my-4">
                                        <svg class="lucide lucide-download" fill="none" height="20" stroke="currentColor"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" x2="12" y1="15" y2="3"></line>
                                        </svg>
                                        Download Event iCal
                                    </a> -->
                                </div>
                            </div>
                        </form>
                        <hr />
                        @elseif(!empty($event->cancelled_at))
                            <div class="">
                                <div class="pricing-section">
                                    <h3>Ticket Information</h3>
                                    <i style="font-style: italic;">This event has been cancelled. Registration is closed.</i>
                                </div>
                            </div>
                        <hr />
                        @elseif($event->participation_type==1 && !$isEventEnded)
                            @php 
                            $now = \Carbon\Carbon::now();
                            $upcomingTickets = $eventAllTickets->filter(function ($ticket) use ($now) {
                                return $ticket->sell_start_time > $now;
                            });
                            $nextTicket = $upcomingTickets->sortBy('sell_start_time')->first();
                            @endphp 

                            @if($eventAllTickets->count() > 0)
                                @if($upcomingTickets->count() > 0)
                                <div class="">
                                    <div class="pricing-section">
                                        <h3>Ticket Information</h3>
                                        <i style="font-style: italic;">Ticket will be available starting {{ \Carbon\Carbon::parse($nextTicket->sell_start_time)->format(config('app.dateFormat')) }}</i>
                                        
                                        @if(auth()->guard('front')->user() && isset(auth()->guard('front')->user()->id))
                                        @php 
                                        $ticketNotifications = auth()->guard('front')->user()->ticketNotifications()->where('is_sent', 0)->get();
                                        @endphp 
                                        @if($ticketNotifications->contains('ticket_id', $nextTicket->id))
                                            <p class="m-0"><i style="font-style: italic;" class="text-primary">You will be notified via email.</i></p>
                                        @else
                                            <a href="{{route('save-ticket-notify', encrypt($nextTicket->id))}}" class="primary-btn btn btn-primary px-4 mt-3"><b>Notify Me</b></a>
                                        @endif 
                                        @endif 
                                    </div>
                                </div>
                                <hr />
                                @else 
                                <div class="">
                                    <div class="pricing-section">
                                        <h3>Ticket Information</h3>
                                        <i style="font-style: italic;">Ticket booking for this event is now closed.</i>
                                    </div>
                                </div>
                                <hr />
                                @endif
                            @else 
                                <div class="">
                                    <div class="pricing-section">
                                        <h3>Ticket Information</h3>
                                        <i style="font-style: italic;">Note: No tickets available for booking at this time.</i>
                                    </div>
                                </div>
                            <hr />
                            @endif
                        @elseif($event->participation_type==2)
                            <div class="">
                                <div class="pricing-section">
                                    <h3>Ticket Information</h3>
                                    <i style="font-style: italic;">No registration required. Just show up.</i>
                                </div>
                            </div>
                        <hr />
                        @elseif($event->participation_type==3)
                            <div class="">
                                <div class="pricing-section">
                                    <h3>Ticket Information</h3>
                                    <i style="font-style: italic;">This event is for informational purposes only.</i>
                                </div>
                            </div>
                        <hr />
                        @elseif($event->participation_type==4 && !empty($event->participation_type_external_link) && !$isEventEnded)
                            <div class="">
                                <div class="pricing-section">
                                    <h3>Ticket Information</h3>
                                    <a href="{{ $event->participation_type_external_link }}" class="primary-btn w-100" target="_blank">Register Here</a>
                                </div>
                            </div>
                        <hr />
                        @elseif($isEventEnded)
                            <div class="">
                                <div class="pricing-section">
                                    <h3>Ticket Information</h3>
                                    <i style="font-style: italic;">This event has ended.</i>
                                </div>
                            </div>
                        <hr />
                        @else
                            <div class="">
                                <div class="pricing-section">
                                    <h3>Ticket Information</h3>
                                    <i style="font-style: italic;">Note: No tickets available for booking at this time.</i>
                                </div>
                            </div>
                        <hr />
                        <!-- elseif(!$event->sellTicketBooking() && $event->eventTickets->count() > 0)
                            <div class="">
                                <div class="pricing-section">
                                    <h3>Ticket Information</h3>
                                    @if(strtotime($event->ticket_sell_end_time)<strtotime(date('Y-m-d')))
                                    <i style="font-style: italic;">Note: Ticket booking period has ended.</i>
                                    @elseif(strtotime($event->ticket_sell_start_time)>strtotime(date('Y-m-d')))
                                    <i style="font-style: italic;">Note: Ticket booking will be available from {{ date(config('app.dateFormat'),strtotime($event->ticket_sell_start_time)) }}.</i>
                                    @endif 
                                </div>
                            </div>
                        <hr /> -->
                        @endif

                        <h3>Event Location</h3>
                        <div class="map-container">
                            <iframe
                                src="https://www.google.com/maps?q={{ urlencode($event->fullAddress) }}&output=embed"
                                style="border:0; width: 100%; min-height: 300px;" allowfullscreen="false"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>

                </div>
            </div>
        </div>
</div>


<div class="container d-none">
    <div class="content-grid">
        <!-- Event Content -->
        <div class="event-content">
            <div class="event-header">
                <div class="date-badge">
                    <span class="month">{{$event->start_datetime->format('F')}}</span>
                    <span class="day">{{$event->start_datetime->format('d')}}</span>
                </div>
                <div class="event-title-section">
                    <h1 class="event-title">{{$event->event_name}}</h1>
                    <div class="event-meta">
                        <span>Published by</span>
                        <strong>{{@$event->creator->name}}</strong>
                        <span class="free-badge">{{$event->click_views}} Views</span>
                    </div>
                </div>
            </div>

            <div class="position-relative">
                <div class="event-image">

                    <div class="image-overlay">
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="sv-btn" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-share"></i>Share
                    </button>
                    <ul class="dropdown-menu" style="z-index:99999999999">
                        <li><a class="dropdown-item" target="_BLANK" href="https://www.facebook.com/sharer/sharer.php?u={{ route('view-event-details', ['slug' => urlencode($event->slug)]) }}"><i class="fa-brands fa-facebook me-3"></i>Facebook</a></li>
                        <li><a class="dropdown-item" target="_BLANK" href="https://twitter.com/share?text={{ urlencode($event->event_name) }}&url={{ route('view-event-details', ['slug' => urlencode($event->slug)]) }}"><i class="fa-brands fa-twitter me-3"></i>Twitter</a></li>
                        <li><a class="dropdown-item" target="_BLANK" href="https://www.linkedin.com/sharing/share-offsite/?url={{ route('view-event-details', ['slug' => urlencode($event->slug)]) }}"><i class="fa-brands fa-linkedin-in me-3"></i>LinkedIn</a></li>
                        <li><a class="dropdown-item" target="_BLANK" href="mailto:?subject={{ urlencode($event->event_name) }}&body={{ route('view-event-details', ['slug' => urlencode($event->slug)]) }}"><i class="fa-regular fa-envelope me-3"></i>Email</a></li>
                    </ul>
                </div>
            </div>

            <div class="event-description">
                <h2>About This Event</h2>
                <div class="fade-in-text">
                    {!! ($event->description) !!}
                </div>

                @if($event->tags->count())
                <div class="event-highlights">
                    @foreach ($event->tags as $tag)
                    <div class="highlight-item">
                        <i class="fas fa-award"></i>
                        <span>{{$tag->name}}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="row row-gap-4 additional-info">
                @if($event->bringTools->count())
                <div class="col-sm-12 col-md-12 col-lg-6 d-flex align-items-stretch"">
                                    <div class=" info-item">
                    <h3>What Should Volunteers Bring?</h3>
                    <div>
                        <span>{{$event->bringTools->pluck('name')->implode(', ')}}</span>
                    </div>
                </div>
            </div>
            @endif
            @if($event->providedTools->count())
            <div class="col-sm-12 col-md-12 col-lg-6 d-flex align-items-stretch"">
                                    <div class=" info-item">
                <h3>What Will Be Provided?</h3>
                <div>
                    <span>{{$event->providedTools->pluck('name')->implode(', ')}}</span>
                </div>
            </div>
        </div>
        @endif
        @if($event->parking_info_details)
        <div class="col-sm-12 col-md-12 col-lg-6 d-flex align-items-stretch">
            <div class="info-item w-100">
                <h3>Parking Information</h3>
                <span>{!! ($event->parking_info_details) !!}
                </span></span>
            </div>
        </div>
        @endif
        @if($event->meeting_spot_details)
        <div class="col-sm-12 col-md-12 col-lg-6 d-flex align-items-stretch">
            <div class="info-item w-100">
                <h3>Meeting Spot Details</h3>
                <span>{!! ($event->meeting_spot_details) !!}
                </span></span>
            </div>
        </div>
        @endif
    </div>

    <div class="organizer-section">
        <h3>Organizer Information</h3>
        <div class="organizer-card">
            <div class="organizer-avatar">
                <img src="{{$event->creator->formattedImage('organisation_logo')}}" class="rounded-circle" height="60px" width="60px" alt="{{@$event->creator->name}}" />
            </div>
            <div class="organizer-info">
                <h4>{{@$event->creator->name}}</h4>
                <div>{!! (@$event->creator->organisation_short_description) !!}</div>
                <div class="d-flex align-items-center gap-2">
                    @if($event->creator->organisation_website)
                    <a href="{{$event->creator->organisation_website}}" target="_BLANK" class="organizer-link">
                        <i class="fa fa-link"></i>
                    </a>
                    @endif
                    @if(@$event->creator->organisation_instagram)
                    <a href="{{@$event->creator->organisation_instagram}}" target="_BLANK" class="organizer-link">
                        <i class="fab fa-instagram"></i>
                    </a>
                    @endif
                    @if(@$event->creator->organisation_facebook)
                    <a href="{{@$event->creator->organisation_facebook}}" target="_BLANK" class="organizer-link">
                        <i class="fab fa-facebook-square"></i>
                    </a>
                    @endif
                    @if(@$event->creator->organisation_linkedin)
                    <a href="{{@$event->creator->organisation_linkedin}}" target="_BLANK" class="organizer-link">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    @endif
                    @if(@$event->creator->organisation_twitter)
                    <a href="{{@$event->creator->organisation_twitter}}" target="_BLANK" class="organizer-link">
                        <i class="fab fa-twitter"></i>
                    </a>
                    @endif
                </div>
                <div class="organizer-actions">
                    <a href="{{route('organizer-detail', ['slug' => @$event->creator->organisation_landing_slug])}}" class="primary-btn w-auto">
                        <i class="fas fa-user"></i>
                        Visit Profile
                    </a>
                    <button class="primary-btn w-auto" data-bs-toggle="modal" data-bs-target="#contactOrgraniser">
                        <i class="fas fa-handshake"></i>
                        Connect
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Sidebar -->
</div>
</div>


</main>
</div>
<!-- Body End-->
<x-reusables.frontend.organiser-contact :organiser="$event->creator_id" />
@endsection
@push('script')
<script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/validation.js')}}"></script>
<script>
    $(document).ready(function() {
        if (window.location.search) {
            var widget_id = window.location.search.split('widget_id=')[1];
            if (widget_id) {
                localStorage.setItem('widget_id', widget_id);
                localStorage.setItem('widget_id_datetime', new Date().toISOString());
            }
        }
    });
</script>
@endpush