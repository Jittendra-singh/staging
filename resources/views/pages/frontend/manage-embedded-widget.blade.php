@extends('layouts.frontend')
@push('style')
<x-reusables.advanceformcss />
<style>
#showWidgetDetail .modal-dialog{
    width: 1000px!important;
    max-width: unset!important;
}
.empty-state-behavior {
    border-top: 1px solid #e5e7eb;
    margin-top: 2.5rem;
    padding-top: 2.5rem;
}

.empty-state-option {
    border: 1px solid #d1d5db;
    border-radius: 10px;
    cursor: pointer;
    height: 100%;
    padding: 1rem;
}

.empty-state-option .form-check-input {
    margin-top: 0.2rem;
}

.empty-state-option-title {
    color: #111827;
    font-size: 14px;
    font-weight: 600;
    margin-left: 10px;

}

.empty-state-option-description {
    color: #6b7280;
    line-height: 1.5;
    margin: 0.45rem 0 0 10px;
}
</style>
@endpush
@section('content')
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            <div class="container-fluid"> 
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-between">
                        <div class="d-main-title">
                            <h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>{{ __('messages.front_manage_embedded_widget') }}</h3>
                            @php
                                $breadcrumbs = [ 
                                    [
                                        'name' => __('messages.front_manage_embedded_widget'),
                                        'url' => route('front-manage-widget')
                                    ],
                                ]
                            @endphp     
                            <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                            <!-- <p class="mb-0 mt-3">You can send a message to your RSVP’s in the ‘Registration List’.</p> --> 
                        </div>
                    </div>
                </div>
                <x-reusables.frontend.alert />
                <form action="{{route('front-save-widget', isset($widget->id)?encrypt($widget->id): null)}}" method="post" id="createWidgetForm">
                    @csrf

                    @php
                        $selectedTagIds = collect(explode(',', $widget->ftr_tag_ids ?? ''))->filter()->map(fn($id) => (int) $id)->toArray();
                        $selectedOrgIds = collect(explode(',', $widget->org_ids ?? ''))->filter()->map(fn($id) => (int) $id)->toArray();
                        $selectedCatIds = collect(explode(',', $widget->cat_ids ?? ''))->filter()->map(fn($id) => (int) $id)->toArray();
                    @endphp

                    <div class="main-card mt-4 p-4 shadow-sm bg-white rounded">
                        <div class="bp-title">
                            <h4 class="p-0 pb-3">{{ __('messages.front_widget_content_filters') }}</h4>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mt-3">
                                <label for="title" class="form-label">{{ __('messages.front_widget_title') }} *</label>
                                <input type="text" id="title" class="text form-control h_40" name="title" placeholder="{{ __('messages.front_enter_title') }}" value="{{ old('title', $widget->title ?? '') }}" required>
                                <small>{{ __('messages.front_widget_title_help') }}</small>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="location" class="form-label">{{ __('messages.front_location') }}</label>
                                <input type="text" id="autocomplete" class="text form-control h_40" name="location" placeholder="Search and choose location" value="{{ old('location', $widget->location ?? '') }}">
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $widget->location_lat ?? '') }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $widget->location_long ?? '') }}">
                                <div id="results"></div>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="distance" class="form-label">{{ __('messages.front_distance_miles') }}</label>
                                <select class="form-select form-control h_40" id="distance" name="distance">
                                    <option value="">{{ __('messages.front_within') }}</option>
                                    @foreach([5,10,25,50,100] as $mile)
                                        <option value="{{ $mile }}" @selected(old('distance', $widget->distance ?? '') == $mile)>{{ $mile }} miles</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="unit" value="mi">
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="organizer_id" class="form-label">{{ __('messages.front_organizer') }}</label>
                                <select class="selectpicker1" name="organizer_id[]" title="Search and Select Organizer" data-selected-text-format="count > 1" data-size="5" multiple data-live-search="true">
                                    @foreach ($organizers as $organizer)
                                        @if(in_array($organizer->id, $selectedOrgIds))
                                            <option value="{{ $organizer->id }}" selected>{{ $organizer->first_name.' '.$organizer->last_name.' ('.$organizer->email_address.')' }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="categories" class="form-label">{{ __('messages.front_categories') }}</label>
                                <select class="selectpicker" name="category_id[]" title="{{ __('messages.front_choose_categories') }}" data-selected-text-format="count > 1" data-size="5" multiple data-live-search="true">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(in_array($category->id, old('category_id', $selectedCatIds)))>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="ftr_tag_ids" class="form-label">Event Tags Filter</label>
                                <select class="form-control select2" id="ftr_tag_ids" name="ftr_tag_ids[]" multiple title="Search or select tags" data-live-search="true">
                                    @foreach ($event_tags as $tag)
                                        <option value="{{ $tag->id }}" @selected(in_array($tag->id, old('ftr_tag_ids', $selectedTagIds)))>{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="ftr_event_title" class="form-label">Event Title Filter</label>
                                <input type="text" id="ftr_event_title" class="form-control h_40" name="ftr_event_title" placeholder="Filter by event name" value="{{ old('ftr_event_title', $widget->ftr_event_title ?? '') }}">
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="ftr_event_start_dt" class="form-label">Event Start Date Filter</label>
                                <input type="date" id="ftr_event_start_dt" class="form-control h_40" name="ftr_event_start_dt" value="{{ old('ftr_event_start_dt', $widget->ftr_event_start_dt ?? '') }}">
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="ftr_event_end_dt" class="form-label">Event End Date Filter</label>
                                <input type="date" id="ftr_event_end_dt" class="form-control h_40" name="ftr_event_end_dt" value="{{ old('ftr_event_end_dt', $widget->ftr_event_end_dt ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="main-card mt-4 p-4 shadow-sm bg-white rounded">
                        <div class="bp-title">
                            <h4 class="p-0 pb-3">Display & Embed Settings</h4>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mt-3">

                                <label for="website" class="form-label">What is the URL you are putting this widget on? *</label>
                                <input
                                    type="url"
                                    id="website"
                                    class="text form-control h_40 urlmask @error('website') is-invalid @enderror"
                                    name="website"
                                    placeholder="Enter embed location"
                                    value="{{ old('website', $widget->website ?? '') }}"
                                    required
                                >
                                @error('website')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            

                            <div class="col-md-4 mt-3">
                                <label for="custom_widget_header" class="form-label">Custom Widget Header</label>
                                <input type="text" id="custom_widget_header" class="form-control h_40" name="custom_widget_header" placeholder="e.g. Featured Events" value="{{ old('custom_widget_header', $widget->custom_widget_header ?? '') }}">
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="event_per_page" class="form-label">Events Per Page Setting</label>
                                <select class="form-select form-control h_40" id="event_per_page" name="event_per_page">
                                    @foreach([3,6,9,10,12,15,20,25] as $perPage)
                                        <option value="{{ $perPage }}" @selected(old('event_per_page', $widget->event_per_page ?? 3) == $perPage)>{{ $perPage }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mt-3">
                                <label class="form-label fs-6">How would you like the Widget to Display?</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="1" id="type" name="type" @checked(old('type', $widget->type ?? 1) == 1)>
                                        <label class="form-check-label" for="type">{{ __('messages.front_list_view') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="2" id="type2" name="type" @checked(old('type', $widget->type ?? 1) == 2)>
                                        <label class="form-check-label" for="type2">{{ __('messages.front_map_view') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 empty-state-behavior">
                                
                                <div class="bp-title">
                                    <h4 class="p-0 pb-2">Empty State Behavior</h4>
                                </div>

                                <p class="mb-4">
                                    Choose what users see when no events match your current filters, even after expanding the search to the next available distance.
                                </p>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="empty-state-option w-100">
                                            <div class="form-check mb-0">
                                                <input
                                                    class="form-check-input"
                                                    type="radio"
                                                    name="empty_behavior"
                                                    id="empty_behavior_notify"
                                                    value="1"
                                                    @checked(old('empty_behavior', $widget->empty_behavior ?? 1) == 1)
                                                >

                                                <span class="empty-state-option-title">
                                                    Offer to Notify (Default)
                                                </span>

                                                <p class="empty-state-option-description">
                                                    Displays an email capture form for visitors to be notified of cleanups in their area via zip code.
                                                </p>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="empty-state-option w-100">
                                            <div class="form-check mb-0">
                                                <input
                                                    class="form-check-input"
                                                    type="radio"
                                                    name="empty_behavior"
                                                    id="empty_behavior_host_only"
                                                    value="2"
                                                    @checked(old('empty_behavior', $widget->empty_behavior ?? 1) == 2)
                                                >

                                                <span class="empty-state-option-title">
                                                    Host Only
                                                </span>

                                                <p class="empty-state-option-description">
                                                    When no matching events are available, encourage visitors to organize a cleanup instead of displaying an empty widget. If chosen, users will see a CTA to “Organize an Event”.
                                                </p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex mt-4 justify-content-start">
                        <button type="submit" class="primary-btn w-fit">
                            {{ isset($widget->id) ? __('messages.front_update') : __('messages.front_create') }} {{ __('messages.front_widget_embed_code') }}
                        </button>
                        <a href="{{route('front-embedded-widget')}}" class="primary-btn w-fit ms-2">{{ __('messages.front_close') }}</a>
                    </div>
                </form>
                <!-- <form action="{{route('front-save-widget', isset($widget->id)?encrypt($widget->id): null)}}" method="post" class="main-card mt-4 p-4 shadow-sm bg-white rounded" id="createWidgetForm">
                    @csrf
                    <div class="row"> 
                        <div class="col-md-4">
                            <label for="title" class="form-label">Widget Title *</label>
                            <input type="text" id="title" class="text form-control h_40" name="title" placeholder="Enter title" value="{{$widget->title ?? ''}}" required>
                            <small>This title will not be displayed in the widget iframe.</small>
                        </div> 
                        <div class="col-md-4">
                            <label for="location" class="form-label">Location *</label>
                            <input type="text" id="autocomplete" class="text form-control h_40" name="location" placeholder="Search and choose location" value="{{$widget->location ?? ''}}" required>
                            <input type="hidden" id="latitude" name="latitude" value="{{$widget->location_lat ?? ''}}">
                            <input type="hidden" id="longitude" name="longitude" value="{{$widget->location_long ?? ''}}">
                            <div id="results"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="distance" class="form-label">Distance (Miles) *</label>
                            <select class="form-select form-control h_40" id="distance" name="distance" required>
                                <option value="">Within</option>
                                <option value="5" {{(isset($widget->id) && $widget->distance == 5) ? 'selected' : ''}}>5 miles</option>
                                <option value="10" {{(isset($widget->id) && $widget->distance == 10) ? 'selected' : ''}}>10 miles</option>
                                <option value="25" {{(isset($widget->id) && $widget->distance == 25) ? 'selected' : ''}}>25 miles</option>
                                <option value="50" {{(isset($widget->id) && $widget->distance == 50) ? 'selected' : ''}}>50 miles</option>
                                <option value="100" {{(isset($widget->id) && $widget->distance == 100) ? 'selected' : ''}}>100 miles</option>
                            </select>
                            <input type="hidden" name="unit" value="mi">
                        </div>
                    </div>
                    <div class="row mt-3"> 
                        <div class="col-md-4">
                            <div class="form-groupd">
                                <label for="organizer_id" class="form-label">Organizer</label>
                                <select class="selectpicker1" name="organizer_id[]" title="Search and Select Organizer"
                                    data-selected-text-format="count > 1" data-size="5" multiple=""
                                    data-live-search="true" >
                                    @foreach ($organizers as $organizer)
                                        @if(isset($widget->id) && in_array($organizer->id, explode(',', $widget->org_ids)))
                                            <option value="{{ $organizer->id }}" selected>{{ $organizer->first_name.' '.$organizer->last_name.' ('.$organizer->email_address.')' }}</option>
                                        @endif 
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4"> 
                            <div class="form-group">
                                <label for="categories" class="form-label">Categories</label>
                                <select class="selectpicker" name="category_id[]" title="Choose Categories"
                                    data-selected-text-format="count > 1" data-size="5" multiple=""
                                    data-live-search="true">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{(isset($widget->id) && in_array($category->id, explode(',', $widget->cat_ids))) ? 'selected' : ''}}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <label for="website" class="form-label">What is the URL you are putting this widget on?</label>
                            <input type="url" id="website" class="text form-control h_40 urlmask" name="website" placeholder="Enter embed location" value="{{$widget->website ?? ''}}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-groupd">
                                <label for="ticket_questions" class="form-label fs-6">How would you like the Widget to Display?</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="1" id="type" name="type" {{isset($widget->id) && $widget->type == 1 ? 'checked' : ''}}>
                                    <label class="form-check-label" for="type">
                                        List View
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="2" id="type2" name="type" {{isset($widget->id) && $widget->type == 2 ? 'checked' : ''}}>
                                    <label class="form-check-label" for="type2">
                                        Map View
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mt-3 justify-content-start">
                        <button type="submit" class="primary-btn w-fit">
                            {{isset($widget->id) ? 'Update' : 'Create'}} Widget Embed Code
                        </button>
                        <a href="{{route('front-embedded-widget')}}" class="primary-btn w-fit ms-2">
                            Close
                        </a>
                    </div>
                </form> -->
            </div>
        </div>
    </div>
<div class="modal fade" id="showWidgetDetail" tabindex="-1" aria-labelledby="showWidgetDetailsLabel"
    aria-modal="true" role="dialog"> 
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showWidgetDetailLabel">{{ __('messages.front_embedded_widget_details') }}</h5>
                <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i
                        class="uil uil-multiply"></i></button>
            </div>
            <div class="modal-body" id="showWidgetDetailBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="primary-btn w-fit" id="copyWidgetCode">{{ __('messages.front_copy_code') }}</button>
                <button type="button" class="primary-btn w-fit"
                    data-bs-dismiss="modal">{{ __('messages.front_close') }}</button>
            </div>
        </div>
    </div>
</div>
<x-reusables.google :locationarray="[]"/>
@endsection
@push('script')
    <x-reusables.advanceformjs />
<script>
 let ajaxTimer = null; // ✅ declare first

$(document).ready(function () {
    $('.select2').select2();
    $('.selectpicker1').selectpicker();

    $(document).on('input', '.bs-searchbox input', function () {
        $('#bs-select-3 .no-results').text('Type at leat 2 character..');

        clearTimeout(ajaxTimer);

        let query = $(this).val();

        if (query.length < 2) return;
        $('#bs-select-3 .no-results').text('Loading...');
        ajaxTimer = setTimeout(function () {
            $.ajax({
                url: "{{ route('front-get-organizer-options') }}",
                type: "POST",
                data: {
                    search: query,
				    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let $select = $('.selectpicker1');
                    $select.empty();
                    if(res.results.length > 0){
                        res.results.forEach(item => {
                            $select.append(
                                `<option value="${item.id}">${item.text}</option>`
                            );
                        });
                    }else{
                        $('#bs-select-3 .no-results').text('No results found');
                    }
                    $select.selectpicker('refresh');
                }
            });

        }, 300);
    });


    /* $('#createWidgetForm').on('submit', function (e) {
        let hasError = false;

        const $form = $(this);
        const $title = $form.find('[name="title"]');
        const $location = $form.find('[name="location"]');
        const $latitude = $form.find('[name="latitude"]');
        const $longitude = $form.find('[name="longitude"]');
        const $distance = $form.find('[name="distance"]');

        $form.find('.client-validation-error').remove();
        $form.find('.is-invalid, .form-control-danger').removeClass('is-invalid form-control-danger');

        function showError($field, message) {
            hasError = true;

            $field.addClass('is-invalid form-control-danger');

            if ($field.closest('.bootstrap-select').length) {
                $field.closest('.bootstrap-select').after('<div class="invalid-feedback d-block client-validation-error">' + message + '</div>');
            } else {
                $field.after('<div class="invalid-feedback d-block client-validation-error">' + message + '</div>');
            }
        }

        if ($.trim($title.val()) === '') {
            showError($title, 'Please enter widget title.');
        }

        if (
            $.trim($location.val()) !== '' &&
            $.trim($latitude.val()) !== '' &&
            $.trim($longitude.val()) !== '' &&
            $.trim($distance.val()) === ''
        ) {
            showError($distance, 'Please select distance when location is selected.');
        }

        if (
            $.trim($distance.val()) !== '' &&
            (
                $.trim($location.val()) === '' ||
                $.trim($latitude.val()) === '' ||
                $.trim($longitude.val()) === ''
            )
        ) {
            showError($location, 'Please select location when distance is selected.');
        }

        if (hasError) {
            e.preventDefault();

            const $firstError = $form.find('.is-invalid').first();

            if ($firstError.length) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 120
                }, 300);

                $firstError.focus();
            }

            return false;
        }
    }); */
    let widgetPreviewRequestRunning = false;
    let widgetFormConfirmed = false;

    function applyDefaultWidgetDistance() {
        const $form = $('#createWidgetForm');
        const location = $.trim($form.find('[name="location"]').val());
        const latitude = $.trim($form.find('[name="latitude"]').val());
        const longitude = $.trim($form.find('[name="longitude"]').val());
        const $distance = $form.find('[name="distance"]');

        if (
            location !== '' &&
            latitude !== '' &&
            longitude !== '' &&
            $.trim($distance.val()) === ''
        ) {
            $distance.val('25').trigger('change');
        }
    }

    /*
    * Google Places fills latitude/longitude shortly before the input loses
    * focus. The small timeout allows place_changed to finish first.
    */
    $('#autocomplete').on('blur change', function () {
        setTimeout(function () {
            applyDefaultWidgetDistance();
        }, 150);
    });

    /*
    * Also apply the default to old/edit data when the page already contains
    * a selected location but no distance.
    */
    applyDefaultWidgetDistance();

    $('#createWidgetForm').on('submit', function (e) {
        const form = this;
        const $form = $(form);

        /*
        * Native form.submit() does not trigger this event again, but this flag
        * also protects the flow if another script triggers submit.
        */
        if (widgetFormConfirmed) {
            return true;
        }

        e.preventDefault();
        applyDefaultWidgetDistance();

        let hasError = false;

        const $title = $form.find('[name="title"]');
        const $location = $form.find('[name="location"]');
        const $latitude = $form.find('[name="latitude"]');
        const $longitude = $form.find('[name="longitude"]');
        const $distance = $form.find('[name="distance"]');

        $form.find('.client-validation-error').remove();
        $form
            .find('.is-invalid, .form-control-danger')
            .removeClass('is-invalid form-control-danger');

        function showFieldError($field, message) {
            hasError = true;

            $field.addClass('is-invalid form-control-danger');

            if ($field.closest('.bootstrap-select').length) {
                $field
                    .closest('.bootstrap-select')
                    .after(
                        '<div class="invalid-feedback d-block client-validation-error">' +
                        message +
                        '</div>'
                    );
            } else {
                $field.after(
                    '<div class="invalid-feedback d-block client-validation-error">' +
                    message +
                    '</div>'
                );
            }
        }

        if ($.trim($title.val()) === '') {
            showFieldError($title, 'Please enter widget title.');
        }


        if (
            $.trim($location.val()) !== '' &&
            (
                $.trim($latitude.val()) === '' ||
                $.trim($longitude.val()) === ''
            )
        ) {
            showFieldError(
                $location,
                'Please choose a valid location from the suggested location list.'
            );
        }

        if (
            $.trim($distance.val()) !== '' &&
            (
                $.trim($location.val()) === '' ||
                $.trim($latitude.val()) === '' ||
                $.trim($longitude.val()) === ''
            )
        ) {
            showFieldError(
                $location,
                'Please select location when distance is selected.'
            );
        }

        if (hasError) {
            const $firstError = $form.find('.is-invalid').first();

            if ($firstError.length) {
                $('html, body').animate({
                    scrollTop: $firstError.offset().top - 120
                }, 300);

                $firstError.focus();
            }

            return false;
        }

        if (widgetPreviewRequestRunning) {
            return false;
        }

        widgetPreviewRequestRunning = true;

        const $submitButton = $form.find('button[type="submit"]');
        const originalButtonText = $submitButton.html();

        $submitButton
            .prop('disabled', true)
            .html('<i class="fa-solid fa-spinner fa-spin me-2"></i>Checking Events...');

        $.ajax({
            url: "{{ route('front-widget-event-count') }}",
            type: 'POST',
            data: $form.serialize(),
            success: function (response) {
                if (!response.status) {
                    showError(
                        response.message || 'Unable to check widget events.',
                        'error'
                    );

                    return;
                }

                let alertIcon = 'success';

                if (response.case === 2) {
                    alertIcon = 'warning';
                } else if (response.case === 3) {
                    alertIcon = 'info';
                }

                Swal.fire({
                    title: 'Event Availability Status',
                    html: response.message,
                    icon: alertIcon,
                    showCancelButton: true,
                    confirmButtonText: 'Save & Copy Code',
                    cancelButtonText: 'Close',
                    confirmButtonColor: '#16c784',
                    cancelButtonColor: '#6c757d',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(function (result) {
                    if (result.isConfirmed) {
                        widgetFormConfirmed = true;

                        /*
                        * Submit using the native DOM method so this AJAX preview
                        * handler is not executed a second time.
                        */
                        form.submit();
                    }
                });
            },
            error: function (xhr) {
                let message = 'Unable to check matching events. Please try again.';

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    if (xhr.responseJSON.errors) {
                        const firstErrorGroup = Object.values(
                            xhr.responseJSON.errors
                        )[0];

                        if (firstErrorGroup && firstErrorGroup[0]) {
                            message = firstErrorGroup[0];
                        }
                    }
                }

                Swal.fire({
                    title: 'Unable to Check Events',
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#6c757d'
                });
            },
            complete: function () {
                widgetPreviewRequestRunning = false;

                $submitButton
                    .prop('disabled', false)
                    .html(originalButtonText);
            }
        });

        return false;
    });
});
</script>

@endpush
