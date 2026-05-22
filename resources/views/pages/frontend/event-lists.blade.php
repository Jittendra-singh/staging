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
            /* .event-empty-state {
                max-width: 560px;
                margin: 30px auto 70px;
                text-align: center;
            }

            .event-empty-state img {
                width: 190px;
                max-width: 100%;
                margin-bottom: 25px;
            }

            .event-empty-state h3 {
                max-width: 480px;
                margin: 0 auto 12px;
                color: #2b2b2b;
                font-size: 26px;
                font-weight: 800;
                line-height: 1.15;
            }

            .event-empty-state p {
                max-width: 440px;
                margin: 0 auto 24px;
                color: #4f4f4f;
                font-size: 14px;
                line-height: 1.5;
            }

            .event-empty-state .primary-btn {
                min-width: 210px;
            } */

            .event-empty-state {
                max-width: 520px;
                margin: 60px auto 80px;
                text-align: center;
            }

            .event-empty-state .empty-image-wrap {
                width: 170px;
                height: 170px;
                margin: 0 auto 26px;
                border-radius: 50%;
                overflow: hidden;
                background: #fff;
                box-shadow: 0 14px 35px rgba(0, 0, 0, 0.18);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .event-empty-state .empty-image-wrap img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .event-empty-state h3 {
                max-width: 460px;
                margin: 0 auto 12px;
                color: #2b2b2b;
                font-size: 24px;
                font-weight: 800;
                line-height: 1.15;
            }

            .event-empty-state p {
                max-width: 420px;
                margin: 0 auto 24px;
                color: #4f4f4f;
                font-size: 14px;
                line-height: 1.45;
            }

            .event-empty-state .primary-btn {
                width: auto;
                min-width: auto;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 13px 22px;
            }


    </style>
@endpush
@section('content')
    <div class="wrapper mt-0" style="background-color: #f5f5f5cf">
        <div class="hero-banner">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-sm-12 col-lg-6 d-none">
                        <div class="hero-banner-content" data-aos="fade-right" data-aos-offset="200" data-aos-duration="800"
                            data-aos-easing="ease-in-sine">
                            <h2>Find a Beach or Neighborhood <span class="d-block">Cleanup Near You</span></h2>
                            <p>Put in your address or zip code to show a list of upcoming cleanups. These cleanups are
                                organized by a variety of third party partners, non-profits, local businesses or
                                individual volunteer leaders. For questions about any specific cleanup below, please
                                contact that cleanup’s organizer from their event page.</p>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-12">
                        <div class="search-form main-form" data-aos="fade-left" data-aos-anchor="#example-anchor" data-aos-duration="800">
                            <form id="eventListForm">
                                <input type="hidden" name="sort_by_date" id="sort_by_date" value="{{request()->sort_by_date ?? 1}}"> 
                                <div class="row g-3">
                                    <div class="col-sm-5">
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <label>Location</label>
                                                <input type="text" id="autocomplete" class="text form-control h_50" name="location" placeholder="Location" value="{{request()->location}}">
                                                
                                                <input type="hidden" id="latitude" name="latitude" value="{{request()->latitude}}">
                                                <input type="hidden" id="longitude" name="longitude" value="{{request()->longitude}}">
                                                <div id="results"></div>
                                            </div>
                                            <div class="col-lg-4">
                                                <label>Distance (Miles)</label>
                                                <select class="selectpicker" id="distance" name="distance" title="Within">
                                                    <option @selected(request()->distance == 5) value="5">5</option>
                                                    <option @selected(request()->distance == 10) value="10">10</option>
                                                    <option @selected(request()->distance == 15  || !request()->has('distance')) value="15">15</option>
                                                    <option @selected(request()->distance == 25) value="25">25</option>
                                                    <option @selected(request()->distance == 50) value="50">50</option>
                                                    <option @selected(request()->distance == 100) value="100">100</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="unit" value="mi">
                                            <!-- <div class="col-lg-3">
                                                <label>Miles</label>
                                                <select class="selectpicker" name="unit" title="Distance Value">
                                                    <option @selected(request()->unit == "mi") value="mi">Miles</option>
                                                    <option @selected(request()->unit == "km") value="km">Kilometers</option>
                                                </select>
                                            </div> -->
                                        </div>
                                        <div class="row">
                                            <!-- <div class="col-lg-12"><small class="text-primary">Location filtering requires both Distance and Unit (Miles) to be specified.</small></div> -->
                                            <div class="col-lg-12"><small class="text-primary">Location filtering requires Distance (Miles) to be specified.</small></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="from-date">
                                                    <label>From</label>
                                                    <div class="loc-group position-relative">
                                                        <input class="form-control h_50 datepicker-here-p" name="start_date" data-language="en" type="date" placeholder="From" value="{{request()->start_date}}" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="to-date">
                                                    <label>To</label>
                                                    <div class="loc-group position-relative">
                                                        <input class="form-control h_50 datepicker-here-p" name="end_date" data-language="en" type="date" placeholder="To" value="{{request()->end_date}}" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <label>Category</label>
                                                <div class="loc-group position-relative">
                                                    <select class="selectpicker" name="event_category_id[]" title="Choose Categories"
                                                        data-selected-text-format="count > 1" data-size="5" multiple=""
                                                        data-live-search="true">
                                                        @foreach ($categories as $category)
                                                            <option 
                                                                @selected(in_array($category->id, request()->event_category_id ?? [])) 
                                                                value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <label>Keywords</label>
                                                <input type="text" name="search" value="{{request()->search}}" class="text form-control h_50"
                                                    placeholder="Keywords">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 pt-2">
                                    <a href="{{route('view-events')}}" class="primary-btn outlined">Reset</a>
                                    <button type="button" onclick="checkEventListForm()" class="primary-btn">Find Event</button>
                                </div>
                            </form>
                            <x-reusables.frontend.alert />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(count($events))
            <div class="container pt-5">
                <div class="row">
                    <div class="col-12 mt-2">
                        <div id="map" style="height:35rem;border-radius: 20px;overflow: hidden;"></div>
                    </div>
                </div>
                <x-reusables.google :locationarray="$location_array"/>
            </div>
        @endif

        <div class="container mt-5 mb-5">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12">
                    @if(count($events))
                        <div class="row" data-aos="fade-up">
                            <div class="row search-map py-4"> </div>
                            <div class="col-lg-4 text-left mb-4">
                                <h2>All Events</h2>
                            </div>
                            <div class="col-lg-8">
                                <div class="col-sm-3 offset-sm-9 mb-3">
                                    <label>Sort By Date</label>
                                    <select class="form-control" id="select_sort_by_date" name="select_sort_by_date" title="Sort By Date">
                                        <option @selected(request()->sort_by_date == 1  || !request()->has('sort_by_date')) value="1">Ascending</option>
                                        <option @selected(request()->sort_by_date == 2) value="2">Descending</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="">
                            <div class="featured-controls">
                                <x-reusables.frontend.event-block-view :events="$events->items()"/>
                                <div class="mt-5" data-aos="fade-up" data-aos-duration="800">
                                    {{ $events->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="event-empty-state" data-aos="fade-left" data-aos-anchor="#example-anchor" data-aos-duration="800">
                            <div class="empty-image-wrap">
                                <img src="{{ asset('assets_front/images/event_not_found.png') }}" alt="No event found">
                            </div>

                            <h3>The world is waiting for your help, but we couldn't find a match here.</h3>
                            <p>
                                Every hand makes a difference! Don't let this stop your journey.
                                Try broadening your search distance or clearing your keywords to find your next impact.
                            </p>

                            <a href="{{ route('view-events') }}" class="primary-btn">
                                <i class="fa fa-refresh"></i>Reset all filters &amp; start fresh
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
            
@endsection
