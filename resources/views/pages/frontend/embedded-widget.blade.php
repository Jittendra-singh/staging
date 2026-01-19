@extends('layouts.frontend')
@push('style')
<style>
</style>
@endpush
@section('content')
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            <div class="container-fluid">
                <!-- <div class="text-center mb-4">
                    <h1 class="d-main-title">Upcoming Feature</h1>
                    <p class="mb-0">This feature is under development as we will launch this feature soon.</p>
                </div> -->
                @if(true)
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-between">
                        <div class="d-main-title">
                            <h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>Embedded Widget</h3>
                            @php
                                $breadcrumbs = [
                                    [
                                        'name' => 'Embedded Widget', 
                                        'url' => route('front-embedded-widget')
                                    ],
                                ]
                            @endphp     
                            <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                            <!-- <p class="mb-0 mt-3">You can send a message to your RSVP’s in the ‘Registration List’.</p> -->
                        </div>
                        <button class="primary-btn w-fits" id="createWidgetBtn">Create Widget</button>
                    </div>
                </div>
                <x-reusables.frontend.alert />
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
                                    <input class="form-check-input" type="radio" value="1" id="type" name="type" checked>
                                    <label class="form-check-label" for="type">
                                        Cleanups Listing
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="2" id="type2" name="type" >
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
                <div class="col-md-12">
                    <div class="conversion-setup">
                        <div class="main-card mt-5">
                            <div class="dashboard-wrap-content p-4">
                                <div class="table-card mt-4">
                                    <div class="main-table">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">ID</th>
                                                        <th scope="col">Title</th>
                                                        <th scope="col">Location</th>
                                                        <th scope="col">Created At</th>
                                                        <th scope="col">Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($listing_data as $data)
                                                        
                                                    <tr class="single-tr">										
                                                        <td>{{$data->wid_number}}</td>								
                                                        <td>{{$data->title}}</td>	
                                                        <td>{{$data->location}}</td>	
                                                        <td>{{$data->created_at}}</td>
                                                        <td>
                                                            @if($data->status)
                                                                <span class="text-success">Active</span>
                                                            @else
                                                                <span class="text-danger">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="dropdown position-absolute translate-middle" >
                                                                <button class="option-btn" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                                                <div class="dropdown-menu dropdown-menu-right">
                                                                    <a class="dropdown-item showWidgetDetailBtn" href="javascript:void(0);" url="{{route('front-view-widget-detail', ['id' => encrypt($data->id)])}}">
                                                                        <i class="fa-solid fa-eye"></i> View Detail
                                                                    </a>
                                                                    <a class="dropdown-item" href="{{route('front-active-inactive-widget', ['id' => encrypt($data->id)])}}">
                                                                        <i class="fa-solid fa-ban"></i> {{$data->status ? 'Inactive Now' : 'Active Now'}}
                                                                    </a>
                                                                    <a class="dropdown-item delete-record" data-url="{{route('front-delete-widget', ['id' => encrypt($data->id)])}}" href="javascript:void(0);">
                                                                        <i class="fa-solid fa-trash-can"></i> Delete
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @if($listing_data->isEmpty())
                                                    <tr>
                                                        <td colspan="6" class="text-center">No widget found.</td>
                                                    </tr>
                                                    @endif
                                                </tbody>									
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>							
                    </div>
                </div>
                @endif
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