@php
    $user = auth()->guard('front')->user();
@endphp
@extends('layouts.frontend')
@push('style')
<style>
    .profile-card{
        margin-top: 117px;
    }

    .release-note-alert-card {
        border: 1px solid #b8b5ff;
        border-radius: 12px;
        transition: all .2s ease;
    }
    .release-note-alert-card:hover {
        box-shadow: 0 10px 28px rgba(88, 91, 255, .12);
    }
.dashboard-whats-new-card {
    border: 1px solid #b9b4ff;
    border-radius: 12px;
    background: #fff;
    padding: 20px 20px;
    min-height: 96px;
    color: inherit;
    box-shadow: 0 8px 24px rgba(40, 44, 63, .04);
    transition: all .2s ease;
}

.dashboard-whats-new-card:hover {
    color: inherit;
    border-color: #8278ff;
    box-shadow: 0 12px 30px rgba(99, 91, 255, .12);
}

.dashboard-whats-new-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: #eef1ff;
    color: #635bff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex: 0 0 56px;
}

.dashboard-new-badge {
    background: #ff2f3d;
    color: #fff;
    border-radius: 4px;
    padding: 4px 7px;
    font-size: 9px;
    font-weight: 700;
    line-height: 1;
}

.dashboard-whats-new-arrow {
    font-size: 26px;
    color: #111827;
}

