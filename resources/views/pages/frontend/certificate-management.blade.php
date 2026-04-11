@extends('layouts.frontend')
@push('style')
<link rel="stylesheet" href="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets_front/css/filter-css.css') }}">
@endpush

@section('content')
<div class="wrapper wrapper-body">
    <div class="dashboard-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-between">
                    <div class="d-main-title">
                        <h3><i class="fa-solid fa-certificate me-3 mb-3"></i>Certificate Management</h3>
                        <p class="mb-2">Review and manage community service certificate requests from volunteers.</p>
                        @php
                            $breadcrumbs = [
                                ['name' => 'Certificate Management', 'url' => route('front-certificate-management')]
                            ]
                        @endphp
                        <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                    </div>
                </div>
            </div>

            <x-reusables.frontend.alert />

            <div class="col-md-12">
                {{-- FILTERS --}}
                <div class="filter-card mt-4">
                    <div class="card-body p-4">
                        <div class="filter-card-header mb-3">
                            <i class="fa fa-filter me-2"></i><span>Filters</span>
                        </div>
                        <form class="filter-form">
                            <div class="row g-3 mb-3">
                                {{-- Event Filter --}}
                                <div class="col-md-3">
                                    <label class="form-label filter-label">Search By Event</label>
                                    <div class="filter-input-wrap">
                                        <i class="fa fa-search filter-icon"></i>
                                        <select class="selectpicker filter-field" name="event" data-live-search="true" title="Find an event by name...">
                                            @foreach($events as $event)
                                                <option value="{{ $event->id }}" @selected(request()->event == $event->id)
                                                    data-content="{{ $event->event_name }}<br/><small>{{ date('M d, Y h:i A', strtotime($event->start_datetime)) }}</small>">
                                                    {{ $event->event_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Volunteer Search --}}
                                <div class="col-md-3">
                                    <label class="form-label filter-label">Search By Volunteer</label>
                                    <div class="filter-input-wrap">
                                        <i class="fa fa-user filter-icon"></i>
                                        <input class="form-control filter-field" value="{{ request()->volunteer_search }}" name="volunteer_search" type="text" placeholder="Name, Email, or Phone">
                                    </div>
                                </div>

                                {{-- Status Filter --}}
                                <div class="col-md-2">
                                    <label class="form-label filter-label">Status</label>
                                    <div class="filter-input-wrap">
                                        <select class="form-select filter-field" name="status">
                                            <option value="">All Status</option>
                                            <option value="requested" @selected(request()->status == 'requested')>Requested</option>
                                            <option value="approved" @selected(request()->status == 'approved')>Approved</option>
                                            <option value="rejected" @selected(request()->status == 'rejected')>Rejected</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Date Filter --}}
                                <div class="col-md-2">
                                    <label class="form-label filter-label">By Request Date</label>
                                    <div class="filter-input-wrap">
                                        <i class="fa fa-calendar filter-icon"></i>
                                        <input autocomplete="off" class="form-control filter-field only_date_picker" value="{{ $by_date ?? null }}" name="by_date" type="text" placeholder="By Date">
                                    </div>
                                </div>

                                {{-- Filter Buttons --}}
                                <div class="col-md-2 align-items-end d-flex gap-2">
                                    <button class="btn btn-filter-search flex-grow-1" type="submit">
                                        <i class="fa fa-filter me-1"></i>Filter
                                    </button>
                                    <a href="{{ url()->current() }}" class="btn btn-filter-reset" title="Reset Filters">
                                        <i class="fa fa-rotate-left"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="conversion-setup">
                    <div class="main-card mt-3">
                        <div class="dashboard-wrap-content p-4">
                            <div class="table-card mt-4">
                                <div class="main-table">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Volunteer</th>
                                                    <th>Event</th>
                                                    <th>Requested Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($requests) && $requests->isNotEmpty())
                                                    @foreach($requests as $ticket)
                                                    <tr class="single-tr">
                                                        {{-- Volunteer --}}
                                                        <td>
                                                            <b>{{ $ticket->volunteer->first_name ?? '' }} {{ $ticket->volunteer->last_name ?? '' }}</b><br/>
                                                            <small class="text-muted">{{ $ticket->volunteer->email_address ?? '' }}</small>
                                                            @if(!empty($ticket->volunteer->phone))
                                                            <br/><small class="text-muted">{{ $ticket->volunteer->phone ?? '' }}</small>
                                                            @endif
                                                        </td>

                                                        {{-- Event --}}
                                                        <td>
                                                            <b>{{ $ticket->event->event_name ?? '' }}</b><br/>
                                                            <small class="text-primary">
                                                                <i class="fa fa-calendar me-1"></i>
                                                                {{ $ticket->event ? date(config('app.dateTimeFormat'), strtotime($ticket->event->start_datetime)) : '' }}
                                                            </small>
                                                        </td>

                                                        {{-- Requested Date --}}
                                                        <td>
                                                            {{ $ticket->certificate_requested_at ? date(config('app.dateTimeFormat'), strtotime($ticket->certificate_requested_at)) : '-' }}
                                                            @if($ticket->is_late_checkin == 1)
                                                                <br/><span class="badge custom-badge-warning mt-1">Late Request</span>
                                                            @endif
                                                        </td>

                                                        {{-- Status --}}
                                                        <td>
                                                            @if($ticket->community_form_status == 'approved')
                                                                <span class="badge custom-badge-success">Approved</span>
                                                                @if($ticket->certificate_approve_reject_at)
                                                                    <br/><small class="text-muted">{{ date(config('app.dateFormat'), strtotime($ticket->certificate_approve_reject_at)) }}</small>
                                                                @endif
                                                            @elseif($ticket->community_form_status == 'rejected')
                                                                <span class="badge custom-badge-danger">Rejected</span>
                                                                @if($ticket->certificate_approve_reject_at)
                                                                    <br/><small class="text-muted">{{ date(config('app.dateFormat'), strtotime($ticket->certificate_approve_reject_at)) }}</small>
                                                                @endif
                                                            @elseif($ticket->community_form_status == 'requested')
                                                                <span class="badge custom-badge-warning">Requested</span>
                                                            @else
                                                                <span class="badge custom-badge-warning">Pending</span>
                                                            @endif
                                                        </td>

                                                        {{-- Actions --}}
                                                        <td>
                                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                                <a data-bs-toggle="tooltip" data-bs-original-title="View Details" class="secondary-btn w-fit px-3" href=""><i class="fa fa-eye"></i></a>

                                                                @if($ticket->community_form_status != 'approved' && $ticket->community_form_status != 'rejected')
                                                                <button
                                                                    class="btn-approve-certificate primary-btn w-fit px-3" url="{{ route('front-approve-certificate', encrypt($ticket->id)) }}"
                                                                    data-name="{{ $ticket->volunteer->first_name ?? '' }} {{ $ticket->volunteer->last_name ?? '' }}">
                                                                    Approve
                                                                </button>
                                                                @endif

                                                                {{-- Reject Button --}}
                                                                @if($ticket->community_form_status != 'rejected' && $ticket->community_form_status != 'approved')
                                                                <button
                                                                    class="btn-reject-certificate secondary-btn w-fit px-3" url="{{ route('front-reject-certificate', encrypt($ticket->id)) }}"
                                                                    data-name="{{ $ticket->volunteer->first_name ?? '' }} {{ $ticket->volunteer->last_name ?? '' }}">
                                                                    Reject
                                                                </button>
                                                                @endif

                                                                {{-- Download (if approved) --}}
                                                                @if($ticket->community_form_status == 'approved' && !empty($ticket->community_form_pdf))
                                                                <a
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-original-title="Download Certificate"
                                                                    class="primary-btn w-fit px-3" href="{{ route('show-community-service-pdf', ['ticket' => encrypt($ticket->id)]) }}" target="_blank">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="6" class="text-center">No certificate requests found.</td>
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
        </div>
    </div>
</div>


<x-reusables.google :locationarray="[]"/>
@endsection
@push('script')
<x-reusables.advanceformjs />
@endpush