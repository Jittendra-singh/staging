@php
    $outPages = true;
@endphp
@extends('layouts.frontend')
@section('content')
    <div class="wrapper">
        <main class="main organizer-detail-page" style="background-color: #f9fafb">
            <div class="container">
                <!-- Page Title -->
                <!-- <h1 class="page-title">{{ $organiser->name }}</h1> -->

                <div class="row justify-content-center">
                    <div class="col-sm-8">
                        <x-reusables.frontend.alert />
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-4">
                                    <div class="d-flex align-items-center gap-3 flex-wrap gap-2">
                                        <img src="{{ $organiser->formattedImage('organisation_logo') }}" alt="logo"
                                            style="width: 128px;height: 128px;border-radius: 100px;" />
                                        <div>
                                            <h2 class="section-title m-0 text-dark">{{ $organiser->organisation_name }}</h2>
                                            <!-- <h2 class="section-title m-0 text-dark">Test user</h2> -->
                                            <!-- <p class="description m-0 font-bold">{{ $organiser->name }} ,
                                                {{ $organiser->email_address }}</p> -->
                                            <p class="description m-0 font-bold">{{ $organiser->name }}</p>


                                            <div class="social-links mt-2">
                                                @if ($organiser->organisation_website)
                                                    <a href="{{ $organiser->organisation_website }}" target="_BLANK" class="organizer-link organizer-soical-link ">
                                                        <i class="fa fa-link"></i>
                                                    </a>
                                                @endif
                                                @if ($organiser->organisation_instagram)
                                                    <a href="{{ $organiser->organisation_instagram }}" target="_BLANK"
                                                        class="organizer-link organizer-soical-link">
                                                        <i class="fab fa-instagram"></i>
                                                    </a>
                                                @endif
                                                @if ($organiser->organisation_facebook)
                                                    <a href="{{ $organiser->organisation_facebook }}" target="_BLANK"
                                                        class="organizer-link organizer-soical-link">
                                                        <i class="fab fa-facebook-square"></i>
                                                    </a>
                                                @endif
                                                @if ($organiser->organisation_linkedin)
                                                    <a href="{{ $organiser->organisation_linkedin }}" target="_BLANK"
                                                        class="organizer-link organizer-soical-link">
                                                        <i class="fab fa-linkedin-in"></i>
                                                    </a>
                                                @endif
                                                @if ($organiser->organisation_twitter)
                                                    <a href="{{ $organiser->organisation_twitter }}" target="_BLANK"
                                                        class="organizer-link organizer-soical-link">
                                                        <i class="fab">𝕏</i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="secondary-btn" data-bs-toggle="modal"
                                            data-bs-target="#contactOrgraniser">Contact</button>
                                    </div>
                                </div>

                                <div class="event-tabs-container">
                                    <ul class="nav nav-tabs custom-tabs" id="eventTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ request()->get('tab')=='upcoming_event' || request()->get('tab')=='past_event' ? '' : 'active' }}" id="current-tab" data-bs-toggle="tab"
                                                data-bs-target="#current-events" type="button" role="tab"
                                                aria-controls="current-events" aria-selected="true">
                                                About
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ request()->get('tab')=='upcoming_event' || request()->get('tab')=='past_event' ? 'active' : '' }}" id="upcoming-tab" data-bs-toggle="tab"
                                                data-bs-target="#upcoming-events" type="button" role="tab"
                                                aria-controls="upcoming-events" aria-selected="false">
                                                Events
                                            </button>
                                        </li>
                                        {{-- <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past-events"
                                                type="button" role="tab" aria-controls="past-events" aria-selected="false">
                                                Past Events
                                            </button>
                                        </li> --}}
                                    </ul>

                                    <!-- Tab Content -->
                                    <div class="tab-content" id="eventTabsContent">
                                        <!-- Current Events Tab -->
                                        <div class="tab-pane fade {{ request()->get('tab')=='upcoming_event' || request()->get('tab')=='past_event' || request()->get('tab')=='ongoing_event' ? '' : 'show active' }}" id="current-events" role="tabpanel"
                                            aria-labelledby="current-tab">
                                            <p class="description m-0">
                                                {!! $organiser->organisation_short_description !!}
                                            </p>
                                            Joined in
                                            <b>{{ \Carbon\Carbon::parse($organiser->created_at)->format('Y') }}</b>
                                        </div>

                                        <!-- Upcoming Events Tab -->
                                        <div class="tab-pane fade  {{ request()->get('tab')=='upcoming_event' || request()->get('tab')=='past_event' || request()->get('tab')=='ongoing_event' ? 'show active' : '' }}" id="upcoming-events" role="tabpanel"
                                            aria-labelledby="upcoming-tab">
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-4  flex-wrap gap-3">
                                                <h3 class="mb-0">Events</h3>
                                                <ul class="nav nav-tabs extra-tab" id="extra-tab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ request()->get('tab')=='ongoing_event' ? 'active' : (!request()->get('tab')?'active':'') }}" id="upcoming-tab" data-bs-toggle="tab"
                                                            data-bs-target="#ongoing-events-tab" type="button"
                                                            role="tab" aria-controls="ongoing-events-tab"
                                                            aria-selected="false">
                                                            Ongoing Events
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ request()->get('tab')=='upcoming_event' ? 'active' : '' }}" id="upcoming-tab" data-bs-toggle="tab"
                                                            data-bs-target="#upcoming-events-tab" type="button"
                                                            role="tab" aria-controls="upcoming-events-tab"
                                                            aria-selected="false">
                                                            Upcoming Events
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link  {{ request()->get('tab')=='past_event' ? 'active' : '' }}" id="past-tab" data-bs-toggle="tab"
                                                            data-bs-target="#past-events-tab" type="button"
                                                            role="tab" aria-controls="past-events-tab"
                                                            aria-selected="false">
                                                            Past Events
                                                        </button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content w-100" id="eventTabsContent">
                                                    {{-- Ongoing --}}
                                                    <div class="tab-pane fade {{ request()->get('tab')=='ongoing_event' ? 'show active' : (!request()->get('tab')?'show active':'') }}" id="ongoing-events-tab"
                                                        role="tabpanel" aria-labelledby="ongoing-events-tab">
                                                        <x-reusables.frontend.event-list-view :events="$present_events" />
                                                        <div class="mt-5" data-aos="fade-up" data-aos-duration="800">
                                                            {{ $present_events->links() }}
                                                        </div>
                                                    </div>
                                                    {{-- Future --}}
                                                    <div class="tab-pane fade {{ request()->get('tab')=='upcoming_event' ? 'show active' : '' }}" id="upcoming-events-tab"
                                                        role="tabpanel" aria-labelledby="upcoming-events-tab">
                                                        <x-reusables.frontend.event-list-view :events="$future_events" />
                                                        <div class="mt-5" data-aos="fade-up" data-aos-duration="800">
                                                            {{ $future_events->links() }}
                                                        </div>
                                                    </div>
                                                    {{-- Past --}}
                                                    <div class="tab-pane fade {{ request()->get('tab')=='past_event' ? 'show active' : '' }}" id="past-events-tab"
                                                        role="tabpanel" aria-labelledby="past-events-tab">
                                                        <x-reusables.frontend.event-list-view :events="$past_events" />
                                                        <div class="mt-5" data-aos="fade-up" data-aos-duration="800">
                                                            {{ $past_events->links() }}
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
                <!-- Hero Section -->
                <!-- <div class="hero-section">
                        <div class="hero-content row">
                            <div class="col-sm-12 col-md-4 col-lg-3">
                                <img src="{{ $organiser->formattedImage('organisation_logo') }}" alt="logo" class="w-100">
                            </div>
                            <div class="hero-text col-sm-12 col-md-8 col-lg-9">
                                <h2 class="section-title">{{ $organiser->organisation_name }}</h2>
                                <p class="description">
                                    {!! $organiser->organisation_short_description !!}
                                </p>
                                <p class="participation-note">
                                    To participate, volunteers must first create a volunteer account online .
                                </p>
                                <div class="social-links">
                                    @if ($organiser->organisation_website)
    <a href="{{ $organiser->organisation_website }}" target="_BLANK" class="organizer-link">
                                        <i class="fa fa-link"></i>
                                    </a>
    @endif
                                    @if ($organiser->organisation_instagram)
    <a href="{{ $organiser->organisation_instagram }}" target="_BLANK"
                                        class="organizer-link">
                                        <i class="fab fa-instagram"></i>
                                    </a>
    @endif
                                    @if ($organiser->organisation_facebook)
    <a href="{{ $organiser->organisation_facebook }}" target="_BLANK"
                                        class="organizer-link">
                                        <i class="fab fa-facebook-square"></i>
                                    </a>
    @endif
                                    @if ($organiser->organisation_linkedin)
    <a href="{{ $organiser->organisation_linkedin }}" target="_BLANK"
                                        class="organizer-link">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
    @endif
                                    @if ($organiser->organisation_twitter)
    <a href="{{ $organiser->organisation_twitter }}" target="_BLANK" class="organizer-link">
                                        <i class="fab fa-twitter"></i>
                                    </a>
    @endif
                                </div>
                                <button class="primary-btn w-fit" data-bs-toggle="modal"
                                    data-bs-target="#contactOrgraniser">Contact Organizer</button>
                            </div>
                        </div>
                    </div> -->

                <!-- Bootstrap Event Tabs -->

            </div>
        </main>
    </div>
    <x-reusables.frontend.organiser-contact :organiser="$organiser->id" />
@endsection
@push('script')
<script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/validation.js')}}"></script>
    <script src="{{ asset('assets/libs/imask/imask.min.js') }}"></script>
@endpush