@extends('layouts.frontend')
@section('title')
    Registrations
@endsection
@push('style')
<style>
.bs-searchbox input::placeholder {
    content: "Start typing to search...";
}
.d-none {
    display: none !important;
}

</style>
@endpush
@section('content')
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            <div class="container-fluid">
                <div class="text-center mb-4">
                    <h1 class="d-main-title">Upcoming Feature</h1>
                    <p class="mb-0">This feature is under development as we will launch this feature soon.</p>
                </div>
                @if(false)
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-between">
                        <div class="d-main-title">
                            <h3><i class="fa-solid fa-calendar-days me-3"></i>Embedded Widget Code</h3>
                            <!-- <p class="mb-0 mt-3">You can send a message to your RSVP’s in the ‘Registration List’.</p> -->
                        </div>
                        <button class="primary-btn w-fits" id="createWidgetBtn">Create Widget</button>
                    </div>
                </div>
                <form action="{{route('front-create-widget')}}" method="post" class="main-card mt-4 p-4 shadow-sm bg-white rounded" id="createWidgetForm" style="display: none;">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" id="title" class="text form-control h_40" name="title" placeholder="Enter title" value="" required>
                        </div>
                        <div class="col-md-8">
                            <div class="form-groupd">
                                <label for="organizer_id" class="form-label">Organizer</label>
                                <select class="selectpicker1" name="organizer_id[]" title="Search and Select Organizer"
                                    data-selected-text-format="count > 1" data-size="5" multiple=""
                                    data-live-search="true" >
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label for="location" class="form-label">Location *</label>
                            <input type="text" id="autocomplete" class="text form-control h_40" name="location" placeholder="Search and choose location" value="" required>
                            <input type="hidden" id="latitude" name="latitude" value="">
                            <input type="hidden" id="longitude" name="longitude" value="">
                            <div id="results"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="distance" class="form-label">Distance (Miles) *</label>
                            <select class="form-select form-control h_40" id="distance" name="distance" required>
                                <option value="">Within</option>
                                <option value="5">5 miles</option>
                                <option value="10">10 miles</option>
                                <option value="25" selected>25 miles</option>
                                <option value="50">50 miles</option>
                                <option value="100">100 miles</option>
                            </select>
                            <input type="hidden" name="unit" value="mi">
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="categories" class="form-label">Categories</label>
                                <select class="selectpicker" name="category_id[]" title="Choose Categories"
                                    data-selected-text-format="count > 1" data-size="5" multiple=""
                                    data-live-search="true">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-groupd">
                                <label for="ticket_questions" class="form-label fs-6">Type of Widget</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="0" id="type" name="type" checked>
                                    <label class="form-check-label" for="type">
                                        Cleanups Listing
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="1" id="type2" name="type" >
                                    <label class="form-check-label" for="type2">
                                        Cleanups On the Map
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mt-3 justify-content-start">
                        <button type="submit" class="primary-btn w-fit">
                            Create Widget Embed Code
                        </button>
                    </div>
                </form>
                @endif
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