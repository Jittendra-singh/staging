@extends('layouts.frontend')
@php 
//print_r($form_data);die();
$is_event_end = true;
if(isset($form_data->id) && !empty($form_data->end_datetime) && strtotime($form_data->end_datetime)>strtotime(date('Y-m-d H:i:s'))){
    $is_event_end = false;
} else if(!isset($form_data->id)){
    $is_event_end = false;
}
$eventCloseMsg = 'Your event has been saved as a draft. To make your event visible to volunteers, turn ON the “Publish” toggle on your Event List.';
$is_event_unpublish = true;
if(isset($form_data->id) && $form_data->status=='published'){
    $eventCloseMsg = 'Event details saved successfully.';
    $is_event_unpublish = false;
}
@endphp 
@push('style')
<link rel="stylesheet" href="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.css') }} ">
<link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }} ">
<style>
    .permission_selector [type="checkbox"]{
            border-color: black;
    }
    .step-app > .step-steps > li{
        width: 15%!important;
    }

    .select2-container{
        width: 100%!important;
    }
    

    .blur-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      backdrop-filter: blur(6px);
      background: rgba(255, 255, 255, 0.4);
      z-index: 10;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 1rem;
      border-radius: 8px;
    }
    .blur-overlay .message {
      background: rgba(0, 0, 0, 0.5);
      color: white;
      padding: 1rem 1.5rem;
      border-radius: 8px;
      font-size: 1.3rem;
      max-width: 90%;
    }
    .target-box {
      position: relative;
      /* border: 1px solid #ccc;
      border-radius: 8px; */
      /* padding: 2rem;
      background: #f9f9f9; */
    }
    .custom-disable-btn{
        pointer-events: none;
        background: gray !important;
        border-color: gray;
        opacity: .6;
    }



    /* Accordation css */
    .custom-ticket-container .accordion-header .accordion-button{
    font-size: 18px !important;
    font-weight: 600 !important;
    color: #000 !important; 
    }
      .custom-ticket-container .accordion-button:focus{
        border-color: transparent !important;
        box-shadow: none; 
    }
    .custom-ticket-container .accordion-button:not(.collapsed){
        background-color: transparent !important; 
    } 
    .custom-ticket-container .accordion-button span{
        font-size: 16px !important;
        font-weight: normal !important;
    }  
    .accordion-item:not(:first-of-type){
        border-top: 1px solid #dadada !important;
    }
    .accordion-button p.toggler:not(.collapsed)::after{
        transform:rotate(-180deg) !important;
    }
    .custom-ticket-container .accordion-item{ 
        margin-bottom: 15px !important;
    }


    .fee-option {
    width: 100%;
    cursor: pointer;
}

.fee-option input[type="radio"] {
    display: none;
}

.fee-card {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 16px;
    transition: all 0.2s ease;
    height: 100%;
}

.fee-option:hover .fee-card {
    border-color: #0d6efd;
}

.radio-dot {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid #ced4da;
    margin-top: 4px;
    position: relative;
}

.fee-option input[type="radio"]:checked + .fee-card {
    border-color: #0d6efd;
    background-color: #f8fbff;
}

.fee-option input[type="radio"]:checked + .fee-card .radio-dot {
    border-color: #0d6efd;
}

