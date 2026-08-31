@php
    $pageName = 'Volunteers - ' . $event->event_name;
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
    ['name' => 'Volunteers & Wrap-Up Report', 'route' => ''],
]])
@section('css')
    <x-reusables.datatablecss />
    <style>
    .remove-new-img {
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

    .remove-new-img:hover {
        background: #b02a37;
    }
    </style>
	<style>
		.resend-opt-card {
			border: 1.5px solid #dee2e6;
			border-radius: 10px;
			padding: 14px 16px;
			cursor: pointer;
			background: #f8f9fa;
			transition: border-color 0.15s, background 0.15s;
			margin-bottom: 10px;
		}
		.resend-opt-card.selected {
			border-color: #414598;
			background: #E6F1FB;
		}
		.resend-opt-card:hover:not(.selected) {
			border-color: #adb5bd;
			background: #f1f3f5;
		}
		.resend-radio-dot {
			width: 18px;
			height: 18px;
			border-radius: 50%;
			border: 2px solid #adb5bd;
			flex-shrink: 0;
			margin-top: 2px;
			position: relative;
			transition: border-color 0.15s, background 0.15s;
		}
		.resend-radio-dot.on {
			border-color: #414598;
			background: #414598;
		}
		.resend-radio-dot.on::after {
			content: '';
			display: block;
			width: 6px;
			height: 6px;
			border-radius: 50%;
			background: white;
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
		}
		.resend-opt-title {
			margin: 0 0 3px;
			font-size: 14px;
			font-weight: 600;
			color: #212529;
		}
		.resend-opt-desc {
			margin: 0 0 10px;
			font-size: 12px;
			color: #6c757d;
			line-height: 1.5;
		}
		.resend-email-badge {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 4px 10px;
			border-radius: 6px;
			background: white;
			border: 1px solid #B5D4F4;
			font-size: 12px;
			font-weight: 500;
			color: #414598;
		}
		.resend-email-badge .ti { font-size: 13px; }
		.resend-alt-input {
			font-size: 13px !important;
			transition: opacity 0.15s;
		}
		.resend-alt-input:disabled {
			opacity: 0.55;
			cursor: not-allowed;
			background: #f8f9fa !important;
		}
		.resend-note {
			display: flex;
			align-items: flex-start;
			gap: 8px;
			padding: 10px 12px;
			background: #f0f7ff;
			border-left: 3px solid #414598;
			border-radius: 0 6px 6px 0;
			margin-top: 4px;
			font-size: 12px;
			color: #6c757d;
			line-height: 1.6;
		}
		.resend-note-icon {
			font-size: 15px;
			color: #414598;
			flex-shrink: 0;
			margin-top: 1px;
		}
		.resend-note-label {
			font-weight: 600;
			color: #212529;
		}
		/* resend last log box */
		.resend-last-log-box {
			margin-top: 12px;
			padding: 12px 14px;
			border: 1px solid #D7E6F5;
			border-radius: 10px;
			background: #fbfdff;
		}
		.resend-last-log-header {
			display: flex;
			align-items: center;
			gap: 8px;
			margin-bottom: 10px;
			font-size: 13px;
			font-weight: 700;
			color: #185FA5;
		}
		.resend-last-log-empty {
			font-size: 12px;
			color: #6c757d;
			background: #f8f9fa;
			border-radius: 6px;
			padding: 9px 10px;
		}
		.resend-last-log-row {
			display: flex;
			justify-content: space-between;
			gap: 12px;
			margin-bottom: 8px;
			font-size: 12px;
		}
		.resend-last-log-label {
			color: #6c757d;
			font-weight: 600;
			min-width: 70px;
		}
		.resend-last-log-value {
			color: #212529;
			font-weight: 600;
			text-align: right;
			word-break: break-word;
		}
	</style>
@endsection
@section('js')
    <x-reusables.datatablejs />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- end row -->
            <div class="table-responsive">
                <table id="custom_datatable" sort="1" class="table table-bordered nowrap w-100 volunteer-records"
                    dt-responsive>
                    <x-reusables.table-header :tableHead="$tableHead" />
                    <tbody>
                        @forelse ($listingData as $data)
                            @php 
                                $resendWaiverLogs = $data->pivot->resendWaiverLogs()->latest('created_at')->get()->map(function($log) {
                                    return [
                                        'type'    => $log->link_send_type == 1 ? 'Original Email' : 'Alternative Email',
                                        'email'   => $log->link_send_to_email,
                                        'send_at' => $log->createdDateTime,
                                    ];
                                });
                            @endphp 
                            <tr>
                                <td>{{ $data->name }}
                                    <div class="tr-detail d-none">
                                        @json($data->pivot)
                                    </div>
                                </td>
                                <td>{{ $data->email_address }}</td>
                                <td>{{ $data->pivot->ticket->ticket_name }}
                                    {{ $data->pivot->ticket->ticket_price ? '/ $' . $data->pivot->ticket->ticket_price : '' }}
                                </td>
                                <td>{{ $data->pivot->createdDateTime }}</td>
                                <td width="12%">
                                    <!-- elseif ($data->pivot->event->waiver_form_type == 'external_link')
                                        <a href="{{ $data->pivot->event->waiver_form_content }}" target="_BLANK"
                                            class="text-primary">External Link</a> -->
                                    @if(!$data->pivot->event->waiver_form_type)
                                        <span class="text-primary">No Waiver</span>
                                    @elseif($data->pivot->waiver_filled_at)
                                        <!-- Signed By: 
                                        {{ $data->pivot->waiver_handler_name.' ('.$data->pivot->waiver_handler_email.')' }}<br/> -->
                                        Signed At (<i data-bs-toggle="tooltip" data-bs-original-title="{{ $data->pivot->waiver_handler_name.' ('.$data->pivot->waiver_handler_email.')' }}" class="mdi mdi-account"></i>):<br/>
                                        {{ $data->pivot->waiver_filled_at->format(config('app.dateTimeFormat')) }}<br/>
                                    @else
                                        @if(!empty($data->pivot->waiver_handler_email))
                                        <!-- Signed By: 
                                        {{ $data->pivot->waiver_handler_name.' ('.$data->pivot->waiver_handler_email.')' }}<br/> -->
                                        @endif
                                        Signed At (<i data-bs-toggle="tooltip" data-bs-original-title="{{ $data->pivot->waiver_handler_name.' ('.$data->pivot->waiver_handler_email.')' }}" class="mdi mdi-account"></i>):<br/> <span class="text-danger">Pending</span> 
                                        <a href="{{ route('view-waiver-form', encrypt($data->pivot->id)) }}" target="_blank" data-bs-toggle="tooltip" data-bs-original-title="Waiver Page Link" target="_blank"><span class="badge bg-warning"><i class="mdi mdi-link-variant"></i></span></a> 
                                        <!-- <a href="javascript:void(0);" url="{{ route('send-waiver-invitation', ['ticket' => $data->pivot->encrypted_id]) }}" message="You want to resend email for waiver" data-bs-toggle="tooltip" data-bs-original-title="Resend Waiver Email" class="show-alert-run-url"><span class="badge bg-info"><i class="mdi mdi-email-send"></i></span></a> -->
                                        <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-original-title="Resend Waiver Email" defaultemail="{{ $data->pivot->is_under_18==1?$data->pivot->waiver_handler_email:$data->email_address }}" url="{{ route('send-waiver-invitation', ['ticket' => $data->pivot->encrypted_id]) }}" checkurl="{{ route('check-resend-waiver-admin', ['ticket' => $data->pivot->encrypted_id]) }}" class="btn-resend-waiver-modal"><span class="badge bg-info"><i class="mdi mdi-email-send"></i></span></a>
                                        <a href="javascript:void(0);" data-logs='@json($resendWaiverLogs)' data-bs-toggle="modal" data-bs-target="#resendWaiverLogsModal" data-bs-toggle="tooltip" data-bs-original-title="Resend Waiver Logs" class="btn-resend-waiver-logs-modal"><span class="badge bg-primary"><i class="mdi mdi-history"></i></span></a><br/>
                                    @endif
                                    @if($data->pivot->rsvp_confirmation===0)
                                    <br/>RSVP: <span class="text-danger">Cancelled</span>
                                    @elseif($data->pivot->rsvp_confirmation==1)
                                    <br/>RSVP: Confirmed
                                    @else
                                    <br/>RSVP: <span class="text-danger">Pending</span>
                                    @endif 
                                </td>
                                
                                <td>
                                    @if($data->pivot->checkin==1 && $data->pivot->community_form_status=='approved')
                                    <a class="btn btn-sm" href="{{ $data->pivot->community_form_pdf }}" download>Download PDF</a>
                                    @elseif(strtotime(date('Y-m-d H:i:s')) < strtotime($data->pivot->event->eventEndDateTime))
                                    <span class="text-danger">Awaiting</span>
                                    @elseif($data->pivot->checkin!=1 && $data->pivot->community_form_status!='requested') 
                                    <span class="text-danger">Pending</span>
                                    @elseif($data->pivot->checkin!=1 && $data->pivot->community_form_status=='requested') 
                                    <span class="text-danger">Requested</span>
                                    @elseif($data->pivot->checkin==1 && $data->pivot->community_form_status!='approved') 
                                    <span class="text-danger">Wrap-up Pending</span>
                                    @endif

                                    <!-- @if($data->pivot->event->allow_community_service_form)
                                        @if($data->pivot->community_form_status)
                                            <span
                                                class="{{ config('app.communityformbadge')[$data->pivot->community_form_status] }}">
                                                {{ ucfirst($data->pivot->community_form_status) }}
                                            </span>
                                        @endif
                                    @else
                                        <span class='text-primary'>No Form</span>
                                    @endif -->
                                </td>
                                <td>
                                    <x-reusables.list-switch :data="$data->pivot" field="checkin"
                                        url="{{ route('mark-checkin', ['ticket' => $data->pivot->encrypted_id]) }}" />
                                </td>
                                <!-- <td>
                                    <div class="d-flex justify-content-start">
                                        <div class="dropdown">
                                            <button class="btn btn-link font-size-16 text-primary py-0 px-0 dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-horizontal-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu drop-menu-end">
                                                @if($data->pivot->event->allow_community_service_form)
                                                    <li class="dropdown-item">
                                                        <a href="javascript:void(0);" data-bs-toggle="modal"
                                                            data-bs-target="#community-form-details">
                                                            <i class="fs-5 mdi mdi-arrow-expand" title="Community Form Details"></i>
                                                            <span class="text-muted">Community Form Details</span>
                                                        </a>
                                                    </li>
                                                    @if ($data->pivot->checkin == 1 && $data->pivot->community_form_status == 'requested' && $event->afterActionDone())
                                                        <li class="dropdown-item">
                                                            <a
                                                                href="{{ route('send-community-form', ['ticket' => $data->pivot->encrypted_id]) }}">
                                                                <i class="fs-5 mdi mdi-share-all-outline"
                                                                    title="Send Community Form"></i>
                                                                <span class="text-muted">Send Community Form</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endif
                                                @if ($data->pivot->event->waiver_form_type  && !$data->pivot->external_link)
                                                    @if (!$data->pivot->waiver_filled_at)
                                                        <li class="dropdown-item">
                                                            <a
                                                                href="{{ route('send-waiver-invitation', ['ticket' => $data->pivot->encrypted_id]) }}">
                                                                <i class="fs-5 mdi mdi-email" title="Send Community Form"></i>
                                                                <span class="text-muted">Re-send Waiver Form</span>
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li class="dropdown-item">
                                                            <a
                                                                href="{{ route('send-waiver-invitation', ['ticket' => $data->pivot->encrypted_id]) }}">
                                                                <i class="fs-5 mdi mdi-download"
                                                                    title="Send Community Form"></i>
                                                                <span class="text-muted">Download Waiver</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </td> -->
                            </tr>
                        @empty
                            <x-reusables.empty-data :colspan="count($tableHead)" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12 mt-3">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">After Event Actions</h4>
            </div>
            <div class="card">
                <div class="card-body">
                    <x-reusables.frontend.alert />
                    <form method="post" action="{{ route('save-after-event-action', ['placeholder' => $event->encrypted_id]) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="send_cleanup_report_email" name="send_cleanup_report_email" value="0">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <!-- <div class="form-group">
                                    <label for="">Waste Collected*</label>
                                    <input type="text" value="{{ $event->waste_collected }}" class="form-control"
                                        name="waste_collected" placeholder="Waste Collected" required />
                                </div> -->
                                <label for="">Waste Collected*</label>
                                <div class="input-group">
                                    <input id="waste_collected" type="number" class="form-control" placeholder="Waste Collected" name="waste_collected" required value="{{ $event->waste_collected }}">
                                    <div class="input-group-text">Pound (lb)</div>
                                </div>
                            </div>

                            <!-- <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <label for="">Team Name</label>
                                    <input type="text" value="{{ $event->team_name }}" class="form-control" required
                                        name="team_name" placeholder="Team Name" />
                                </div>
                            </div> -->
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <label for="">Total Volunteer Hours</label>
                                    <input type="number" step="0.01" min="0" value="{{ $event->volunteer_hours }}" class="form-control" required name="volunteer_hours" placeholder="Total Volunteer Hours" />
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <label for="">Post Event Notes</label>
                                    <input type="text" value="{{ $event->post_event_notes }}" class="form-control " required
                                        name="post_event_notes" placeholder="Post Event Notes" />
                                </div>
                            </div>
                            <div class="colmd-12 mb-2">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            @if(isset($event) && isset($event->id) && isset($event->after_event_images) && !empty($event->after_event_images))
                                                @php 
                                                $images = !empty($event->after_event_images) ? json_decode($event->after_event_images) : null;
                                                @endphp 
                                            @endif
                                            <label class="form-label fs-6">Images* <small class="text-muted"> (max 4 images allowed)</small></label>
                                            <input id="after_event_images" class="form-control h_50" type="file" name="after_event_images[]" accept="image/*" multiple {{ (isset($images) && !empty($images))?'':'required' }}>

                                            @if(isset($event) && isset($event->id) && isset($event->after_event_images) && !empty($event->after_event_images))
                                                @php 
                                                $images = !empty($event->after_event_images) ? json_decode($event->after_event_images) : null;
                                                @endphp 
                                                <div class="d-flex flex-wrap align-items-center mt-2" >
                                                <div class="d-flex flex-wrap align-items-center mt-2" id="savedImages">
                                                    @if(!empty($images))
                                                    @foreach($images as $key => $image)
                                                        @php
                                                            $imagePath = is_object($image) ? $image->path : $image;
                                                        @endphp
                                                        <div class="position-relative me-3 mt-2" id="saved-img-{{ $key }}">
                                                            <img style="width: 50px; height: 50px; border-radius: 6px; object-fit: cover;" src="{{ asset($imagePath) }}">
                                                            <button divid="saved-img-{{ $key }}" url="{{ route('remove-after-event-image', ['index' => $key, 'id' => $event->encrypted_id??0]) }}"
                                                                type="button" 
                                                                class="remove-event-img-btn remove-new-img">✕
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-wrap align-items-center mt-2" id="newImagePreview">
                                                </div>
                                                </div>
                                            @endif 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 text-center mt-2">
                                <a href="{{ url()->current() }}" class="btn btn-secondary ms-2">Reset</a>
                                <button id="submit-cleanup-report-btn" type="submit" class="btn btn-primary ms-2">Save</button>
                                @if($event->is_completed==1)
                                    @if(isset($event->wrapup_email_sender) && isset($event->wrapup_email_sender->id))
                                    <p class="text-sm text-primary mt-2">Wrap-Up Email sent by: {{ $event->wrapup_email_sender->first_name.' '.$event->wrapup_email_sender->last_name }}.</p>
                                    @else 
                                    <p class="text-sm text-primary mt-2">Wrap-Up Email has been sent.</p>
                                    @endif 
                                @else 
                                <!-- <button type="button" {{ !$event->afterActionDone()?'disabled':'' }} ajaxurl="{{route('send-wrapup-email-admin',$event->encrypted_id ?? 0)}}" event_id="{{ $event->encrypted_id ?? 0 }}" class="btn btn-primary ms-2 send-thankyou-request-email-btn">Finalize & Send Wrap-up Email</button> -->
                                <button type="button" url="{{route('check-event-for-wrapup-admin',$event->encrypted_id ?? 0)}}" class="btn btn-primary ms-2 submit-send-wrapup-btn">Save & Finalize Cleanup Report</button>
                                @endif
                                @if (!$event->afterActionDone())
                                <!-- <p class="text-sm text-primary mt-2">Note: Save Waste Collected and Area Cleaned data to send the wrap-up email and certificate email to all volunteers.</p> -->
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Popup --}}
    <div id="community-form-details" class="modal fade modal-lg detailed-modal-popup" tabindex="-1"
        aria-labelledby="community-form-detailsLabel" aria-hidden="true" data-bs-scroll="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="community-form-detailsLabel">Organization Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <label for="hours_completed" class="form-label">Hours Completed</label>
                            <input class="form-control" type="text" name="hours_completed" id="hours_completed">
                        </div>
                        <div class="col-sm-12 mt-4">
                            <label for="participation_proof" class="form-label">Participation proof</label>
                            <input class="form-control" type="text" name="participation_proof"
                                id="participation_proof">
                        </div>
                        <div class="col-sm-12 mt-4">
                            <label for="additional_notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" name="additional_notes" id="additional_notes"></textarea>
                        </div>
                        <div class="col-sm-12 mt-4">
                            <label for="activity_description" class="form-label">Activity Description</label>
                            <textarea class="form-control" name="activity_description" id="activity_description"></textarea>
                        </div>
                        <div class="col-sm-12 mt-4 text-center">
                            <button type="button" class="btn btn-light waves-effect me-3"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
