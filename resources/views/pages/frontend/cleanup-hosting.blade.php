@extends('layouts.frontend')
@push('style')
<style>
.contact-list .bottom .icon-box{
    width: 20%;
}
.link-deactive{
    color: grey!important;
}
a[disabled]{
    pointer-events: none;
    opacity: 0.6;
}
.border-default,.border-default:hover{
    border:1px solid var(--bs-body-color);
}
</style>
@endpush
@section('content')
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-9">
                        <div class="d-main-title">
                            <h3><i class="fa-solid fa-clock-rotate-left me-3 mb-3"></i>Cleanups I'm Hosting</h3>
                             @php
								$breadcrumbs = [
									[
										'name' => 'Cleanups I\'m Hosting', 
										'url' => route('front-cleanup-hosting', ['type' => 'organizer'])
									],
								]
							@endphp     
							<x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <form method="get">
                                <input type="hidden" name="tabs" id="tabsInput" value="{{ request('tabs') }}">
                                <div class="d-flex align-items-center">
                                    <label class="form-label fs-6 me-2 mb-0">Sort By Start Date:</label>
                                    <div class="loc-group position-relative flex-grow-1">
                                        <select onchange="this.form.submit()" class="selectpicker" name="start_date_sort" title="Sort By">
                                            <option value="desc" {{ request('start_date_sort', 'desc') == 'desc' ? 'selected' : '' }} >Newest First</option>
                                            <option value="asc" {{ request('start_date_sort') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                                        </select>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="main-card mt-5">
                            <div class="dashboard-wrap-content p-4">
                                <div class="d-md-flex flex-wrap align-items-center">
                                    <div class="dashboard-date-wrap col-sm-6 rs">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('front-cleanup-hosting', ['type' => 'organizer']) }}"
                                                class="btn btn-outline-primary px-3 py-2 {{ request()->segment(2)!='cohost'?'active text-white':'' }} ">
                                                Organizing Cleanups ({{ $hosting_event_count }})
                                            </a>
                                            <a href="{{ route('front-cleanup-hosting', ['type' => 'cohost']) }}"
                                                class="btn btn-outline-primary px-3 py-2 {{ request()->segment(2)=='cohost'?'active text-white':'' }}">
                                                Co-Hosting Cleanups ({{ $cohost_event_count }})
                                            </a>
                                        </div>
                                        <!-- <h5 class="mb-0">
                                            <a href="{{ route('front-cleanup-hosting', ['type' => 'organizer']) }}" {{ request()->segment(2)=='cohost'?'class=link-deactive':'' }}>Organizing Cleanups ({{ $hosting_event_count }})</a> / <a href="{{ route('front-cleanup-hosting', ['type' => 'cohost']) }}" {{ request()->segment(2)!='cohost'?'class=link-deactive':'' }}>Co-Hosting Cleanups ({{ $cohost_event_count }})</a> 
                                        </h5> -->
                                        <!-- <h5 class="mb-0">
                                            @if (request()->segment(2) == 'cohost')
                                                Events I'm Co-Hosting
                                            @else
                                                Events I'm Hosting ({{ $hosting_events->total() }})
                                            @endif
                                        </h5> -->
                                    </div>
                                    <div class="rs mt_r4 col-sm-6 ps-3 text-end">
                                        <div class="btn-group" role="group">
                                            <a href="javascript:void(0);" onclick="showHostingEvent('upcoming',this)" hosting-event-val="upcoming"
                                                class="btn btn-outline-primary px-3 py-2 {{ request('tabs', 'upcoming') == 'upcoming' ? 'active text-white' : '' }}">
                                                Upcoming 
                                            </a>
                                            <a href="javascript:void(0);" onclick="showHostingEvent('ongoing',this)" hosting-event-val="ongoing"
                                                class="btn btn-outline-primary px-3 py-2 {{ request('tabs') == 'ongoing' ? 'active text-white' : '' }}">
                                                In-Progress 
                                            </a>
                                            <a href="javascript:void(0);" onclick="showHostingEvent('ended',this)" hosting-event-val="ended"
                                                class="btn btn-outline-primary px-3 py-2 {{ request('tabs') == 'ended' ? 'active text-white' : '' }}">
                                                Completed 
                                            </a>
                                            <a href="javascript:void(0);" onclick="showHostingEvent('action_required',this)" hosting-event-val="action_required"
                                                class="btn btn-outline-primary px-3 py-2 {{ request('tabs') == 'action_required' ? 'active text-white' : '' }}">
                                                Action Required
                                            </a>
                                        </div>
                                        <!-- <div class="btn-group" role="group">
                                            <a href="{{ route('front-cleanup-hosting', ['type' => 'organizer']) }}"
                                                class="btn btn-outline-primary px-3 py-2 {{ request()->segment(2) != 'cohost' ? 'active text-white' : '' }}">
                                                Organising Events
                                            </a>
                                            <a href="{{ route('front-cleanup-hosting', ['type' => 'cohost']) }}"
                                                class="btn btn-outline-primary px-3 py-2 {{ request()->segment(2) == 'cohost' ? 'active text-white' : '' }}">
                                                Co-Hosting Events
                                            </a>
                                        </div> -->
                                        {{-- <div class="nav custom2-tabs btn-group justify-content-end row-gap-3" role="tablist">
											<a href="{{route('front-post-a-event')}}"><i class="fa-solid fa-plus fs-16 menu--link" title="Add New Event"></i></a>
											<a href="#" class="ms-4"><i class="fa-solid fa-calendar-check fs-16 menu--link" title="Download Events iCal"></i></a>
											<a href="#" data-toggle-target="event_filter" class="ms-4"><i class="fa-solid fa-filter fs-16 menu--link" title="Filter"></i></a>											
										</div>										 --}}
                                    </div>
                                </div>
                                <!-- filter dropdown -->
                                <div class="mt-4" id="event_filter" style="display: none;">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="relative-input position-relative">
                                                    <input class="form-control h_40 w-100" type="text"
                                                        placeholder="Keywords" value="">
                                                    <i class="uil uil-search"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 dropdown bootstrap-select">
                                            <div class="loc-group position-relative">
                                                <select name="search_order_by" id="search_order_by" class="form-control">
                                                    <option value="Order by">Order by</option>
                                                    <optgroup label="Event Title">
                                                        <option value="title|asc">Ascending (ASC)</option>
                                                        <option value="title|desc">Descending (DESC)</option>
                                                    </optgroup>
                                                    <optgroup label="Event Start Date">
                                                        <option value="event_start_date|asc">Ascending (ASC)</option>
                                                        <option value="event_start_date|desc">Descending (DESC)</option>
                                                    </optgroup>
                                                    <optgroup label="Event End Date">
                                                        <option value="event_end_date|asc">Ascending (ASC)</option>
                                                        <option value="event_end_date|desc">Descending (DESC)</option>
                                                    </optgroup>
                                                    <optgroup label="Event Location">
                                                        <option value="event_location|asc">Ascending (ASC)</option>
                                                        <option value="event_location|desc">Descending (DESC)</option>
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 rs mt_r4ps-3">
                                            <div class="nav custom2-tabs btn-group justify-content-between row-gap-3"
                                                role="tablist">
                                                <button class="tab-link active w-48" type="submit">Filter</button>
                                                <!-- <button class="tab-link" data-bs-toggle="tab" data-bs-target="#online-tab" type="button" role="tab" aria-controls="online-tab" aria-selected="false">Online Event (<span class="total_event_counter">0</span>)</button> -->
                                                <input type="reset" value="Reset" class="tab-link active w-48">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    	<x-reusables.frontend.alert />
                        <div class="event-list">
                            <div class="main-card mt-2 event-row-ongoing-not-found" style="display:none;">
                                <div class="contact-list">
                                    <div class="card-top event-top p-4 align-items-center top d-flex flex-wrap justify-content-center">
                                        <div class="text-center py-4">
                                            <div class="empty-icon-wrap mx-auto mb-3">
                                                <i class="fa-regular fa-image fa-2x text-muted"></i>
                                            </div>
                                            <h5 class="fw-semibold mb-2">No cleanup found</h5>
                                            <p class="text-muted small mb-3">You aren't currently organizing any ongoing cleanups.<br/>Start a new initiative to help your community!</p>
                                            <a href="{{route('front-post-a-event')}}" class="btn btn-clear-filters">
                                                <i class="fa-solid fa-paper-plane me-2"></i>Post A New Cleanup
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="main-card mt-2 event-row-upcoming-not-found" style="display:none;">
                                <div class="contact-list">
                                    <div class="card-top event-top p-4 align-items-center top d-flex flex-wrap justify-content-center">
                                        <div class="text-center py-4">
                                            <div class="empty-icon-wrap mx-auto mb-3">
                                                <i class="fa-regular fa-image fa-2x text-muted"></i>
                                            </div>
                                            <h5 class="fw-semibold mb-2">No cleanup found</h5>
                                            <p class="text-muted small mb-3">You aren't currently organizing any upcoming cleanups.<br/>Start a new initiative to help your community!</p>
                                            <a href="{{route('front-post-a-event')}}" class="btn btn-clear-filters">
                                                <i class="fa-solid fa-paper-plane me-2"></i>Post A New Cleanup
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="main-card mt-2 event-row-ended-not-found" style="display:none;">
                                <div class="contact-list">
                                    <div class="card-top event-top p-4 align-items-center top d-flex flex-wrap justify-content-center">
                                        <div class="text-center py-4">
                                            <div class="empty-icon-wrap mx-auto mb-3">
                                                <i class="fa-regular fa-image fa-2x text-muted"></i>
                                            </div>
                                            <h5 class="fw-semibold mb-2">No cleanup found</h5>
                                            <p class="text-muted small mb-3">You don't have any past cleanups.<br/>Start a new initiative to help your community!</p>
                                            <a href="{{route('front-post-a-event')}}" class="btn btn-clear-filters">
                                                <i class="fa-solid fa-paper-plane me-2"></i>Post A New Cleanup
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="main-card mt-2 event-row-action_required-not-found" style="display:none;">
                                <div class="contact-list">
                                    <div class="card-top event-top p-4 align-items-center top d-flex flex-wrap justify-content-center">
                                        <div class="text-center py-4">
                                            <div class="empty-icon-wrap mx-auto mb-3">
                                                <i class="fa-regular fa-image fa-2x text-muted"></i>
                                            </div>
                                            <h5 class="fw-semibold mb-2">No cleanup found</h5>
                                            <p class="text-muted small mb-3">No action-required cleanups at the moment.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @foreach ($hosting_events as $event)
                                @php 
                                    $eventStatusClass = strtolower($event->runningStatus);
                                    $now = \Carbon\Carbon::now();
                                    $canCancelEvent = auth()->guard('front')->user()->id == $event->creator_id
                                    && $event->is_completed != 1
                                    && !empty($event->start_datetime)
                                    && $event->start_datetime->gt($now);
                                    /* $canCancelEvent = auth()->guard('front')->user()->id == $event->creator_id
                                        && $event->is_completed != 1
                                        && (!empty($event->end_datetime) ? $event->end_datetime->gte($now) : true); */
                                    if($event->end_datetime && \Carbon\Carbon::parse($event->end_datetime)->lt($now) && $event->is_completed != 1)
                                    {
                                        $eventStatusClass = 'action_required';
                                    }
                                    $approvedCohostCount = $event->cohostRecords->where('request_status', 'approved')->count();
                                    $totalCohostCount = $event->cohostRecords->count();
                                @endphp 
                                <div class="main-card mt-2 event-row-{{$eventStatusClass}}">
                                    <div class="contact-list">
                                        <div
                                            class="card-top event-top p-4 align-items-center top d-md-flex flex-wrap justify-content-between">
                                            <div class="d-md-flex align-items-center event-top-info">
                                                <div class="card-event-img">
                                                    <img src="{{ $event->formattedBanner() }}" alt="{{ $event->event_name }}">
                                                </div>
                                                <div class="card-event-dt">
                                                     @if($event->isDetailActive() && !empty($event->slug))
                                                        <a href="{{route('view-event-details', ['slug' => $event->slug])}}" target="_BLANK"><h5 class="m-0">{{ $event->event_name }}</h5></a>
                                                    @else 
                                                        <h5 class="m-0">{{ $event->event_name }}</h5>
                                                    @endif
                                                    <p class="mb-2">{{ $event->fullAddress }}</p>
                                                    @if(!empty($event->cancelled_at))
                                                    <h6 class="coupon-status">
                                                        <span data-bs-toggle="tooltip" data-bs-original-title="This event has been cancelled at {{ date(config('app.dateTimeFormat'),strtotime($event->cancelled_at)) }}" class="badge custom-badge-danger 1">Cancelled</span>
                                                    </h6>
                                                    @endif
                                                    @if($event->is_active!=1)
                                                    	<h6 class="coupon-status">
                                                    		<span data-bs-toggle="tooltip" data-bs-original-title="This event has been marked as inactive by admin." class="badge custom-badge-danger 1">Not Active</span>
                                                    	</h6>
                                                    @else
                                                        <div class="card-event-dt">
                                                            @if(empty($event->cancelled_at))
                                                            <button {{ $event->is_completed==1?'disabled':'' }} class="btn btn-sm btn-default border-default show-alert-send-waiver-to-all" url_registration_list="{{ route('front-registration-list',['event'=>$event->id]) }}" url="{{route('send-waiver-bulk-email', ['id' => $event->encrypted_id])}}"><i class="fa fa-envelope me-2"></i>Send Wavier To All</button>
                                                            <button {{ $event->is_completed==1?'disabled':'' }} class="btn btn-sm btn-default ms-2 border-default show-alert-run-url" message="You want to mark all attendees as checked-in" url="{{route('mark-bulk-checkin-with-email', ['id' => $event->encrypted_id])}}"><i class="fa fa-check-square me-2"></i>Check-In Signed Volunteers</button>
                                                            @endif
                                                            <button class="btn btn-sm btn-default ms-2 border-default send-event-attendees-email" data-route="{{ route('front-send-custom-email-event-attendees', ['id' => $event->encrypted_id]) }}" data-bs-toggle="modal" data-bs-target="#sendEventAttendeesEmailModal"><i class="fa fa-paper-plane me-2"></i>Send Email</button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="dropdown">
                                                <button class="option-btn" type="button" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis-vertical"></i></button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="{{route('front-post-a-event', ['id'=> $event->encrypted_id])}}" class="dropdown-item">
                                                        <i class="fa-solid fa-gear me-3"></i>Edit</a>
                                                    @if($event->isDetailActive() && !empty($event->slug))
                                                        <a href="{{route('view-event-details', ['slug' => $event->slug])}}" target="_BLANK" class="dropdown-item">
                                                        <i class="fa-solid fa-eye me-3"></i>View Event Page</a>
                                                        <!-- <a class="dropdown-item" href="javascript:void(0);" data-route="{{route('front-send-rsvp-emails', ['slug'=> $event->slug])}}" data-bs-toggle="modal" data-bs-target="#sendRsvpEmail">
                                                            <i class="fa-solid fa-envelope me-3"></i>Send RSVP Emails
                                                        </a> -->
                                                        <a href="javascript:void(0);" onclick="copyText('{{route('view-event-details', ['slug' => $event->slug])}}')" class="dropdown-item">
                                                            <i class="fa-solid fa-copy me-3"></i>Copy Event Link
                                                        </a>
                                                    @endif

                                                    <a href="{{route('front-post-a-event', ['id'=> $event->encrypted_id,'open_tab'=>'step3'])}}" class="dropdown-item"> <i class="fa-solid fa-user-plus me-3"></i>Manage Co-Host</a>
                                                    <a target="_blank" href="{{route('front-registration-list', ['event' => $event->id])}}" class="dropdown-item">
                                                    <i class="fa-solid fa-eye me-3"></i>Manage Registrations / Check-ins</a>
                                                    <a href="{{route('front-post-a-event', ['id'=> $event->encrypted_id,'open_tab'=>'step5'])}}" class="dropdown-item"> <i class="fa-solid fa-bar-chart me-3"></i>Event Cleanup Report</a>
                                                    @if(!empty($event->slug))
                                                    <a href="{{route('front-duplicate-an-event', ['slug' => $event->slug])}}" class="dropdown-item">
                                                        <i class="fa-solid fa-clone me-3"></i>Duplicate Event
                                                    </a>
                                                    @if(!empty($event->start_datetime) && \Carbon\Carbon::parse($event->start_datetime)->gt(now()))
                                                    @endif
                                                    <a href="javascript:void(0);" class="dropdown-item recurring-event-btn"
                                                    data-action="{{ route('front-recurring-event-setup', ['event' => $event->encrypted_id]) }}"
                                                    data-bs-toggle="modal" data-bs-target="#recurringEventModal">
                                                        <i class="fa-solid fa-repeat me-3"></i>Recurring Events
                                                    </a>
                                                    @endif
                                                    @if($event->is_completed!=1)
                                                    <!-- <a message="You want to send the waiver email to all attendees" href="javascript:void(0);" url="{{route('send-waiver-bulk-email', ['id' => $event->encrypted_id])}}" class="dropdown-item show-alert-run-url">
                                                        <i class="fa-solid fa-envelope me-3"></i>Send Waiver Email To All
                                                    </a>
                                                    <a message="You want to mark all attendees as checked-in" href="javascript:void(0);" url="{{route('mark-bulk-checkin-with-email', ['id' => $event->encrypted_id])}}" class="dropdown-item show-alert-run-url">
                                                        <i class="fa-solid fa-envelope me-3"></i> Check-in All
                                                    </a> -->
                                                    @endif
                                                    @if(true) <!-- canCancelEvent -->
                                                    <a class="dropdown-item btn-cancel-event" data-url="" href="javascript:void(0);" reschedule-url="{{ route('front-reschedule-event', ['id' => $event->encrypted_id]) }}" cancel-url="{{ route('front-cancel-event', ['id' => $event->encrypted_id]) }}">
                                                        <i class="fa-solid fa-ban me-3"></i>Cancel Event
                                                    </a>
                                                    @endif
                                                    <!-- <a href="javascript:void(0);"
                                                    class="dropdown-item send-event-attendees-email"
                                                    data-route="{{ route('front-send-custom-email-event-attendees', ['id' => $event->encrypted_id]) }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#sendEventAttendeesEmailModal">
                                                        <i class="fa-solid fa-envelope me-3"></i>Send Email
                                                    </a> -->
                                                    @if(auth()->guard('front')->user()->id==$event->creator_id)
                                                    <a class="dropdown-item delete-record" data-url="{{route('front-cleanup-hosting-delete', ['id'=> $event->id])}}">
                                                        <i class="fa-solid fa-trash-can me-3"></i>Delete
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bottom d-flex flex-wrap justify-content-between align-items-center p-4">
                                            <div class="icon-box">
                                                <span class="icon">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                </span>
                                                <p>Starts on</p>
                                                <h6 class="coupon-status">{{$event->start_datetime->format(config('app.dateTimeFormat'))}}</h6>
                                            </div>
                                            <div class="icon-box ">
                                                <span class="icon">
                                                    <i class="fa-solid fa-user-group"></i>
                                                </span>
                                                <p>Co-Hosts</p>
                                                <h6 class="coupon-status">{{ $approvedCohostCount }} / {{ $totalCohostCount }}</h6>
                                            </div>
                                            <div class="icon-box ">
                                                <span class="icon">
                                                    <i class="fa-solid fa-user"></i>
                                                </span>
                                                <p><!-- <i class="fa-solid fa-user me-1"></i> --> Registered</p>
                                                <h6 class="coupon-status">{{$event->tickets()->active()->count()}} / {{$event->eventTickets()->sum('available_quantity')}}</h6>
                                            </div>
                                            <div class="icon-box ">
                                                <span class="icon">
                                                    <i class="fa-solid fa-check-square"></i>
                                                </span>
                                                <p><!-- <i class="fa-solid fa-check-square me-1"></i> -->Check-In</p>
                                                <h6 class="coupon-status">{{$event->tickets()->active()->where('checkin',1)->count()}} / {{$event->tickets()->active()->count()}}</h6>
                                            </div>
                                            <div class="icon-box ">
                                                <span class="icon">
                                                    <i class="fa-solid fa-handshake"></i>
                                                </span>
                                                <p><!-- <i class="fa-solid fa-handshake me-1"></i> --> Waiver Signed</p>
                                                <h6 class="coupon-status">{{$event->tickets()->active()->whereNotNull('waiver_filled_at')->count()}} / {{$event->tickets()->active()->count()}}</h6>
                                            </div>
                                            <!-- <div class="icon-box ">
                                                <span class="icon">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                </span>
                                                <p>Status</p>
                                                <h6 class="coupon-status">{{ ucfirst($event->runningStatus)=='Ongoing'?'In-Progress':ucfirst($event->runningStatus) }} </h6>
                                            </div>
                                            <div class="icon-box">
                                                <span class="icon">
                                                    <i class="fa-solid fa-ticket"></i>
                                                </span>
                                                <p>Total Ticket</p>
                                                <h6 class="coupon-status">{{$event->eventTickets()->sum('available_quantity')}}</h6>
                                            </div>
                                            <div class="icon-box">
                                                <span class="icon">
                                                    <i class="fa-solid fa-tag"></i>
                                                </span>
                                                <p>Tickets sold</p>
                                                <h6 class="coupon-status">{{$event->tickets()->active()->count()}}</h6>
                                            </div> -->
                                            <!-- <div class="icon-box">
                                                <span class="icon">
                                                    <i class="fa-solid fa-eye"></i>
                                                </span>
                                                <p>Visibility</p>
                                                <h6 class="coupon-status">{{$event->is_private ? 'Private' : 'Public'}}</h6>
                                            </div> -->
                                            <!-- <div class="icon-box">
                                                <span class="icon">
                                                    <i class="fa-solid fa-eye"></i>
                                                </span>
                                                <p>Publish</p>
                                                <div class="form-group">
                                                    <div class="d-flex align-items-start">
                                                        <label class="btn-switch m-0 me-3">
                                                            @if($event->is_completed==1)
                                                            <input disabled type="checkbox" {{ $event->status == 'published' ? 'checked' : '' }} class="event_publish_toggle" data-event-id="{{ $event->encrypted_id }}">
                                                            <span class="checkbox-slider" onclick=redirectWithMessage(this) url="" message="Publish status cannot be changed after the event is completed."></span>
                                                            @else 
                                                            <input {{ $event->tickets()->active()->count()>0?'disabled':'' }} type="checkbox" {{ $event->status == 'published' ? 'checked' : '' }} class="event_publish_toggle" data-event-id="{{ $event->encrypted_id }}">
                                                            <span class="checkbox-slider" {{ $event->tickets()->active()->count()>0?'onclick=redirectWithMessage(this)':'' }} url="" message="An event cannot be unpublished once an attendee has registered."></span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div>
                                        <div class="bottom d-flex flex-wrap justify-content-end align-items-center p-4">
                                            <div class="icon-box me-auto">
                                                <span class="icon">
                                                    <i class="fa-solid fa-eye"></i>
                                                </span>
                                                <p>Publish</p>
                                                <div class="form-group">
                                                    <div class="d-flex align-items-start">
                                                        <label class="btn-switch m-0 me-3">
                                                            @if($event->is_completed==1)
                                                            <input disabled type="checkbox" {{ $event->status == 'published' ? 'checked' : '' }} class="event_publish_toggle" data-event-id="{{ $event->encrypted_id }}">
                                                            <span class="checkbox-slider" onclick=redirectWithMessage(this) url="" message="Publish status cannot be changed after the event is completed."></span>
                                                            @else 
                                                            <input {{ $event->tickets()->active()->count()>0?'disabled':'' }} type="checkbox" {{ $event->status == 'published' ? 'checked' : '' }} class="event_publish_toggle" data-event-id="{{ $event->encrypted_id }}">
                                                            <span class="checkbox-slider" {{ $event->tickets()->active()->count()>0?'onclick=redirectWithMessage(this)':'' }} url="" message="An event cannot be unpublished once an attendee has registered."></span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <a class="primary-btn w-fit" href="{{route('front-reschedule-event', ['id' => $event->encrypted_id])}}" {{ ($event->is_completed==1 || $event->is_active!=1)?'disabled':'' }}>Reschedule</a>
                                            <a class="sign-up-btn text-white w-fit ms-3" href="{{route('front-post-a-event', ['id'=> $event->encrypted_id,'open_tab'=>'step5'])}}" {{ ($eventStatusClass!='action_required' || $event->is_active!=1)?'disabled':'' }}>Submit Wrap-Up Report</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2">
                            {{ $hosting_events->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-reusables.frontend.manage-cohosts  />
<div class="modal fade" id="sendEventAttendeesEmailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="sendEventAttendeesEmailForm">
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
<div class="modal fade" id="recurringEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="recurringEventForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-primary">Create Recurring Events</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body main-form p-4">
                    <div class="form-group mb-3">
                        <label>Recurring Pattern*</label>
                        <div class="d-flex align-items-center gap-4 mt-2">
                            <label class="d-inline-flex align-items-center gap-2 mb-0">
                                <input type="radio" name="recurring_duration" value="weekly" checked required>
                                <span>Weekly</span>
                            </label>

                            <label class="d-inline-flex align-items-center gap-2 mb-0">
                                <input type="radio" name="recurring_duration" value="monthly" required>
                                <span>Monthly</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label>Number of Occurrences*</label>
                        <input type="number" name="recurring_numbers" class="form-control" min="1" max="52" required>
                        <small>Example: 6 weeks/months sequence will be generated.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="primary-btn w-fit">Generate Preview</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('script')
    <script src="{{ asset('assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
    <script>
        $('.send-event-attendees-email').click(function () {
            $('#sendEventAttendeesEmailForm').attr('action', $(this).data('route'));
            $('#sendEventAttendeesEmailForm')[0].reset();
            $('#description').val('');
        });
        $('.btn-send-custom-email').click(function(e){
            e.preventDefault();
            $(this).text('Sending...').attr('disabled', true);
            $('#sendEventAttendeesEmailForm').submit();
        });
        $('.recurring-event-btn').click(function () {
            $('#recurringEventForm').attr('action', $(this).data('action'))[0].reset();
        });
    </script>
@endpush
@endsection
