@extends('layouts.frontend')
@push('style')
<link rel="stylesheet" href="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.css') }} ">
<link rel="stylesheet" href="{{ asset('assets_front/css/filter-css.css') }} ">
<style>
</style>
@endpush
@section('content')
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-between">
                        <div class="d-main-title">
                            <h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>{{ __('messages.my_requests') }}</h3>
							<p class="mb-2">{{ __('messages.front_my_requests_desc') }}</p>
                            @php
                                $breadcrumbs = [
                                    [
                                        'name' => __('messages.my_requests'),
                                        'url' => route('front-certificate-my-requests')
                                    ],
                                ]
                            @endphp
                            <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                            <!-- <p class="mb-0 mt-3">You can send a message to your RSVP’s in the ‘Registration List’.</p> -->
                        </div>
                    </div>
                </div>
                <x-reusables.frontend.alert />
                <div class="col-md-12">
                    <div class="filter-card mt-4">
                        <div class="card-body p-4">
                            <div class="filter-card-header mb-3">
                                <i class="fa fa-filter me-2"></i>
                                <span>{{ __('messages.front_filters') }}</span>
                            </div>
                            <form class="filter-form">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-5">
                                        <label class="form-label filter-label">{{ __('messages.front_search_by_event') }}</label>
                                        <div class="filter-input-wrap">
                                            <i class="fa fa-search filter-icon"></i>
                                            <select class="selectpicker filter-field" name="event" data-live-search="true" title="{{ __('messages.front_find_event_by_name') }}">
                                                @if(isset($events) && $events->isNotEmpty())
                                                @foreach($events as $event)
                                                    <option value="{{ $event->id }}" @selected(request()->event == $event->id)
                                                        data-content="{{ $event->event_name }}<br/><small class='p-0'>{{ date('M d, Y h:i A', strtotime($event->start_datetime)) }} - {{ date('M d, Y h:i A', strtotime($event->end_datetime)) }}</small>">
                                                        {{ $event->event_name }}
                                                    </option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label filter-label">{{ __('messages.front_search_by_organizer') }}</label>
                                        <div class="filter-input-wrap">
                                            <i class="fa fa-user filter-icon"></i>
                                            <input class="form-control filter-field" value="{{ request()->organizer_search }}" name="organizer_search" type="text" placeholder="{{ __('messages.front_name_email_phone') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label filter-label">{{ __('messages.front_by_date') }}</label>
                                        <div class="filter-input-wrap">
                                            <i class="fa fa-calendar filter-icon"></i>
                                            <input autocomplete="off" class="form-control filter-field only_date_picker" value="{{ $by_date??null }}" name="by_date" type="text" placeholder="{{ __('messages.front_by_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2 align-items-end d-flex gap-2">
                                        <button class="btn btn-filter-search flex-grow-1" type="submit">
                                            <i class="fa fa-filter me-2"></i>{{ __('messages.front_filter') }}
                                        </button>
                                        <a href="{{ url()->current() }}" class="btn btn-filter-reset" title="{{ __('messages.front_reset_filters') }}">
                                            <i class="fa fa-rotate-left"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="conversion-setup">
                        <div class="main-card mt-3">
                            <div class="dashboard-wrap-content p-4">
                                <div class="table-card mt-4">
                                    <div class="main-table">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">{{ __('messages.front_event') }}</th>
                                                        <th scope="col">{{ __('messages.front_requested_date') }}</th>
                                                        <th scope="col">{{ __('messages.front_certificate_status') }}</th>
                                                        <th scope="col">{{ __('messages.front_action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($requests) && $requests->isNotEmpty())
                                                    @foreach ($requests as $data)
                                                        <tr class="single-tr">
                                                            <td>
                                                            <b class="mb-1">{{$data->event_name}}</b><br/>
                                                            <p class="mb-0">{{ __('messages.front_organizer') }}: {{ $data->creator->first_name.' '.$data->creator->last_name }}</p>
                                                            <p class="text-primary"><i class="fa fa-calendar me-1"></i>{{ $data->eventStartDateTime }}</p>
                                                            </td>
                                                            <td>{{date(config('app.dateTimeFormat'),strtotime($data->pivot->certificate_requested_at))}}
                                                                @if($data->pivot->is_late_checkin==1)
                                                                <br/><span class="badge custom-badge-warning mt-2">{{ __('messages.front_late_request') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                            @if($data->pivot->community_form_status=='approved')
                                                                <span class="badge custom-badge-success">{{ __('messages.front_approved') }}</span>
                                                            @elseif($data->pivot->community_form_status=='rejected')
                                                                <span class="badge custom-badge-danger">{{ __('messages.front_rejected') }}</span>
                                                                @if($data->pivot->certificate_approve_reject_at)
                                                                    <br/><small class="text-muted">{{ date(config('app.dateFormat'), strtotime($data->pivot->certificate_approve_reject_at)) }}</small>
                                                                @endif
                                                            @else
                                                                <span class="badge custom-badge-warning">{{ __('messages.front_pending') }}</span>
                                                            @endif
                                                            </td>
                                                            <td>
                                                                <div class="d-flex flex-wrap  align-items-center">
                                                                <a data-bs-toggle="tooltip" data-bs-original-title="{{ __('messages.front_view_details') }}" class="secondary-btn w-fit px-3" href="{{route('front-certificate-request-detail', encrypt($data->pivot->id))}}"><i class="fa fa-eye"></i></a>
                                                                @if($data->pivot->community_form_status=='approved')
                                                                <a data-bs-toggle="tooltip" data-bs-original-title="{{ __('messages.front_download_certificate') }}" class="primary-btn w-fit px-3 ms-2" href="{{ route('show-community-service-pdf', ['ticket' => encrypt($data->pivot->id)]) }}" target="_blank"><i class="fa fa-download"></i></a>
                                                                @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="4" class="text-center">{{ __('messages.front_no_request_found') }}</td>
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
