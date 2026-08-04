@php
    $outPages = true;
@endphp

@extends('layouts.frontend')

@push('style')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">

    <style>
        .grid-event-layout {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 20px;
        }

        @media (min-width: 768px) {
            .grid-event-layout {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 992px) {
            .grid-event-layout {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .main-card:hover {
            border: 1px solid #048cda;
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .event-content .badge {
            padding: 0.4rem 0.75rem;
            text-transform: uppercase;
            border-radius: 1rem;
        }

        .event-content .badge.ongoing {
            color: #22c55e;
            background-color: rgb(220 252 231 / 1);
        }

        .event-content .badge.ended {
            color: gray;
            background: #e3e3e3;
        }

        .event-content .badge.upcoming {
            color: #1d4ed8;
            background-color: rgb(219 234 254 / 1);
        }

        header.header,
        footer.footer {
            display: none !important;
        }

        .widget-empty-state {
            max-width: 520px;
            margin: 60px auto 26px;
            text-align: center;
        }

        .widget-empty-state .empty-image-wrap {
            width: 115px;
            height: 115px;
            margin: 0 auto;
            border-radius: 50%;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .widget-empty-state .empty-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .empty-widget-text {
            max-width: 600px;
            margin: 0 auto 12px;
            color: #2b2b2b;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.15;
        }

        .widget-empty-description {
            max-width: 650px;
            margin: 0 auto 25px;
            color: #555;
            font-size: 16px;
        }

        .widget-action-box {
            max-width: 700px;
            margin: 0 auto 30px;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }

        .select2-container--default .select2-selection--single {
            background: #f9f9f9 !important;
            border-color: #dee2e6;
        }

        .select2-container .select2-selection--single,
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 50px;
        }
    </style>
@endpush

@section('content')
    @php
        $miles100Count = (int) ($miles100_count ?? 0);

        $hasListEvents = $widget->type == 1
            && isset($events)
            && $events->count() > 0;

        $hasMapEvents = $widget->type == 2
            && !empty($location_array);

        $hasEvents = $hasListEvents || $hasMapEvents;
        $is100MileFallback = $miles100Count > 0;
        $emptyBehavior = (int) ($widget->empty_behavior ?? 0);

        $eventHeading = $is100MileFallback
            ? 'Suggested for you'
            : ($widget->custom_widget_header ?: 'Upcoming Cleanups');
    @endphp

    <div class="wrapper mt-0" style="background-color: #f5f5f5cf">
        <div class="container mt-3 mb-5">
            @if(isset($widget->id))

                {{-- Events were found only after expanding the search to 100 miles. --}}
                @if($is100MileFallback)
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center mb-4">
                                <div class="widget-empty-state">
                                    <div class="empty-image-wrap">
                                        <img
                                            src="{{ asset('assets_front/images/widget1.png') }}"
                                            alt="No matching events found"
                                        >
                                    </div>
                                </div>

                                <h3 class="empty-widget-text">
                                    No matching events found
                                </h3>

                                <p class="widget-empty-description">
                                    No matching events found for your current filters. We've expanded your search to a 100-mile radius to find the nearest upcoming events in your area.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Normal events or 100-mile suggested events. --}}
                @if($hasEvents)
                    <div class="row">
                        <div class="col-12 text-left mb-4">
                            <h2>{{ $eventHeading }}</h2>
                        </div>

                        @if($widget->type == 2)
                            <div class="col-12 mt-2">
                                <div
                                    id="map"
                                    style="height:35rem;border-radius:20px;overflow:hidden;"
                                ></div>
                            </div>

                            <x-reusables.google :zoom="9" :locationarray="$location_array" />
                        @else
                            <div class="col-12">
                                <div class="featured-controls">
                                    <x-reusables.frontend.widget-event-block-view
                                        :widget="$widget"
                                        :events="$events->items()"
                                    />

                                    <div class="mt-3" data-aos="fade-up" data-aos-duration="800">
                                        {{ $events->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <small>
                        Powered by
                        <a href="https://volunteercleanup.org" target="_blank">
                            VolunteerCleanup.org
                        </a>
                    </small>

                {{-- No events exist even after the 100-mile fallback. --}}
                @else
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center mb-4">

                                @if($emptyBehavior === 1)
                                    <div class="widget-empty-state">
                                        <div class="empty-image-wrap">
                                            <img
                                                src="{{ asset('assets_front/images/widget2.png') }}"
                                                alt="No event found"
                                            >
                                        </div>
                                    </div>
                                    <h3 class="empty-widget-text">
                                        Stay Informed on Upcoming Events
                                    </h3>

                                    <p class="widget-empty-description">
                                        @if(!empty($widget->location))
                                            No upcoming events were found near {{$widget->location}}. Sign up to be notified when a new cleanup or community event is scheduled in this area.
                                        @else
                                            No upcoming events were found. Sign up to be notified when a new cleanup or community event is scheduled in your area.
                                        @endif
                                    </p>

                                @elseif($emptyBehavior === 2)
                                    <div class="widget-empty-state">
                                        <div class="empty-image-wrap">
                                            <img
                                                src="{{ asset('assets_front/images/widget1.png') }}"
                                                alt="No event found"
                                            >
                                        </div>
                                    </div>
                                    <h3 class="empty-widget-text">
                                        No matching events found
                                    </h3>

                                    <p class="widget-empty-description">
                                        We couldn't find any upcoming events within a 100-mile radius
                                        of your location.
                                    </p>

                                @else
                                    <h3 class="empty-widget-text">
                                        The world is waiting for your help,<br>
                                        but we couldn't find a match here.
                                    </h3>
                                @endif
                            </div>

                            {{-- empty_behavior = 1: alert creation form --}}
                            @if($emptyBehavior === 1)
                                <div class="widget-action-box">

                                    <form id="widget-create-alert-form" method="POST" action="{{ route('submit-create-alert') }}">
                                        @csrf
                                        <input type="hidden" name="from_widget" value="1">
                                        <input type="hidden" name="create_from" value="5">
                                        <div class="row g-3">
                                            <x-reusables.frontend.alert />
                                            <div class="col-md-6">
                                                <label>
                                                    First Name <span class="text-danger">*</span>
                                                </label>

                                                <input
                                                    type="text"
                                                    name="first_name"
                                                    class="form-control h_50 specialtextonlymask"
                                                    value="{{ old('first_name') }}"
                                                    placeholder="First Name"
                                                    required
                                                >
                                            </div>

                                            <div class="col-md-6">
                                                <label>
                                                    Last Name <span class="text-danger">*</span>
                                                </label>

                                                <input
                                                    type="text"
                                                    name="last_name"
                                                    class="form-control h_50 specialtextonlymask"
                                                    value="{{ old('last_name') }}"
                                                    placeholder="Last Name"
                                                    required
                                                >
                                            </div>

                                            <div class="col-md-6">
                                                <label>
                                                    Email Address <span class="text-danger">*</span>
                                                </label>

                                                <input
                                                    type="email"
                                                    name="email"
                                                    class="form-control h_50"
                                                    value="{{ old('email') }}"
                                                    placeholder="Email Address"
                                                    required
                                                >
                                            </div>

                                            <div class="col-md-6">
                                                <label>
                                                    Zipcode <span class="text-danger">*</span>
                                                </label>

                                                <select
                                                    datatype="pincode"
                                                    class="form-select select2ajax city_state_zip"
                                                    name="checkout_pincode"
                                                    required
                                                >
                                                    @if(old('checkout_pincode'))
                                                        <option value="{{ old('checkout_pincode') }}" selected>
                                                            {{ old('checkout_pincode') }}
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="text-center mt-4">
                                            <button id="widget-get-notified-button" type="submit" class="primary-btn m-auto">
                                                Get Notified
                                            </button>
                                        </div>
                                    </form>
                                </div>

                            {{-- empty_behavior = 2: organize event box --}}
                            @elseif($emptyBehavior === 2)
                                <div class="widget-action-box text-center">
                                    <h3>Want to make a difference?</h3>

                                    <p class="mb-4">
                                        Be the change in your community. If you don't see a cleanup
                                        near you, why not organize one yourself?
                                    </p>

                                    <a href="{{ route('front-post-a-event') }}" target="_blank" rel="noopener">
                                        <button type="button" class="primary-btn m-auto">ORGANIZE A CLEANUP</button>
                                    </a>
                                </div>
                            @endif

                            <div class="text-center">
                                <small>
                                    Powered by
                                    <a href="https://volunteercleanup.org" target="_blank">
                                        VolunteerCleanup.org
                                    </a>
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

            @else
                <div class="row">
                    <div class="col-12">
                        <div class="text-center mb-5">
                            <div class="widget-empty-state">
                                <div class="empty-image-wrap">
                                    <img
                                        src="{{ asset('assets_front/images/widget3.png') }}"
                                        alt="Widget unavailable"
                                    >
                                </div>
                            </div>

                            <p class="h3">The widget is currently unavailable</p>

                            <small>
                                Powered by
                                <a href="https://volunteercleanup.org" target="_blank">
                                    VolunteerCleanup.org
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('widget-create-alert-form');
            const submitButton = document.getElementById('widget-get-notified-button');

            if (!form || !submitButton) {
                return;
            }

            form.addEventListener('submit', function () {
                if (!form.checkValidity()) {
                    return;
                }

                submitButton.disabled = true;
                submitButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...';
            });
        });
    </script>
@endpush