.fee-option input[type="radio"]:checked + .fee-card .radio-dot::after {
    content: "";
    width: 8px;
    height: 8px;
    background: #0d6efd;
    border-radius: 50%;
    position: absolute;
    top: 3px;
    left: 3px;
}
</style>
@endpush
@section('content')
    <!-- Body Start-->
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            <div class="container-fluid">
                <div class="row ">
                    <div class="col-lg-12 col-md-12">
                        <div class="d-main-title">
                            @if(isset($form_data->id) && !empty($form_data->id))
                                <h3><i class="fa-solid fa-square-plus me-3 mb-3"></i>Edit Cleanup</h3>
                                @php
                                    $breadcrumbs = [
                                        [
                                            'name' => 'Edit Cleanup', 
                                            'url' => route('front-post-a-event',[$form_data->encrypted_id])
                                        ],
                                    ]
                                @endphp     
                            @else 
                                <h3><i class="fa-solid fa-square-plus me-3 mb-3"></i>Post A New Cleanup</h3>
                                @php
                                    $breadcrumbs = [
                                        [
                                            'name' => 'Post A New Cleanup', 
                                            'url' => route('front-post-a-event')
                                        ],
                                    ]
                                @endphp     
                            @endif 
							<x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                           
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="wizard-steps-block">
                            <div id="add-event-tab" open_tab="{{ $open_tab }}" class="step-app">
                                <ul class="step-steps">
                                    <li class="active">
                                        <a href="#tab_step0">
                                            <span class="number"></span>
                                            <span class="step-name">Event Details</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#tab_step1">
                                            <span class="number"></span>
                                            <span class="step-name">Media & Other Info.</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#tab_step2">
                                            <span class="number"></span>
                                            <span class="step-name">Tickets & Waiver Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#tab_step3">
                                            <span class="number"></span>
                                            <span class="step-name">Co-Host Management</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#tab_step4">
                                            <span class="number"></span>
                                            <span class="step-name">SEO Details</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#tab_step5">
                                            <span class="number"></span>
                                            <span class="step-name">Cleanup Reporting</span>
                                        </a>
                                    </li>
                                </ul>
                                <div class="step-content">
                                    <div class="step-tab-panel step-tab-info active" id="tab_step0">
                                        <form id="basic-data-form" method="post" action="{{ route('front-save-cleanup-basic-details') }}">
                                            @csrf
                                            <input type="hidden" name="event_id" class="event_instance first_event_instance" value="{{ $form_data->encrypted_id ?? 0 }}">
                                            <div class="tab-from-content">
                                                <div class="main-card py-2">
                                                    <div class="bp-title">
                                                        <h4>
                                                            <i class="fa-solid fa-circle-info step_icon me-3"></i>
                                                            Event Details
                                                        </h4>
                                                    </div>
                                                    <x-reusables.frontend.alert />
                                                    <div class="p-4 bp-form main-form">
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12">
                                                                <div class="form-group">
                                                                    <label class="form-label fs-6">Event Title*</label>
                                                                    <input id="event_name" class="form-control h_50 get-slug" name="event_name"
                                                                        data-slugtarget="#slug"
                                                                        data-existingid="{{$form_data->id ?? 0}}"
                                                                        data-url="{{ route('get-ajax-slug', ['type' => 'event']) }}"
                                                                        type="text" placeholder="Enter event name here"
                                                                        value="{{ old('event_name', $form_data->event_name ?? '') }}" required />
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mt-4">
                                                                <label class="form-label fs-6">Your Event URL Slug <i class="fa fa-info-circle" data-bs-toggle="tooltip" data-bs-title="Your event page URL will display as www.volunteercleanup.com/events/slug"></i>

                                                                </label>
                                                                <div class="form-group hstack ">
                                                                    <input class="form-control h_50 slugmask event_slug" name="slug" type="text" placeholder="Enter event slug and validate" id="slug" value="{{ old('slug', $form_data->slug ?? '') }}">

                                                                    <button ajaxurl="{{ route('front-check-event-slug') }}" id="validate_event_slug" class="primary-btn w-fit ms-2" type="button">Validate</button>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 mt-4">
                                                                <div class="form-group">
                                                                    <label class="form-label fs-6">Event Category*</label>
                                                                    <select class="selectpicker" name="event_category_id"
                                                                        data-selected-text-format="count > 4" data-size="5"
                                                                        title="Select category" data-live-search="true" required>
                                                                        @foreach ($categories as $category)
                                                                            <option value="{{ $category->id }}"
                                                                                @selected(old('event_category_id', $form_data->event_category_id ?? 0) == $category->id)>
                                                                                {{ $category->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 mt-4">
                                                                <div class="form-group">
                                                                    <label class="form-label fs-16">Description*</label>
                                                                    <div class="text-editor">
                                                                        <textarea id="description" name="description" class="form-control ckeditor" rows="8"required >{{ old('description', $form_data->description ?? '') }}</textarea>
                                                                    </div>
                                                                    <!-- <p class="mt-1 text-primary">NOTE: If you are linking to
                                                                        an
                                                                        external site,
                                                                        make sure you highlight the link and then click the
                                                                        chain icon to turn it into a clickable link</p> -->
                                                                </div>
                                                            </div>
                                                            {{-- <div class="col-md-12 mt-4 text-center">
                                                                <div class="form-check">
                                                                    <label class="form-check-label gap-2"
                                                                        for="allow_community_service_form">
                                                                        <input class="form-check-input" type="checkbox"
                                                                            value="1" id="allow_community_service_form"
                                                                            name="allow_community_service_form"
                                                                            @checked(old('allow_community_service_form', $form_data->allow_community_service_form ?? false)) />
                                                                        Allow Community Form For this Event ?
                                                                    </label>
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="main-card py-2 mt-4">
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12">
                                                            <div class="bp-title">
                                                                <h4><i class="fa-solid fa-calendar step_icon me-3"></i>
                                                                    Date, Time & Location*
                                                                </h4>
                                                            </div>
                                                            <div class="p-4 bp-form main-form">
                                                                <div class="row g-4">
                                                                    @php 
                                                                    $longerThanOneWeek = false;
                                                                    $sDate = old('start_datetime') ? old('start_datetime') : ($form_data->start_datetime ?? '');
                                                                    $eDate = old('end_datetime') ? old('end_datetime') : ($form_data->end_datetime ?? '');
                                                                    if(!empty($sDate) && !empty($eDate)){
                                                                        $sDateP = \Carbon\Carbon::parse($sDate);
                                                                        $eDateP = \Carbon\Carbon::parse($eDate);
                                                                        $diffDays = $sDateP->diffInDays($eDateP);

                                                                        if ($diffDays >= 7) {
                                                                            $longerThanOneWeek = true;
                                                                        } else {
                                                                            $longerThanOneWeek = false;
                                                                        }
                                                                    }
                                                                    @endphp 
                                                                    <p class="text-secondary m-0" id="longerThanOneWeekText" style="{{$longerThanOneWeek?'':'display: none'}};">NOTE: This cleanup is longer than one week. If this is a recurring event, we recommend creating individual cleanups using the clone feature to keep attendance and reporting accurate.</p>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label  fs-6">Start Date Time.*</label>
                                                                        <div class="loc-group position-relative">
                                                                            <input autocomplete="off" class="form-control h_50 datepicker-here-pp" id="start_datetime" data-language="en" name="start_datetime" type="text" placeholder="YYYY/MM/DD" value="{{ !empty($sDate)?date('Y-m-d, H:i',strtotime($sDate)):'' }}" required />
                                                                            <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label class="form-label fs-6">End Date
                                                                            Time.*</label>
                                                                        <div class="loc-group position-relative">
                                                                            <input autocomplete="off" name="end_datetime"
                                                                                class="form-control h_50 datepicker-here-pp" id="end_datetime" data-language="en" type="text"
                                                                                placeholder="YYYY/MM/DD"
                                                                                value="{{!empty($eDate)?date('Y-m-d, H:i',strtotime($eDate)):''}}" required />
                                                                            <span class="absolute-icon"><i
                                                                                    class="fa-solid fa-calendar-days"></i></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group mt-4">
                                                                    <div class="row g-4">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label fs-6">Event Page Publish Date*</label>
                                                                            <div class="loc-group position-relative">
                                                                                <input autocomplete="off"
                                                                                    class="form-control h_50 datepicker-here-pp"
                                                                                    data-language="en"
                                                                                    name="site_publish_date" id="site_publish_date" type="text"
                                                                                    placeholder="YYYY/MM/DD"
                                                                                    value="{{ old('site_publish_date', $form_data->site_publish_date ?? date('Y-m-d')) }}" required />
                                                                                <span class="absolute-icon">
                                                                                    <i
                                                                                        class="fa-solid fa-calendar-days"></i></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="form-label fs-6">Event Page Expiry Date*</label>
                                                                            <div class="loc-group position-relative">
                                                                                <input autocomplete="off" name="site_depublish_date"
                                                                                    class="form-control h_50 datepicker-here-pp" id="site_depublish_date"
                                                                                    data-language="en" type="text"
                                                                                    placeholder="YYYY/MM/DD"
                                                                                    value="{{ old('site_depublish_date', $form_data->site_depublish_date ?? '') }}" required />
                                                                                <span class="absolute-icon"><i
                                                                                        class="fa-solid fa-calendar-days"></i></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group mt-4">
                                                                    <div class="row g-4">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label fs-6">Event
                                                                                timezone*</label>
                                                                            <div class="loc-group position-relative">
                                                                                <select class="selectpicker"
                                                                                    name="event_timezone"
                                                                                    title="Select Timezone" required>
                                                                                    @foreach ($timezones as $timezone)
                                                                                        <option
                                                                                            value="{{ $timezone }}"
                                                                                            @selected(old('event_timezone', $form_data->event_timezone ?? '') == $timezone)>
                                                                                            {{ ucfirst($timezone) }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="form-label fs-6">Event
                                                                                Country*</label>
                                                                            <div class="loc-group position-relative">
                                                                                <select class="selectpicker"
                                                                                    name="country_id"
                                                                                    title="Select Event Country" required>
                                                                                    @foreach ($countries as $country)
                                                                                        <option
                                                                                            value="{{ $country->country_id }}"
                                                                                            @selected(old('country_id', $form_data->country_id ?? 1) == $country->country_id)>
                                                                                            {{ $country->country_name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group mt-4">
                                                                    <div class="row g-4">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label fs-6">Event Address or Venue Name*</label>
                                                                            <input class="form-control h_50" name="venue" id="venue" type="text" placeholder="Enter here" value="{{ old('venue', $form_data->venue ?? '') }}" required />
                                                                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $form_data->latitude ?? '') }}">
                                                                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $form_data->longitude ?? '') }}">
                                                                            <div id="results"></div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="form-label fs-6">Zip
                                                                                Code*</label>
                                                                            <input class="form-control h_50"
                                                                                name="pincode" id="pincode" type="text"
                                                                                placeholder="Enter here"
                                                                                value="{{ old('pincode', $form_data->pincode ?? '') }}" required />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group mt-2">
                                                                        <label class="private-event d-flex mt-4">
                                                                            <input type="checkbox" name="is_private"
                                                                                value="1"
                                                                                @checked(old('is_private', $form_data->is_private ?? false))>
                                                                            <span class="ms-2">This is a private
                                                                                event, do not list it on public
                                                                                website</span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="main-card py-2 mt-4">
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12">
                                                            <div class="bp-title">
                                                                <h4><i class="fa-solid fa-flag step_icon me-3"></i>
                                                                    Participation Type
                                                                </h4>
                                                            </div>
                                                            <div class="p-4 bp-form main-form">
                                                                <div class="content-holder row  ">
                                                                    <div class="setting-item col-sm-12">
                                                                        <!-- <div class="d-flex align-items-start">
                                                                            <div class="d-flex flex-column">
                                                                                <label class="color-black fw-bold mb-3">Who will bear the Stripe processing charges and platform fees for paid tickets?</label>
                                                                            </div>
                                                                        </div> -->
                                                                        <div class="col-12">
                                                                            <div class="row g-3">
                                                                                <!-- Organizer -->
                                                                                <div class="col-md-6">
                                                                                    <label class="fee-option">
                                                                                        <input type="radio" value="1" id="participation_type" name="participation_type" {{ old('participation_type', $form_data->participation_type ?? 1) == 1 ? 'checked' : '' }}>
                                                                                        <div class="fee-card">
                                                                                            <div class="d-flex align-items-start">
                                                                                                <span class="radio-dot"></span>
                                                                                                <div class="ms-3">
                                                                                                    <h6 class="mb-1">Use VolunteerCleanup Tickets</h6>
                                                                                                    <small class="text-muted">
                                                                                                        Manage registrations, issue tickets, and track attendees within the system.
                                                                                                    </small>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </label>
                                                                                </div>

                                                                                <!-- Buyer -->
                                                                                <div class="col-md-6">
                                                                                    <label class="fee-option">
                                                                                        <input type="radio" value="2" id="participation_type2" name="participation_type" {{ old('participation_type', $form_data->participation_type ?? 1) == 2 ? 'checked' : '' }}>
                                                                                        <div class="fee-card">
                                                                                            <div class="d-flex align-items-start">
                                                                                                <span class="radio-dot"></span>
                                                                                                <div class="ms-3">
                                                                                                    <h6 class="mb-1">Walk-in Mode</h6>
                                                                                                    <small class="text-muted">
                                                                                                       Allow attendees without prior registration or ticket requirements.
                                                                                                    </small>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </label>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <label class="fee-option">
                                                                                        <input type="radio" value="3" id="participation_type3" name="participation_type" {{ old('participation_type', $form_data->participation_type ?? 1) == 3 ? 'checked' : '' }}>
                                                                                        <div class="fee-card">
                                                                                            <div class="d-flex align-items-start">
                                                                                                <span class="radio-dot"></span>
                                                                                                <div class="ms-3">
                                                                                                    <h6 class="mb-1">Informational Only</h6>
                                                                                                    <small class="text-muted">
                                                                                                        Display event details only — no registration or ticketing required.
                                                                                                    </small>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </label>
                                                                                </div>

                                                                                <div class="col-md-6">
                                                                                    <label class="fee-option">
                                                                                        <input type="radio" value="4" id="participation_type4" name="participation_type" {{ old('participation_type', $form_data->participation_type ?? 1) == 4 ? 'checked' : '' }}>
                                                                                        <div class="fee-card">
                                                                                            <div class="d-flex align-items-start">
                                                                                                <span class="radio-dot"></span>
                                                                                                <div class="ms-3">
                                                                                                    <h6 class="mb-1">External Registration Link</h6>
                                                                                                    <small class="text-muted">
                                                                                                        Redirect attendees to an external platform to complete registration.
                                                                                                    </small>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </label>
                                                                                    <input class="form-control h_50 urlmask mt-2" style="{{ old('participation_type', $form_data->participation_type ?? 1) == 4 ? 'display: block;' : 'display: none;' }}"
                                                                                        name="participation_type_external_link" id="participation_type_external_link" type="url"
                                                                                        placeholder="Enter external link here"
                                                                                        value="{{ old('participation_type_external_link', $form_data->participation_type_external_link ?? '') }}" />
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
                                        </form>
                                    </div>
                                    <div class="step-tab-panel step-tab-extra" id="tab_step1">
                                        <form method="post" action="{{ route('front-save-cleanup-media-details') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="event_id" class="event_instance"
                                                value="{{ $form_data->encrypted_id ?? 0 }}">
                                            <div class="tab-from-content">
                                                <div class="main-card py-2">
                                                    <div class="bp-title">
                                                        <h4><i class="fa-solid fa-circle-info step_icon me-3"></i>Media &
                                                            Other Information</h4>
                                                    </div>
                                                    <div class="p-4 bp-form main-form">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <div class="row g-2">
                                                                        <div class="col-md-12">
                                                                            <div class="content-holder">
                                                                                <label class="form-label fs-6">Event Banner
                                                                                    and Thumbnail Image</label>
                                                                                <div class="default-event-thumb image-container">
                                                                                    <div class="default-event-thumb-btn">
                                                                                        <div class="thumb-change-btn">
                                                                                            <input accept="image/*" type="file" class="change-image-preview" id="banner_image" name="banner_image">
                                                                                            <label
                                                                                                for="banner_image">Change Image</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    @php 
                                                                                    $banner_image = isset($form_data) ? $form_data->formattedBanner() : asset('assets_front/images/event-imgs/big-2.jpg');
                                                                                    if(!isset($form_data)){
                                                                                        $bimage = old('banner_image', $form_data->banner_image ?? '');
                                                                                        if(!$bimage){
                                                                                            $path = 'assets_front/images/event-imgs/big-2.jpg';
                                                                                        }else{
                                                                                            $path = $bimage->path;
                                                                                        }              
                                                                                        $banner_image =  asset($path);
                                                                                    }
                                                                                    @endphp 
                                                                                    <img src="{{ $banner_image }}" class="image-preview"
                                                                                        alt="">
                                                                                    @if(isset($bimage) && !empty($bimage))
                                                                                    <input type="hidden" name="banner_image_data" value="{{json_encode($bimage)}}">
                                                                                    @endif 
                                                                                </div>
                                                                                <p class="mt-1">Recommended file size: 5 MB and image size: 1000 × 660 px</p>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" name="flyer_image">
                                                                        <!-- <div class="col-md-6">
                                                                            <div class="content-holder">
                                                                                <label class="form-label fs-6">Event Graphic or Flyer</label>
                                                                                <div class="default-event-thumb image-container">
                                                                                    <div class="default-event-thumb-btn">
                                                                                        <div class="thumb-change-btn">
                                                                                            <input accept="image/*" type="file" class="change-image-preview" id="flyer_image" name="flyer_image">
                                                                                            <label for="flyer_image">Change Image</label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <img src="{{ isset($form_data) ? $form_data->formattedFlyerImage() : asset('assets_front/images/event-imgs/big-2.jpg')}}" class="image-preview"
                                                                                        alt="">
                                                                                </div>
                                                                                <p class="mt-1">If you have an additional flyer you can include it here</p>
                                                                            </div>
                                                                        </div> -->
                                                                        <!-- <div class="mt-2 col-md-12">
                                                                            <label class="form-label fs-6">Video URL</label>
                                                                            <input class="form-control h_50 urlmask"
                                                                                type="text" name="video_url"
                                                                                placeholder=""
                                                                                value="{{ old('video_url', $form_data->video_url ?? '') }}" />
                                                                        </div> -->
                                                                        <input type="hidden" name="event_reoccurance" value="yearly">
                                                                        <!-- <div class="mt-2 col-md-6">
                                                                            <label
                                                                                class="form-label fs-6">Frequency</label>
                                                                            <select name="event_reoccurance"
                                                                                id="event_reoccurance"
                                                                                class="form-select h_50">
                                                                                @foreach ($reoccurring as $value)
                                                                                    <option value="{{ $value }}"
                                                                                        @selected(old('event_reoccurance', $form_data->event_reoccurance ?? '') == $value)>
                                                                                        {{ ucfirst($value) }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div> -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="main-card py-2 mt-4">
                                                    <div class="form-group">
                                                        <div class="bp-title">
                                                            <h4><i class="fa-solid fa-info step_icon me-3"></i>Information
                                                                for Volunteers</h4>
                                                        </div>
                                                        <div class="stepper-data-set px-4 pb-4">
                                                            <div class="content-holder template-selector">
                                                                <div class="row g-2">
                                                                    @php
                                                                        $bring_tool_obj = old('bring_tools', @$form_data->bringTools ? $form_data->bringTools->pluck('id')->toArray() : []);
                                                                        $provided_tool_obj = old('provided_tools', @$form_data->providedTools ? $form_data->providedTools->pluck('id')->toArray() : [])
                                                                    @endphp
                                                                    <div class="col-md-6">
                                                                        <div class="form-group mt-2">
                                                                            <label class="form-label fs-6">What Should Volunteers Bring?</label>
                                                                            <select class="selectpicker-not select2" id="bring_tools"
                                                                                name="bring_tools[]" multiple
                                                                                data-selected-text-format="count > 1"
                                                                                data-size="5" title="Select Some Options"
                                                                                data-live-search="true">
                                                                                @foreach ($bring_tools as $tool)
                                                                                    <option value="{{ $tool->id }}"
                                                                                        @disabled(in_array($tool->id, $provided_tool_obj)) 
                                                                                        @selected(collect($bring_tool_obj)->contains($tool->id))>
                                                                                        {{ $tool->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group mt-2">
                                                                            <label class="form-label fs-6">What Will Be Provided?</label>
                                                                            <select class="selectpicker-old select2" id="provided_tools"
                                                                                name="provided_tools[]" multiple
                                                                                data-selected-text-format="count > 1"
                                                                                data-size="5" title="Select Some Options"
                                                                                data-live-search="true">
                                                                                @foreach ($provided_tools as $tool)
                                                                                    <option value="{{ $tool->id }}"
                                                                                        @disabled(in_array($tool->id, $bring_tool_obj))
                                                                                        @selected(collect($provided_tool_obj)->contains($tool->id))>
                                                                                        {{ $tool->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <!-- <div class="col-md-6">
                                                                        <div class="form-group mt-2">
                                                                            <label class="form-label fs-6">Volunteer
                                                                                Tags</label>
                                                                            <select class="selectpicker select2"
                                                                                name="volunteer_tags[]" multiple
                                                                                data-selected-text-format="count > 1"
                                                                                data-size="5" title="Select Some Options"
                                                                                data-live-search="true">
                                                                                @foreach ($volunteer_tags as $tago)
                                                                                    <option value="{{ $tago->id }}"
                                                                                        @selected(collect(old('volunteer_tags', $form_data && $form_data->volunteertags ? $form_data->volunteertags->pluck('id')->toArray() : []))->contains($tago->id))>
                                                                                        {{ $tago->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div> -->
                                                                    <!-- <div class="col-md-4">
                                                                        <div class="form-group mt-2">
                                                                            <label class="form-label fs-6">Event Tags</label>
                                                                            <select class="selectpicker select2" name="tags[]"
                                                                                multiple
                                                                                data-selected-text-format="count > 1"
                                                                                data-size="5" title="Select Some Options"
                                                                                data-live-search="true">
                                                                                @foreach ($event_tags as $tagp)
                                                                                    <option value="{{ $tagp->id }}"
                                                                                        @selected(collect(old('tags', $form_data ? $form_data->tags->pluck('id')->toArray() : []))->contains($tagp->id))>
                                                                                        {{ $tagp->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div> -->
                                                                    <div class="col-md-6">
                                                                        <div class="form-group mt-2">
                                                                            <label class="form-label fs-6">Parking
                                                                                Info</label>
                                                                            <textarea name="parking_info_details" rows="5" class="form-control h_50 ckeditor" placeholder="">{{ old('parking_info_details', $form_data->parking_info_details ?? '') }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group mt-2">
                                                                            <label class="form-label fs-6">Meeting Spot
                                                                                Details</label>
                                                                            <textarea name="meeting_spot_details" rows="5" class="form-control h_50 ckeditor" placeholder="">{{ old('meeting_spot_details', $form_data->meeting_spot_details ?? '') }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="step-tab-panel step-tab-gallery target-box" id="tab_step2">
                                        @php
                                            $tickets = old('tickets', $form_data->eventTickets ?? []);
                                        @endphp
                                        <form method="post" action="{{ route('front-save-cleanup-ticket-details') }}">
                                            @csrf
                                            <input type="hidden" name="save_ticket_type_url" id="save_ticket_type_url" value="{{route('save-ticket-type')}}">
                                            <input type="hidden" name="event_id" class="event_instance"
                                                value="{{ $form_data->encrypted_id ?? 0 }}">
                                            <div class="tab-from-content">
                                                <div class="main-card p-4 mt-4">
                                                    <div
                                                        class="bp-title mb-4 d-flex align-items-center justify-content-between">
                                                        <h4 class="px-0 mb-0">
                                                            <i class="fa-solid fa-ticket step_icon me-3"></i>Ticket Types
                                                        </h4>
                                                        <button type="button" onclick="openTicketTypePopup(this);" url="{{ route('manage-ticket-type-html') }}" class="primary-btn w-auto" >Add Ticket Type</button>
                                                    </div>
                                                    @if(!(!empty(auth()->guard('front')->user()->stripe_email) && !empty(auth()->guard('front')->user()->stripe_account_id)))
                                                    <div class="alert alert-warning" role="alert">
                                                        Connect your Stripe account to display paid tickets on the front end. <a target="_blank" href="{{ route('front-profile') }}">Connect Now</a>
                                                    </div>
                                                    @endif

                                                    <div class="custom-ticket-container">
                                                        <div class="accordion mb-4 ticket-container ticket-container-section" id="accordionExample">
                                                            <table class="table">
                                                                <tr>
                                                                    <th>Ticket Name</th>
                                                                    <th>Type</th>
                                                                    <th>Price</th>
                                                                    <th>Sold</th>
                                                                    <th>Remaining</th>
                                                                    <th>Dates</th>
                                                                    <th>Status</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                                @if(isset($tickets[0]) && $tickets->count() > 0)
                                                                @foreach ($tickets as $counter => $ticket)
                                                                    <tr>
                                                                        <td>{{ $ticket->ticket_name }}</td>
                                                                        <td>{{ ucfirst($ticket->ticket_type) }}</td>
                                                                        <td>{{ config('app.currency').formatAmt($ticket->ticket_price) }}</td>
                                                                        <td>{{ $ticket->approvedSold->count() ?? 0 }}</td>
                                                                        <td>{{ $ticket->remainingTickets() }}</td>
                                                                        <td>{{ date(config('app.dateFormat'),strtotime($ticket->sell_start_time)).' - '.date(config('app.dateFormat'),strtotime($ticket->sell_end_time)) }}</td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <div class="d-flex align-items-start">
                                                                                    <label class="btn-switch m-0 me-3">
                                                                                        <input value="1" name="event_ticket_status_toggle" type="checkbox" {{ isset($ticket->is_active)?($ticket->is_active==1 ? 'checked' : ''):'checked' }}  class="event_ticket_status_toggle" data-url="{{route('update-ticket-status',encrypt($ticket->id))}}">
                                                                                        <span class="checkbox-slider"></span>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td><button onclick="openTicketTypePopup(this);" url="{{ route('manage-ticket-type-html', ['id'=>encrypt($ticket->id)]) }}" type="button" class="btn btn-primary btn-sm"><i class="fa-solid fa-pen"></i></button></td>
                                                                    </tr>
                                                                @endforeach
                                                                @else 
                                                                <tr>
                                                                    <td colspan="8" class="text-center">No ticket type found</td>
                                                                </tr>
                                                                @endif 
                                                            </table>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="main-card p-4 mt-4">
                                                    <div
                                                        class="bp-title d-flex align-items-center justify-content-between">
                                                        <h4 class="px-0 mb-0">
                                                            <i class="fa-solid fa-ticket step_icon me-3"></i>Ticket Settings
                                                        </h4>
                                                    </div>
                                                    <input type="hidden" name="ticket_sell_start_time" value="">
                                                    <input type="hidden" name="ticket_sell_end_time" value="">
                                                    <!-- <div class="row mb-4">
                                                        <div class="col-md-6">
                                                            <label for="ticket_sell_start_time" class="form-label fs-6">Sell Start Date*</label>
                                                            <div class="loc-group position-relative">
                                                                <input type="text" class="form-control h_50" id="ticket_sell_start_time" name="ticket_sell_start_time" value="{{ old('ticket_sell_start_time', $form_data->ticket_sell_start_time ?? '') }}" required />
                                                                <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="ticket_sell_end_time" class="form-label fs-6">Sell End Date*</label>
                                                            <div class="loc-group position-relative">
                                                                <input type="text" class="form-control h_50" id="ticket_sell_end_time" name="ticket_sell_end_time" value="{{ old('ticket_sell_end_time', $form_data->ticket_sell_end_time ?? '') }}" required />
                                                                <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                    <div class="row mb-4">
                                                        <div class="form-group">
                                                            <div class="ticket-section">
                                                                <label class="form-label fs-16 mt-3">CHECKOUT & REGISTRATION</label>
                                                                <div class="content-holder row border_bottom pb_30">
                                                                    <div class="setting-item col-sm-6 mt-4">
                                                                        @php 
                                                                        $questionText = old('ticket_questions', $form_data->ticket_questions ?? '');
                                                                        @endphp 
                                                                        <div class="d-flex align-items-start">
                                                                            <label class="btn-switch m-0 me-3">
                                                                                <input type="checkbox" name="show_ticket_questions" class="" id="bird-discount" value="1" {{!empty($questionText)?'checked':''}}>
                                                                                <span class="checkbox-slider"></span>
                                                                            </label>
                                                                            <div class="d-flex flex-column">
                                                                                <label class="color-black fw-bold mb-1">Do you want to ask any questions of the volunteers?</label>
                                                                                <small class="fs-14 d-block m-0">This will display under each attendee details on Checkout Page</small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="online-event-discount-wrapper" style="{{!empty($questionText)?'':'display:none;'}}">
                                                                            <div class="ticket-instructions-content tags-container mt-3">
                                                                                <input name="ticket_questions" class="form-control tags-input" type="text" placeholder="Enter question here" value="{{ $questionText }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="setting-item col-sm-6 mt-4">
                                                                        <div class="d-flex align-items-start">
                                                                            <label class="btn-switch m-0 me-3">
                                                                                <input type="checkbox" value="1" id="allow_donations" name="allow_donations" {{ old('allow_donations', $form_data->allow_donations ?? false) ? 'checked' : '' }}>
                                                                                <span class="checkbox-slider"></span>
                                                                            </label>
                                                                            <div class="d-flex flex-column">
                                                                                <label class="color-black fw-bold mb-1">Allow optional donations</label>
                                                                                <small class="fs-14 d-block m-0">Enable an additional open donation field</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="ticket-section">
                                                                <label class="form-label fs-16 pt_30">DISPLAY & POLICIES</label>
                                                                <div class="content-holder row border_bottom pb_30">
                                                                    <div class="setting-item col-sm-6 mt-4">
                                                                        <div class="d-flex align-items-start">
                                                                            <label class="btn-switch m-0 me-3">
                                                                                <input type="checkbox" value="1" id="show_ticket_description" name="show_ticket_description" {{ old('show_ticket_description', $form_data->show_ticket_description ?? false) ? 'checked' : '' }}>
                                                                                <span class="checkbox-slider"></span>
                                                                            </label>
                                                                            <div class="d-flex flex-column">
                                                                                <label class="color-black fw-bold mb-1">Show ticket description on event page?</label>
                                                                                <small class="fs-14 d-block m-0">Provide extra context for each ticket type</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="setting-item col-sm-6 mt-4">
                                                                        <div class="d-flex align-items-start">
                                                                            <label class="btn-switch m-0 me-3">
                                                                                <input type="checkbox" value="1" id="show_remaining_tickets" name="show_remaining_tickets" {{ old('show_remaining_tickets', $form_data->show_remaining_tickets ?? false) ? 'checked' : '' }}>
                                                                                <span class="checkbox-slider"></span>
                                                                            </label>
                                                                            <div class="d-flex flex-column">
                                                                                <label class="color-black fw-bold mb-1">Show remaining tickets with tickets details?</label>
                                                                                <small class="fs-14 d-block m-0">Display real-time availability on event page</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="setting-item col-sm-6 mt-4">
                                                                        <div class="d-flex align-items-start">
                                                                            <label class="btn-switch m-0 me-3">
                                                                                <input type="checkbox" value="1" id="allow_refund" name="allow_refund" {{ old('allow_refund', $form_data->allow_refund ?? false) ? 'checked' : '' }}>
                                                                                <span class="checkbox-slider"></span>
                                                                            </label>
                                                                            <div class="d-flex flex-column">
                                                                                <label class="color-black fw-bold mb-1">Allow paid tickets refund</label>
                                                                                <small class="fs-14 d-block m-0">Set if users can request refunds via dashboard</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="ticket-section">
                                                                <label class="form-label fs-16 mt-3">FEES & PROCESSING</label>
                                                                <div class="content-holder row  ">
                                                                    <div class="setting-item col-sm-12 mt-4">
                                                                        <div class="d-flex align-items-start">
                                                                            <div class="d-flex flex-column">
                                                                                <label class="color-black fw-bold mb-3">Who will bear the Stripe processing charges and platform fees for paid tickets?</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="row g-3">
                                                                                <!-- Organizer -->
                                                                                <div class="col-md-6">
                                                                                    <label class="fee-option">
                                                                                        <input type="radio" value="0" id="charge_bear_by" name="charge_bear_by" {{ old('charge_bear_by', $form_data->charge_bear_by ?? 0) == 0 ? 'checked' : '' }}>
                                                                                        <div class="fee-card">
                                                                                            <div class="d-flex align-items-start">
                                                                                                <span class="radio-dot"></span>
                                                                                                <div class="ms-3">
                                                                                                    <h6 class="mb-1">Organizer</h6>
                                                                                                    <small class="text-muted">
                                                                                                        Deducted from your ticket revenue
                                                                                                    </small>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </label>
                                                                                </div>

                                                                                <!-- Buyer -->
                                                                                <div class="col-md-6">
                                                                                    <label class="fee-option">
                                                                                        <input type="radio" value="1" id="charge_bear_by2" name="charge_bear_by" {{ old('charge_bear_by', $form_data->charge_bear_by ?? 0) == 1 ? 'checked' : '' }}>
                                                                                        <div class="fee-card">
                                                                                            <div class="d-flex align-items-start">
                                                                                                <span class="radio-dot"></span>
                                                                                                <div class="ms-3">
                                                                                                    <h6 class="mb-1">Buyer</h6>
                                                                                                    <small class="text-muted">
                                                                                                        Added on top of the ticket price
                                                                                                    </small>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="row mb-4">
                                                        <div class="col-md-12">
                                                            <label for="ticket_questions" class="form-label fs-6">Do you want to ask any questions of the volunteers?</label>
                                                            <textarea name="ticket_questions" class="form-control" id="ticket_questions" cols="30" rows="3">{{ old('ticket_questions', $form_data->ticket_questions ?? '') }}</textarea>
                                                        </div>
                                                    </div> -->
                                                    <!-- <div class="row mb-4">
                                                        <div class="col-md-4">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="1" id="show_ticket_description"
                                                                    name="show_ticket_description"
                                                                    {{ old('show_ticket_description', $form_data->show_ticket_description ?? true) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="show_ticket_description">
                                                                    Show ticket description on event page?
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="1" id="allow_donations"
                                                                    name="allow_donations"
                                                                    {{ old('allow_donations', $form_data->allow_donations ?? true) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="allow_donations">
                                                                    Allow optional donations
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="1" id="allow_refund"
                                                                    name="allow_refund"
                                                                    {{ old('allow_refund', $form_data->allow_refund ?? true) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="allow_refund">
                                                                    Allow paid tickets refund
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="1" id="show_remaining_tickets"
                                                                    name="show_remaining_tickets"
                                                                    {{ old('show_remaining_tickets', $form_data->show_remaining_tickets ?? true) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="show_remaining_tickets">
                                                                    Show remaining tickets with tickets detail at single
                                                                    event page?
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                    <!-- <div class="row mb-4">
                                                        <div class="col-md-12">
                                                            <label for="ticket_questions" class="form-label fs-6">Who will bear the Stripe processing charges and platform fees for paid tickets?</label>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" value="0" id="charge_bear_by" name="charge_bear_by"
                                                                    {{ old('charge_bear_by', $form_data->charge_bear_by ?? 0) == 0 ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="charge_bear_by">
                                                                    Organizer
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" value="1" id="charge_bear_by2" name="charge_bear_by"
                                                                    {{ old('charge_bear_by', $form_data->charge_bear_by ?? 0) == 1 ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="charge_bear_by2">
                                                                    Buyer
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                </div>
                                                <div class="main-card p-4 mt-4">
                                                    <div
                                                        class="bp-title mb-4 d-flex align-items-center justify-content-between">
                                                        <h4 class="px-0 mb-0">
                                                            <i class="fa-solid fa-ticket step_icon me-3"></i> Registration & Waiver Settings
                                                        </h4>
                                                    </div>

                                                    <div class="waiver-container-section">
                                                        @php
                                                            $waiver_form_type = old('waiver_form_type', $form_data->waiver_form_type ?? null);
                                                            $waiver_form_content = old('waiver_form_content', $form_data->waiver_form_content ?? null);
                                                            //$waiver_form_type = $form_data->waiver_form_type ?? null;
                                                        @endphp
                                                       <div class="row mt-3">
                                                            <div class="col-12">
                                                                <div class="mb-3">
                                                                    <label for="waiver_form_type" class="form-label">Waiver Form Type</label>
                                                                    <select class="form-select" id="waiver_form_type" name="waiver_form_type">
                                                                        <option value="default" @selected($waiver_form_type == 'default') >Default Content</option>
                                                                        <option value="text" @selected($waiver_form_type == 'text') >Custom Content</option>
                                                                        <option value="pdf"  @selected($waiver_form_type == 'pdf')> Upload PDF</option>
                                                                        <option value="external_link" @selected($waiver_form_type == 'external_link')> External Link</option>
                                                                        <option value="" @selected(!$waiver_form_type)> No Waiver Form</option>
                                                                    </select>
                                                                    <small>Volunteers will be required to sign this digital waiver after completing registration.</small>
                                                                </div>
                                                                <div id="waiver_form_content_wrapper">
                                                                    @if($waiver_form_type == 'external_link')
                                                                        <input type="url" class="form-control" name="waiver_form_content" id="waiver_form_content" placeholder="Enter external link" value="{{ $waiver_form_content}}">
                                                                    @elseif($waiver_form_type == 'pdf')
                                                                        <input type="file" class="form-control" name="waiver_form_content" id="waiver_form_content" accept="application/pdf">
                                                                        @if(!empty($waiver_form_content) && $waiver_form_type == 'pdf')
                                                                            <input type="hidden" class="form-control" name="waiver_form_content_helper" id="waiver_form_content_helper" value="{{$waiver_form_content}}">
                                                                            @if(isset($form_data->waiver_form_content))
                                                                            <a href="{{ asset($form_data->waiverFormContentFilter()) }}" 
                                                                            target="_blank" class="d-block mt-2">View Uploaded PDF</a>
                                                                            @elseif(str_contains($waiver_form_content, '"path"'))
                                                                                @php 
                                                                                $decoded_content = json_decode($waiver_form_content, true);
                                                                                $pathcontent = $decoded_content['path'];
                                                                                @endphp 
                                                                                <a href="{{ asset($pathcontent) }}" target="_blank" class="d-block mt-2">View Uploaded PDF</a>
                                                                            @endif 
                                                                        @endif
                                                                    @elseif($waiver_form_type == 'text') 
                                                                        <textarea class="form-control ckeditor" name="waiver_form_content" id="waiver_form_content" rows="4" placeholder="Enter custom waiver content">
                                                                            {{ old('waiver_form_content', $form_data->waiver_form_content ?? $setting->waiver_text) }}
                                                                        </textarea>
                                                                    @elseif($waiver_form_type == 'default') 
                                                                        <textarea  class="form-control ckeditor-disabled" name="waiver_form_content" id="waiver_form_content" rows="4" placeholder="Enter custom waiver content">
                                                                            {{ old('waiver_form_content', $setting->waiver_text ?? '') }}
                                                                        </textarea>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="blur-overlay" id="ticket_setting_overlay" style="display: none;">
                                            <div class="message bg-primary">
                                                This section is available only for the "Use VolunteerCleanup Tickets" participation type.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="step-tab-panel step-tab-gallery target-box" id="tab_step3">
                                        @php
                                            $tickets = old('tickets', $form_data->eventTickets ?? []);
                                        @endphp
                                        <div class="tab-from-content">
                                            <div class="main-card p-4 mt-4">
                                                <div
                                                    class="bp-title mb-4 d-flex align-items-center justify-content-between">
                                                    <h4 class="px-0 mb-0">
                                                        <i class="fa-solid fa-ticket step_icon me-3"></i>Co-Host Management
                                                    </h4>
                                                    <button type="button" class="primary-btn w-auto" id="open-add-co-host-popup" data-toggle="modal" data-target="#add-co-host-popup">Add
                                                        Co-Host</button>
                                                </div>

                                                <div class="">
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Email</th>
                                                            <th>Status</th>
                                                            <th>Requested Date</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                        @if (!isset($form_data->id) || (isset($form_data->id) && $form_data->cohostRecords->isEmpty()))
                                                            <tr>
                                                                <td colspan="5" class="text-center">No Co-Hosts Found</td>
                                                            </tr>
                                                        @else
                                                            @foreach ($form_data->cohostRecords as $cohost)
                                                            <tr>
                                                                <td>{{ $cohost->volunteer->name }}</td>
                                                                <td>{{ $cohost->volunteer->email_address }}</td>
                                                                <td>{{ ucfirst($cohost->request_status) }}</td>
                                                                <td>{{ date(config('app.dateFormat'),strtotime($cohost->created_at)) }}</td>
                                                                <td>
                                                                    @php 
                                                                    $permissions = $cohost->permissions;
                                                                    $permissionsString = implode(',', $permissions->pluck('permission')->toArray());
                                                                    @endphp 
                                                                    <div class="dropdown">
                                                                        <button class="option-btn" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                                                        <div class="dropdown-menu dropdown-menu-right">	
                                                                            @if($cohost->request_status!='approved')
                                                                            <a href="javascript:void(0);" url="{{route('front-re-invite-cohost',['id' => encrypt($cohost->id)])}}" class="dropdown-item reinvite-cohost">
                                                                                <i class="fa-solid fa-link me-3"></i>Re-Invite
                                                                            </a>
                                                                            @endif
                                                                            <!-- <a vname="{{ $cohost->volunteer->name }}" permissions="{{ $permissionsString }}" extranotes="{{$cohost->extra_notes}}" class="dropdown-item open-update-co-host-popup" href="javascript:void(0);" url="{{route('front-update-co-host', ['id' => encrypt($cohost->id)])}}"><i class="fa-solid fa-cog me-3"></i>Change Permissions
                                                                            </a> -->
                                                                            <a href="javascript:void(0);" url="{{route('front-remove-cohost',['id'=>encrypt($cohost->id)])}}" class="dropdown-item remove-cohost">
                                                                                <i class="fa-solid fa-trash me-3"></i>Remove
                                                                            </a>	
                                                                        </div>
                                                                    </div>

                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        @if(isset($form_data->id) && auth()->guard('front')->user()->id!=$form_data->creator_id)
                                        <!-- Full overlay over the form -->
                                        <div class="blur-overlay">
                                            <div class="message bg-primary">
                                                You don't have permission to manage co-hosts.
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="step-tab-panel step-tab-location" id="tab_step4">
                                        <form method="post" action="{{ route('front-save-cleanup-seo-details') }}">
                                            @csrf
                                            <input type="hidden" name="event_id" class="event_instance"
                                                value="{{ $form_data->encrypted_id ?? 0 }}">
                                            @if(isset($form_data->id))
                                            <input type="hidden" name="is_create" id="is_create" value="0">
                                            @else 
                                            <input type="hidden" name="is_create" id="is_create" value="1">
                                            @endif
                                            <div class="tab-from-content">
                                                <div class="main-card">
                                                    <div class="bp-title">
                                                        <h4><i class="fa-solid fa-gear step_icon me-3"></i>SEO Details</h4>
                                                    </div>
                                                    <div class="p-4 bp-form main-form">
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12">
                                                                <div class="form-group">
                                                                    <label class="form-label fs-6">Meta Title*</label>
                                                                    <input id="meta_title" class="form-control h_50" type="text" value="{{ old('meta_title', $form_data->meta_title ?? '') }}"
                                                                        name="meta_title" placeholder="" value="" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 mt-4">
                                                                <div class="form-group">
                                                                    <label class="form-label fs-6">Meta
                                                                        Description*</label>
                                                                    <div class="text-editor">
                                                                        <textarea required id="meta_description" value="" name="meta_description" class="form-control" rows="4">{{ old('meta_description', $form_data->meta_description ?? '') }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 mt-4">
                                                                <div class="form-group">
                                                                    <label class="form-label fs-6">Focus Keywords*</label>
                                                                    <div class="input-group">
                                                                        <input class="form-control h_50" type="text"
                                                                        value="{{ old('meta_focus_keyword', $form_data->meta_focus_keyword ?? '') }}" name="meta_focus_keyword" placeholder="" value="" id="focus_keywords">
                                                                        <!-- <div class="input-group-append">
                                                                            <button type="button" data-url="{{ route('get-focus-keywords-frontend') }}" class="primary-btn w-fit generate-focus-keywords">Generate</button>
                                                                        </div> -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 mt-4 d-flex justify-content-center">
                                                                <button type="button" data-url="{{ route('get-event-meta-data') }}" class="primary-btn w-fit generate-event-meta-data">Generate By AI</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="step-tab-panel step-tab-after-event target-box" id="tab_step5">
                                        <form method="post" action="{{ route('front-save-cleanup-after-action') }}">
                                            @csrf
                                            <input type="hidden" name="event_id" class="event_instance" value="{{ $form_data->encrypted_id ?? 0 }}">
                                            <input type="hidden" name="is_event_end" id="is_event_end" value="{{$is_event_end?1:0}}">
                                            <div class="tab-from-content">
                                                <div class="main-card mt-4">
                                                    <div class="bp-title">
                                                        <h4><i class="fa-solid fa-gear step_icon me-3"></i>After Event Actions
                                                        @if(isset($form_data->encrypted_id))
                                                        <button type="button" ajaxurl="{{route('send-thankyou-request-email-front')}}" event_id="{{ $form_data->encrypted_id ?? 0 }}" class="primary-btn w-fit float-end send-thankyou-request-email-btn">Send Thank-You & Request Email</button>
                                                        @endif
                                                        </h4>
                                                    </div>
                                                    <div class="p-4 bp-form main-form">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label class="form-label fs-6">Waste Collected*</label>
                                                                    <!-- <input id="waste_collected" class="form-control h_50" type="text" value="{{ old('waste_collected', $form_data->waste_collected ?? '') }}" name="waste_collected" placeholder="" value="" required> -->
                                                                    <div class="input-group">
                                                                        <input autocomplete="off" step="0.01" min="0.01" id="waste_collected" type="number" class="form-control decimalonly h_50" placeholder="" name="waste_collected" required value="{{ old('waste_collected', $form_data->waste_collected ?? '') }}">
                                                                        <div class="input-group-text">Pound (lb)</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label class="form-label fs-6">Area Cleaned*</label>
                                                                    <!-- <input id="area_cleaned" class="form-control h_50" type="text" value="{{ old('area_cleaned', $form_data->area_cleaned ?? '') }}" name="area_cleaned" placeholder="" value="" required> -->
                                                                    <div class="input-group">
                                                                        <input autocomplete="off" step="0.01" min="0.01" id="area_cleaned" type="number" class="form-control decimalonly h_50" placeholder="" name="area_cleaned" required value="{{ old('area_cleaned', $form_data->area_cleaned ?? '') }}">
                                                                        <div class="input-group-text">Yards</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label class="form-label fs-6">Team Name</label>
                                                                    <input id="team_name" class="form-control h_50" type="text" value="{{ old('team_name', $form_data->team_name ?? '') }}" name="team_name" placeholder="" value="">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label class="form-label fs-6">Post Event Notes</label>
                                                                    <input id="post_event_notes" class="form-control h_50" type="text" value="{{ old('post_event_notes', $form_data->post_event_notes ?? '') }}" name="post_event_notes" placeholder="" value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        @if(!$is_event_end)
                                        <!-- Full overlay over the form -->
                                        <div class="blur-overlay">
                                            <div class="message bg-primary">
                                                Cleanup reporting will be available to submit after the event ends on <span id="show_event_end_date">{{(isset($form_data->end_datetime) && !empty($form_data->end_datetime))?$form_data->end_datetime->format(config('app.dateTimeFormat')):''}}</span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div
                                    class="step-footer step-tab-pager my-4 d-flex align-items-center justify-content-center gap-2 ">
                                    <button data-direction="prev" class="primary-btn w-fit">Previous</button>
                                    <button data-direction="next" class="primary-btn w-fit d-none stepper-next-button" current-active-tab="#tab_step0" type="button">Next</button>
                                    <button class="primary-btn w-fit go-to-next" is_event_end="{{$is_event_end?1:0}}" type="button">Save & Next</button>
                                    <!-- <a style="display: none;" href="javascript:void(0);" onclick="redirectWithMessage(this,'success')" message="{{$eventCloseMsg}}" url="{{ route('front-cleanup-hosting','organizer') }}" class="primary-btn w-fit close-post-cleanup-btn" type="button">Save & Exit</a> -->
                                    @if($is_event_unpublish)
                                    <a style="display: none;" href="{{ route('front-cleanup-hosting','organizer') }}" class="primary-btn w-fit close-post-cleanup-btn" type="button">Save as Draft</a>
                                    <a style="display: none;" href="javascript:void(0);" url2="{{ route('front-cleanup-hosting','organizer') }}" url="{{route('update-event-publish-status')}}" class="primary-btn w-fit close-post-cleanup-btn save_publish_event" type="button">Publish Event</a>
                                    @else 
                                    <a style="display: none;" href="{{ route('front-cleanup-hosting','organizer') }}" class="primary-btn w-fit close-post-cleanup-btn" type="button">Close</a>
                                    @endif
                                    {{-- <button data-direction="finish" class="primary-btn w-fit">Create</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-none ticket-form-replica">
            <x-reusables.frontend.ticket-form frontend="1" />
        </div>
    </div>
    @if (!$is_updating)
        @push('script')
            <script>
                $('.step-steps li, .step-steps span, .step-steps a').on('click', function(e) {
                    e.preventDefault(); // Prevent default click behavior
                    e.stopPropagation(); // Stop event from bubbling up
                    return false; // Ensure no navigation occurs
                });
            </script>
        @endpush
    @endif
    <!-- Body End-->
    @push('script')
        <script src="{{ asset('assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
        <script>
            $(document).ready(function () {
                function toggleWaiverInput() {
                    var type = $('#waiver_form_type').val();
                    var wrapper = $('#waiver_form_content_wrapper');
                    wrapper.html('');
                    if (type === 'external_link') {
                        wrapper.html(`<input type="url" class="form-control" name="waiver_form_content" id="waiver_form_content" placeholder="Enter external link" value="{{ old('waiver_form_content', $formData->waiver_form_content ?? '') }}">`);
                    } else if (type === 'pdf') {
                        wrapper.html(`<input type="file" class="form-control" name="waiver_form_content" id="waiver_form_content" accept="application/pdf">`);
                    } else if (type === 'text' ) {
                        wrapper.html(`<textarea class="form-control waiver_form_content_editor ckeditor" name="waiver_form_content" id="waiver_form_content" rows="4" placeholder="Enter custom waiver content">
                            {{ old('waiver_form_content', $formData->waiver_form_content ?? $setting->waiver_text) }}
                             </textarea>`);
                    } else if (type === 'default' ) {
                        wrapper.html(`<textarea disabled class="form-control waiver_form_content_editor ckeditor" name="waiver_form_content" id="waiver_form_content" rows="4" placeholder="Default waiver content">
                            {{ old('waiver_form_content', $setting->waiver_text ?? '') }}
                             </textarea>`);
                    }
                }
                $('#waiver_form_type').on('change', toggleWaiverInput);
            });
        </script>
            
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBqlhfqT1MQ2MscLa_1z_MqFy8T9rwOOZ8&libraries=places&callback=initMap" async></script>
        <script>
            function initMap() {
                // Initialize autocomplete 
                var autoCompleteField = document.getElementById('venue');
                const autocomplete = new google.maps.places.Autocomplete(
                    autoCompleteField,
                    { types: ['geocode',"establishment"], componentRestrictions: { country: 'us' } }
                );
                autoCompleteField.addEventListener('input', function() {
                    document.getElementById('latitude').value = '';
                    document.getElementById('longitude').value = '';
                    document.getElementById('pincode').value = '';
                });
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (place.geometry && place.geometry.location) {
                        const lat = place.geometry.location.lat();
                        const lng = place.geometry.location.lng();
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;

                        var pincode = ''; // Initialize pincode

                        // Loop through address components to find the postal_code
                        for (var i = 0; i < place.address_components.length; i++) {
                            for (var j = 0; j < place.address_components[i].types.length; j++) {
                                if (place.address_components[i].types[j] === 'postal_code') {
                                    pincode = place.address_components[i].long_name;
                                    break; // Found the pincode, no need to continue inner loop
                                }
                            }
                            if (pincode) {
                                break; // Found the pincode, no need to continue outer loop
                            }
                        }
                        document.getElementById('pincode').value = pincode;
                        
                    } else {
                        document.getElementById('results').innerHTML = '<p>No valid place selected.</p>';
                    }
                });
            }
        </script>
    @endpush
    <div class="modal fade" id="update-co-host-popup" tabindex="-1" aria-labelledby="updateCoHostPopupLabel" aria-hidden="false">
		<div class="modal-dialog modal-medium-2 modal-dialog-centered modal-sm-height modal-lg">
			<div class="modal-content modal-dialog-scrollable">
				<div class="modal-header">
					<h5 class="modal-title" id="updateCoHostPopupLabel">Update Co-Host</h5>
					<button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
				</div>
                <div class="modal-body">
                    <div class="model-content main-form">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" id="u_co_host_extra_notes" rows="4"></textarea>
                                    <input type="hidden" id="u_co_host_url" value="">
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 permission_selector" style="display: none;">
                                <div class="form-group mt-4">
                                    <label class="form-label">Select Permissions*</label>
                                    @foreach (config('app.cohost_permissions') as $pkey => $permission)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $pkey }}" id="u_permission_{{ $pkey }}" name="u_permissions[]" checked>
                                            <label class="form-check-label" for="u_permission_{{ $pkey }}">{{ $permission }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="update_co_host_btn" class="primary-btn w-fit">Update Permissions</button>
                </div>
			</div>
		</div>
	</div>
    <div class="modal fade" id="add-co-host-popup" tabindex="-1" aria-labelledby="addCoHostPopupLabel" aria-hidden="false">
		<div class="modal-dialog modal-medium-2 modal-dialog-centered modal-sm-height modal-lg">
			<div class="modal-content modal-dialog-scrollable">
				<div class="modal-header">
					<h5 class="modal-title" id="addCoHostPopupLabel">Add Co-Host</h5>
					<button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
				</div>
                <div class="modal-body">
                    <div class="model-content main-form">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group mt-4">
                                    <label class="form-label">Name*</label>
                                    <input class="form-control h_40" required type="text" name="first_name" id="co_host_name">						
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group mt-4">
                                    <label class="form-label">Email*</label>
                                    <input class="form-control h_40" required type="email" name="email" id="co_host_email">							
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Include a Note (optional)</label>
                                    <textarea class="form-control" name="extra_notes" id="co_host_extra_notes" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 permission_selector"  style="display: none;">
                                <div class="form-group mt-4">
                                    <label class="form-label">Select Permissions*</label>
                                    @foreach (config('app.cohost_permissions') as $pkey => $permission)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $pkey }}" id="permission_{{ $pkey }}" name="permissions[]" checked>
                                            <label class="form-check-label" for="permission_{{ $pkey }}">{{ $permission }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" url="{{ route('front-add-co-host') }}" id="add_co_host_btn" class="primary-btn w-fit">Send Invitation</button>
                </div>
			</div>
		</div>
	</div>
    <div class="modal fade" id="manage-ticket-popup" tabindex="-1" aria-labelledby="manage-ticket-popupPopupLabel" aria-hidden="false">
		<div class="modal-dialog modal-medium-2 modal-dialog-centered modal-sm-height modal-lg">
			<div class="modal-content modal-dialog-scrollable">
				<div class="modal-header">
					<h5 class="modal-title" id="manage-ticket-popupPopupLabel">Manage Ticket Type</h5>
					<button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
				</div>
                <div class="modal-body">
                    <div class="model-content main-form" id="manage-ticket-popup-body">
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="save_ticket_type_btn" onclick="submitTicketTypeForm();" id="" class="primary-btn w-fit">Save</button>
                </div>
			</div>
		</div>
	</div>
@endsection
@push('script')
    <script src="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/imask/imask.min.js') }}"></script>
    <script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script>
        /**select picker */
        if ($('.select2').length) {
            $('.select2').select2();

            if ($('#bring_tools').length && $('#provided_tools').length) {
                    $('#bring_tools').on('select2:select', function (e) {
                        let selectedId = e.params.data.id;
                        let $providedTools = $('#provided_tools');
                        let option = $providedTools.find(`option[value="${selectedId}"]`);
                        option.prop('disabled', true);
                        $providedTools.trigger('change.select2');
                    });
                    $('#bring_tools').on('select2:unselect', function (e) {
                        let unselectedId = e.params.data.id;
                        let $providedTools = $('#provided_tools');
                        let option = $providedTools.find(`option[value="${unselectedId}"]`);
                        option.prop('disabled', false);
                        $providedTools.trigger('change.select2');
                    });

                    $('#provided_tools').on('select2:select', function (e) {
                        let selectedId = e.params.data.id;
                        let $bring_tools = $('#bring_tools');
                        let option = $bring_tools.find(`option[value="${selectedId}"]`);
                        option.prop('disabled', true);
                        $bring_tools.trigger('change.select2');
                    });
                    $('#provided_tools').on('select2:unselect', function (e) {
                        let unselectedId = e.params.data.id;
                        let $bring_tools = $('#bring_tools');
                        let option = $bring_tools.find(`option[value="${unselectedId}"]`);
                        option.prop('disabled', false);
                        $bring_tools.trigger('change.select2');
                    });
            }
        }
    </script>
@endpush