@extends('layouts.frontend')
@push('style')
<x-reusables.advanceformcss />
<style>
#showWidgetDetail .modal-dialog{
    width: 1000px!important;
    max-width: unset!important;
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
                            <h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>Manage Embedded Widget</h3>
                            @php
                                $breadcrumbs = [ 
                                    [
                                        'name' => 'Manage Embedded Widget', 
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
                            <h4 class="p-0 pb-3">Widget Content & Filters</h4>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mt-3">
                                <label for="title" class="form-label">Widget Title *</label>
                                <input type="text" id="title" class="text form-control h_40" name="title" placeholder="Enter title" value="{{ old('title', $widget->title ?? '') }}" required>
                                <small>This title will not be displayed in the widget iframe.</small>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" id="autocomplete" class="text form-control h_40" name="location" placeholder="Search and choose location" value="{{ old('location', $widget->location ?? '') }}">
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $widget->location_lat ?? '') }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $widget->location_long ?? '') }}">
                                <div id="results"></div>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="distance" class="form-label">Distance (Miles)</label>
                                <select class="form-select form-control h_40" id="distance" name="distance">
                                    <option value="">Within</option>
                                    @foreach([5,10,25,50,100] as $mile)
                                        <option value="{{ $mile }}" @selected(old('distance', $widget->distance ?? '') == $mile)>{{ $mile }} miles</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="unit" value="mi">
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="organizer_id" class="form-label">Organizer</label>
                                <select class="selectpicker1" name="organizer_id[]" title="Search and Select Organizer" data-selected-text-format="count > 1" data-size="5" multiple data-live-search="true">
                                    @foreach ($organizers as $organizer)
                                        @if(in_array($organizer->id, $selectedOrgIds))
                                            <option value="{{ $organizer->id }}" selected>{{ $organizer->first_name.' '.$organizer->last_name.' ('.$organizer->email_address.')' }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mt-3">
                                <label for="categories" class="form-label">Categories</label>
                                <select class="selectpicker" name="category_id[]" title="Choose Categories" data-selected-text-format="count > 1" data-size="5" multiple data-live-search="true">
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
                                <label for="website" class="form-label">What is the URL you are putting this widget on?</label>
                                <input type="url" id="website" class="text form-control h_40 urlmask" name="website" placeholder="Enter embed location" value="{{ old('website', $widget->website ?? '') }}">
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
                                        <label class="form-check-label" for="type">List View</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="2" id="type2" name="type" @checked(old('type', $widget->type ?? 1) == 2)>
                                        <label class="form-check-label" for="type2">Map View</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex mt-4 justify-content-start">
                        <button type="submit" class="primary-btn w-fit">
                            {{isset($widget->id) ? 'Update' : 'Create'}} Widget Embed Code
                        </button>
                        <a href="{{route('front-embedded-widget')}}" class="primary-btn w-fit ms-2">Close</a>
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
                <h5 class="modal-title" id="showWidgetDetailLabel">Embedded Widget Details</h5>
                <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i
                        class="uil uil-multiply"></i></button>
            </div>
            <div class="modal-body" id="showWidgetDetailBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="primary-btn w-fit" id="copyWidgetCode">Copy Code</button>
                <button type="button" class="primary-btn w-fit"
                    data-bs-dismiss="modal">Close</button>
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
});
</script>

@endpush