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
</style>
@endpush
@section('content')
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
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
                    <div class="col-md-12">
                        <div class="main-card mt-5">
                            <div class="dashboard-wrap-content p-4">
                                <div class="d-md-flex flex-wrap align-items-center">
                                    <div class="dashboard-date-wrap col-sm-7 rs">
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
                                    <div class="rs mt_r4 col-sm-5 ps-3 text-end">
                                        <div class="btn-group" role="group">
                                            <a href="javascript:void(0);" onclick="showHostingEvent('upcoming',this)"
                                                class="btn btn-outline-primary px-3 py-2 active text-white">
                                                Upcoming 
                                            </a>
                                            <a href="javascript:void(0);" onclick="showHostingEvent('ongoing',this)"
                                                class="btn btn-outline-primary px-3 py-2">
                                                In-Progress 
                                            </a>
                                            <a href="javascript:void(0);" onclick="showHostingEvent('ended',this)"
                                                class="btn btn-outline-primary px-3 py-2">
                                                Past 
                                            </a>
                                            <a href="javascript:void(0);" onclick="showHostingEvent('action_required',this)"
                                                class="btn btn-outline-primary px-3 py-2">
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
                                    <div
                                        class="card-top event-top p-4 align-items-center top d-md-flex flex-wrap justify-content-between row ">
                                        <h5 class="text-center">No cleanup found</h5>
                                        <p class="text-center m-0">You aren't currently organizing any ongoing cleanups. <br/>Start a new initiative to help your community!</p>
                                    </div>
                                </div>
                            </div>
                            <div class="main-card mt-2 event-row-upcoming-not-found" style="display:none;">
                                <div class="contact-list">
                                    <div
                                        class="card-top event-top p-4 align-items-center top d-md-flex flex-wrap justify-content-between row">
                                        <h5 class="text-center">No cleanup found</h5>
                                        <p class="text-center m-0">You aren't currently organizing any upcoming cleanups. <br/>Start a new initiative to help your community!</p>
                                    </div>
                                </div>
                            </div>
                            <div class="main-card mt-2 event-row-ended-not-found" style="display:none;">
                                <div class="contact-list">
                                    <div
                                        class="card-top event-top p-4 align-items-center top d-md-flex flex-wrap justify-content-between row">
                                        <h5 class="text-center">No cleanup found</h5>
                                        <p class="text-center m-0">You don't have any past cleanups. <br/>Start a new initiative to help your community!</p>
                                    </div>
                                </div>
                            </div>
                            <div class="main-card mt-2 event-row-action_required-not-found" style="display:none;">
                                <div class="contact-list">
                                    <div
                                        class="card-top event-top p-4 align-items-center top d-md-flex flex-wrap justify-content-between row">
                                        <h5 class="text-center">No cleanup found</h5>
                                        <p class="text-center m-0">No action-required cleanups at the moment.</p>
                                    </div>
                                </div>
                            </div>
                            @foreach ($hosting_events as $event)
                                @php 
                                    $eventStatusClass = strtolower($event->runningStatus);
                                    $now = \Carbon\Carbon::now();
                                    if($event->end_datetime && \Carbon\Carbon::parse($event->end_datetime)->lt($now) && $event->is_completed != 1)
                                    {
                                        $eventStatusClass = 'action_required';
                                    }
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
                                                    <h5 class="m-0">{{ $event->event_name }}</h5>
                                                    <p>{{ $event->fullAddress }}</p>
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

                                                    <a target="_blank" href="{{route('front-registration-list', ['event' => $event->id])}}" class="dropdown-item">
                                                    <i class="fa-solid fa-eye me-3"></i>View Attendees</a>
                                                    <a href="{{route('front-post-a-event', ['id'=> $event->encrypted_id,'open_tab'=>'step3'])}}" class="dropdown-item"> <i class="fa-solid fa-user-plus me-3"></i>Manage Co-Host</a>
                                                    <a href="{{route('front-post-a-event', ['id'=> $event->encrypted_id,'open_tab'=>'step5'])}}" class="dropdown-item"> <i class="fa-solid fa-bar-chart me-3"></i>Event Cleanup Report</a>
                                                    @if(!empty($event->slug))
                                                    <a href="{{route('front-duplicate-an-event', ['slug' => $event->slug])}}" class="dropdown-item">
                                                        <i class="fa-solid fa-clone me-3"></i>Duplicate Event
                                                    </a>
                                                    @endif
                                                    @if($event->is_completed!=1)
                                                    <a message="You want to send the waiver email to all attendees" href="javascript:void(0);" url="{{route('send-waiver-bulk-email', ['id' => $event->encrypted_id])}}" class="dropdown-item show-alert-run-url">
                                                        <i class="fa-solid fa-envelope me-3"></i>Send Waiver Email To All
                                                    </a>
                                                    <a message="You want to mark all attendees as checked-in" href="javascript:void(0);" url="{{route('mark-bulk-checkin-with-email', ['id' => $event->encrypted_id])}}" class="dropdown-item show-alert-run-url">
                                                        <i class="fa-solid fa-envelope me-3"></i> Check-in All
                                                    </a>
                                                    @endif
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
                                            </div>
                                            <!-- <div class="icon-box">
                                                <span class="icon">
                                                    <i class="fa-solid fa-eye"></i>
                                                </span>
                                                <p>Visibility</p>
                                                <h6 class="coupon-status">{{$event->is_private ? 'Private' : 'Public'}}</h6>
                                            </div> -->
                                            <div class="icon-box">
                                                <span class="icon">
                                                    <i class="fa-solid fa-eye"></i>
                                                </span>
                                                <p>Publish</p>
                                                <div class="form-group">
                                                    <div class="d-flex align-items-start">
                                                        <label class="btn-switch m-0 me-3">
                                                            <input {{ $event->tickets()->active()->count()>0?'disabled':'' }} type="checkbox" {{ $event->status == 'published' ? 'checked' : '' }} class="event_publish_toggle" data-event-id="{{ $event->encrypted_id }}">
                                                            <span class="checkbox-slider" {{ $event->tickets()->active()->count()>0?'onclick=redirectWithMessage(this)':'' }} url="" message="An event cannot be unpublished once an attendee has registered."></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bottom d-flex flex-wrap justify-content-end align-items-center p-4">
                                            <a class="primary-btn w-fit" href="{{route('front-reschedule-event', ['id' => $event->encrypted_id])}}" {{ $event->is_completed==1?'disabled':'' }}>Reschedule</a>
                                            <a class="sign-up-btn text-white w-fit ms-3" href="{{route('front-post-a-event', ['id'=> $event->encrypted_id,'open_tab'=>'step5'])}}" {{ $eventStatusClass!='action_required'?'disabled':'' }}>Submit Wrap-Up Report</a>
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
@endsection
