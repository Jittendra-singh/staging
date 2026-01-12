@php
    $pageName = 'Dashboard';
@endphp
@extends('layouts.backend_new')
@section('content')
    {{-- Dashboard --}}
    <div class="row g-4 text-center">
        <!-- Total Events -->
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0 rounded-4 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white">Total Events</h6>
                    <h3 class="fw-bold text-white">{{$details->total_events}}</h3>
                    <a class="text-white" href="{{route('index-events')}}">View Detail</a>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0 rounded-4 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white">Upcoming Events</h6>
                    <h3 class="fw-bold text-white">{{$details->upcoming_events}}</h3>
                    <a class="text-white" href="{{route('index-events', ['start_date' => date('Y-m-d')])}}">View Detail</a>
                </div>
            </div>
        </div>

        <!-- Total Volunteers -->
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0 rounded-4 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white">Total Volunteers Registered</h6>
                    <h3 class="fw-bold text-white">{{$details->total_volunteers_registered}}</h3>
                    <a class="text-white" href="{{route('index-volunteers')}}">View Detail</a>
                </div>
            </div>
        </div>

        <!-- Total organizers -->
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0 rounded-4 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white">Total Organizers</h6>
                    <h3 class="fw-bold text-white">{{$details->total_organizers}}</h3>
                    <a class="text-white" href="{{route('index-volunteers')}}">View Detail</a>
                </div>
            </div>
        </div>

        <!-- New Registrations -->
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0 rounded-4 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white">New Registrations This Month</h6>
                    <h3 class="fw-bold text-white">{{$details->total_registration_month}}</h3>
                    <a class="text-white" href="{{route('index-volunteers', ['start_date' => date('Y-m-01')])}}">View Detail</a>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0 rounded-4 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white">Total Orders (Tickets/Donations)</h6>
                    <h3 class="fw-bold text-white">{{$details->total_orders}}</h3>
                    <a class="text-white" href="{{route('index-orders')}}">View Detail</a>
                </div>
            </div>
        </div>

        <!-- Pending Community Service Requests -->
        <div class="col-md-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0 rounded-4 bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white">Community Service Requests</h6>
                    <h3 class="fw-bold text-white text-danger">{{$details->pending_community_service_req}}</h3>
                    <a class="text-white" href="#">Checkin Individual Event</a>
                </div>
            </div>
        </div>

    </div>
@endsection
