@php
    $is_update_form = $formData ? true : false;
    $action_url = $is_update_form
        ? route('update-event', ['placeholder' => $formData->encrypted_id])
        : route('create-event');
    $parentRoute = route('index-volunteers');
    $parentPageName = $module;
    $module = $module ?? 'Volunteer';
    $pageName = 'Manage ' . $module;
    /**variable for form fields */
    $event_name = old('event_name', $formData->event_name ?? '');
    $slug = old('slug', $formData->slug ?? '');
    $event_category_id = old('event_category_id', $formData->event_category_id ?? '');
    $description = old('description', $formData->description ?? '');
    $start_datetime = old('start_datetime', $formData->start_datetime ?? '');
    if (old('start_datetime') !== null) {
        $start_datetime = date('Y-m-d H:i' , strtotime(old('start_datetime')));
    }
    $end_datetime = old('end_datetime', $formData->end_datetime ?? '');
    if (old('end_datetime') !== null) {
        $end_datetime = date('Y-m-d H:i' , strtotime(old('end_datetime')));
    }
    $country_id = old('country_id', $formData->country_id ?? '');
    $pincode = old('pincode', $formData->pincode ?? '');
    $venue = old('venue', $formData->venue ?? '');
    $meeting_spot_details = old('meeting_spot_details', $formData->meeting_spot_details ?? '');
    $parking_info_details = old('parking_info_details', $formData->parking_info_details ?? '');
    $latitude = old('latitude', $formData->latitude ?? '');
    $longitude = old('longitude', $formData->longitude ?? '');
    $is_private = old('is_private', $formData->is_private ?? false);
    $banner_image = isset($formData) && $formData->banner_image ? asset($formData->banner_image->path) : null;
    $flyer_image = isset($formData) && $formData->flyer_image ? asset($formData->flyer_image->path) : null;
    $provided_tools_obj = old(
        'provided_tools',
        @$formData->providedTools ? $formData->providedTools->pluck('id')->toArray() : [],
    );
    $tags = old('event_tags', @$formData->tags ? $formData->tags->pluck('id')->toArray() : []);
    $volunteer_tags_id = old(
        'volunteer_tags',
        @$formData->volunteertags ? $formData->volunteertags->pluck('id')->toArray() : [],
    );
    $bring_tools_obj = old('bring_tools', @$formData->bringTools ? $formData->bringTools->pluck('id')->toArray() : []);
    $event_reoccurance = old('event_reoccurance', $formData->event_reoccurance ?? '');
    $creator_id = old('volunteer_id', $formData->creator_id ?? 0);
    // Tickets
    $show_remaining_tickets = old('show_remaining_tickets', $formData->show_remaining_tickets ?? true);
    $sell_tickets_individually = old('sell_tickets_individually', $formData->sell_tickets_individually ?? false);
    $admin_commission = old('admin_commission', $formData->admin_commission ?? '');
    $ticket_sell_start_time = old('ticket_sell_start_time', $formData->ticket_sell_start_time ?? '');
    $ticket_sell_end_time = old('ticket_sell_end_time', $formData->ticket_sell_end_time ?? '');
    $show_ticket_description = old('show_ticket_description', $formData->show_ticket_description ?? true);
    $allow_donations = old('allow_donations', $formData->allow_donations ?? false);
    $allow_refund = old('allow_refund', $formData->allow_refund ?? false);
    $charge_bear_by = (int) old('charge_bear_by', $formData->charge_bear_by ?? 0);
    $is_private_ticket = old('is_private_ticket', $formData->is_private_ticket ?? false);
    $ticket_questions = old('ticket_questions', $formData->ticket_questions ?? false);
    $tickets = old('tickets', $formData->eventTickets ?? []);
    // SEO Details
    $meta_title = old('meta_title', $formData->meta_title ?? '');
    $meta_description = old('meta_description', $formData->meta_description ?? '');
    $meta_focus_keyword = old('meta_focus_keyword', $formData->meta_focus_keyword ?? '');
    $site_publish_date = old('site_publish_date', $formData->site_publish_date ?? date('Y-m-d'));
    $site_depublish_date = old(
        'site_depublish_date',
        $formData->site_depublish_date ?? date('Y-m-d', strtotime('+1 month')),
    );
    //waiver form details
    $waiver_form_type = old('waiver_form_type', $formData->waiver_form_type ?? null);
    $waiver_form_content = old('waiver_form_content', $formData->waiver_form_content ?? 'null');
