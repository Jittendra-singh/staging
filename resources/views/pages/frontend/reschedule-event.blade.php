@extends('layouts.frontend')
@push('style')
<link rel="stylesheet" href="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.css') }} ">
<style>
#showWidgetDetail .modal-dialog{
    width: 1000px!important;
    max-width: unset!important;
}
</style>
  <style>
    .confirm-alert {
      background-color: #eef3fb;
      border: 1px solid #c6d8f5;
      border-radius: 10px;
      padding: 14px 18px;
      width: 100%;
    }

    .confirm-alert .icon-wrap {
      flex-shrink: 0;
      width: 22px;
      height: 22px;
      background-color: #3b6fd4;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 2px;
    }

    .confirm-alert .icon-wrap i {
      color: #fff;
      font-size: 13px;
      line-height: 1;
    }

    .confirm-alert .alert-title {
      font-size: 0.875rem;
      font-weight: 600;
      color: #1e3a6e;
      margin-bottom: 3px;
    }

    .confirm-alert .alert-body {
      font-size: 0.8125rem;
      color: #3d5a8a;
      line-height: 1.5;
      margin: 0;
    }
  </style>
@endpush
@section('content')
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            <div class="container-fluid"> 
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-between">
                        <div class="d-main-title">
                            <h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>Reschedule Cleanup: <span class="text-primary">{{$event->event_name}}</span></h3>
                            <p class="mb-2">Update the schedule and notify all registered participants about the new timing.</p>
                            @php
                                $breadcrumbs = [ 
                                    [
                                        'name' => 'Reschedule Cleanup', 
                                        'url' => route('front-reschedule-event',encrypt($event->id))
                                    ],
                                ]
                            @endphp     
                            <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                            <!-- <p class="mb-0 mt-3">You can send a message to your RSVP’s in the ‘Registration List’.</p> --> 
                        </div>
                    </div>
                </div>
                <x-reusables.frontend.alert />
                <form action="{{route('submit-reschedule-event-form', encrypt($event->id))}}" method="post" class="" id="rescheduleEventForm">
                    @csrf
                    <div class="main-card p-4 mt-4">
                        <div class="bp-title mb-2 d-flex align-items-center justify-content-between">
                            <h4 class="px-0 mb-0 py-2">
                                <i class="fa-solid fa-clock step_icon me-3"></i>Date and Time Details
                            </h4>
                        </div>
                        <div class="row"> 
                            <div class="col-md-6 mt-3">
                                <label for="start_datetime" class="form-label">Start Date Time *</label>
                                <div class="loc-group position-relative">
                                    <input autocomplete="off" class="form-control h_50 datepicker-here-pp" id="start_datetime" data-language="en" name="start_datetime" type="text" placeholder="YYYY/MM/DD, --:--" value="" required />
                                    <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                </div>
                            </div> 
                            <div class="col-md-6 mt-3">
                                <label for="end_datetime" class="form-label">End Date Time *</label>
                                <div class="loc-group position-relative">
                                    <input autocomplete="off" name="end_datetime"
                                        class="form-control h_50 datepicker-here-pp" id="end_datetime" data-language="en" type="text"
                                        placeholder="YYYY/MM/DD, --:--"
                                        value="" required />
                                    <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                </div>
                            </div> 
                        </div>
                        <div class="row"> 
                            <div class="col-md-6 mt-3">
                                <label class="form-label fs-6">Event Page Publish Date*</label>
                                <div class="loc-group position-relative">
                                    <input autocomplete="off"
                                        class="form-control h_50 datepicker-here-pp"
                                        data-language="en"
                                        name="site_publish_date" id="site_publish_date" type="text"
                                        placeholder="YYYY/MM/DD"
                                        value="" required />
                                    <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                </div>
                            </div> 
                            <div class="col-md-6 mt-3">
                                <label class="form-label fs-6">Event Page Expiry Date*</label>
                                <div class="loc-group position-relative">
                                    <input autocomplete="off" name="site_depublish_date"
                                        class="form-control h_50 datepicker-here-pp" id="site_depublish_date"
                                        data-language="en" type="text"
                                        placeholder="YYYY/MM/DD"
                                        value="" required />
                                    <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="main-card p-4 mt-4">
                        <div class="bp-title mb-2 d-flex align-items-center justify-content-between">
                            <h4 class="px-0 mb-0 py-2">
                                <i class="fa-solid fa-ticket step_icon me-3"></i>Ticket Details
                            </h4>
                        </div>
                        @foreach($event->eventTickets as $ticket)
                        <div class="row"> 
                            <div class="col-md-4 mt-3">
                                <div class="form-group">
                                    <label class="form-label fs-6">Ticket Name</label>
                                    <input id="" class="form-control h_50 get-slug" name="" type="text" placeholder="Ticket Name" value="{{ $ticket->ticket_name }}" disabled />
                                </div>
                            </div> 
                            <div class="col-md-2 mt-3">
                                <div class="form-group">
                                    <label class="form-label fs-6">Ticket Type</label>
                                    <input id="" class="form-control h_50 get-slug" name="" type="text" placeholder="Ticket Type" value="{{ ucfirst($ticket->ticket_type) }}" disabled />
                                </div>
                            </div> 
                            <div class="col-md-2 mt-3">
                                <div class="form-group">
                                    <label class="form-label fs-6">Ticket Price</label>
                                    <input id="" class="form-control h_50 get-slug" name="" type="text" placeholder="Ticket Price" value="{{ config('app.currency').formatAmt($ticket->ticket_price) }}" disabled />
                                </div>
                            </div> 
                            <div class="col-md-2 mt-3">
                                <div class="form-group">
                                    <label class="form-label fs-6">Sell Start Date</label>
                                    <div class="loc-group position-relative">
                                        <input autocomplete="off" type="text" class="form-control h_50 mt-1 form-ele tk_sell_start_date" name="sell_start_time[]" value="" required>
                                        <span class="absolute-icon" ><i class="fa-solid fa-calendar-days"></i></span>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-2 mt-3">
                                <div class="form-group">
                                    <label class="form-label fs-6">Sell End Date</label>
                                    <div class="loc-group position-relative">
                                        <input autocomplete="off" type="text" class="form-control h_50 mt-1 form-ele tk_sell_end_date" name="sell_end_time[]" value="" required>
                                        <span class="absolute-icon" ><i class="fa-solid fa-calendar-days"></i></span>
                                    </div>
                                </div>
                            </div> 
                        </div>
                        @endforeach
                    </div>
                    <div class="main-card p-4 mt-4">
                        <div class="bp-title mb-2 d-flex align-items-center justify-content-between">
                            <h4 class="px-0 mb-0 py-2">
                                <i class="fa-solid fa-comment step_icon me-3"></i>Note
                            </h4>
                        </div>
                        <div class="row mt-3">
                            <div class="form-group">
                                <label class="form-label fs-6">Note to Volunteers (optional)</label>
                                <textarea name="reschedule_note" class="form-control" rows="4" placeholder="Briefly explain the reason for the reschedule (e.g., weather conditions, location change).."></textarea>
                                <small>This note will be included in the automated notification email sent to all participants.</small>
                            </div>
                        </div>
                        <div class="row mt-3"> 
                            <!-- <small class="text-center">Note: Rescheduling an event will update the event details and automatically send a rescheduling email to all attendees.</small> -->
                            <div class="col-sm-12">
                                <div class="confirm-alert d-flex gap-3">
                                    <div class="icon-wrap">
                                        <i class="bi bi-info-lg"></i>
                                    </div>
                                    <div>
                                        <p class="alert-title">Before you confirm</p>
                                        <p class="alert-body">
                                            Rescheduling will notify all registered volunteers immediately. Some volunteers may no
                                            longer be able to attend and might cancel their registration.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mt-3 justify-content-center">
                        <button type="button" id="reschedule_now_btn" class="primary-btn w-fit">
                            Reschedule Now
                        </button>
                        <a href="{{route('front-cleanup-hosting',['type'=>'organizer'])}}" class="primary-btn w-fit ms-2">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<div class="modal fade" id="showWidgetDetail" tabindex="-1" aria-labelledby="showWidgetDetailsLabel"
    aria-modal="true" role="dialog"> 
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showWidgetDetailLabel">{{ __('messages.front_embedded_widget_details') }}</h5>
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
    <script src="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.js') }}"></script>
<script>
</script>

@endpush
