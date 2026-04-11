@extends('layouts.frontend')
@push('style')
<style>
    /* .cert-detail-wrapper {
        background: #f8f9fa;
        min-height: 100vh;
        padding-bottom: 40px;
    } */
    .cert-status-banner {
        padding: 18px 24px;
        border-radius: 10px;
        margin-bottom: 24px;
    }
    .cert-status-banner.pending  { background: #fff8e1; border-left: 4px solid #f59e0b; }
    .cert-status-banner.approved { background: #e8f5e9; border-left: 4px solid #22c55e; }
    .cert-status-banner.rejected { background: #fef2f2; border-left: 4px solid #ef4444; }
    .cert-status-banner.requested{ background: #eff6ff; border-left: 4px solid #3b82f6; }

    .cert-section-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 24px;
        margin-bottom: 20px;
    }
    .cert-section-title {
        font-size: 15px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cert-section-title i {
        color: #6b7280;
        font-size: 14px;
    }
    .cert-meta-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #9ca3af;
        margin-bottom: 4px;
    }
    .cert-meta-value {
        font-size: 14px;
        color: #1f2937;
        font-weight: 500;
        margin-bottom: 0;
    }
    .cert-meta-value a {
        color: #3b82f6;
        text-decoration: none;
    }
    .cert-meta-value a:hover { text-decoration: underline; }

    .cert-meta-row {
        margin-bottom: 18px;
    }
    .cert-proof-img {
        width: 100%;
        height: 130px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .cert-proof-img:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    }
    .cert-sidebar-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        margin-bottom: 16px;
    }
    .cert-sidebar-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #9ca3af;
        margin-bottom: 12px;
    }
    .cert-organizer-name {
        font-size: 15px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }
    .cert-organizer-sub {
        font-size: 13px;
        color: #6b7280;
    }
    .cert-help-card {
        background: #fef3c7;
        border-radius: 12px;
        border: 1px solid #fde68a;
        padding: 18px;
    }
    .cert-help-card.rejected-help {
        background: #fef2f2;
        border-color: #fecaca;
    }
    .cert-timeline-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #d1d5db;
        display: inline-block;
        margin-right: 8px;
        flex-shrink: 0;
        margin-top: 4px;
    }
    .cert-timeline-dot.active { background: #22c55e; }
    .cert-timeline-dot.danger { background: #ef4444; }
    .cert-timeline-dot.warning{ background: #f59e0b; }
    .cert-timeline-dot.info   { background: #3b82f6; }

    .cert-reject-reason-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 14px 16px;
        margin-top: 10px;
    }
    .proof-placeholder {
        width: 100%;
        height: 130px;
        background: #f3f4f6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 13px;
        border: 1px dashed #d1d5db;
    }
</style>
@endpush

@section('content')
<div class="wrapper wrapper-body cert-detail-wrapper">
    <div class="dashboard-body">
        <div class="container-fluid">

            {{-- PAGE HEADER --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-main-title">
                        <h3>
                            <i class="fa-solid fa-certificate me-3"></i>
                            Certificate Request Details
                            @if($ticket->community_form_status == 'approved')
                                <span class="badge custom-badge-success ms-2">Approved</span>
                            @elseif($ticket->community_form_status == 'rejected')
                                <span class="badge custom-badge-danger ms-2">Rejected</span>
                            @elseif($ticket->community_form_status == 'requested')
                                <span class="badge custom-badge-info ms-2">Requested</span>
                            @else
                                <span class="badge custom-badge-warning ms-2">Pending</span>
                            @endif
                        </h3>
                        @php
                            $breadcrumbs = [
                                ['name' => 'Certificate Requests', 'url' => route('front-certificate-requests')],
                                ['name' => 'Request Details',      'url' => '#'],
                            ]
                        @endphp
                        <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                    </div>
                </div>
            </div>

            <x-reusables.frontend.alert />

            {{-- STATUS BANNER --}}
            <div class="cert-status-banner {{ $ticket->community_form_status == 'approved' ? 'approved' : ($ticket->community_form_status == 'rejected' ? 'rejected' : ($ticket->community_form_status == 'requested' ? 'requested' : 'pending')) }}">
                @if($ticket->community_form_status == 'approved')
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fa fa-circle-check text-success"></i>
                        <strong class="text-success">Certificate Approved</strong>
                        @if($ticket->is_late_checkin == 1)
                            <span class="badge custom-badge-warning ms-1">Late Request</span>
                        @endif
                    </div>
                    <p class="mb-0 text-muted" style="font-size:13px;">Your certificate has been approved. You can download it from the button below.</p>
                @elseif($ticket->community_form_status == 'rejected')
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fa fa-circle-xmark text-danger"></i>
                        <strong class="text-danger">Certificate Rejected</strong>
                    </div>
                    <p class="mb-0 text-muted" style="font-size:13px;">Your certificate request has been rejected by the organizer. Please review the reason below.</p>
                @elseif($ticket->community_form_status == 'requested')
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fa fa-clock text-primary"></i>
                        <strong class="text-primary">Request Submitted</strong>
                        @if($ticket->is_late_checkin == 1)
                            <span class="badge custom-badge-warning ms-1">Late Request</span>
                        @endif
                    </div>
                    <p class="mb-0 text-muted" style="font-size:13px;">Your request is currently under review by the organizer. You will be notified once a decision has been made.</p>
                @else
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fa fa-hourglass-half text-warning"></i>
                        <strong class="text-warning">Pending Review</strong>
                        @if($ticket->is_late_checkin == 1)
                            <span class="badge custom-badge-warning ms-1">Late Request</span>
                        @endif
                    </div>
                    <p class="mb-0 text-muted" style="font-size:13px;">Your certificate request is currently under review by the organizer. You will be notified once a decision has been made.</p>
                @endif
            </div>

            <div class="row">
                {{-- LEFT COLUMN --}}
                <div class="col-md-8">

                    {{-- STATUS SUMMARY CARD --}}
                    <div class="cert-section-card">
                        <div class="cert-section-title">
                            <i class="fa fa-info-circle"></i> Status Summary
                            @if($ticket->community_form_status == 'approved')
                                <span class="badge custom-badge-success ms-auto">Approved</span>
                            @elseif($ticket->community_form_status == 'rejected')
                                <span class="badge custom-badge-danger ms-auto">Rejected</span>
                            @elseif($ticket->community_form_status == 'requested')
                                <span class="badge custom-badge-info ms-auto">Requested</span>
                            @else
                                <span class="badge custom-badge-warning ms-auto">Pending</span>
                            @endif
                        </div>

                        {{-- STATUS TIMELINE --}}
                        <div class="mb-3">
                            <div class="d-flex align-items-start mb-2">
                                <span class="cert-timeline-dot info"></span>
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:#1f2937;">Request Submitted</div>
                                    <div style="font-size:12px;color:#6b7280;">
                                        {{ $ticket->certificate_requested_at ? date(config('app.dateTimeFormat'), strtotime($ticket->certificate_requested_at)) : '-' }}
                                    </div>
                                </div>
                            </div>

                            @if($ticket->community_form_status == 'approved')
                            <div class="d-flex align-items-start mb-2">
                                <span class="cert-timeline-dot active"></span>
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:#1f2937;">Certificate Approved</div>
                                    <div style="font-size:12px;color:#6b7280;">
                                        {{ $ticket->certificate_approve_reject_at ? date(config('app.dateTimeFormat'), strtotime($ticket->certificate_approve_reject_at)) : '-' }}
                                    </div>
                                </div>
                            </div>
                            @elseif($ticket->community_form_status == 'rejected')
                            <div class="d-flex align-items-start mb-2">
                                <span class="cert-timeline-dot danger"></span>
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:#1f2937;">Request Rejected</div>
                                    <div style="font-size:12px;color:#6b7280;">
                                        {{ $ticket->certificate_approve_reject_at ? date(config('app.dateTimeFormat'), strtotime($ticket->certificate_approve_reject_at)) : '-' }}
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="d-flex align-items-start mb-2">
                                <span class="cert-timeline-dot"></span>
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:#9ca3af;">Awaiting Decision</div>
                                    <div style="font-size:12px;color:#9ca3af;">Organizer review in progress</div>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- STATUS MESSAGE --}}
                        @if($ticket->community_form_status == 'approved')
                            <div class="alert alert-success py-2 mb-0" style="font-size:13px;">
                                <i class="fa fa-check-circle me-2"></i>
                                Your certificate has been approved. Download it using the button on the right.
                            </div>
                        @elseif($ticket->community_form_status == 'rejected')
                            <div class="alert alert-danger py-2 mb-0" style="font-size:13px;">
                                <i class="fa fa-times-circle me-2"></i>
                                Your request has been rejected. Please review the reason below and contact the organizer if needed.
                            </div>
                            @if($ticket->certificate_reject_reason)
                            <div class="cert-reject-reason-box mt-2">
                                <div style="font-size:11px;font-weight:600;text-transform:uppercase;color:#9ca3af;margin-bottom:4px;">Rejection Reason</div>
                                <p class="mb-0" style="font-size:13px;color:#374151;">{{ $ticket->certificate_reject_reason }}</p>
                            </div>
                            @endif
                        @else
                            <div class="alert alert-warning py-2 mb-0" style="font-size:13px;">
                                <i class="fa fa-hourglass-half me-2"></i>
                                Your request is being reviewed. This usually takes 1-3 business days.
                            </div>
                        @endif
                    </div>

                    {{-- VOLUNTEER SUBMISSION DETAILS --}}
                    <div class="cert-section-card">
                        <div class="cert-section-title">
                            <i class="fa fa-file-lines"></i> Volunteer Submission Details
                        </div>

                        @if($ticket->activity_description)
                        <div class="cert-meta-row">
                            <div class="cert-meta-label">Motivation / Activity Description</div>
                            <div class="cert-meta-value">{{ $ticket->activity_description }}</div>
                        </div>
                        @endif

                        @if($ticket->additional_notes)
                        <div class="cert-meta-row">
                            <div class="cert-meta-label">Additional Notes</div>
                            <div class="cert-meta-value">{{ $ticket->additional_notes }}</div>
                        </div>
                        @endif

                        @if($ticket->hours_completed)
                        <div class="cert-meta-row">
                            <div class="cert-meta-label">Hours Completed</div>
                            <div class="cert-meta-value">{{ $ticket->hours_completed }} hours</div>
                        </div>
                        @endif

                        {{-- PROOF OF PARTICIPATION --}}
                        <div class="cert-meta-label mb-2">Proof of Participation</div>
                        @php
                            $proofImages = [];
                            if(!empty($ticket->participation_proof)){
                                $decoded = json_decode($ticket->participation_proof, true);
                                if(is_array($decoded)){
                                    $proofImages = $decoded;
                                }
                            }
                        @endphp

                        @if(count($proofImages) > 0)
                            <div class="row g-2">
                                @foreach($proofImages as $img)
                                @php $imgPath = isset($img['path']) ? $img['path'] : $img; @endphp
                                <div class="col-6 col-md-3">
                                    <a href="{{ asset($imgPath) }}" target="_blank">
                                        <img src="{{ asset($imgPath) }}" class="cert-proof-img" alt="Proof of participation">
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="proof-placeholder">
                                <span><i class="fa fa-image me-2"></i>No proof images submitted</span>
                            </div>
                        @endif
                    </div>

                    {{-- WAIVER INFO --}}
                    @if($ticket->waiver_filled_at)
                    <div class="cert-section-card">
                        <div class="cert-section-title">
                            <i class="fa fa-pen-to-square"></i> Waiver Information
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="cert-meta-row">
                                    <div class="cert-meta-label">Waiver Signed</div>
                                    <div class="cert-meta-value">
                                        <i class="fa fa-check-circle text-success me-1"></i>
                                        {{ date(config('app.dateTimeFormat'), strtotime($ticket->waiver_filled_at)) }}
                                    </div>
                                </div>
                            </div>
                            @if($ticket->waiver_handler_name)
                            <div class="col-md-6">
                                <div class="cert-meta-row">
                                    <div class="cert-meta-label">Signed By</div>
                                    <div class="cert-meta-value">{{ $ticket->waiver_handler_name }}</div>
                                </div>
                            </div>
                            @endif
                            @if($ticket->is_under_18 == 1)
                            <div class="col-md-6">
                                <div class="cert-meta-row">
                                    <div class="cert-meta-label">Guardian Email</div>
                                    <div class="cert-meta-value">{{ $ticket->waiver_handler_email ?? '-' }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                </div>

                {{-- RIGHT SIDEBAR --}}
                <div class="col-md-4">

                    {{-- DOWNLOAD CERTIFICATE --}}
                    @if($ticket->community_form_status == 'approved' && $ticket->community_form_pdf)
                    <div class="cert-sidebar-card" style="border-color:#bbf7d0;background:#f0fdf4;">
                        <div class="cert-sidebar-title" style="color:#15803d;">Certificate Ready</div>
                        <p style="font-size:13px;color:#374151;margin-bottom:14px;">Your community service certificate is ready to download.</p>
                        <a href="{{ route('show-community-service-pdf', encrypt($ticket->id)) }}" target="_blank" class="primary-btn w-100 text-center d-block py-2">
                            <i class="fa fa-download me-2"></i> Download Certificate
                        </a>
                    </div>
                    @endif

                    {{-- ORIGINAL SUBMISSION INFO --}}
                    <div class="cert-sidebar-card">
                        <div class="cert-sidebar-title">Original Submission</div>

                        <div class="cert-meta-row">
                            <div class="cert-meta-label">Event Name</div>
                            <div class="cert-meta-value">
                                <a href="{{ route('view-event-details', ['slug' => $event->slug]) }}" target="_blank">
                                    {{ $event->event_name }}
                                </a>
                            </div>
                        </div>

                        <div class="cert-meta-row">
                            <div class="cert-meta-label">Organizer</div>
                            <div class="cert-meta-value">
                                @if($event->creator && $event->creator->organisation_landing_slug)
                                    <a href="{{ route('organizer-detail', ['slug' => $event->creator->organisation_landing_slug]) }}" target="_blank">
                                        {{ $event->creator->first_name ?? '' }} {{ $event->creator->last_name ?? '' }}
                                    </a>
                                @else
                                    {{ $event->creator->first_name ?? '' }} {{ $event->creator->last_name ?? '' }}
                                @endif
                            </div>
                        </div>

                        <div class="cert-meta-row">
                            <div class="cert-meta-label">Event Date</div>
                            <div class="cert-meta-value">
                                <i class="fa fa-calendar me-1 text-muted"></i>
                                {{ date(config('app.dateFormat'), strtotime($event->start_datetime)) }}
                                @if($event->start_datetime != $event->end_datetime)
                                    – {{ date(config('app.dateFormat'), strtotime($event->end_datetime)) }}
                                @endif
                            </div>
                        </div>

                        <div class="cert-meta-row mb-0">
                            <div class="cert-meta-label">Request Date</div>
                            <div class="cert-meta-value">
                                {{ $ticket->certificate_requested_at ? date(config('app.dateFormat'), strtotime($ticket->certificate_requested_at)) : '-' }}
                            </div>
                        </div>
                    </div>

                    {{-- CONTACT ORGANIZER --}}
                    @if($event->creator && $event->creator->organisation_landing_slug)
                    <div class="cert-sidebar-card">
                        <div class="cert-sidebar-title">Need Help?</div>
                        <a href="{{ route('organizer-detail', ['slug' => $event->creator->organisation_landing_slug]) }}" 
                           target="_blank" class="secondary-btn w-100 text-center d-block py-2">
                            <i class="fa fa-envelope me-2"></i> Contact Organizer
                        </a>
                    </div>
                    @endif

                    {{-- HELP CARD --}}
                    @if($ticket->community_form_status == 'rejected')
                    <div class="cert-help-card rejected-help">
                        <div style="font-size:13px;font-weight:600;color:#991b1b;margin-bottom:6px;">
                            <i class="fa fa-circle-info me-1"></i> Need help?
                        </div>
                        <p style="font-size:12px;color:#7f1d1d;margin-bottom:0;">
                            Check our certificate guidelines to ensure your next submission meets all requirements. You may contact the organizer for more information.
                        </p>
                    </div>
                    @else
                    <div class="cert-help-card">
                        <div style="font-size:13px;font-weight:600;color:#92400e;margin-bottom:6px;">
                            <i class="fa fa-circle-info me-1"></i> Need help?
                        </div>
                        <p style="font-size:12px;color:#78350f;margin-bottom:0;">
                            Check our certificate guidelines to ensure your submission meets all requirements.
                        </p>
                    </div>
                    @endif

                    {{-- BACK BUTTON --}}
                    <div class="mt-3">
                        <a href="{{ route('front-certificate-requests') }}" class="secondary-btn w-100 text-center d-block py-2">
                            <i class="fa fa-arrow-left me-2"></i> Back to Certificates
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<x-reusables.google :locationarray="[]"/>
@endsection