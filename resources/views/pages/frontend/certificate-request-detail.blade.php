@extends('layouts.frontend')
@push('style')
<style>
    .bg-danger{
        border-color: rgba(var(--bs-danger-rgb), var(--bs-bg-opacity)) !important;
    }
    /* ── Status Banner ── */
    .cert-status-banner {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 24px;
        border-left: 4px solid transparent;
    }
    .cert-status-banner.pending  { background: #fff8e1; border-left-color: #f59e0b; }
    .cert-status-banner.approved { background: #e8f5e9; border-left-color: #22c55e; }
    .cert-status-banner.rejected { background: #fef2f2; border-left-color: #ef4444; }
    .cert-status-banner.requested{ background: #eff6ff; border-left-color: #3b82f6; }

    /* ── Cards ── */
    .cert-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e8e8e8;
        padding: 22px 24px;
        margin-bottom: 20px;
    }
    .cert-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .cert-card-title i {
        color: #6366f1;
        margin-right: 6px;
    }

    /* ── Status Message Box ── */
    .cert-status-msg-box {
        background: #fffbf0;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 12px 14px;
        margin-bottom: 0;
    }
    .cert-status-msg-box.approved-box {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }
    .cert-status-msg-box.rejected-box {
        background: #fef2f2;
        border-color: #fecaca;
    }
    .cert-status-msg-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9ca3af;
        margin-bottom: 5px;
    }
    .cert-status-msg-text {
        font-size: 13px;
        color: #374151;
        margin-bottom: 0;
        line-height: 1.5;
    }

    /* ── Meta Label/Value ── */
    .cert-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9ca3af;
        margin-bottom: 3px;
    }
    .cert-value {
        font-size: 13px;
        color: #1f2937;
        font-weight: 500;
        margin-bottom: 0;
    }
    .cert-value a {
        color: #4f46e5;
        text-decoration: none;
        font-weight: 600;
    }
    .cert-value a:hover { text-decoration: underline; }

    .cert-meta-item {
        margin-bottom: 14px;
    }
    .cert-meta-item:last-child { margin-bottom: 0; }

    /* ── Section Divider ── */
    .cert-divider {
        border: none;
        border-top: 1px solid #f3f4f6;
        margin: 16px 0!important;
    }

    /* ── Motivation Text ── */
    .cert-motivation-text {
        background: #f9fafb;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 13px;
        color: #374151;
        line-height: 1.6;
        border: 1px solid #f3f4f6;
    }

    /* ── Proof Images ── */
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

    /* ── Sidebar Cards ── */
    .cert-sidebar-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e8e8e8;
        padding: 20px 22px;
        margin-bottom: 16px;
    }
    .cert-sidebar-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9ca3af;
        margin-bottom: 14px;
        display: flex;
        justify-content: space-between;
    }

    /* ── Reject Reason Box ── */
    .cert-reject-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 12px 14px;
        margin-top: 12px;
    }
    .cert-reject-box-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #f87171;
        margin-bottom: 4px;
    }

    /* ── Help Card ── */
    .cert-help-card {
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 16px;
    }
    .cert-help-card.warning-help {
        background: #fffbeb;
        border: 1px solid #fde68a;
    }
    .cert-help-card.danger-help {
        background: #fef2f2;
        border: 1px solid #fecaca;
    }
    .cert-help-title {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .cert-help-text {
        font-size: 12px;
        line-height: 1.5;
        margin-bottom: 0;
        color: #78350f;
    }
    .cert-help-card.danger-help .cert-help-text { color: #7f1d1d; }
    .cert-help-card.danger-help .cert-help-title { color: #991b1b; }
    .cert-help-card.warning-help .cert-help-title { color: #92400e; }

    /* ── Download Card ── */
    .cert-download-card {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        padding: 20px 22px;
        margin-bottom: 16px;
    }
    .cert-download-title {
        font-size: 13px;
        font-weight: 700;
        color: #15803d;
        margin-bottom: 6px;
    }
    .cert-download-text {
        font-size: 12px;
        color: #374151;
        margin-bottom: 14px;
    }

    /* ── Status Badge Inline ── */
    .cert-inline-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .cert-inline-badge.pending  { background: #fef9c3; color: #92400e; }
    .cert-inline-badge.approved { background: #dcfce7; color: #15803d; }
    .cert-inline-badge.rejected { background: #fee2e2; color: #991b1b; }
    .cert-inline-badge.requested{ background: #dbeafe; color: #1d4ed8; }
    .cert-inline-badge .dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        display: inline-block;
    }
    .cert-inline-badge.pending .dot  { background: #f59e0b; }
    .cert-inline-badge.approved .dot { background: #22c55e; }
    .cert-inline-badge.rejected .dot { background: #ef4444; }
    .cert-inline-badge.requested .dot{ background: #3b82f6; }

    /* ── Back Link ── */
    .cert-back-link {
        font-size: 13px;
        color: #6b7280;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 18px;
        transition: color 0.2s;
    }
    .cert-back-link:hover { color: #1f2937; }

    /* ── Sidebar Dates Row ── */
    .cert-dates-row {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }
    .cert-dates-row .cert-meta-item { flex: 1; min-width: 100px; }
    .d-main-title i{
        font-size: 16px;
    }
</style>
@endpush

@section('content')
<div class="wrapper wrapper-body">
    <div class="dashboard-body">
        <div class="container-fluid">

            {{-- PAGE HEADER (unchanged) --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-main-title">
                        <h3>
                            <i class="fa-solid fa-certificate me-3 mb-3"></i>
                            Certificate Request Details
                            
                            @if($authUser->id==$ticket->volunteer_id)
                            <a href="{{ route('front-certificate-my-requests') }}" class="cert-back-link float-end">
                                <i class="fa fa-arrow-left"></i> Back to Certificates
                            </a>
                            @else 
                            <a href="{{ route('front-certificate-volunteer-requests') }}" class="cert-back-link float-end">
                                <i class="fa fa-arrow-left"></i> Back to Certificates
                            </a>
                            @endif
                            <!-- @if($ticket->community_form_status == 'approved')
                                <span class="badge custom-badge-success ms-2">Approved</span>
                            @elseif($ticket->community_form_status == 'rejected')
                                <span class="badge custom-badge-danger ms-2">Rejected</span>
                            @elseif($ticket->community_form_status == 'requested')
                                <span class="badge custom-badge-info ms-2">Requested</span>
                            @else
                                <span class="badge custom-badge-warning ms-2">Pending</span>
                            @endif -->
                        </h3>
                        @php
                            /* $breadcrumbs = [
                                ['name' => 'Certificate Requests', 'url' => route('front-certificate-my-requests')],
                                ['name' => 'Request Details', 'url' => '#'],
                            ]; */
                            $isMyCertificateRequest = $authUser->id == $ticket->volunteer_id;

                            $certificateParentRoute = $isMyCertificateRequest
                                ? route('front-certificate-my-requests')
                                : route('front-certificate-volunteer-requests');

                            $certificateParentLabel = $isMyCertificateRequest
                                ? 'My Requests'
                                : 'Volunteer Requests';

                            $breadcrumbs = [
                                ['name' => $certificateParentLabel, 'url' => $certificateParentRoute],
                                ['name' => 'Request Details', 'url' => '#'],
                            ];
                        @endphp

                        <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                    </div>
                </div>
            </div>

            <x-reusables.frontend.alert />

            @if(!in_array($authUser->id,$cohostCreatorIds))
            <div class="cert-status-banner {{ $ticket->community_form_status == 'approved' ? 'approved' : ($ticket->community_form_status == 'rejected' ? 'rejected' : ($ticket->community_form_status == 'requested' ? 'pending' : 'pending')) }}">
                @if($ticket->community_form_status == 'approved')
                    <p class="mb-0 text-muted" style="font-size:13px;">Review your submission and download your certificate. This certificate recognizes your dedicated service to environmental cleanup efforts.</p>
                @elseif($ticket->community_form_status == 'rejected')
                    <p class="mb-0 text-muted" style="font-size:13px;">Review the feedback from your organizer.</p>
                @else
                    <p class="mb-0 text-muted" style="font-size:13px;">Review your submission details. Your request is currently being processed and verified by the organizer.</p>
                @endif
            </div>
            @endif

            {{-- BACK LINK --}}
            <!-- <a href="{{ route('front-certificate-my-requests') }}" class="cert-back-link">
                <i class="fa fa-arrow-left"></i> Back to Certificates
            </a> -->

            <div class="row">

                {{-- ── LEFT COLUMN ── --}}
                <div class="col-lg-8">

                    {{-- PENDING SUMMARY / STATUS CARD --}}
                    <div class="cert-card">
                        <div class="cert-card-title">
                            <span>
                                @if($ticket->community_form_status == 'approved')
                                    <i class="fa fa-circle-check text-success"></i>
                                @elseif($ticket->community_form_status == 'rejected')
                                    <i class="fa fa-circle-xmark text-danger"></i>
                                @else
                                    <i class="fa fa-clock text-warning"></i>
                                @endif
                                {{ ucfirst($ticket->community_form_status == 'requested' ? 'Pending' : $ticket->community_form_status) }} Summary
                            </span>

                            {{-- Inline Status Badge --}}
                            @php
                                $statusKey = in_array($ticket->community_form_status, ['approved','rejected','requested']) ? $ticket->community_form_status : 'pending';
                                $statusLabel = ucfirst($statusKey == 'requested' ? 'Pending' : $statusKey);
                            @endphp
                            <span class="cert-inline-badge {{ $statusKey }}">
                                <span class="dot"></span>
                                {{ strtoupper($statusLabel) }}
                            </span>
                        </div>

                        {{-- Status Message Box --}}
                        @php
                            $msgBoxClass = $ticket->community_form_status == 'approved' ? 'approved-box' : ($ticket->community_form_status == 'rejected' ? 'rejected-box' : '');
                        @endphp
                        @if(in_array($authUser->id,$cohostCreatorIds))
                        <div class="cert-status-msg-box {{ $msgBoxClass }}">
                            <div class="cert-status-msg-label">Status Message</div>
                            @if($ticket->community_form_status == 'approved')
                                <p class="cert-status-msg-text">
                                    @if($ticket->certificate_approve_reject_by_role==3)
                                    Admin
                                    @elseif($authUser->id == $ticket->certificate_approve_reject_by)
                                    You 
                                    @else
                                    {{ $ticket->certificateApproverRejectBy ? $ticket->certificateApproverRejectBy->first_name.' '.$ticket->certificateApproverRejectBy->last_name. ($ticket->certificate_approve_reject_by_role==2?' (Co-Host)':' (Organizer)') : 'An organizer' }}
                                    @endif
                                    have approved this certificate request. The certificate has been generated and is now available for the volunteer to download.</p>
                            @elseif($ticket->community_form_status == 'rejected')
                                <p class="cert-status-msg-text">
                                    @if($ticket->certificate_approve_reject_by_role==3)
                                    Admin
                                    @elseif($authUser->id == $ticket->certificate_approve_reject_by)
                                    You 
                                    @else
                                    {{ $ticket->certificateApproverRejectBy ? $ticket->certificateApproverRejectBy->first_name.' '.$ticket->certificateApproverRejectBy->last_name. ($ticket->certificate_approve_reject_by_role==2?' (Co-Host)':' (Organizer)') : 'An organizer' }}
                                    @endif
                                    have rejected this certificate request. The volunteer has been notified along with the reason provided.</p>
                            @else
                                <p class="cert-status-msg-text">This request is pending your review. Please verify the details and approve or reject it accordingly.</p>
                            @endif
                        </div>
                        @else 
                        <div class="cert-status-msg-box {{ $msgBoxClass }}">
                            <div class="cert-status-msg-label">Status Message</div>
                            @if($ticket->community_form_status == 'approved')
                                <p class="cert-status-msg-text">Congratulations! Your certificate request has been approved by the organizer. You can now download your certificate for your community service.</p>
                            @elseif($ticket->community_form_status == 'rejected')
                                <p class="cert-status-msg-text">Your certificate request has been rejected. Please review the reason below and contact the organizer if needed.</p>
                            @else
                                <p class="cert-status-msg-text">Your certificate request is currently under review by the organizer. You will be notified once a decision has been made.</p>
                            @endif
                        </div>
                        @endif

                        {{-- Reject Reason --}}
                        @if($ticket->community_form_status == 'rejected' && $ticket->certificate_reject_reason)
                        <div class="cert-reject-box">
                            <div class="cert-reject-box-label">Rejection Reason</div>
                            <p class="cert-status-msg-text">{{ $ticket->certificate_reject_reason }}</p>
                        </div>
                        @endif

                        <!-- @if($ticket->is_late_checkin == 1)
                            <div class="mt-3">
                                <span class="badge custom-badge-warning">Late Request</span>
                            </div>
                        @endif -->
                    </div>

                    {{-- VOLUNTEER SUBMISSION DETAILS --}}
                    <div class="cert-card">
                        <div class="cert-card-title">
                            <span><i class="fa fa-file-lines"></i> Volunteer Submission Details</span>
                        </div>

                        @if(!empty($ticket->certificate_request_text))
                        <div class="cert-meta-item">
                            <!-- <div class="cert-label">Motivation</div> -->
                            <div class="cert-motivation-text">{{ $ticket->certificate_request_text }}</div>
                        </div>
                        @else
                        <div class="cert-proof-placeholder">
                            <div class="col-sm-8 offset-sm-2">
                                <i class="fa fa-file-text"></i>
                                <h5>Standard Participation Request</h5>
                                No additional submission details were required for this event type. Hours have been automatically cross-referenced with the event check-in logs.
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="cert-card">
                        <div class="cert-card-title">
                            <span><i class="fa fa-image"></i> Proof of Participation</span>
                        </div>

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
                                <div class="col-sm-8 offset-sm-2">
                                    <i class="fa fa-image"></i>
                                    <h5>No Proof Documentation Required</h5>
                                    Visual proof is currently optional for established community partners. Administrative logs serve as the primary source of truth.
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- WAIVER INFO (only if signed) --}}
                    @if(!empty($ticket->waiver_filled_at) && in_array($authUser->id,$cohostCreatorIds))
                    <div class="cert-card">
                        <div class="cert-card-title">
                            <span><i class="fa fa-pen-to-square"></i> Waiver Information</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="cert-label">Waiver Signed</div>
                                <div class="cert-value">
                                    <i class="fa fa-check-circle text-success me-1"></i>
                                    {{ date(config('app.dateTimeFormat'), strtotime($ticket->waiver_filled_at)) }}
                                </div>
                            </div>
                            @if($ticket->waiver_handler_name)
                            <div class="col-sm-6">
                                <div class="cert-label">Signed By 
                                    @if($ticket->is_under_18 == 1)
                                    Guardian
                                    @endif
                                </div>
                                @if($ticket->is_under_18 == 1 && $ticket->waiver_handler_email)
                                <div class="cert-value">{{ $ticket->waiver_handler_name }}</div>
                                <div class="cert-value">{{ $ticket->waiver_handler_email }}</div>
                                @else 
                                <div class="cert-value">{{ $ticket->volunteer->first_name.' '.$ticket->volunteer->last_name }}</div>
                                <div class="cert-value">{{ $ticket->volunteer->email_address }}</div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                </div>

                {{-- ── RIGHT SIDEBAR ── --}}
                <div class="col-lg-4">

                    {{-- DOWNLOAD CERTIFICATE (approved only) --}}
                    @if($ticket->community_form_status == 'approved' && $ticket->community_form_pdf)
                    <div class="cert-download-card">
                        <div class="cert-download-title"><i class="fa fa-certificate me-1"></i> Certificate Ready</div>
                        @if(in_array($authUser->id, $cohostCreatorIds))
                            <p class="cert-download-text">Certificate is ready to download.</p>
                        @else 
                            <p class="cert-download-text">Your certificate is ready to download.</p>
                        @endif
                        <a href="{{ route('show-community-service-pdf', encrypt($ticket->id)) }}" target="_blank" class="primary-btn w-100 text-center d-block">
                            <i class="fa fa-download me-2"></i> Download Certificate
                        </a>
                    </div>
                    @endif
                    @if(in_array($authUser->id, $cohostCreatorIds) && $ticket->community_form_status != 'approved' && $ticket->community_form_status != 'rejected')
                    <div class="cert-sidebar-card">
                        <div class="cert-sidebar-label">
                            Take action no the request
                        </div>
                        <div class="cert-card-title m-0">
                            <button url="{{ route('front-approve-certificate', encrypt($ticket->id)) }}" data-name="{{ $ticket->volunteer->first_name ?? '' }} {{ $ticket->volunteer->last_name ?? '' }}" class="btn-approve-certificate sign-up-btn text-white text-center">
                                <i class="fa fa-check me-0 text-white"></i> Approve
                            </button>
                            <button url="{{ route('front-reject-certificate', encrypt($ticket->id)) }}" data-name="{{ $ticket->volunteer->first_name ?? '' }} {{ $ticket->volunteer->last_name ?? '' }}" class="btn-reject-certificate sign-up-btn text-white text-center bg-danger">
                                <i class="fa fa-times me-0 text-white"></i> Reject
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- ORIGINAL SUBMISSION --}}
                    <div class="cert-sidebar-card">
                        <div class="cert-sidebar-label">
                            Original Submission
                            @if($ticket->is_late_checkin == 1)
                            <span class="cert-inline-badge pending">
                                LATE REQUEST
                            </span>
                            @endif
                        </div>

                        <div class="cert-meta-item">
                            <div class="cert-label">Event Name</div>
                            <div class="cert-value">
                                <a href="{{ route('view-event-details', ['slug' => $event->slug]) }}" target="_blank">
                                    {{ $event->event_name }}
                                </a>
                            </div>
                        </div>

                        <div class="cert-meta-item">
                            <div class="cert-label">Organizer</div>
                            <div class="cert-value">
                                @if($event->creator && $event->creator->organisation_landing_slug)
                                    <a href="{{ route('organizer-detail', ['slug' => $event->creator->organisation_landing_slug]) }}" target="_blank">
                                        {{ $event->creator->first_name ?? '' }} {{ $event->creator->last_name ?? '' }}
                                    </a>
                                @else
                                    {{ $event->creator->first_name ?? '' }} {{ $event->creator->last_name ?? '' }}
                                @endif
                            </div>
                        </div>

                        @if(in_array($authUser->id,$cohostCreatorIds))
                        <div class="cert-meta-item">
                            <div class="cert-label">Volunteer Name</div>
                            <div class="cert-value">
                                <a href="javascript:void(0);">
                                    {{ $ticket->volunteer->first_name.' '.$ticket->volunteer->last_name }}
                                </a><br/>
                                <small>{{$ticket->volunteer->email_address}}</small>
                                @if(!empty($ticket->volunteer->phone))
                                <br/><small>{{$ticket->volunteer->phone}}</small>
                                @endif 
                            </div>
                        </div>
                        @endif

                        <hr class="cert-divider">

                        <div class="cert-dates-row">
                            <div class="cert-meta-item">
                                <div class="cert-label">Event Date</div>
                                <div class="cert-value">
                                    {{ date(config('app.dateFormat'), strtotime($event->start_datetime)) }}
                                    @if(date('Y-m-d', strtotime($event->start_datetime)) != date('Y-m-d', strtotime($event->end_datetime)))
                                        <br>{{ date(config('app.dateFormat'), strtotime($event->end_datetime)) }}
                                    @endif
                                </div>
                            </div>
                            <div class="cert-meta-item">
                                <div class="cert-label">Request Date</div>
                                <div class="cert-value">
                                    {{ $ticket->certificate_requested_at ? date(config('app.dateTimeFormat'), strtotime($ticket->certificate_requested_at)) : '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!in_array($authUser->id,$cohostCreatorIds))
                    {{-- CONTACT ORGANIZER --}}
                    @if($event->creator && $event->creator->organisation_landing_slug)
                    <div class="cert-sidebar-card">
                        <a href="{{ route('organizer-detail', ['slug' => $event->creator->organisation_landing_slug]) }}"
                           target="_blank" class="primary-btn w-100 text-center d-block">
                            <i class="fa fa-envelope me-2"></i> Contact Organizer
                        </a>
                    </div>
                    @endif

                    {{-- HELP CARD --}}
                    <div class="cert-help-card {{ $ticket->community_form_status == 'rejected' ? 'danger-help' : 'warning-help' }}">
                        <div class="cert-help-title">
                            <i class="fa fa-circle-info"></i> Need help?
                        </div>
                        <p class="cert-help-text">
                            @if($ticket->community_form_status == 'rejected')
                                Check our certificate guidelines to ensure your next submission meets all requirements. Contact the organizer for more information.
                            @else
                                Check our certificate guidelines to ensure your next submission meets all requirements.
                            @endif
                        </p>
                    </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

<x-reusables.google :locationarray="[]"/>
@endsection