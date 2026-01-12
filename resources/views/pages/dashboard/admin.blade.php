@php
    $pageName = 'Dashboard';
@endphp
@extends('layouts.backend_new')
@section('content')
<div class="container py-4">
    <h2 class="mb-4">Admin Dashboard</h2>
    <div class="row g-4">
        <!-- Quick Stats -->
        <div class="col-md-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Events</h5>
                    <h2>128</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <h5 class="card-title">Upcoming Events</h5>
                    <h2>12</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Volunteers</h5>
                    <h2>540</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <h5 class="card-title">New Registrations</h5>
                    <h2>34 <small>/week</small></h2>
                    <h2>120 <small>/month</small></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mt-3">
        <div class="col-md-3">
            <div class="card text-white bg-secondary h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <h2>210</h2>
                    <small>Tickets / Donations</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Top Active Organizers</h5>
                    <ul class="list-unstyled mb-0">
                        <li>Org A <span class="badge bg-primary">24 Events</span></li>
                        <li>Org B <span class="badge bg-primary">18 Events</span></li>
                        <li>Org C <span class="badge bg-primary">15 Events</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Pending Service Requests</h5>
                    <h2>7</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Payment Summaries</h5>
                    <h2>$2,340</h2>
                    <small>Fees Collected</small>
                </div>
            </div>
        </div>
    </div>
    <!-- Analytics Section (Optional: Add charts here) -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Analytics Overview
                </div>
                <div class="card-body">
                    <p>Charts and analytics can be integrated here (e.g., using Chart.js or similar libraries).</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection