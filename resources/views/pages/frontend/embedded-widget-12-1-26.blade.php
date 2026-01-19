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
                            <p class="mb-0 mt-3">You can send a message to your RSVP’s in the ‘Registration List’.</p>
                        </div>
                    </div>
                </div>
                <form class="main-card mt-5 p-4 shadow-sm bg-white rounded">
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
                            <label for="eventCount" class="form-label">How many events do you want to display?</label>
                            <input type="number" class="form-control h_40" id="eventCount" value="5" min="1">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="categories" class="form-label">Categories</label>
                                <input type="text" class="form-control h_40" id="categories" placeholder="Choose Categories">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-groupd">
                                <label for="keyword" class="form-label">Keyword</label>
                                <input type="text" class="form-control h_40" id="keyword"
                                    placeholder="Optionally choose a keyword to search">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 justify-content-start">
                        <div class="col-md-6 d-flex justify-content-start gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="onlyMine">
                                <label class="form-check-label" for="onlyMine">
                                    Show only cleanups I am hosting?
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="showMoreBtn">
                                <label class="form-check-label" for="showMoreBtn">
                                    Display a “Show More Cleanups” Button
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mt-3 justify-content-start">
                        <button type="submit" class="primary-btn w-fit">
                            Get Widget Embed Code
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
@endsection
