@php
$outPages = true;
@endphp
@extends('layouts.frontend')
@push('style')
<style>
    :target {
        scroll-margin-top: 120px;
    }

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

    .event-content .badge.upcoming {
        color: #1d4ed8;
        background-color: rgb(219 234 254 / 1);
    }

    .list-group-item.active a {
        color: white !important;
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
</style>
@endpush
@section('content')
<div class="wrapper mt-0" style="background-color: #f5f5f5cf">
    <div class="hero-banner">
        <!-- Hero Section -->
        <div class="container text-center my-5">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <img src="https://volunteercleanup.org/wp-content/uploads/2025/08/ICC-2025.jpg" class="w-100"
                        alt="International Coastal Cleanup 2025">
                </div>
                <div class="col-md-8 text-start">
                    <h1>International Coastal Cleanup Miami-Dade County - Saturday, Sept 20, 2025</h1>
                    <p>Spearheaded globally by the <a href="https://oceanconservancy.org/trash-free-seas/international-coastal-cleanup/" target="_blank">Ocean Conservancy</a>, VolunteerCleanup.org organizes
                        Miami-Dade County’s
                        local participation in this worldwide event with 70+ shoreline cleanups around the county each led
                        by our local non-profit and community partners.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="primary-btn scroll-mt-28" href="#find-icc-miami-dade-county-cleanup">Find A Miami-Dade Cleanup</a>
                        <a class="primary-btn scroll-mt-28" href="#groud-registration">Large Group Registration</a>
                        <a class="primary-btn" target="_blank" href="https://oceanconservancy.org/trash-free-seas/international-coastal-cleanup/map/">Find A Worldwide ICC Cleanup</a>
                        <a class="primary-btn" target="_blank" href="https://www.bonfire.com/store/volunteercleanuporg/">Purchase The 2025 Shirt</a>
                        <a class="primary-btn scroll-mt-28" href="#2024-result-and-news-coverage">See The 2024 ICC Results</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pledge Section -->
        <div class="container text-center my-5">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <img src="https://volunteercleanup.org/wp-content/uploads/2025/07/Stacked-blackcolor.png"
                        class="w-100" alt="Pledge to the ICC Trashathon">
                </div>
                <div class="col-md-8 text-start">
                    <h1>Pledge to the ICC Trashathon</h1>
                    <p>This year, we are launching our brand new TRASHATHON platform to raise money for the local
                        organizations making a difference every day, and who deserve (and need) our support. </p>
                    <p>You or your company can make a per-pound pledge for every pound of trash collected at your favorite
                        site, or across the entire program (make sure to put a maximum amount!).</p>
                    <p>After ICC, we will be opening this new platform to the world so groups and organizations can raise
                        funds by taking local community actions. Spread the word!</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="https://events.trashathon.org/icc2025/" target="_blank" class="sign-up-btn text-white">Make a per-pound pledge for the TRASHATHON</a>
                        <a href="https://volunteercleanup.org/sponsor-icc/" target="_blank" class="sign-up-btn text-white">Become a Corporate Sponsor</a>
                    </div>
                </div>
            </div>
        </div>



        <div class="container mt-5" id="find-icc-miami-dade-county-cleanup">
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
                    <h2 class="text-center mb-4">Find an ICC Miami-Dade County Cleanup</h2>
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
                                                <select class="selectpicker" name="event_category_id" title="Choose Categories"
                                                    data-selected-text-format="count > 1" data-size="5" multiple=""
                                                    data-live-search="true">
                                                    @foreach ($categories as $category)
                                                    <option
                                                        @selected($category->id == request()->event_category_id)
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
                                <a href="{{route('view-international-events')}}" class="primary-btn outlined">Reset</a>
                                <button type="button" onclick="checkEventListForm()" class="primary-btn">Find Event</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container pt-5">
        <div class="row">

            <div class="col-12 mt-2">
                <div id="map" style="height:35rem;border-radius: 20px;overflow: hidden;"></div>
            </div>
        </div>
        <x-reusables.google :locationarray="$location_array" />
    </div>

    <div class="container mt-5 mb-5">
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
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12">
                @if(count($events))
                <div class="">
                    <div class="featured-controls">
                        <x-reusables.frontend.event-block-view :events="$events->items()" />
                        <div class="mt-5" data-aos="fade-up" data-aos-duration="800">
                            {{ $events->links() }}
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="300" height="300" viewBox="0 0 748.974 457.275" role="img" artist="Katerina Limpitsouni" source="https://undraw.co/">
                        <g id="Group_201" data-name="Group 201" transform="translate(-382.003 -195.455)">
                            <g id="Group_200" data-name="Group 200" transform="translate(382.003 195.455)">
                                <path id="Path_3120-202" data-name="Path 3120" d="M695.225,508.82,433.394,576.244a34.622,34.622,0,0,1-42.114-24.866L312.1,243.879a34.622,34.622,0,0,1,24.866-42.114l243.591-62.727L642.9,166.948l77.191,299.757A34.622,34.622,0,0,1,695.225,508.82Z" transform="translate(-311.003 -139.037)" fill="#f2f2f2" />
                                <path id="Path_3121-203" data-name="Path 3121" d="M338.989,210.925a24.655,24.655,0,0,0-17.708,29.99l79.185,307.5a24.655,24.655,0,0,0,29.99,17.708L692.287,498.7a24.655,24.655,0,0,0,17.708-29.99L634,173.595l-54.792-24.529Z" transform="translate(-310.548 -138.556)" fill="#fff" />
                                <path id="Path_3122-204" data-name="Path 3122" d="M629.927,168.5l-40.522,10.435a11.518,11.518,0,0,1-14.026-8.282l-7.707-29.929a.72.72,0,0,1,.989-.837l61.379,27.258a.72.72,0,0,1-.113,1.355Z" transform="translate(-298.695 -139)" fill="#f2f2f2" />
                                <path id="Path_3123-205" data-name="Path 3123" d="M612.519,418.284l-119.208,30.7a5.759,5.759,0,0,1-2.872-11.154l119.208-30.7a5.759,5.759,0,1,1,2.872,11.154Z" transform="translate(-302.605 -126.189)" fill="#ccc" />
                                <path id="Path_3124-206" data-name="Path 3124" d="M640.149,430.592,497.936,467.214a5.759,5.759,0,1,1-2.872-11.154l142.213-36.622a5.759,5.759,0,0,1,2.872,11.154Z" transform="translate(-302.384 -125.599)" fill="#ccc" />
                                <circle id="Ellipse_44" data-name="Ellipse 44" cx="20.355" cy="20.355" r="20.355" transform="translate(121.697 319.055)" fill="#048cda" />
                                <path id="Path_3125-207" data-name="Path 3125" d="M604.421,374.437,446.1,415.191a17.835,17.835,0,0,1-21.694-12.812L391.229,273.49A17.835,17.835,0,0,1,404.041,251.8l158.32-40.754a17.835,17.835,0,0,1,21.694,12.812l33.178,128.889A17.835,17.835,0,0,1,604.421,374.437Z" transform="translate(-307.183 -135.611)" fill="#fff" />
                                <path id="Path_3126-208" data-name="Path 3126" d="M604.421,374.437,446.1,415.191a17.835,17.835,0,0,1-21.694-12.812L391.229,273.49A17.835,17.835,0,0,1,404.041,251.8l158.32-40.754a17.835,17.835,0,0,1,21.694,12.812l33.178,128.889A17.835,17.835,0,0,1,604.421,374.437ZM404.563,253.826a15.737,15.737,0,0,0-11.3,19.142l33.178,128.889a15.737,15.737,0,0,0,19.142,11.3L603.9,372.407a15.737,15.737,0,0,0,11.3-19.142L582.025,224.376a15.737,15.737,0,0,0-19.142-11.3Z" transform="translate(-307.183 -135.611)" fill="#e6e6e6" />
                                <path id="Path_411-209" data-name="Path 411" d="M550.66,252.63l-79.9,20.568a2.862,2.862,0,0,1-3.467-1.8,2.757,2.757,0,0,1,1.942-3.5l81.335-20.937c3.286,1.665,2.421,5.07.091,5.67Z" transform="translate(-303.514 -133.861)" fill="#f2f2f2" />
                                <path id="Path_412-210" data-name="Path 412" d="M554.1,266l-79.9,20.568a2.862,2.862,0,0,1-3.467-1.8,2.757,2.757,0,0,1,1.942-3.5l81.335-20.937c3.286,1.665,2.421,5.07.091,5.67Z" transform="translate(-303.349 -133.22)" fill="#f2f2f2" />
                                <path id="Path_413-211" data-name="Path 413" d="M461.146,298.825,436.761,305.1a3.1,3.1,0,0,1-3.776-2.23L425.577,274.1a3.1,3.1,0,0,1,2.23-3.776l24.385-6.277a3.105,3.105,0,0,1,3.776,2.23l7.408,28.777a3.1,3.1,0,0,1-2.23,3.776Z" transform="translate(-305.513 -133.047)" fill="#048cda" />
                                <path id="Path_414-212" data-name="Path 414" d="M562.854,293.445,440.909,324.835a2.862,2.862,0,0,1-3.467-1.8,2.757,2.757,0,0,1,1.942-3.5l123.38-31.76c3.286,1.665,2.421,5.07.091,5.67Z" transform="translate(-304.946 -131.904)" fill="#f2f2f2" />
                                <path id="Path_415-213" data-name="Path 415" d="M566.3,306.822,444.353,338.213a2.862,2.862,0,0,1-3.467-1.8,2.757,2.757,0,0,1,1.942-3.5l123.38-31.76c3.286,1.665,2.421,5.07.091,5.67Z" transform="translate(-304.781 -131.263)" fill="#f2f2f2" />
                                <path id="Path_416-214" data-name="Path 416" d="M569.739,320.192,447.794,351.582a2.862,2.862,0,0,1-3.467-1.8,2.757,2.757,0,0,1,1.942-3.5l123.379-31.76c3.286,1.665,2.421,5.07.091,5.67Z" transform="translate(-304.616 -130.621)" fill="#f2f2f2" />
                                <path id="Path_417-215" data-name="Path 417" d="M573.183,333.569,451.237,364.959a2.862,2.862,0,0,1-3.467-1.8,2.757,2.757,0,0,1,1.942-3.5l123.38-31.76C576.377,329.564,575.513,332.969,573.183,333.569Z" transform="translate(-304.45 -129.98)" fill="#f2f2f2" />
                                <path id="Path_418-216" data-name="Path 418" d="M576.624,346.939,454.679,378.329a2.862,2.862,0,0,1-3.467-1.8,2.757,2.757,0,0,1,1.942-3.5l123.38-31.76C579.819,342.934,578.954,346.339,576.624,346.939Z" transform="translate(-304.285 -129.339)" fill="#f2f2f2" />
                                <path id="Path_395-217" data-name="Path 395" d="M448.363,470.511a2.111,2.111,0,0,1-1.335-.092l-.026-.011-5.545-2.351a2.126,2.126,0,1,1,1.664-3.913l3.593,1.528,4.708-11.076a2.125,2.125,0,0,1,2.787-1.124h0l-.028.072.029-.073a2.127,2.127,0,0,1,1.124,2.788l-5.539,13.023a2.126,2.126,0,0,1-1.431,1.224Z" transform="translate(-304.809 -123.966)" fill="#fff" />
                            </g>
                            <g id="Group_199" data-name="Group 199" transform="translate(673.007 225.872) rotate(-8)">
                                <g id="Group_198" data-name="Group 198" transform="translate(125.896 0) rotate(19)">
                                    <path id="Path_3127-218" data-name="Path 3127" d="M304.956,386.7H34.583A34.622,34.622,0,0,1,0,352.114V34.583A34.622,34.622,0,0,1,34.583,0H286.121l53.418,42.577V352.114A34.622,34.622,0,0,1,304.956,386.7Z" transform="translate(0 0)" fill="#e6e6e6" />
                                    <path id="Path_3128-219" data-name="Path 3128" d="M24.627,0A24.655,24.655,0,0,0,0,24.627V342.158a24.655,24.655,0,0,0,24.627,24.627H295a24.655,24.655,0,0,0,24.627-24.627V37.418L272.683,0Z" transform="translate(9.956 9.956)" fill="#fff" />
                                    <path id="Path_3129-220" data-name="Path 3129" d="M128.856,11.518H5.759A5.759,5.759,0,0,1,5.759,0h123.1a5.759,5.759,0,0,1,0,11.518Z" transform="translate(123.512 90.767)" fill="#048cda" />
                                    <path id="Path_3130-221" data-name="Path 3130" d="M152.612,11.518H5.759A5.759,5.759,0,0,1,5.759,0H152.612a5.759,5.759,0,1,1,0,11.518Z" transform="translate(123.512 110.204)" fill="#048cda" />
                                    <path id="Path_3131-222" data-name="Path 3131" d="M128.852,0H5.758a5.758,5.758,0,1,0,0,11.517H128.852a5.759,5.759,0,0,0,0-11.517Z" transform="translate(123.517 177.868)" fill="#ccc" />
                                    <path id="Path_3132-223" data-name="Path 3132" d="M152.609,0H5.758a5.759,5.759,0,1,0,0,11.517h146.85a5.759,5.759,0,1,0,0-11.517Z" transform="translate(123.517 197.307)" fill="#ccc" />
                                    <path id="Path_3133-224" data-name="Path 3133" d="M128.856,11.518H5.759A5.759,5.759,0,0,1,5.759,0h123.1a5.759,5.759,0,0,1,0,11.518Z" transform="translate(123.512 264.975)" fill="#ccc" />
                                    <path id="Path_3134-225" data-name="Path 3134" d="M152.612,11.518H5.759A5.759,5.759,0,0,1,5.759,0H152.612a5.759,5.759,0,1,1,0,11.518Z" transform="translate(123.512 284.411)" fill="#ccc" />
                                    <circle id="Ellipse_44-2" data-name="Ellipse 44" cx="20.355" cy="20.355" r="20.355" transform="translate(57.655 85.89)" fill="#048cda" />
                                    <path id="Path_395-2-226" data-name="Path 395" d="M6.909,15.481a2.111,2.111,0,0,1-1.27-.422l-.023-.017L.832,11.382A2.126,2.126,0,0,1,3.419,8.008l3.1,2.376L13.839.832A2.125,2.125,0,0,1,16.819.439h0L16.774.5l.047-.063a2.127,2.127,0,0,1,.393,2.98L8.6,14.649a2.126,2.126,0,0,1-1.691.829Z" transform="translate(69.085 98.528)" fill="#fff" />
                                    <path id="Path_3135-227" data-name="Path 3135" d="M40.707,20.359A20.354,20.354,0,0,1,20.356,40.721a4.372,4.372,0,0,1-.524-.021A20.353,20.353,0,1,1,40.707,20.359Z" transform="translate(59.75 172.987)" fill="#048cda" />
                                    <circle id="Ellipse_44-3" data-name="Ellipse 44" cx="20.355" cy="20.355" r="20.355" transform="translate(57.655 260.097)" fill="#048cda" />
                                    <path id="Path_3136-228" data-name="Path 3136" d="M53.362,43.143H11.518A11.518,11.518,0,0,1,0,31.625V.72A.72.72,0,0,1,1.167.156l52.642,41.7a.72.72,0,0,1-.447,1.284Z" transform="translate(285.137 0.805)" fill="#ccc" />
                                </g>
                            </g>
                            <path id="Path_3140-229" data-name="Path 3140" d="M754.518,518.049a9.158,9.158,0,0,1-12.587,3.05L635.078,455.923a9.158,9.158,0,0,1,9.538-15.637l106.852,65.176a9.158,9.158,0,0,1,3.049,12.587Z" transform="translate(123.58 101.359)" fill="#3f3d56" />
                            <path id="Path_3141-230" data-name="Path 3141" d="M688.648,486.5a73.265,73.265,0,1,1-24.4-100.7A73.265,73.265,0,0,1,688.648,486.5ZM579.19,419.73a54.949,54.949,0,1,0,75.524-18.3,54.949,54.949,0,0,0-75.524,18.3Z" transform="translate(82.597 67.737)" fill="#3f3d56" />
                            <circle id="Ellipse_44-4" data-name="Ellipse 44" cx="57.007" cy="57.007" r="57.007" transform="translate(672.542 442.858) rotate(19)" fill="#048cda" />
                        </g>
                    </svg>
                    <p class="h3">No Event Found</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Group Registration Section -->
    <div class="container mb-5" id="groud-registration">
        <hr />
        <h2 class="text-center mb-4">Group Registration</h2>
        <p>The International Coastal Cleanup Miami-Dade is a free county-wide community event. We aim to create
            opportunities for any community members to join in, including school groups, service groups, activity groups
            and corporate groups alike. Here are some guidelines for groups, and if you have additional questions,
            <a href="https://volunteercleanup.org/contact-us/"> contact us.</a>
        </p>
        <p><strong>FOR GROUPS OF LESS THAN 10:</strong> Most organizers allow up to 10 tickets per order to facilitate
            families, a few
            friends or handful of colleagues volunteering together. The system will ask you to provide a name, email and
            phone for each of your attendees. If members of your group are not 100% committed, send them the link and
            have them register on their own. Please do not order a ‘placeholder’ ticket, as we allocate specific number
            of spaces and supplies, and accurate headcounts is really critical to maximizing impact of the ICC.
        </p>
        <p><strong>CORPORATE GROUPS (LARGER THAN 10):</strong> The public ICC cleanup might not be the best choice for
            large corporate
            groups as it poses a few challenges, including finding locations with enough available spaces to get
            everyone together. Additionally, many of the participating non-profits (including us) offer private
            corporate day of service cleanups (done by donation) throughout the year. Private Corporate Days of Service
            help fund the non-profit community, and in return, they are able to give you a personalized experience at
            your choice of location and date (like weekdays). Check out our <a href="https://volunteercleanup.org/corporate-days-of-service/">Corporate Day of Service Program here</a>, or
            you can Sponsor ICC which gives you dedicated reserved spots at most any location we have space. With that
            said, if you still want to join one of the public events, we ask that you communicate with the individual
            site captain (through the ‘Contact Organizer‘ button on their event page) to see how they can accommodate
            you. You can also consider breaking up into smaller groups and attending several different cleanups. We do
            encourage your business to support the organization leading the cleanup with a donation to their <a
                href="https://events.trashathon.org/icc2025/">Trashathon</a>,
            supplies, or in-kind support.
        </p>
        <p><strong>SERVICE / SCHOOL GROUPS:</strong> If the “Site Captain” has capacity to accommodate your group, they
            are able to
            “RESERVE” a special block of tickets for your folks. By creating a dedicated ticket type for your group, you
            will have plenty of time to organize and confirm attendance. You can either have everyone register
            individually (usually recommended so they receive any updates themselves) or you can register for everyone
            (great if you are all coming together by bus). When you register, the system will ask for a name, email and
            phone for each of your attendees. After September 15th, any unregistered tickets for your ticket block will
            be released back into the public pool to make space for other volunteers.
        </p>


    </div>

    <div class="container results-section my-5" id="2024-result-and-news-coverage">
        <hr />
        <h4 class="section-title text-center">2024 RESULTS AND NEWS COVERAGE !!!</h4>
        <div class="d-flex justify-content-between gap-2 flex-wrap my-4">
            <a target="_blank" href="https://www.bonfire.com/store/volunteercleanuporg/" class="primary-btn">Buy A Shirt From Previous Years</a>
            <a target="_blank" href="https://volunteercleanup.org/wp-content/uploads/2024/10/ICC-2024-Survey-Report-PDF-Final.pdf" class="primary-btn">See 2024 Event Impact</a>
            <a target="_blank" href="https://volunteercleanup.org/wp-content/uploads/2024/06/2023-ICC-Survey-Results.pdf" class="primary-btn">See 2023 Event Impact</a>
        </div>
        <p>ICC Miami-Dade County 2024 is a WRAP. Thank you to our Site Captains, Sponsors and Volunteers who supported
            this event with their time, energy and love for our Bay and Oceans.!. See the high-level results and news
            coverage below. If you want to join future cleanups throughout the year, you can search for a future
            <a href="{{route('view-events')}}">cleanup</a>, sign up for <a
                href="{{route('front-create-alert')}}"> weekly alerts</a>, or do your own <a
                href="https://volunteercleanup.org/solo-cleanup/">solo cleanup</a>.
        </p>
        <p>
            The 2024 ICC event was made possible by the generous support of our presenting sponsor
            <a href="https://bradyhunter.org/">The Brady Hunter Foundation</a>, with additional support from The
            Benjamin
            & Gloria Joannou, Jr. Family Conservation Fund,
            <a href="https://www.blackstone.com/our-impact/blackstone-charitable-foundation/">Blackstone Charitable
                Foundation</a>, <a href="https://www.citadel.com/">Citadel</a>, <a
                href="https://relatedgroup.com/philanthropy/">Related Group</a>, <a
                href="https://www.mbrisingabove.com/">City of
                Miami Beach</a>, <a href="https://www.miamidade.gov/biscaynebay/">Miami-Dade County RER-DERM</a>,
            <a href="http://www.pwc.com/">PricewaterhouseCoopers LLP</a>, <a href="https://www.miamiironside.com/">Miami
                Ironside</a>, <a href="http://www.savethemanatee.org/">Save The Manatee</a>, <a
                href="https://www.wm.com/">Waste
                Management</a> and Watersports Association
        </p>
        <p> If you would like to become a financial sponsor or partner for ICC Miami-Dade 2025, please contact us <a href="https://volunteercleanup.org/contact-us/">here</a>.
            If you would like to hire us for a Private Day of Service, you can find details here:
            <a
                href="https://volunteercleanup.org/corporate-days-of-service/">https://volunteercleanup.org/corporate-days-of-service/</a>.
        </p>


        <div class="d-flex align-items-center justify-content-center my-4">
            <div class="text-center">
                <img src="https://volunteercleanup.org/wp-content/uploads/2024/09/ICC-2024-Results-Large-1024x1024.jpg"
                    alt="Final Results" class="h-auto">
                <img src="https://volunteercleanup.org/wp-content/uploads/2025/05/ICC-2024-Sponsors-Thank-You-1024x348.png"
                    alt="">
            </div>
        </div>

        <!-- Embedded YouTube Video -->
        <!-- Video Section -->
        <div class="container my-5">
            <div class="row">
                <!-- Video List -->
                <div class="col-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h4 class="mb-0 p-0 text-center">ICC 2024 News Coverage</h4>
                        <p class="mb-0 p-0">3 videos</p>
                    </div>
                    <ul class="list-group" id="videoList">
                        <li class="list-group-item active" data-video="GUjvIOo-Z_s">
                            <a href="javascript:void(0)" class="d-flex align-items-center gap-2">
                                <img src="https://img.youtube.com/vi/GUjvIOo-Z_s/mqdefault.jpg" alt="" width="40px">
                                Channel 10 Pre-ICC Segment
                            </a>
                        </li>
                        <li class="list-group-item" data-video="oBTY8-ZmMKM">
                            <a href="javascript:void(0)" class="d-flex align-items-center gap-2">
                                <img src="https://img.youtube.com/vi/XHOmBV4js_E/mqdefault.jpg" alt="" width="40px">
                                Dora On The Weather Channel
                            </a>
                        </li>
                        <li class="list-group-item" data-video="dp-61p2H0Ag">
                            <a href="javascript:void(0)" class="d-flex align-items-center gap-2">
                                <img src="https://img.youtube.com/vi/ScMzIvxBSi4/mqdefault.jpg" alt="" width="40px">
                                Channel 10 Post-ICC Segment
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Video Player -->
                <div class="col-md-8">
                    <div class="ratio ratio-16x9">
                        <iframe id="mainVideo" src="https://www.youtube.com/embed/GUjvIOo-Z_s" title="YouTube video" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

@endsection