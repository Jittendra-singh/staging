@php
    $pageName = 'Certificate Request Details';
    $parentRoute = route('certificate-requests');
    $parentPageName = $module;
@endphp
@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $parentPageName, 'parentRoute' => $parentRoute,'breadcrumb' => [
    ['name' => 'Certificate Requests', 'route' => route('certificate-requests')],
    ['name' => 'Certificate Requests Details', 'route' => ''],
]])
@section('css')
    <x-reusables.datatablecss />
    <style>
    .cert-proof-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .cert-proof-img-wrap {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        aspect-ratio: 4/3;
    }
    .cert-proof-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.2s;
        cursor: pointer;
    }
    .cert-proof-img:hover { transform: scale(1.03); }
    .cert-proof-placeholder {
        background: #f9fafb;
        border: 1.5px dashed #d1d5db;
        border-radius: 8px;
        padding: 28px;
        text-align: center;
        color: #9ca3af;
        font-size: 13px;
    }
    .cert-proof-placeholder i { font-size: 24px; margin-bottom: 8px; display: block; }
    </style>
@endsection
@section('js')
    <x-reusables.datatablejs />
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-8">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>Request Status</strong>
                            @if($ticket->community_form_status=='pending' || $ticket->community_form_status=='requested')
                            <span class="text-muted"><span class="badge bg-warning">PENDING</span></span>
                            @elseif($ticket->community_form_status=='approved')
                            <span class="text-muted"><span class="badge bg-success">APPROVED</span></span>
                            @elseif($ticket->community_form_status=='rejected')
                            <span class="text-muted"><span class="badge bg-danger">REJECTED</span></span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($ticket->community_form_status == 'approved')
                                    <p class="m-0">
                                    @if($ticket->certificate_approve_reject_by_role==3)
                                    Admin
                                    @else
                                    {{ $ticket->certificateApproverRejectBy ? $ticket->certificateApproverRejectBy->first_name.' '.$ticket->certificateApproverRejectBy->last_name. ($ticket->certificate_approve_reject_by_role==2?' (Co-Host)':' (Organizer)') : 'An organizer' }}
                                    @endif
                                    have approved this certificate request. The certificate has been generated and is now available for the volunteer to download.</p>
                                @elseif($ticket->community_form_status == 'rejected')
                                    <p class="m-0">
                                    @if($ticket->certificate_approve_reject_by_role==3)
                                    Admin
                                    @else
                                    {{ $ticket->certificateApproverRejectBy ? $ticket->certificateApproverRejectBy->first_name.' '.$ticket->certificateApproverRejectBy->last_name. ($ticket->certificate_approve_reject_by_role==2?' (Co-Host)':' (Organizer)') : 'An organizer' }}
                                    @endif
                                    have rejected this certificate request. The volunteer has been notified along with the reason provided.</p>
                                    <div class="col-sm-12 mt-3">
                                        <div class="card bg-danger border-danger text-white-50 m-0">
                                            <div class="card-body">
                                                <h5 class="mb-4 text-white">Rejection Reason</h5>
                                                <p class="card-text text-white">{{ $ticket->certificate_reject_reason }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @else 
                                    <p class="m-0">This certificate request is pending your review. Please verify the submission details and take appropriate action to approve or reject the request.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>Volunteer Submission Details</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if(!empty($ticket->certificate_request_text))
                                <div class="col-sm-12">
                                    <label>Motivation</label>
                                    <p class="m-0 border bg-light px-3 py-3">{{ $ticket->certificate_request_text }}</p>
                                </div>
                                @else
                                <p>No submission details provided.</p>
                                @endif
                                <div class="col-sm-12 mt-3">
                                    <label>Proof of Participation</label>
                                    @php
                                        $proofImages = [];
                                        if(!empty($ticket->certificate_request_images)){
                                            $decoded = json_decode($ticket->certificate_request_images, true);
                                            if(is_array($decoded)) $proofImages = $decoded;
                                        }
                                    @endphp

                                    @if(count($proofImages) > 0)
                                        <div class="cert-proof-grid">
                                            @foreach($proofImages as $img)
                                            @php $imgPath = isset($img['path']) ? $img['path'] : $img; @endphp
                                            <div class="cert-proof-img-wrap">
                                                <a href="{{ asset($imgPath) }}" target="_blank">
                                                    <img src="{{ asset($imgPath) }}" class="cert-proof-img" alt="Proof of participation">
                                                </a>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="cert-proof-placeholder">
                                            <i class="fa fa-image"></i>
                                            No proof images submitted
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="row">
                @if($ticket->community_form_status == 'approved' && $ticket->community_form_pdf)
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Certificate Ready</h4>
                            <p class="card-text">Your certificate is ready to download.</p>
                            <a href="{{ route('show-community-service-pdf', encrypt($ticket->id)) }}" target="_blank" class="btn btn-primary"><i class="bx bx-download me-1"></i>  Download Certificate</a>
                        </div>
                    </div>
                </div>
                @endif
                @if($ticket->community_form_status != 'approved' && $ticket->community_form_status != 'rejected')
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Take action no the request</h4>
                            <p class="card-text">Approve or Reject the request for certificate.</p>
                            <button url="{{ route('approve-certificate', encrypt($ticket->id)) }}" data-name="{{ $ticket->volunteer->first_name ?? '' }} {{ $ticket->volunteer->last_name ?? '' }}" class="btn btn-primary btn-approve-certificate"><i class="mdi mdi-book-check me-1"></i>  Approve</button>
                            <button url="{{ route('reject-certificate', encrypt($ticket->id)) }}" data-name="{{ $ticket->volunteer->first_name ?? '' }} {{ $ticket->volunteer->last_name ?? '' }}" class="btn btn-secondary float-end btn-reject-certificate"><i class="mdi mdi-book-cancel me-1"></i>  Reject</button>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Original Submission <span class="badge bg-warning float-end">LATE REQUEST</span></h4>
                            <b class="card-text">Event Name</b>
                            <p class="card-text">{{ $event->event_name }}</p>

                            <b class="card-text">Organizer</b>
                            <p class="card-text">{{ $event->creator->first_name ?? '' }} {{ $event->creator->last_name ?? '' }}</p>

                            <b class="card-text">Volunteer Name</b>
                            <p class="card-text">{{ $ticket->volunteer->first_name.' '.$ticket->volunteer->last_name }}<br/>
                            <small>{{$ticket->volunteer->email_address}}</small>
                            @if(!empty($ticket->volunteer->phone))
                            <br/><small>{{$ticket->volunteer->phone}}</small>
                            @endif</p>
                            
                            <div class="row">
                            <div class="col-sm-6">
                                <b class="card-text">Event Date</b>
                                <p class="card-text">{{ date(config('app.dateFormat'), strtotime($event->start_datetime)) }}
                                @if(date('Y-m-d', strtotime($event->start_datetime)) != date('Y-m-d', strtotime($event->end_datetime)))
                                    <br>{{ date(config('app.dateFormat'), strtotime($event->end_datetime)) }}
                                @endif </p>
                            </div>
                            <div class="col-sm-6">
                                <b class="card-text">Request Date</b>
                                <p class="card-text">{{ $ticket->certificate_requested_at ? date(config('app.dateTimeFormat'), strtotime($ticket->certificate_requested_at)) : '-' }}</p>
                            </div>
                            </div>

                        </div>
                    </div>
                </div>
                 @if(!empty($ticket->waiver_filled_at))
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Waiver Information</h4>
                            <div class="row">
                            <div class="col-sm-6">
                                <b class="card-text">Waiver Signed</b>
                                <p class="card-text">{{ date(config('app.dateTimeFormat'), strtotime($ticket->waiver_filled_at)) }}</p>
                            </div>
                            <div class="col-sm-6">
                                <b class="card-text">Signed By @if($ticket->is_under_18 == 1)
                                    Guardian
                                    @endif</b>
                                @if($ticket->is_under_18 == 1 && $ticket->waiver_handler_email)
                                <p class="card-text">{{ $ticket->waiver_handler_name }}<br/>{{ $ticket->waiver_handler_email }}</p>
                                @else 
                                <p class="card-text">{{ $ticket->volunteer->first_name.' '.$ticket->volunteer->last_name }}<br/>{{ $ticket->volunteer->email_address }}</p>
                                @endif
                            </div>
                            </div>

                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
