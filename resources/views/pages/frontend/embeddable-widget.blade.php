@extends('layouts.frontend')
@section('title')
    Registrations
@endsection
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
                <form class="main-card mt-4 p-4 shadow-sm bg-white rounded" id="createWidgetForm" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control h_40" id="location"
                                placeholder="Optionally choose a location to search">
                        </div>
                        <div class="col-md-4">
                            <label for="distance" class="form-label">Distance (Miles)</label>
                            <select class="form-select form-control h_40" id="distance">
                                <option selected>Within</option>
                                <option value="5">5 miles</option>
                                <option value="10">10 miles</option>
                                <option value="25">25 miles</option>
                                <option value="50">50 miles</option>
                                <option value="100">100 miles</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="categories" class="form-label">Categories</label>
                                <input type="text" class="form-control h_40" id="categories" placeholder="Choose Categories">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-groupd">
                                <label for="ticket_questions" class="form-label fs-6">Type of Widget</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="0" id="charge_bear_by" name="charge_bear_by">
                                    <label class="form-check-label" for="charge_bear_by">
                                        Cleanups Listing
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="1" id="charge_bear_by2" name="charge_bear_by" checked="">
                                    <label class="form-check-label" for="charge_bear_by2">
                                        Cleanups On the Map
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-groupd">
                                <label for="organizer_id" class="form-label">Organizer</label>
                                <input type="text" class="form-control h_40" id="organizer_id"
                                    placeholder="Search and Select Organizer">
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
@endsection
