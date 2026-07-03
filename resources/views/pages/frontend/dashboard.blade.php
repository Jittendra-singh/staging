@php
    $user = auth()->guard('front')->user();
    $attendingEventsCount = $user ? $user->attendingEvents()->count() : 0;
    $organizedEventsCount = $user ? $user->events()->active()->count() : 0;
    $createdEventsCount = $user ? $user->events()->count() : 0;
    $hasStripeAccount = $user && !empty($user->stripe_account_id);
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
                                    <h1 class="welcome-title">{{ __('messages.dashboard_welcome', ['name' => $user->name]) }}</h1>
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
                                                    <span class="fw-bold fs-5 text-white">{{ __('messages.start_checkin') }}</span>
                                                </span>
                                                <span class="text-muted fs-6 text-white">{{ __('messages.manage_checkins') }}</span>
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
                                                    <span class="fw-bold fs-5 text-white">{{ __('messages.whats_new') }}</span>
                                                    @if(!empty($hasNewReleaseNotes))
                                                    <span class="dashboard-new-badge">{{ __('messages.new_updates') }}</span>
                                                    @endif
                                                </span>
                                                <span class="text-muted fs-6 text-white">{{ __('messages.view_latest_updates') }}</span>
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

                            <div class="help-cards">
                                <div class="help-card" onclick="window.location.href='{{route('front-post-a-event')}}'">
                                    <input type="radio" name="help-option" id="creating-event" value="Creating an event" hidden>
                                    <label for="creating-event" class="help-card">
                                        <div class="help-icon"><i class="fa-solid fa-plus"></i></div>
                                        <h4>{{ __('messages.create_event') }}</h4>
                                    </label>
                                </div>
                                <div class="help-card" onclick="window.location.href='{{route('front-profile')}}'">
                                    <input type="radio" name="help-option" id="your-account" value="Your account" hidden>
                                    <label for="your-account" class="help-card">
                                        <div class="help-icon"><i class="fa-solid fa-user"></i></div>
                                        <h4>{{ __('messages.manage_profile') }}</h4>
                                    </label>
                                </div>
                                <div class="help-card" onclick="window.location.href='{{route('front-cleanup-attending')}}'">
                                    <input type="radio" name="help-option" id="marketing-event" value="Marketing an event" hidden>
                                    <label for="marketing-event" class="help-card">
                                        <div class="help-icon"><i class="fa-solid fa-bullhorn"></i></div>
                                        <h4>{{ __('messages.events_attending_count', ['count' => $attendingEventsCount]) }}</h4>
                                    </label>
                                </div>
                                <div class="help-card" onclick="window.location.href='{{route('front-cleanup-hosting', ['type' => 'organizer'])}}'">
                                    <input type="radio" name="help-option" id="payouts-taxes" value="Payouts and taxes" hidden>
                                    <label for="payouts-taxes" class="help-card">
                                        <div class="help-icon"><i class="fa-solid fa-money-check"></i></div>
                                        <h4>{{ __('messages.events_organized_count', ['count' => $organizedEventsCount]) }}</h4>
                                    </label>
                                </div>
                            </div>
                        </section>
                        <section class="section">
                            <div class="section-header">
                                <div>
                                    <h2 class="section-title">{{ __('messages.your_checklist') }}</h2>
                                    <p class="section-subtitle">{{ __('messages.checklist_subtitle') }}</p>
                                </div>
                            </div>

                            <div class="checklist">
                                <div class="checklist-item">
                                    <div class="d-flex align-items-start gap-4">
                                        <div class="checklist-icon {{$createdEventsCount ? "" : "pending"}}" >
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div class="checklist-content">
                                            <h4>{{ __('messages.create_event') }}</h4>
                                            <p>{{ __('messages.create_event_desc') }}</p>
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
                                        {{ __('messages.post_new_event') }}
                                    </a>
                                </div>
                                <div class="checklist-item">
                                    <div class="d-flex align-items-start gap-4">
                                        <div class="checklist-icon {{$user->isOrganisationProfileSetup ? "" : "pending"}}" >
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div class="checklist-content">
                                            <h4>{{ __('messages.setup_organizer_profile') }}</h4>
                                            <p>{{ __('messages.setup_organizer_profile_desc') }}</p>
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
                                        {{ __('messages.setup_org_profile_btn') }}
                                    </a>
                                </div>
                                <div class="checklist-item">
                                    <div class="d-flex align-items-start gap-4">
                                        <div class="checklist-icon {{$hasStripeAccount ? "" : "pending"}}">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                        <div class="checklist-content">
                                            <h4>{{ __('messages.add_stripe_account') }}</h4>
                                            <p>{{ __('messages.add_stripe_account_desc') }}</p>
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
                                        {{ __('messages.connect_stripe') }}
                                    </a>
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
                                    <div><b>{{$createdEventsCount}}</b></div>
                                    <div>
                                        <a href="{{route('front-cleanup-hosting', 'organizer')}}">
                                            <b>{{ __('messages.organized_events') }}</b>
                                        </a>
                                    </div>
                                </div>
                                <div>
                                    <div><b>{{$attendingEventsCount}}</b></div>
                                    <div>
                                        <a href="{{route('front-cleanup-attending')}}">
                                            <b>{{ __('messages.attended_events') }}</b>
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