@endphp
@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $parentPageName, 'parentRoute' => $parentRoute,'breadcrumb' => [
    ['name' => 'Events', 'route' => route('index-events')],
    ['name' => 'Manage Event', 'route' => ''],
]])
@section('css')
    <x-reusables.advanceformcss />
    <style>
        .accordion-button:not(.collapsed){
            background: none!important;
        }
        .custom-ticket-container .accordion-item{ 
            margin-bottom: 15px !important;
        }
    .accordion-item:not(:first-of-type){
        border-top: 1px solid #dadada !important;
    }
    </style>
@endsection
@section('js')
    <x-reusables.advanceformjs />
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
                    wrapper.html(`<textarea class="form-control waiver_form_content_editor" name="waiver_form_content" id="waiver_form_content" rows="4" placeholder="Enter custom waiver content">{{ old('waiver_form_content', $formData->waiver_form_content ?? $setting->waiver_text) }}</textarea>`);
                } else if (type === 'default' ) {
                    wrapper.html(`<textarea class="form-control waiver_form_content_editor" name="waiver_form_content" id="waiver_form_content" rows="4" placeholder="Enter custom waiver content">{{ old('waiver_form_content', $setting->waiver_text ?? '') }}</textarea>`);
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
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <form id="event-form" action="{{ $action_url }}" method="post" enctype="multipart/form-data">
                @csrf
                @if ($is_update_form)
                    @method('PUT')
                @endif
                <div class="card">
                    <div class="card-body">
                        <x-reusables.frontend.alert />
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="event_name" class="form-label">Event Name*</label>
                                <input type="text" class="form-control onlyTextNumberSplaceMask get-slug" 
                                    data-slugtarget="#slug"
                                    data-existingid="{{$formData->id ?? 0}}"
                                    data-url="{{ route('get-ajax-slug', ['type' => 'event']) }}"
                                    placeholder="Enter Event Name"
                                    id="event_name" name="event_name"
                                    value="{{ $event_name }}" required>
                            </div>
                            <div class="col-md-6  ">
                                <label for="slug" class="form-label">Event Slug <span class="fs-7 text-info">If you leave this empty, system will generate slug.</span></label>
                                <div class="hstack"> 
                                    <input class="form-control event_slug slugmask" id="slug" name="slug" value="{{ $slug }}">
                                    <button event_id="{{$formData->encrypted_id ?? 0}}" ajaxurl="{{ route('front-check-event-slug') }}" id="validate_event_slug" class="btn btn-primary waves-effect waves-light ms-2 px-3" type="button">Validate</button>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="event_category_id" class="form-label">Event Category*</label>
                                <select class="form-select" id="event_category_id" name="event_category_id" required>
                                    @foreach ($categories as $value)
                                        <option value="{{ $value->id }}" @selected($event_category_id == $value->id)>{{ $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="volunteers" class="form-label">Event Organizer*</label>
                                <select class="form-select select2" id="volunteer_id" name="volunteer_id">
                                    @foreach ($volunteers as $volunteer)
                                        @if(!empty(trim($volunteer->name)))
                                        <option value="{{ $volunteer->id }}" @selected($volunteer->id == $creator_id)>
                                            {{ $volunteer->name }} {{ (!empty($volunteer->organisation_name) && strtolower($volunteer->organisation_name)!=strtolower($volunteer->name) ? ' (' . $volunteer->organisation_name . ')' : '') }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Event Description*</label>
                                <textarea class="form-control ckeditor-classic" id="description" name="description" rows="3" >{{ $description }}</textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center gap-4 mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_private"
                                    name="is_private" {{ $is_private ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_private">Mark as Private?</label>
                            </div>
                            <!-- <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_featured"
                                    name="is_featured"
                                    {{ old('is_featured', $formData->is_featured ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Mark as Featured?</label>
                            </div> -->
                            {{-- <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="allow_community_service_form"
                                    name="allow_community_service_form"
                                    {{ old('allow_community_service_form', $formData->allow_community_service_form ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="allow_community_service_form">Allow Community Form?</label>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title pb-3 border-bottom fw-bold admin-card-header">Timings & Venue</h4>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="start_datetime" class="form-label">Start Date & Time*</label>
                                <input type="text" class="form-control" id="start_datetime" name="start_datetime"
                                    value="{{ $start_datetime }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="end_datetime" class="form-label">End Date & Time*</label>
                                <input type="text" class="form-control" id="end_datetime" name="end_datetime"
                                    value="{{ $end_datetime }}" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="event_timezone" class="form-label">Timezones*</label>
                                <select class="form-select" id="event_timezone" name="event_timezone" required>
                                    @foreach ($timezones as $key => $value)
                                        <option value="{{ $value }}" @selected(old('event_timezone', $formData->event_timezone ?? '') == $value)>
                                            {{ ucfirst($value) }} ({{$timezonescode[$value]}})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="site_publish_date" class="form-label">Event Page Publish Date*</label>
                                <input type="text" class="form-control" id="site_publish_date" required name="site_publish_date"
                                    value="{{ $site_publish_date }}">
                            </div>
                            <div class="col-md-6">
                                <label for="site_depublish_date" class="form-label">Event Page Expiry Date*</label>
                                <input type="text" class="form-control" id="site_depublish_date" required
                                    name="site_depublish_date" value="{{ $site_depublish_date }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="venue" class="form-label">Venue*</label>
                                <input type="text" class="form-control onlyTextNumberSplaceMask" id="venue" name="venue" value="{{ $venue }}" required>
                                        
                                <input type="hidden" id="latitude" name="latitude" value="{{ $latitude }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ $longitude }}">
                                <div id="results"></div>
                            </div>
                            <div class="col-md-4">
                                <label for="pincode" class="form-label">Zipcode*</label>
                                <input type="text" class="form-control pincodemask" id="pincode" name="pincode" value="{{ $pincode }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="country_id" class="form-label">Country*</label>
                                <select class="form-select" id="country_id" name="country_id" required>
                                    @foreach ($countries as $value)
                                        <option value="{{ $value->country_id }}" @selected($country_id == $value->country_id)>
                                            {{ $value->country_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title pb-3 border-bottom fw-bold admin-card-header">Other Details</h4>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="banner_image" class="form-label">Banner Image</label> <small>(Recommended file size: 5 MB and image size: 1000 × 660 px)</small>
                                <input type="file" accept="image/*" class="form-control" id="banner_image" name="banner_image">
                                @if ($banner_image)
                                    <div class="mt-2">
                                        <img src="{{ asset($banner_image) }}" alt="Banner Image" class="img-fluid"
                                            style="max-height: 50px;">
                                    </div>
                                @endif
                            </div>
                            <input type="hidden" name="flyer_image" value="">
                            <!-- <div class="col-md-3">
                                <label for="flyer_image" class="form-label">Flyer Image</label>
                                <input type="file" accept="image/*" class="form-control" id="flyer_image" name="flyer_image">
                                @if ($flyer_image)
                                    <div class="mt-2">
                                        <img src="{{ asset($flyer_image) }}" alt="Flyer Image" class="img-fluid"
                                            style="max-height: 50px;">
                                    </div>
                                @endif
                            </div> -->
                            <!-- <div class="col-md-6">
                                <label for="video_url" class="form-label">Video URL</label>
                                <input type="url" class="form-control urlmask" id="video_url" name="video_url"
                                    value="{{ old('video_url', $formData->video_url ?? '') }}">
                            </div> -->
                            <input type="hidden" name="event_reoccurance" value="yearly">
                            <!-- <div class="col-md-3">
                                <label for="meeting_spot_details" class="form-label">Frequency*</label>
                                <select class="form-select" id="event_reoccurance" name="event_reoccurance" required>
                                    @foreach (config('app.reoccurring') as $value)
                                        <option value="{{ $value }}" @selected($event_reoccurance == $value)>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> -->
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label for="bring_tools" class="form-label">Bring Tools</label>
                                <select class="form-select select2" id="bring_tools" name="bring_tools[]" multiple>
                                    @foreach ($bring_tools as $tool)
                                        <option 
                                            value="{{ $tool->id }}" 
                                            @selected(in_array($tool->id, $bring_tools_obj))
                                            @disabled(in_array($tool->id, $provided_tools_obj)) 
                                        >
                                            {{ $tool->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="provided_tools" class="form-label">Provided Tools</label>
                                <select class="form-select select2" id="provided_tools" name="provided_tools[]" multiple>
                                    @foreach ($provided_tools as $tool)
                                        <option value="{{ $tool->id }}" 
                                            @selected(in_array($tool->id, $provided_tools_obj))
                                            @disabled(in_array($tool->id, $bring_tools_obj)) 
                                        >
                                            {{ $tool->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tags" class="form-label">Event Tags</label>
                                <select class="form-select select2" id="tags" name="tags[]" multiple>
                                    @foreach ($event_tags as $otag)
                                        <option value="{{ $otag->id }}" @selected(in_array($otag->id, $tags))>
                                            {{ $otag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="volunteer" class="form-label">Volunteer Tags</label>
                                <select class="form-select select2" id="volunteer" name="volunteer_tags[]" multiple>
                                    @foreach ($volunteer_tags as $vtag)
                                        <option value="{{ $vtag->id }}" @selected(in_array($vtag->id, $volunteer_tags_id))>
                                            {{ $vtag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="meeting_spot_details" class="form-label">Meeting Spot Details</label>
                                <textarea type="text" class="form-control ckeditor-classic" id="meeting_spot_details"
                                    name="meeting_spot_details">{{ $meeting_spot_details }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="parking_info_details" class="form-label">Parking Info Details</label>
                                <textarea type="text" class="form-control ckeditor-classic" id="parking_info_details"
                                    name="parking_info_details">{{ $parking_info_details }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title pb-3 border-bottom fw-bold admin-card-header">SEO Details</h4>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="meta_title" name="meta_title"
                                    value="{{ $meta_title }}">
                            </div>
                            <div class="col-md-6">
                                <label for="focus_keywords" class="form-label">
                                    Meta Focus Keywords
                                    <span class="text-info fs-7">Provide comma separated values</span>
                                </label>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control me-2" id="focus_keywords"
                                        name="meta_focus_keyword" value="{{ $meta_focus_keyword }}">
                                    <!-- <button type="button" data-url="{{ route('get-focus-keywords') }}"
                                        class="btn btn-dark d-flex align-items-center generate-focus-keywords">
                                        <span class="bx bx-loader font-size-16 align-middle me-2" role="status"
                                            aria-hidden="true"></span>
                                        Generate
                                    </button> -->
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control w-100 " id="meta_description" name="meta_description">{{ $meta_description }}</textarea>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 d-flex justify-content-center">
                                <button formid="event-form" type="button" data-url="{{route('get-event-meta-data-backend')}}"
                                    class="btn btn-dark d-flex align-items-center generate-event-meta-data">
                                    <span class="bx bx-loader font-size-16 align-middle me-2" role="status" aria-hidden="true"></span>
                                    Generate By AI
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title pb-3 fw-bold border-bottom admin-card-header">Ticket Settings</h4>
                        <input type="hidden" name="ticket_sell_start_time">
                        <input type="hidden" name="ticket_sell_end_time">
                        <!-- <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ticket_sell_start_time" class="form-label">Sell Start Date*</label>
                                    <input type="text" class="form-control" required id="ticket_sell_start_time" name="ticket_sell_start_time" value="{{ $ticket_sell_start_time }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ticket_sell_end_time" class="form-label">Sell End Date*</label>
                                    <input type="text" class="form-control" required id="ticket_sell_end_time" name="ticket_sell_end_time" value="{{ $ticket_sell_end_time }}">
                                </div>
                            </div>
                        </div> -->
                        <div class="row mt-3">
                            <div class="col">
                                <div class="form-group">
                                    <label for="ticket_question">Ticket Question</label>
                                    <textarea name="ticket_questions" class="form-control ckeditor-classic" id="ticket_question" cols="30" rows="3">{{$ticket_questions}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-3 gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1"
                                    id="show_ticket_description" name="show_ticket_description"
                                    {{ $show_ticket_description ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_ticket_description">Show ticket description on event page?</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1"
                                    id="allow_donations" name="allow_donations"
                                    {{ $allow_donations ? 'checked' : '' }}>
                                <label class="form-check-label" for="allow_donations">Allow optional donations</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1"
                                    id="allow_refund" name="allow_refund"
                                    {{ $allow_refund ? 'checked' : '' }}>
                                <label class="form-check-label" for="allow_refund">Allow paid tickets refund</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1"
                                    id="show_remaining_tickets" name="show_remaining_tickets"
                                    {{ $show_remaining_tickets ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_remaining_tickets">Show remaining tickets with tickets detail at single event page?</label>
                            </div>
                            <!-- <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1"
                                    id="sell_tickets_individually" name="sell_tickets_individually"
                                    {{ $sell_tickets_individually ? 'checked' : '' }}>
                                <label class="form-check-label" for="sell_tickets_individually">Tickets will be sold one ticket per
                                    customer?</label>
                            </div> -->
                        </div>
                        <div class="row mt-3">
                            <div class="col">
                                <div class="form-group">
                                    <label for="charge_bear_by">Who will bear the Stripe processing charges and platform fees for paid tickets?</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="0" id="charge_bear_by" name="charge_bear_by"
                                            {{ $charge_bear_by!=1?'checked':'' }}>
                                        <label class="form-check-label"
                                            for="charge_bear_by">
                                            Organizer
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="1" id="charge_bear_by2" name="charge_bear_by"
                                            {{ $charge_bear_by==1?'checked':'' }}>
                                        <label class="form-check-label"
                                            for="charge_bear_by2">
                                            Buyer
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-center border-bottom mb-3 admin-card-header py-2">
                            <h4 class="card-title p-0 m-0 fw-bold">
                                Ticket Details
                            </h4>
                            <button type="button" class="btn btn-light waves-effect add-ticket">Add Ticket</button>
                        </div>
                        @if (false)
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <label for="ticket_name" class="form-label">Ticket Name</label>
                                    <input type="text" class="form-control" id="ticket_name" name="ticket_name"
                                        value="{{ $ticket_name }}" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <label for="ticket_description" class="form-label">Ticket Description</label>
                                    <textarea class="form-control ckeditor-classic" id="ticket_description" name="ticket_description" rows="2">{{ $ticket_description }}</textarea>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="free_tickets" class="form-label">Free Tickets</label>
                                    <input type="number" class="form-control" id="free_tickets" name="free_tickets"
                                        value="{{ $free_tickets }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="paid_tickets" class="form-label">Paid Tickets</label>
                                    <input type="number" class="form-control" id="paid_tickets" name="paid_tickets"
                                        value="{{ $paid_tickets }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="donation_tickets" class="form-label">Donation Tickets</label>
                                    <input type="number" class="form-control" id="donation_tickets"
                                        name="donation_tickets" value="{{ $donation_tickets }}">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="paid_ticket_price" class="form-label">Paid Ticket Price</label>
                                    <input type="number" class="form-control" id="paid_ticket_price"
                                        name="paid_ticket_price" value="{{ $paid_ticket_price }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="min_ticket" class="form-label">Minimum Tickets to be Sold to Accomplish
                                        Event</label>
                                    <input type="number" class="form-control" id="min_ticket" name="min_ticket"
                                        value="{{ $min_ticket }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="max_ticket" class="form-label">Maximum Tickets Allowed in Single
                                        Order</label>
                                    <input type="number" class="form-control" id="max_ticket" name="max_ticket"
                                        value="{{ $max_ticket }}">
                                </div>
                            </div>
                        @endif
                        <div class="custom-ticket-container">
                            <div class="accordion mb-4 ticket-container ticket-container-section" id="accordionExample">
                        <!-- <div class="ticket-container mt-3"> -->
                            @foreach ($tickets as $counter => $ticket)
                                <x-reusables.frontend.ticket-form frontend="1" :ticket="$ticket" :counter="$counter" :event="$formData??null" />
                            @endforeach
                        <!-- </div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title pb-3 fw-bold border-bottom admin-card-header">
                            Waiver Forms
                        </h4>
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
                                </div>
                                <div id="waiver_form_content_wrapper">
                                    @if($waiver_form_type == 'external_link')
                                        <input type="url" class="form-control" name="waiver_form_content" id="waiver_form_content" placeholder="Enter external link" value="{{ old('waiver_form_content', $formData->waiver_form_content ?? '') }}">
                                    @elseif($waiver_form_type == 'pdf')
                                        <input type="file" class="form-control" name="waiver_form_content" id="waiver_form_content" accept="application/pdf">
                                        @if(isset($formData->waiver_form_content) && $waiver_form_type == 'pdf')
                                            <a href="{{ asset($formData->waiverFormContentFilter()) }}" 
                                               target="_blank" class="d-block mt-2">View Uploaded PDF</a>
                                        @endif
                                    @elseif($waiver_form_type == 'text') 
                                        <textarea class="form-control ckeditor-classic" name="waiver_form_content" id="waiver_form_content" rows="4" placeholder="Enter custom waiver content">{{ old('waiver_form_content', $formData->waiver_form_content ?? $setting->waiver_text) }}</textarea>
                                    @elseif($waiver_form_type == 'default') 
                                        <textarea class="form-control ckeditor-classic-disabled" name="waiver_form_content" id="waiver_form_content" rows="4" placeholder="Enter custom waiver content">{{ old('waiver_form_content', $setting->waiver_text ?? '') }}</textarea>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <x-reusables.form-footer route="index-events" action="{{ $is_update_form ? 'update' : 'create' }}"
                        module="{{ $module }}" />
                </div>
            </form>
        </div>
    </div> <!-- end row -->
    <div class="d-none ticket-form-replica">
        <x-reusables.frontend.ticket-form />
    </div>
@endsection

