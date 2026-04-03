@php
    $pageName = 'Reschedule Event - ' . $event->event_name;
    $parentPageName = $module;
    $parentRoute = route('index-events');
    $tableHead = [
        'Full Name',
        'Email',
        'Ticket Name',
        'Created Date',
        'Waiver',
        'Community Form',
        'Checkin',
        //'Actions',
    ];
@endphp
@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $parentPageName, 'parentRoute' => $parentRoute,'breadcrumb' => [
    ['name' => 'Events', 'route' => route('index-events')],
    ['name' => 'Reschedule Event', 'route' => ''],
]])
@section('css')
    <x-reusables.datatablecss />
    <x-reusables.advanceformcss />
    <style>
    
    .remove-event-img-btn {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        line-height: 1;
    }

    .remove-event-img-btn:hover {
        background: #b02a37;
    }
    </style>
@endsection
@section('js')
    <x-reusables.datatablejs />
    <x-reusables.advanceformjs />
@endsection
@section('content')
    <div class="row">
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <x-reusables.frontend.alert />
                    <form id="rescheduleEventForm" method="post" action="{{ route('submit-reschedule-event-form-admin', ['id' => $event->encrypted_id]) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-sm-3 mb-2">
                                <label for="start_datetime" class="form-label">Start Date & Time*</label>
                                <input autocomplete="off" type="text" class="form-control" id="start_datetime" name="start_datetime" value="" required>
                            </div>
                            <div class="col-sm-3 mb-2">
                                <label for="end_datetime" class="form-label">End Date & Time*</label>
                                <input autocomplete="off" type="text" class="form-control" id="end_datetime" name="end_datetime" value="" required>
                            </div>
                            <div class="col-sm-3 mb-2">
                                <label for="site_publish_date" class="form-label">Event Page Publish Date*</label>
                                <input type="text" class="form-control" id="site_publish_date" required name="site_publish_date" value="">
                            </div>
                            <div class="col-sm-3 mb-2">
                                <label for="site_depublish_date" class="form-label">Event Page Expiry Date*</label>
                                <input type="text" class="form-control" id="site_depublish_date" required
                                    name="site_depublish_date" value="">
                            </div>
                            <div class="col-sm-12 mt-2"> 
                                <label class="form-label fs-6">Tickets</label>
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Ticket Name</th>
                                                <th>Type</th>
                                                <th>Price</th>
                                                <th>Sell Start Date</th>
                                                <th>Sell End Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($event->eventTickets as $ticket)
                                                <tr>
                                                    <td>{{$ticket->ticket_name}}</td>
                                                    <td>{{ ucfirst($ticket->ticket_type) }}</td>
                                                    <td>{{ config('app.currency').formatAmt($ticket->ticket_price) }}</td>
                                                    <td>
                                                    <div class="loc-group position-relative">
                                                        <input autocomplete="off" type="text" class="form-control mt-1 form-ele tk_sell_start_date" name="sell_start_time[]" value="" required>
                                                        <span class="absolute-icon" style="top:0;"><i class="fa-solid fa-calendar-days"></i></span>
                                                    </div>
                                                    </td>
                                                    <td>
                                                    <div class="loc-group position-relative">
                                                        <input autocomplete="off" type="text" class="form-control mt-1 form-ele tk_sell_end_date" name="sell_end_time[]" value="" required>
                                                        <span class="absolute-icon" style="top:0;"><i class="fa-solid fa-calendar-days"></i></span>
                                                    </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label fs-6">Note to Volunteers (optional)</label>
                                <textarea name="reschedule_note" class="form-control" rows="4" placeholder="Briefly explain the reason for the reschedule (e.g., weather conditions, location change).."></textarea>
                                <small>This note will be included in the automated notification email sent to all participants.</small>
                            </div> 
                            <div class="col-12 text-center mt-2">
                                <a href="{{ route('index-events') }}" class="btn btn-secondary ms-2">Cancel</a>
                                <button id="reschedule_now_btn" type="button" class="btn btn-primary ms-2">Reschedule Now</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
