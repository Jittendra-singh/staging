@php
    $outPages = true;
@endphp
@extends('layouts.frontend')
@push('style')
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
        /* @media (min-width: 1200px) {
            .grid-event-layout {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        .grid {
            display: grid;
        }
        .grid-cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }
        .grid-cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }
        .grid-cols-1 {
            grid-template-columns: repeat(1, 1fr);
        } */
        header.header,footer.footer{
            display: none!important;
        }


        .widget-empty-state {
    max-width: 520px;
    margin: 60px auto 40px;
    text-align: center;
}

.widget-empty-state .empty-image-wrap {
    width: 115px;
    height: 115px;
    margin: 0 auto 26px;
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
    max-width: 460px;
    margin: 0 auto 12px;
    color: #2b2b2b;
    font-size: 24px;
    font-weight: 800;
    line-height: 1.15;
}
    </style>
@endpush
@section('content')
    <div class="wrapper mt-0" style="background-color: #f5f5f5cf">

        <div class="container mt-3 mb-5">
            @if(isset($widget->id))
                @if($widget->type==2)
                    @if(isset($location_array) && !empty($location_array))
                    <div class="row">
                        <div class="col-lg-12 text-left mb-2">
                            <h2>{{ $widget->custom_widget_header ?: 'Upcoming Cleanups' }}</h2>
                        </div>
                        <div class="col-12 mt-2">
                            <div id="map" style="height:35rem;border-radius: 20px;overflow: hidden;"></div>
                        </div>
                    </div>
                    <x-reusables.google :zoom="9" :locationarray="$location_array"/>
                    <small>Powered by <a href="https://volunteercleanup.org" target="_blank">VolunteerCleanup.org</a></small>
                    @else 
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <div class="text-center mb-5">
                                <div class="widget-empty-state">
                                    <div class="empty-image-wrap">
                                        <img src="{{ asset('assets_front/images/event_not_found.png') }}" alt="No event found">
                                    </div>
                                </div>
                                <h3 class="empty-widget-text">The world is waiting for your help, <br/>but we couldn't find a match here.</h3>
                                <small>Powered by <a href="https://volunteercleanup.org" target="_blank">VolunteerCleanup.org</a></small>
                            </div>
                        </div>
                    </div>
                    @endif
                @else 
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        @if(count($events))
                        <div class="col-lg-4 text-left mb-4">
                            <h2>{{ $widget->custom_widget_header ?: 'Upcoming Cleanups' }}</h2>
                        </div>
                        <div class="">
                            <div class="featured-controls">
                                <x-reusables.frontend.widget-event-block-view :widget="$widget" :events="$events->items()"/>
                                <div class="mt-3" data-aos="fade-up" data-aos-duration="800">
                                    {{ $events->links() }}
                                </div>
                                <small>Powered by <a href="https://volunteercleanup.org" target="_blank">VolunteerCleanup.org</a></small>
                            </div>
                        </div>
                        @else
                        <div class="text-center mb-5">
                            <div class="widget-empty-state">
                                <div class="empty-image-wrap">
                                    <img src="{{ asset('assets_front/images/event_not_found.png') }}" alt="No event found">
                                </div>
                            </div>
                            <h3 class="empty-widget-text">The world is waiting for your help, <br/>but we couldn't find a match here.</h3>
                            <small>Powered by <a href="https://volunteercleanup.org" target="_blank">VolunteerCleanup.org</a></small>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            @else 
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="text-center mb-5">
                        <div class="widget-empty-state">
                            <div class="empty-image-wrap">
                                <img src="{{ asset('assets_front/images/event_not_found.png') }}" alt="No event found">
                            </div>
                        </div>
                        <p class="h3">The widget is currently unavailable</p>
                        <small>Powered by <a href="https://volunteercleanup.org" target="_blank">VolunteerCleanup.org</a></small>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
            
@endsection
