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
                        <h3><i class="fa-solid fa-certificate me-3 mb-3"></i>{{ __('messages.volunteer_requests') }}</h3>
                        <p class="mb-2">{{ __('messages.front_volunteer_requests_desc') }}</p>
                        @php
                            $breadcrumbs = [
                                ['name' => __('messages.volunteer_requests'), 'url' => route('front-certificate-volunteer-requests')]
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
                            <i class="fa fa-filter me-2"></i><span>{{ __('messages.front_filters') }}</span>
                        </div>
                        <form class="filter-form">
                            <div class="row g-3 mb-3">
                                {{-- Event Filter --}}
                                <div class="col-md-3">
                                    <label class="form-label filter-label">{{ __('messages.front_search_by_event') }}</label>
                                    <div class="filter-input-wrap">
                                        <i class="fa fa-search filter-icon"></i>
                                        <select class="selectpicker filter-field" name="event" data-live-search="true" title="{{ __('messages.front_find_event_by_name') }}">
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
                                    <label class="form-label filter-label">{{ __('messages.front_search_by_volunteer') }}</label>
                                    <div class="filter-input-wrap">
                                        <i class="fa fa-user filter-icon"></i>
                                        <input class="form-control filter-field" value="{{ request()->volunteer_search }}" name="volunteer_search" type="text" placeholder="{{ __('messages.front_name_email_phone') }}">
                                    </div>
                                </div>

                                {{-- Status Filter --}}
                                <div class="col-md-2">
                                    <label class="form-label filter-label">{{ __('messages.front_status') }}</label>
                                    <select class="selectpicker filter-field w-100" name="status" title="{{ __('messages.front_status_all') }}">
                                        <option value="">{{ __('messages.front_status_all') }}</option>
                                        <option value="requested" @selected(request()->status == 'requested')>{{ __('messages.front_requested') }}</option>
                                        <option value="approved" @selected(request()->status == 'approved')>{{ __('messages.front_approved') }}</option>
                                        <option value="rejected" @selected(request()->status == 'rejected')>{{ __('messages.front_rejected') }}</option>
                                    </select>
                                </div>

                                {{-- Date Filter --}}
                                <div class="col-md-2">
                                    <label class="form-label filter-label">{{ __('messages.front_by_request_date') }}</label>
                                    <div class="filter-input-wrap">
                                        <i class="fa fa-calendar filter-icon"></i>
                                        <input autocomplete="off" class="form-control filter-field only_date_picker" value="{{ $by_date ?? null }}" name="by_date" type="text" placeholder="{{ __('messages.front_by_date') }}">
                                    </div>
                                </div>

                                {{-- Filter Buttons --}}
                                <div class="col-md-2 align-items-end d-flex gap-2">
                                    <button class="btn btn-filter-search flex-grow-1" type="submit">
                                        <i class="fa fa-filter me-1"></i>{{ __('messages.front_filter') }}
                                    </button>
                                    <a href="{{ url()->current() }}" class="btn btn-filter-reset" title="{{ __('messages.front_reset_filters') }}">
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
                                                    <th>{{ __('messages.front_volunteer') }}</th>
                                                    <th>{{ __('messages.front_event') }}</th>
                                                    <th>{{ __('messages.front_requested_date') }}</th>
                                                    <th>{{ __('messages.front_status') }}</th>
                                                    <th>{{ __('messages.front_action') }}</th>
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
                                                                <br/><span class="badge custom-badge-warning mt-1">{{ __('messages.front_late_request') }}</span>
                                                            @endif
                                                        </td>

                                                        {{-- Status --}}
                                                        <td>
                                                            @if($ticket->community_form_status == 'approved')
                                                                <span class="badge custom-badge-success">{{ __('messages.front_approved') }}</span>
                                                                @if($ticket->certificate_approve_reject_at)
                                                                    <br/><small class="text-muted">{{ date(config('app.dateFormat'), strtotime($ticket->certificate_approve_reject_at)) }}</small>
                                                                @endif
                                                            @elseif($ticket->community_form_status == 'rejected')
                                                                <span class="badge custom-badge-danger">{{ __('messages.front_rejected') }}</span>
                                                                @if($ticket->certificate_approve_reject_at)
                                                                    <br/><small class="text-muted">{{ date(config('app.dateFormat'), strtotime($ticket->certificate_approve_reject_at)) }}</small>
                                                                @endif
                                                            @elseif($ticket->community_form_status == 'requested')
                                                                <span class="badge custom-badge-warning">{{ __('messages.front_requested') }}</span>
                                                            @else
                                                                <span class="badge custom-badge-warning">{{ __('messages.front_pending') }}</span>
                                                            @endif
                                                        </td>

                                                        {{-- Actions --}}
                                                        <td>
                                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                                <a data-bs-toggle="tooltip" data-bs-original-title="{{ __('messages.front_view_details') }}" class="secondary-btn w-fit px-3" href="{{route('front-certificate-request-detail', encrypt($ticket->id))}}"><i class="fa fa-eye"></i></a>

                                                                @if($ticket->community_form_status != 'approved' && $ticket->community_form_status != 'rejected')
                                                                <button
                                                                    class="btn-approve-certificate primary-btn w-fit px-3" url="{{ route('front-approve-certificate', encrypt($ticket->id)) }}"
                                                                    data-name="{{ $ticket->volunteer->first_name ?? '' }} {{ $ticket->volunteer->last_name ?? '' }}">
                                                                    {{ __('messages.front_approve') }}
                                                                </button>
                                                                @endif

                                                                {{-- Reject Button --}}
                                                                @if($ticket->community_form_status != 'rejected' && $ticket->community_form_status != 'approved')
                                                                <button
                                                                    class="btn-reject-certificate secondary-btn w-fit px-3" url="{{ route('front-reject-certificate', encrypt($ticket->id)) }}"
                                                                    data-name="{{ $ticket->volunteer->first_name ?? '' }} {{ $ticket->volunteer->last_name ?? '' }}">
                                                                    {{ __('messages.front_reject') }}
                                                                </button>
                                                                @endif

                                                                {{-- Download (if approved) --}}
                                                                @if($ticket->community_form_status == 'approved' && !empty($ticket->community_form_pdf))
                                                                <a
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-original-title="{{ __('messages.front_download_certificate') }}"
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
                                                        <td colspan="6" class="text-center">{{ __('messages.front_no_certificate_requests_found') }}</td>
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
