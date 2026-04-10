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
    #swal2-html-container{
        text-align: left;
    }
    #swal2-html-container ul{
        list-style-type: none;
    }
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
                                <td>
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
                                        <a href="javascript:void(0);" url="{{ route('send-waiver-invitation', ['ticket' => $data->pivot->encrypted_id]) }}" message="You want to resend email for waiver" data-bs-toggle="tooltip" data-bs-original-title="Resend Waiver Email" class="show-alert-run-url"><span class="badge bg-info"><i class="mdi mdi-email-send"></i></span></a><br/>
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
                            <div class="col-md-6 mb-2">
                                <!-- <div class="form-group">
                                    <label for="">Area Cleaned*</label>
                                    <input type="text" value="{{ $event->area_cleaned }}" class="form-control"
                                        name="area_cleaned" placeholder="Area Cleaned" required />
                                </div> -->
                                <label for="">Area Cleaned*</label>
                                <div class="input-group">
                                    <input id="area_cleaned" type="number" class="form-control" placeholder="Area Cleaned" name="area_cleaned" required value="{{ $event->area_cleaned }}">
                                    <div class="input-group-text">Yards</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-group">
                                    <label for="">Team Name</label>
                                    <input type="text" value="{{ $event->team_name }}" class="form-control" required
                                        name="team_name" placeholder="Team Name" />
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
@endsection