<div class="modal fade" id="resendWaiverEmailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="resendWaiverEmailForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Resend Waiver Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
				<div class="modal-body p-4">

                    {{-- Hidden radio inputs --}}
                    <input type="radio" name="resend_waiver_type" id="resend_waiver_type1" value="1" checked hidden>
                    <input type="radio" name="resend_waiver_type" id="resend_waiver_type2" value="2" hidden>

                    {{-- Option 1: Original Email --}}
                    <div class="resend-opt-card selected" id="optionCard1" onclick="selectResendOption(1)">
                        <div class="d-flex align-items-start gap-3">
                            <div class="resend-radio-dot on" id="radioDot1"></div>
                            <div class="flex-grow-1">
                                <p class="resend-opt-title">Resend to volunteer's registered email</p>
                                <p class="resend-opt-desc">The waiver link will be sent to the email address registered against this ticket.</p>
                                <span class="resend-email-badge">
                                    <i class="fa fa-envelope"></i>
                                    <span id="resend_waiver_default_email">example@gmail.com</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Option 2: Alternative Email --}}
                    <div class="resend-opt-card" id="optionCard2" onclick="selectResendOption(2)">
                        <div class="d-flex align-items-start gap-3">
                            <div class="resend-radio-dot" id="radioDot2"></div>
                            <div class="flex-grow-1">
                                <p class="resend-opt-title">Resend to a different email address</p>
                                <p class="resend-opt-desc">Send the waiver link to a different address. Useful when the volunteer has reported an incorrect or inaccessible email.</p>
                                <input type="email"
                                    name="alternative_waiver_email"
                                    id="alternative_waiver_email"
                                    class="form-control form-control-sm resend-alt-input mt-1"
                                    placeholder="Enter email address"
                                    disabled>
                            </div>
                        </div>
                    </div>

                    {{-- Admin Note --}}
                    <div class="resend-note">
                        <i class="ti ti-info-circle resend-note-icon"></i>
                        <p class="mb-0" id="resend_waiver_note">
                            <span class="resend-note-label">Registered email selected — </span>
                            The waiver link will be resent to the volunteer's email on record. The volunteer's profile will not be modified.
                        </p>
                    </div>
					{{-- Last Resend Waiver Log --}}
					<div class="resend-last-log-box" id="resend_waiver_last_log_box">
						<div class="resend-last-log-header">
							<i class="mdi mdi-history"></i>
							<span>Last Resend Record</span>
						</div>

						<div class="resend-last-log-empty" id="resend_waiver_last_log_empty">
							No resend waiver email record found.
						</div>

						<div class="resend-last-log-content" id="resend_waiver_last_log_content" style="display:none;">
							<div class="resend-last-log-row">
								<span class="resend-last-log-label">Type</span>
								<span class="resend-last-log-value" id="resend_waiver_last_log_type"></span>
							</div>
							<div class="resend-last-log-row">
								<span class="resend-last-log-label">Email</span>
								<span class="resend-last-log-value" id="resend_waiver_last_log_email"></span>
							</div>
							<div class="resend-last-log-row mb-0">
								<span class="resend-last-log-label">Sent At</span>
								<span class="resend-last-log-value" id="resend_waiver_last_log_datetime"></span>
							</div>
						</div>
					</div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary waves-effect waves-light submit-btn-send-waiver-email">Send Email</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="resendWaiverLogsModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
                <h5 class="modal-title">Resend Waiver Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body p-4">
				<div class="resend-waiver-log-empty text-center py-4" style="display:none;">
					<i class="fa-solid fa-clock-rotate-left fa-2x text-muted mb-3"></i>
					<h6 class="mb-1">No logs found</h6>
					<p class="text-muted small mb-0">No waiver resend email has been sent yet.</p>
				</div>

				<div class="table-responsive resend-waiver-log-table-wrap">
					<table class="table table-bordered table-hover align-middle resend-waiver-log-table mb-0">
						<thead>
							<tr>
								<th>Type</th>
								<th>Email</th>
								<th>Send At</th>
							</tr>
						</thead>
						<tbody id="resendWaiverLogsTableBody"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