@media (max-width: 991.98px) {
    .dashboard-whats-new-card {
        margin-top: 20px;
    }
}
.dashboard-link-card1{
    background: #0EA5E9;
    border: 1px solid #0EA5E9!important;
}
.dashboard-link-card2{
    background: #0284C7;
    border: 1px solid #0284C7!important;
}
.dashboard-link-card1 .text-white, .dashboard-link-card2 .text-white{
    color:white!important;
}
</style>
@endpush
@section('content')
<div class="wrapper wrapper-body">
    <div class="dashboard-body">
        <div class="container-fluid">
            <!-- <div class="row">
                    <div class="col-md-12">
                        <div class="d-main-title">
                            <h3><i class="fa-solid fa-gauge me-3"></i>Dashboard</h3>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="main-card add-organisation-card p-4 mt-5">
                            <div class="ocard-left">
                                <div class="ocard-avatar">
                                    <img src="{{asset('assets_front/images/profile-imgs/img-13.jpg')}}" alt="">
                                </div>
                                <div class="ocard-name">
                                    <h4>Welcome John Doe</h4>
                                    <span>Manage Events Here.</span>
                                </div>
                            </div>
                            <div class="ocard-right">
                                <button class="primary-btn w-fit">View Events</button>
                            </div>
                        </div>
                        <div class="main-card mt-4">
                            <div class="dashboard-wrap-content">
                                @if(false)
                                <div class="d-flex flex-wrap justify-content-between align-items-center p-4">
                                    <div class="dashboard-date-wrap d-flex flex-wrap justify-content-between align-items-center">
                                        <div class="dashboard-date-arrows">
                                            <a href="#" class="before_date"><i class="fa-solid fa-angle-left"></i></a>
                                            <a href="#" class="after_date disabled"><i class="fa-solid fa-angle-right"></i></a>
                                        </div>
                                        <h5 class="dashboard-select-date">
                                            <span>1st April, 2022</span>
                                            -
                                            <span>30th April, 2022</span>
                                        </h5>
                                    </div>
                                    <div class="rs">
                                        <div class="dropdown dropdown-text event-list-dropdown">
                                            <button class="dropdown-toggle event-list-dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span>Selected Events (1)</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">1</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="dashboard-report-content">
                                    <div class="row">
                                        <div class="col-xl-3 col-lg-6 col-md-6">
                                            <div class="dashboard-report-card purple">
                                                <div class="card-content">
                                                    <div class="card-content">
                                                        <span class="card-title fs-6">Attending Events</span>
                                                        <span class="card-sub-title fs-3">5</span>
                                                        <div class="d-flex align-items-center">
                                                            <span><i class="fa-solid fa-arrow-trend-up"></i></span>
                                                            <span class="text-Light font-12 ms-2 me-2">First Event on 24th July.</span>
                                                            <span class="font-12 color-body text-nowrap"></span>
                                                        </div>
                                                    </div>
                                                    <div class="card-media">
                                                        <i class="fa-solid fa-broom"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-6 col-md-6">
                                            <div class="dashboard-report-card red">
                                                <div class="card-content">
                                                    <div class="card-content">
                                                        <span class="card-title fs-6">Hosting Events</span>
                                                        <span class="card-sub-title fs-3">2</span>
                                                        <div class="d-flex align-items-center">
                                                            <span><i class="fa-solid fa-arrow-trend-up"></i></span>
                                                            <span class="text-Light font-12 ms-2 me-2">First Event on 24th July.</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-media">
                                                        <i class="fa-solid fa-umbrella-beach"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-6 col-md-6">
                                            <div class="dashboard-report-card info">
                                                <div class="card-content">
                                                    <div class="card-content">
                                                        <span class="card-title fs-6">Total Registrations</span>
                                                        <span class="card-sub-title fs-3">30</span>
                                                        <div class="d-flex align-items-center">
                                                            <span><i class="fa-solid fa-arrow-trend-up"></i></span>
                                                            <span class="text-Light font-12 ms-2 me-2"></span>
                                                            <span class="font-12 color-body text-nowrap">Max. From beach Cleaning</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-media">
                                                        <i class="fa-solid fa-rectangle-list"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-lg-6 col-md-6">
                                            <div class="dashboard-report-card success">
                                                <div class="card-content">
                                                    <div class="card-content">
                                                        <span class="card-title fs-6">Total Tickets</span>
                                                        <span class="card-sub-title fs-3">3</span>
                                                        <div class="d-flex align-items-center">
                                                            <span><i class="fa-solid fa-arrow-trend-up"></i></span>
                                                            <span class="text-Light font-12 ms-2 me-2">0.00%</span>
                                                            <span class="font-12 color-body text-nowrap">From Previous Period</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-media">
                                                        <i class="fa-solid fa-ticket"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="main-card mt-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center border_bottom p-4">
                                <div class="dashboard-date-wrap d-flex flex-wrap justify-content-between align-items-center">
                                    <div class="select-graphic-category">
                                        <div class="form-group main-form mb-2">
                                            <select class="selectpicker" data-width="150px">
                                                <option value="revenue">Revenue</option>
                                                <option value="orders">Orders</option>
                                                <option value="pageviews">Page Views</option>
                                                <option value="ticketsales">Ticket Sales</option>
                                            </select>
                                        </div>
                                        <small class="mt-4">See the graphical representation below</small>
                                    </div>
                                </div>
                                <div class="rs">
                                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="btnradio" id="btnradio1">
                                        <label class="btn btn-outline-primary" for="btnradio1">Monthly</label>
                                        <input type="radio" class="btn-check" name="btnradio" id="btnradio2" checked>
                                        <label class="btn btn-outline-primary" for="btnradio2">Weekly</label>
                                        <input type="radio" class="btn-check" name="btnradio" id="btnradio3">
                                        <label class="btn btn-outline-primary" for="btnradio3">Dailty</label>
                                    </div>
                                </div>
                            </div>
                            <div class="item-analytics-content p-4 ps-1 pb-2">
                                <div id="views-graphic"></div>
                            </div>
                        </div>
                    </div>
                </div> -->

            <!-- New design -->

            <main class="main-content">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <section class="welcome-section">
                            <div class="row align-items-start g-4">
                                <div class="col-12 col-lg-6">
                                    <h1 class="welcome-title">Welcome, {{ $user->name }}</h1>
                                    @php
                                        $breadcrumbs = [];
                                    @endphp
                                    <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs" />
                                </div>

                                <div class="col-12 col-lg-3">
                                    <a href="{{ route('front-cleanup-ongoing') }}" class="dashboard-link-card1 dashboard-whats-new-card d-flex align-items-center justify-content-between text-decoration-none">
                                        <div class="d-flex align-items-center gap-3">
                                            <span>
                                                <span class="d-flex align-items-center gap-3 mb-2">
                                                    <span class="fw-bold fs-5 text-white">Start Check-in</span>
                                                </span>
                                                <span class="text-muted fs-6 text-white">Manage attendee check-ins</span>
                                            </span>
                                        </div>

                                        <span class="dashboard-whats-new-arrow">
                                            <i class="fa-solid fa-angle-right text-white"></i>
                                        </span>
                                    </a>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <a href="{{ route('front-release-notes') }}" class="dashboard-link-card2 dashboard-whats-new-card d-flex align-items-center justify-content-between text-decoration-none">
                                        <div class="d-flex align-items-center gap-3">
                                            <span>
                                                <span class="d-flex align-items-center gap-3 mb-2">
                                                    <span class="fw-bold fs-5 text-white">What's New?</span>
                                                    @if(!empty($hasNewReleaseNotes))
                                                    <span class="dashboard-new-badge">New Updates</span>
                                                    @endif
                                                </span>
                                                <span class="text-muted fs-6 text-white">View latest updates</span>
                                            </span>
                                        </div>

                                        <span class="dashboard-whats-new-arrow">
                                            <i class="fa-solid fa-angle-right text-white"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </section>
                        <section class="section">
                            <div class="main-card mt-4">
                                <div class="dashboard-wrap-content p-4">
                            <div class="help-cards">
                                <div class="help-card" onclick="window.location.href='{{route('front-post-a-event')}}'">
                                    <input type="radio" name="help-option" id="creating-event" value="Creating an event" hidden>
                                    <label for="creating-event" class="help-card">
                                        <div class="help-icon"><i class="fa-solid fa-plus"></i></div>
                                        <h4>Create an event</h4>
                                    </label>
                                </div>
                                <div class="help-card" onclick="window.location.href='{{route('front-profile')}}'">
                                    <input type="radio" name="help-option" id="your-account" value="Your account" hidden>
                                    <label for="your-account" class="help-card">
                                        <div class="help-icon"><i class="fa-solid fa-user"></i></div>
                                        <h4>Manage Profile</h4>
                                    </label>
                                </div>
                                <div class="help-card" onclick="window.location.href='{{route('front-cleanup-attending')}}'">
                                    <input type="radio" name="help-option" id="marketing-event" value="Marketing an event" hidden>
                                    <label for="marketing-event" class="help-card">
                                        <div class="help-icon"><i class="fa-solid fa-bullhorn"></i></div>
                                        <h4>Events I'm Attending ({{$user->attendingEvents->count()}})</h4>
                                    </label>
                                </div>
                                <div class="help-card" onclick="window.location.href='{{route('front-cleanup-hosting', ['type' => 'organizer'])}}'">
                                    <input type="radio" name="help-option" id="payouts-taxes" value="Payouts and taxes" hidden>
                                    <label for="payouts-taxes" class="help-card">
                                        <div class="help-icon"><i class="fa-solid fa-money-check"></i></div>
                                        <h4>Events I've Organized ({{$user->events()->active()->count()}})</h4>
                                    </label>
                                </div>
                            </div>
                                </div>
                            </div>
                        </section>
                        
                        <!-- Waitlist Section (Static UI) -->
                        @if(isset($events) && $events->count() > 0)
                        <section class="section mt-5 mb-5">
                            <div class="main-card mt-4">
                                <div class="dashboard-wrap-content p-4">
                            <h2 class="section-title mb-1">Waitlist</h2>
                            <p class="text-secondary fw-semibold mb-4" style="font-size: 15px;">{{ $events->where('id', $selectedEventId)->first()->event_name ?? 'None' }}</p>

                            @if(isset($waitlists))
                            <!-- Controls Row: Filter, Search, Export -->
                            <div class="row align-items-center mb-4">
                                <div class="col-md-9 d-flex flex-wrap align-items-center gap-3">
                                    <!-- Filter Dropdown -->
                                    <div class="d-flex align-items-center">
                                        <label for="eventFilter" class="form-label fs-6 me-2 mb-0 text-nowrap">Filter by Event:</label>
                                        <div class="loc-group position-relative flex-grow-1">
                                            <select id="eventFilter" class="selectpicker" onchange="window.location.href='?event_id=' + this.value + (new URLSearchParams(window.location.search).get('search') ? '&search=' + new URLSearchParams(window.location.search).get('search') : '')">
                                                @foreach($events as $event)
                                                    <option value="{{ $event->id }}" {{ $selectedEventId == $event->id ? 'selected' : '' }}>{{ $event->event_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Search Bar -->
                                    <form id="waitlistSearchForm" method="GET" action="{{ route('front-dashboard') }}" class="position-relative flex-grow-1" style="max-width: 400px; min-width: 250px;">
                                        <input type="hidden" name="event_id" value="{{ $selectedEventId }}">
                                        <i class="fa fa-search position-absolute text-secondary" style="top: 50%; left: 16px; transform: translateY(-50%); z-index: 10;"></i>
                                        <input type="text" id="waitlistSearchInput" name="search" value="{{ request('search') }}" class="form-control shadow-sm" style="padding-left: 42px !important; border-radius: 8px; border: 1px solid #dee2e6; position: relative; z-index: 1;" placeholder="Search volunteers by name or email...">
                                    </form>
                                </div>
                                <div class="col-md-3 d-flex justify-content-md-end justify-content-start mt-3 mt-md-0">
                                    <!-- Export Button -->
                                    <a id="waitlist-export-btn" href="{{ route('export.waitlist', ['event_id' => $selectedEventId, 'search' => request('search')]) }}" class="start-btn text-white fw-semibold shadow-sm d-flex align-items-center gap-2 custom-export-btn" style="border-radius: 8px; padding: 8px 20px; text-decoration: none;">
                                        <i class="fa fa-download" style="font-size: 13px;"></i> Export Waitlist
                                    </a>
                                </div>
                            </div>

                            <!-- Waitlist Table Card -->
                            <div id="waitlist-table-card" class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb !important;">
                                <style>
                                    .waitlist-table td { border-bottom: 2px solid #e5e7eb !important; }
                                    .waitlist-table tr:last-child td { border-bottom: none !important; }
                                    .custom-export-btn:hover, .custom-export-btn:focus { color: white !important; }
                                </style>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 waitlist-table">
                                        <thead style="background-color: #f3f4f6;">
                                            <tr style="background-color: #f3f4f6;">
                                                <th class="py-3 px-4 fw-semibold text-secondary border-0" style="font-size: 13px; background-color: #f3f4f6 !important;">Volunteer Name</th>
                                                <th class="py-3 px-4 fw-semibold text-secondary border-0" style="font-size: 13px; background-color: #f3f4f6 !important;">Email</th>
                                                <th class="py-3 px-4 fw-semibold text-secondary border-0" style="font-size: 13px; background-color: #f3f4f6 !important;">Ticket Type</th>
                                                <th class="py-3 px-4 fw-semibold text-secondary border-0" style="font-size: 13px; background-color: #f3f4f6 !important;">Joined Date/Time</th>
                                                <th class="py-3 px-4 fw-semibold text-secondary border-0" style="font-size: 13px; background-color: #f3f4f6 !important;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="border-top-0">
                                            @forelse($waitlists as $entry)
                                            <tr>
                                                <td class="px-4 py-3 border-bottom border-light">
                                                    <div class="d-flex align-items-center gap-3">
                                                        @php
                                                            $initials = strtoupper(substr($entry->first_name, 0, 1) . substr($entry->last_name, 0, 1));
                                                            $colors = ['#6366f1', '#6ee7b7', '#7dd3fc', '#818cf8', '#f87171', '#fbbf24'];
                                                            $bgColor = $colors[strlen($entry->first_name) % count($colors)];
                                                        @endphp
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px; background-color: {{ $bgColor }}; font-size: 14px; font-weight: 600; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                                            {{ $initials }}
                                                        </div>
                                                        <span class="fw-medium text-dark">{{ $entry->first_name }} {{ $entry->last_name }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 border-bottom border-light text-secondary">{{ $entry->email }}</td>
                                                <td class="px-4 py-3 border-bottom border-light text-secondary">{{ $entry->ticket->ticket_name ?? 'N/A' }}</td>
                                                <td class="px-4 py-3 border-bottom border-light text-dark fw-medium">{{ \Carbon\Carbon::parse($entry->joined_at)->format('M d, g:i A') }}</td>
                                                <td class="px-4 py-3 border-bottom border-light">
                                                    @if(strtolower($entry->status) == 'waiting')
                                                        <span class="badge rounded-pill text-secondary fw-medium" style="background-color: #e5e7eb; padding: 6px 14px; font-size: 12px;">Waiting</span>
                                                    @else
                                                        <span class="badge rounded-pill text-success fw-medium" style="background-color: #a7f3d0; padding: 6px 14px; font-size: 12px; color: #065f46 !important;">{{ ucfirst($entry->status) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5" style="border-bottom: none; background-color: #f8fafc;">
                                                    <div class="empty-state d-flex flex-column align-items-center justify-content-center" style="padding: 40px 20px;">
                                                        @if(request()->filled('search'))
                                                            <div class="empty-state-icon mb-3">
                                                                <i class="fa-solid fa-magnifying-glass" style="font-size: 56px; color: #9ca3af;"></i>
                                                            </div>
                                                            <h4 class="fw-bold mb-2" style="color: #1f2937; font-size: 20px;">No volunteers found</h4>
                                                            <p class="text-muted" style="font-size: 14px; max-width: 400px; margin: 0 auto 15px auto;">We couldn't find any volunteers matching your search. Try adjusting your search criteria.</p>
                                                        @else
                                                            <div class="empty-state-icon mb-3">
                                                                <i class="fa-solid fa-user-plus" style="font-size: 64px; color: #9ca3af;"></i>
                                                            </div>
                                                            @php $activeEvent = isset($events) ? $events->where('id', $selectedEventId)->first() : null; @endphp
                                                            @if($activeEvent && !$activeEvent->enable_waitlist)
                                                                <h4 class="fw-bold mb-2" style="color: #1f2937; font-size: 20px;">Waitlist is not enabled for this event</h4>
                                                                <p class="text-muted" style="font-size: 14px;">Enable the waitlist in your event settings to allow volunteers to join.</p>
                                                            @else
                                                                <h4 class="fw-bold mb-2" style="color: #1f2937; font-size: 20px;">No volunteers on this event's waitlist yet</h4>
                                                                <p class="text-muted" style="font-size: 14px;">When people join your event's waitlist, they will appear here.</p>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Table Footer -->
                                <div class="card-footer py-3 px-4 d-flex justify-content-between align-items-center border-top" style="background-color: #f3f4f6; border-color: #e5e7eb !important;">
                                    <span class="text-secondary" style="font-size: 14px;">Showing <strong class="text-dark">{{ $waitlists->count() }}</strong> of <strong class="text-dark">{{ $waitlists->total() }}</strong></span>
                                    <a href="{{ route('waitlist.detail', ['event_id' => $selectedEventId]) }}" class="text-decoration-none fw-semibold d-flex align-items-center gap-1" style="color: #4f46e5; font-size: 14px;">
                                        View Full Waitlist <i class="fa-solid fa-arrow-right-long" style="font-size: 12px;"></i>
                                    </a>
                                </div>
                            </div>
                            @endif

                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const searchInput = document.getElementById('waitlistSearchInput');
                                    const searchForm = document.getElementById('waitlistSearchForm');
                                    
                                    if (searchInput && searchForm) {
                                        let timeout = null;
                                        
                                        function performSearch() {
                                            const url = new URL(searchForm.action);
                                            url.searchParams.set('event_id', searchForm.querySelector('[name="event_id"]').value);
                                            if (searchInput.value) {
                                                url.searchParams.set('search', searchInput.value);
                                            }
                                            
                                            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                                                .then(response => response.text())
                                                .then(html => {
                                                    const parser = new DOMParser();
                                                    const doc = parser.parseFromString(html, 'text/html');
                                                    
                                                    // Replace table card content
                                                    const newTableCard = doc.getElementById('waitlist-table-card');
                                                    const oldTableCard = document.getElementById('waitlist-table-card');
                                                    if (newTableCard && oldTableCard) {
                                                        oldTableCard.innerHTML = newTableCard.innerHTML;
                                                    }
                                                    
                                                    // Update total count
                                                    const newTotal = doc.getElementById('waitlist-total-count');
                                                    const oldTotal = document.getElementById('waitlist-total-count');
                                                    if (newTotal && oldTotal) {
                                                        oldTotal.innerHTML = newTotal.innerHTML;
                                                    }
                                                    
                                                    // Update export link
                                                    const newExport = doc.getElementById('waitlist-export-btn');
                                                    const oldExport = document.getElementById('waitlist-export-btn');
                                                    if (newExport && oldExport) {
                                                        oldExport.href = newExport.href;
                                                    }
                                                });
                                        }
                                        
                                        // Auto submit form with a 500ms delay when typing using AJAX
                                        searchInput.addEventListener('input', function() {
                                            clearTimeout(timeout);
                                            timeout = setTimeout(performSearch, 500);
                                        });

                                        // Prevent full page reload on Enter
                                        searchForm.addEventListener('submit', function(e) {
                                            e.preventDefault();
                                            clearTimeout(timeout);
                                            performSearch();
                                        });
                                    }
                                });
                            </script>
                                </div>
                            </div>
                        </section>
                        @endif

                        <section class="section">
                            <div class="main-card mt-4">
                                <div class="dashboard-wrap-content p-4">
                            <div class="section-header">
                                <div>
                                    <h2 class="section-title">Your checklist</h2>
                                    <p class="section-subtitle">We make it easy to plan successful events. Here's how to start!</p>
                                </div>
                            </div>

                            <div class="checklist">
                                <div class="checklist-item">
                                    <div class="d-flex align-items-start gap-4">
                                        <div class="checklist-icon {{$user->events->count() ? "" : "pending"}}" >
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div class="checklist-content">
                                            <h4>Create event</h4>
                                            <p>Publish an event to reach millions of people on Eventbrite</p>
                                        </div>
                                    </div>
                                    <a href="{{route('front-post-a-event')}}" class="start-btn text-white">
                                        <svg id="religion_svg__eds-icon--politics_svg" x="0" y="0" viewBox="0 0 24 24" xml:space="preserve">
                                            <path id="religion_svg__eds-icon--religion_base" fill-rule="evenodd" clip-rule="evenodd" d="M10.8 13.9l-.2-.6-.6-.2L5.8 12l4.3-1.2.6-.1.2-.6L12 5.8l1.2 4.3.1.6.6.1 4.3 1.2-4.3 1.2-.6.2-.1.6-1.2 4.2-1.2-4.3zM12 2L9.9 9.9 2 12l7.9 2.1L12 22l2.1-7.9L22 12l-7.9-2.1L12 2z"></path>
                                            <g id="religion_svg__eds-icon--religion_dashes" fill-rule="evenodd" clip-rule="evenodd">
                                                <path transform="rotate(-45.001 6.64 17.312)" d="M5.8 16.9h1.7v.8H5.8z"></path>
                                                <path transform="rotate(-44.988 17.31 17.36)" d="M16.9 16.5h.8v1.7h-.8z"></path>
                                                <path transform="rotate(-45.001 17.336 6.664)" d="M16.5 6.2h1.7V7h-1.7z"></path>
                                                <path transform="rotate(-44.988 6.64 6.688)" d="M6.2 5.9H7v1.7h-.8z"></path>
                                            </g>
                                        </svg>
                                        Post A New Event
                                    </a>
                                </div>
                                <div class="checklist-item">
                                    <div class="d-flex align-items-start gap-4">
                                        <div class="checklist-icon {{$user->isOrganisationProfileSetup ? "" : "pending"}}" >
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div class="checklist-content">
                                            <h4>Set up your organizer profile</h4>
                                            <p>Highlight your brand by adding your organizer's name, image, and bio</p>
                                        </div>
                                    </div>
                                    <a href="{{route('front-profile')}}" class="start-btn text-white">
                                        <svg id="religion_svg__eds-icon--politics_svg" x="0" y="0" viewBox="0 0 24 24" xml:space="preserve">
                                            <path id="religion_svg__eds-icon--religion_base" fill-rule="evenodd" clip-rule="evenodd" d="M10.8 13.9l-.2-.6-.6-.2L5.8 12l4.3-1.2.6-.1.2-.6L12 5.8l1.2 4.3.1.6.6.1 4.3 1.2-4.3 1.2-.6.2-.1.6-1.2 4.2-1.2-4.3zM12 2L9.9 9.9 2 12l7.9 2.1L12 22l2.1-7.9L22 12l-7.9-2.1L12 2z"></path>
                                            <g id="religion_svg__eds-icon--religion_dashes" fill-rule="evenodd" clip-rule="evenodd">
                                                <path transform="rotate(-45.001 6.64 17.312)" d="M5.8 16.9h1.7v.8H5.8z"></path>
                                                <path transform="rotate(-44.988 17.31 17.36)" d="M16.9 16.5h.8v1.7h-.8z"></path>
                                                <path transform="rotate(-45.001 17.336 6.664)" d="M16.5 6.2h1.7V7h-1.7z"></path>
                                                <path transform="rotate(-44.988 6.64 6.688)" d="M6.2 5.9H7v1.7h-.8z"></path>
                                            </g>
                                        </svg>
                                        Setup Org. Profile
                                    </a>
                                </div>
                                <div class="checklist-item">
                                    <div class="d-flex align-items-start gap-4">
                                        <div class="checklist-icon {{$user->stripe_account_id!='' ? "" : "pending"}}">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div class="checklist-content">
                                            <h4>Add your Stripe Account</h4>
                                            <p>Get paid for future ticket sales by connecting your Stripe account</p>
                                        </div>
                                    </div>

                                    <a href="{{route('front-profile')}}" class="start-btn text-white">
                                        <svg id="religion_svg__eds-icon--politics_svg" x="0" y="0" viewBox="0 0 24 24" xml:space="preserve">
                                            <path id="religion_svg__eds-icon--religion_base" fill-rule="evenodd" clip-rule="evenodd" d="M10.8 13.9l-.2-.6-.6-.2L5.8 12l4.3-1.2.6-.1.2-.6L12 5.8l1.2 4.3.1.6.6.1 4.3 1.2-4.3 1.2-.6.2-.1.6-1.2 4.2-1.2-4.3zM12 2L9.9 9.9 2 12l7.9 2.1L12 22l2.1-7.9L22 12l-7.9-2.1L12 2z"></path>
                                            <g id="religion_svg__eds-icon--religion_dashes" fill-rule="evenodd" clip-rule="evenodd">
                                                <path transform="rotate(-45.001 6.64 17.312)" d="M5.8 16.9h1.7v.8H5.8z"></path>
                                                <path transform="rotate(-44.988 17.31 17.36)" d="M16.9 16.5h.8v1.7h-.8z"></path>
                                                <path transform="rotate(-45.001 17.336 6.664)" d="M16.5 6.2h1.7V7h-1.7z"></path>
                                                <path transform="rotate(-44.988 6.64 6.688)" d="M6.2 5.9H7v1.7h-.8z"></path>
                                            </g>
                                        </svg>
                                        Connect Stripe
                                    </a>
                                </div>
                            </div>
                                </div>
                            </div>
                        </section>

                        <!-- <section class="section">
                            <div class="section-header">
                                <h2 class="section-title">Top resources for you</h2>
                                <a href="#" style="color: #6366f1; font-size: 14px; text-decoration: none;">Visit Eventbrite Blog</a>
                            </div>

                            <div class="resource-cards">
                                <div class="resource-card">
                                    <div class="resource-card-header">
                                        <div class="resource-icon">📚</div>
                                        <span class="resource-label">Eventbrite Marketing Events</span>
                                    </div>
                                    <h3>Digital Marketing Guide for Events</h3>
                                    <a href="#" class="read-link">Read article</a>
                                </div>
                                <div class="resource-card">
                                    <div class="resource-card-header">
                                        <div class="resource-icon">🛡️</div>
                                        <span class="resource-label">Eventbrite Marketing Events</span>
                                    </div>
                                    <h3>Safety Playbook for Events</h3>
                                    <a href="#" class="read-link">Read article</a>
                                </div>
                                <div class="resource-card">
                                    <div class="resource-card-header">
                                        <div class="resource-icon">🎯</div>
                                        <span class="resource-label">Eventbrite Marketing Experts</span>
                                    </div>
                                    <h3>10 Best Ways to Market and Sell Out Your Event</h3>
                                    <a href="#" class="read-link">Read article</a>
                                </div>
                            </div>
                        </section> -->

                        
                    </div>

                    <!-- <div class="col-lg-3 col-md-12 col-sm-12">
                        <div class="profile-card">
                            <div class="profile-header">
                                <div class="profile-avatar-large">
                                    <img style="border-radius: 100%;" height="48" width="48" src="{{ $user->formattedImage('profile_picture') }}" alt="{{ $user->name }}"></div>
                                <div>
                                    <div class="profile-name">{{$user->name}}</div>
                                    <div class="edit-profile d-flex justify-content-center">
                                        <a href="{{route('front-profile')}}" target="_BLANK">
                                            View <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="profile-stats">
                                <div>
                                    <div><b>{{$user->events->count()}}</b></div>
                                    <div>
                                        <a href="{{route('front-cleanup-hosting', 'organizer')}}">
                                            <b> Organized events </b>
                                        </a>
                                    </div>
                                </div>
                                <div>
                                    <div><b>{{$user->attendingEvents->count()}}</b></div>
                                    <div>
                                        <a href="{{route('front-cleanup-attending')}}">
                                            <b> Attended events </b>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </main>
        </div>
    </div>
</div>
@endsection