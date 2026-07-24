@extends('layouts.frontend')

@php
    $event = $form_data;
    $authUser = auth()->guard('front')->user();

    $text = function ($value, $fallback = 'Not Provided') {
        if (is_null($value)) {
            return $fallback;
        }

        $cleanValue = is_string($value)
            ? trim(strip_tags($value))
            : $value;

        return $cleanValue === '' ? $fallback : $cleanValue;
    };

    $yesNo = fn ($value) => $value ? 'Yes' : 'No';

    $formatDateTime = function ($value) {
        return empty($value)
            ? 'Not Provided'
            : \Carbon\Carbon::parse($value)
                ->format(config('app.dateTimeFormat'));
    };

    $formatDate = function ($value) {
        return empty($value)
            ? 'Not Provided'
            : \Carbon\Carbon::parse($value)
                ->format(config('app.dateFormat', 'M d, Y'));
    };

    $participationTypes = [
        1 => 'VolunteerCleanup Integrated Ticket System',
        2 => 'No Tickets Required',
        3 => 'Display Event Details Only',
        4 => 'External Registration Website',
    ];

    $waiverTypes = [
        'default' => 'Default Content',
        'text' => 'Custom Content',
        'pdf' => 'PDF Document',
        'external_link' => 'External Link',
    ];

    $now = now();

    $startDate = !empty($event->start_datetime)
        ? \Carbon\Carbon::parse($event->start_datetime)
        : null;

    $endDate = !empty($event->end_datetime)
        ? \Carbon\Carbon::parse($event->end_datetime)
        : null;

    if (!empty($event->cancelled_at)) {
        $runningStatus = 'Cancelled';
        $statusClass = 'danger';
        $statusDetailLabel = 'Cancelled At';
        $statusDetailValue = $formatDateTime($event->cancelled_at);
    } elseif ((int) $event->is_completed === 1) {
        $runningStatus = 'Completed';
        $statusClass = 'success';
        $statusDetailLabel = 'Completed On';
        $statusDetailValue = $formatDateTime($event->end_datetime);
    } elseif ($startDate && $startDate->isFuture()) {
        $runningStatus = 'Upcoming';
        $statusClass = 'primary';
        $statusDetailLabel = 'Upcoming';
        $statusDetailValue = $formatDateTime($event->start_datetime);
    } elseif (
        $startDate &&
        $endDate &&
        $now->between($startDate, $endDate)
    ) {
        $runningStatus = 'In-Progress';
        $statusClass = 'warning';
        $statusDetailLabel = 'In-Progress';
        $statusDetailValue = 'Until '.$formatDateTime(
            $event->end_datetime
        );
    } elseif ($endDate && $endDate->isPast()) {
        $runningStatus = 'Action Required';
        $statusClass = 'danger';
        $statusDetailLabel = 'Action Required';
        $statusDetailValue = 'Ended '.$formatDateTime(
            $event->end_datetime
        );
    } else {
        $runningStatus = 'Not Scheduled';
        $statusClass = 'secondary';
        $statusDetailLabel = 'Event Status';
        $statusDetailValue = 'Schedule Not Provided';
    }

    $publicEventUrl = !empty($event->slug)
        ? route('view-event-details', ['slug' => $event->slug])
        : null;

    $iCalUrl = !empty($event->slug)
        ? route('front-registration-calendar', ['slug' => $event->slug])
        : null;

    $organizerName = trim(
        ($event->creator?->first_name ?? '').' '.
        ($event->creator?->last_name ?? '')
    );

    if ($organizerName === '') {
        $organizerName = $text($event->creator?->name);
    }

    $isCreator = $authUser &&
        (int) $event->creator_id === (int) $authUser->id;

    $isApprovedCohost = $authUser &&
        $event->cohostRecords->contains(function ($cohost) use ($authUser) {
            return (int) $cohost->volunteer_id === (int) $authUser->id
                && $cohost->request_status === 'approved';
        });

    $canEditEvent = (
        $isCreator ||
        $isApprovedCohost
    ) &&
        empty($event->cancelled_at) &&
        (int) $event->is_completed !== 1 &&
        $startDate &&
        $startDate->isFuture();

    $afterEventImages = [];

    if (!empty($event->after_event_images)) {
        $afterEventImages =
            json_decode($event->after_event_images, true) ?: [];
    }

    $waiverPdfPath = null;

    if (
        $event->waiver_form_type === 'pdf' &&
        !empty($event->waiver_form_content)
    ) {
        $waiverData = json_decode($event->waiver_form_content);

        if (is_object($waiverData) && !empty($waiverData->path)) {
            $waiverPdfPath = asset($waiverData->path);
        } elseif (
            filter_var(
                $event->waiver_form_content,
                FILTER_VALIDATE_URL
            )
        ) {
            $waiverPdfPath = $event->waiver_form_content;
        }
    }

    $scheduleItems = [
        [
            'label' => 'Start Date & Time',
            'value' => $formatDateTime($event->start_datetime),
        ],
        [
            'label' => 'End Date & Time',
            'value' => $formatDateTime($event->end_datetime),
        ],
        [
            'label' => 'Page Publish Date',
            'value' => $formatDate($event->site_publish_date),
        ],
        [
            'label' => 'Page Expiry Date',
            'value' => $formatDate($event->site_depublish_date),
        ],
        [
            'label' => 'Timezone',
            'value' => ucfirst($text($event->event_timezone)),
        ],
        [
            'label' => 'Country',
            'value' => $text($event->country?->country_name),
        ],
    ];

    $registrationItems = [
        [
            'label' => 'Participation Method',
            'value' => $participationTypes[$event->participation_type]
                ?? 'Not Provided',
        ],
        [
            'label' => 'Allow Donations',
            'value' => $yesNo($event->allow_donations),
        ],
        [
            'label' => 'Allow Paid Ticket Refund',
            'value' => $yesNo($event->allow_refund),
        ],
        [
            'label' => 'Show Ticket Description',
            'value' => $yesNo($event->show_ticket_description),
        ],
        [
            'label' => 'Show Remaining Tickets',
            'value' => $yesNo($event->show_remaining_tickets),
        ],
        [
            'label' => 'Fees & Processing Paid By',
            'value' => (int) $event->charge_bear_by === 1
                ? 'Volunteer'
                : 'Organizer',
        ],
    ];

    $hasVolunteerQuestions = !empty(
        trim(strip_tags($event->ticket_questions ?? ''))
    );

    $hasReminderNote = !empty(
        trim(strip_tags($event->reminder_custom_note ?? ''))
    );

    $hasWaiverContent = !empty(
        trim((string) $event->waiver_form_content)
    );
@endphp

@push('style')
<style>
    .cleanup-detail-page {
        min-width: 0;
    }

    .cleanup-detail-page .container-fluid,
    .cleanup-detail-page .dashboard-body,
    .cleanup-detail-page .main-card,
    .cleanup-detail-page .collapse,
    .cleanup-detail-page .collapsing {
        max-width: 100%;
        min-width: 0;
    }

    .cleanup-detail-page img {
        max-width: 100%;
    }

    .cleanup-detail-page .summary-toolbar {
        gap: 9px;
    }

    .cleanup-detail-page .summary-action-btn {
        align-items: center;
        background: #fff;
        border: 1px solid var(--linear-gradient-2);
        border-radius: 6px;
        color: var(--linear-gradient-2);
        display: inline-flex;
        font-size: 13px;
        font-weight: 600;
        gap: 7px;
        justify-content: center;
        min-height: 40px;
        padding: 8px 15px;
        transition: all .2s ease;
    }

    .cleanup-detail-page .summary-action-btn:hover {
        background: var(--linear-gradient-2);
        color: #fff !important;
    }

    .cleanup-detail-page .summary-action-btn.disabled {
        cursor: not-allowed;
        opacity: .55;
        pointer-events: none;
    }

    .cleanup-detail-page .detail-section {
        border: 1px solid #e5e9ec;
        overflow: hidden;
    }

    .cleanup-detail-page .detail-section-toggle {
        align-items: center;
        background: #f7f8f9;
        border: 0;
        border-bottom: 1px solid #e6e9ec;
        color: #17191c;
        display: flex;
        justify-content: space-between;
        padding: 16px 19px;
        text-align: left;
        width: 100%;
    }

    .cleanup-detail-page .detail-section-toggle:hover {
        background: #f1f4f6;
    }

    .cleanup-detail-page .detail-section-toggle:focus {
        box-shadow: none;
        outline: none;
    }

    .cleanup-detail-page .detail-section-toggle h4 {
        align-items: center;
        display: flex;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .cleanup-detail-page .section-toggle-icon {
        color: #6c757d;
        font-size: 12px;
        transition: transform .2s ease;
    }

    .cleanup-detail-page
    .detail-section-toggle[aria-expanded="true"]
    .section-toggle-icon {
        transform: rotate(180deg);
    }

    .cleanup-detail-page
    .detail-section-toggle[aria-expanded="false"] {
        border-bottom-color: transparent;
    }

    .cleanup-detail-page .detail-section-body {
        padding: 22px;
    }

    .cleanup-detail-page .summary-banner {
        background: #f3f5f6;
        border-radius: 7px;
        height: 330px;
        overflow: hidden;
        position: relative;
    }

    .cleanup-detail-page .summary-banner img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }

    .cleanup-detail-page .summary-status {
        position: absolute;
        right: 14px;
        top: 14px;
    }

    .cleanup-detail-page .event-title {
        color: #17191c;
        font-size: 20px;
        line-height: 28px;
        font-weight: 700;
        margin-bottom: 0;
    }

    .cleanup-detail-page .event-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .cleanup-detail-page .soft-badge {
        background: rgba(var(--bs-primary-rgb), .08); 
        color: var(--linear-gradient-2);
        display: inline-flex;
        font-size: 13px;
        font-weight: 500;
        padding: 0.4rem 0.75rem;
        border-radius: 1rem;
    }

    /*
     * Compact organizer presentation.
     * No oversized avatar or disconnected caption.
     */
    .cleanup-detail-page .organizer-row {
        align-items: center;
        display: flex;
        gap: 7px; 
    }

    .cleanup-detail-page .organizer-row i {
        color: var(--linear-gradient-2);
        font-size: 13px;
        width: 15px;
    }

    .cleanup-detail-page .organizer-label {
        color: #6c767f;
        font-size: 14px;
        font-weight: 600;
    }

    .cleanup-detail-page .organizer-name {
        color: #24282c;
        font-size: 14px;
        font-weight: 600;
    }

    /*
     * Shared compact label/value spacing.
     */
    .cleanup-detail-page .detail-label {
        color: #69747e;
        display: block;
        font-size: 14px;
        font-weight: 500;
        letter-spacing: .035em;
        line-height: 1.35;
        margin: 0 0 3px;
        text-transform: uppercase;
    }

    .cleanup-detail-page .detail-value {
        color: #272b2f;
        font-size: 14px;
        line-height: 1.55;
        margin-top: 5px; 
    }

    

    .cleanup-detail-page .description-text {
        color: #30353a;
        font-size: 14px;
        line-height: 1.625;
        margin-top: 10px; 
    }

  

    .cleanup-detail-page .simple-grid {
        display: grid;
        gap: 24px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .cleanup-detail-page .simple-field {
        border-bottom: 1px solid #edf0f2;
        min-width: 0;
        padding: 10px 0;
    }

    .cleanup-detail-page .overview-meta .simple-field {
        border-bottom: 0;
        padding-bottom: 2px;
        padding-top: 5px;
    }

    .cleanup-detail-page .subsection-title {
        border-bottom: 1px solid #dfe5e8;
        color: var(--linear-gradient-2);
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .02em;
        margin: 0 0 7px;
        padding-bottom: 10px;
        text-transform: uppercase;
    }
    .cleanup-detail-page .reporting-details-row {
        margin-top: 0;
        padding-top: 20px;
        margin-bottom: 30px;
    }

    .cleanup-detail-page .location-box {
        border: 1px solid #e3e8eb;
        border-radius: 5px;
        padding: 13px 15px;
        margin-top: 20px;
    }
    .cleanup-detail-page .location-box .location-content .detail-value{
        margin-bottom: 0;
    }

    .cleanup-detail-page .location-heading-row {
        align-items: flex-start;
        display: flex;
        gap: 15px;
        justify-content: space-between;
    }

    .cleanup-detail-page .location-content {
        min-width: 0;
    }

    .cleanup-detail-page .map-link {
        align-items: center;
        display: inline-flex;
        flex: 0 0 auto;
        font-size: 12px;
        font-weight: 400;
        gap: 5px;
        line-height: 1.4;
        margin-top: 1px;
        white-space: nowrap;
    }

    .cleanup-detail-page .media-banner {
        background: #f3f5f6;
        border: 1px solid #e5e9ec;
        border-radius: 5px;
        height: 270px;
        overflow: hidden;
    }

    .cleanup-detail-page .media-banner img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }

    .cleanup-detail-page .text-list {
        color: #30353a;
        font-size: 13px;
        line-height: 1.55;
        margin: 0;
    }

    .cleanup-detail-page .settings-list {
        display: grid;
        gap: 0 26px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .cleanup-detail-page .setting-row {
        align-items: center;
        border-bottom: 1px solid #e7ebed;
        display: flex;
        gap: 18px;
        justify-content: space-between;
        min-height: 40px;
        padding: 8px 0;
    }

    .cleanup-detail-page .setting-label {
        color: #505960;
        font-size: 16px;
        line-height: 1.45;
    }

    .cleanup-detail-page .setting-value {
        color: #24282c;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.45;
        max-width: 60%;
        overflow-wrap: anywhere;
        text-align: right;
    }

    .cleanup-detail-page .popover-trigger {
        align-items: center;
        background: none;
        border: 0;
        color: var(--linear-gradient-2);
        display: inline-flex;
        font-size: 11px;
        gap: 4px;
        margin-left: 5px;
        padding: 0;
    }

    .cleanup-detail-page .popover-trigger:hover {
        text-decoration: underline;
    }

    .cleanup-detail-page .status-dot {
        border-radius: 50%;
        display: inline-block;
        height: 7px;
        margin-right: 6px;
        width: 7px;
    }

    .cleanup-detail-page .status-dot.active {
        background: #12a875;
    }

    .cleanup-detail-page .status-dot.inactive {
        background: #dc3545;
    }

    .cleanup-detail-page .ticket-table-wrap {
        border: 1px solid #e1e6e9;
        border-radius: 5px;
        overflow-x: auto;
        margin-top: 20px;
    }

    .cleanup-detail-page .ticket-table {
        color: #30353a;
        font-size: 11px;
        margin: 0;
        min-width: 1000px;
        width: 100%;
    }

    .cleanup-detail-page .ticket-table thead th {
        background: #f1f3f4;
        border-bottom: 1px solid #dfe4e7;
        color: #68737d;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: .025em;
        padding: 10px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .cleanup-detail-page .ticket-table tbody td {
        border-bottom: 1px solid #edf0f2;
        padding: 10px;
        vertical-align: middle;
        font-size: 14px;
    }

    .cleanup-detail-page .ticket-name {
        font-weight: 600;
    }

    .cleanup-detail-page .ticket-subvalue,
    .cleanup-detail-page .ticket-date-secondary {
        color: #7a838b;
        display: block;
        font-size: 9px;
        margin-top: 2px;
    }
    .cleanup-detail-page .ticket-subvalue{
        font-size: 12px;
    }

    .cleanup-detail-page .ticket-date-primary,
    .cleanup-detail-page .ticket-date-secondary {
        white-space: nowrap;
    }

    .cleanup-detail-page .ticket-description-row td {
        background: #fafbfb;
        color: #6f7880;
        font-size: 10px;
        line-height: 1.55;
        padding-bottom: 10px !important;
        padding-top: 7px !important;
    }

    .cleanup-detail-page .ticket-description-label {
        color: #505960;
        font-weight: 700;
        margin-right: 4px;
    }

    .cleanup-detail-page .waiver-row {
        align-items: center;
        border-bottom: 1px solid #e6eaed;
        display: flex;
        gap: 18px;
        justify-content: space-between;
        min-height: 43px;
        padding: 7px 0;
        align-items: start;
    }

    .cleanup-detail-page .waiver-information {
        min-width: 0;
    }

    .cleanup-detail-page .waiver-toggle {
        align-items: center;
        background: none;
        border: 0;
        color: var(--linear-gradient-2);
        display: inline-flex;
        flex: 0 0 auto;
        font-size: 12px;
        font-weight: 500;
        gap: 5px;
        padding: 4px 0;
    }

    .cleanup-detail-page .waiver-toggle i {
        transition: transform .2s ease;
    }

    .cleanup-detail-page
    .waiver-toggle[aria-expanded="true"] i {
        transform: rotate(180deg);
    }

    .cleanup-detail-page .waiver-collapse-content {
        background: #f8fafb;
        border: 1px solid #e5e9ec;
        border-radius: 5px;
        color: #30353a;
        font-size: 16px;
        line-height: 1.2;
        margin-top: 20px;
        overflow-wrap: anywhere;
        padding: 13px 14px; 
    }

    .cleanup-detail-page .summary-stat {
        border: 1px solid #e2e7ea;
        border-radius: 5px;
        height: 100%;
        padding: 15px 9px;
        text-align: center;
    }

    .cleanup-detail-page .summary-stat-label {
        color: #75808a;
        display: block;
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
    }

    .cleanup-detail-page .summary-stat-value {
        color: #17191c;
        display: block;
        font-size: 20px;
        font-weight: 700;
        margin-top: 3px;
    }

    .cleanup-detail-page .report-status-alert {
        background: rgba(220, 53, 69, .10);
        border: 1px solid rgba(220, 53, 69, .18);
        border-radius: 5px;
        color: #b02a37;
        font-size: 15px;
        padding: 11px 13px;
    }

    .cleanup-detail-page .after-event-image {
        border: 1px solid #e5e9ec;
        border-radius: 5px;
        height: 190px;
        object-fit: cover;
        width: 100%;
    }

    .cleanup-detail-page .empty-value {
        color: #929aa2 !important;
        font-style: italic;
        font-weight: 400 !important;
    }


    @media (max-width: 991px) {
        .cleanup-detail-page .summary-banner {
            height: 280px;
        }

        .cleanup-detail-page .simple-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .cleanup-detail-page .settings-list {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .cleanup-detail-page .detail-section-body {
            padding: 16px;
        }

        .cleanup-detail-page .summary-banner {
            height: 220px;
        }

        .cleanup-detail-page .media-banner {
            height: 210px;
        }

        .cleanup-detail-page .simple-grid {
            grid-template-columns: 1fr;
        }

        .cleanup-detail-page .summary-toolbar {
            width: 100%;
        }

        .cleanup-detail-page .summary-action-btn {
            flex: 1 1 auto;
        }

        .cleanup-detail-page .location-heading-row {
            display: block;
        }

        .cleanup-detail-page .map-link {
            margin-top: 7px;
        }

        .cleanup-detail-page .setting-row {
            align-items: flex-start;
        }

        .cleanup-detail-page .waiver-row {
            align-items: flex-start;
            flex-direction: column;
            gap: 5px;
        }
    }
    .cleanup-detail-page .seo-field {
        border-bottom: 1px solid #edf0f2;
        padding: 14px 0;
    }

    .cleanup-detail-page .seo-field:first-child {
        padding-top: 0;
    }

    .cleanup-detail-page .seo-field:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .cleanup-detail-page .seo-field .detail-label {
        margin-bottom: 2px;
    }

    .cleanup-detail-page .seo-field .detail-value {
        line-height: 1.55;
        white-space: normal;
    }
    .cleanup-detail-page .quick-actions-section {
        background: #f8faf9;
        border: 1px solid #dce5e1;
        border-radius: 7px;
        padding: 22px 24px;
    }

    .cleanup-detail-page .quick-actions-title {
        color: #17191c;
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 17px;
    }

    .cleanup-detail-page .quick-actions-list {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
    }

    .cleanup-detail-page .quick-action-form {
        margin: 0;
    }

    .cleanup-detail-page .quick-actions-section {
        background: #f8faf9;
        border: 1px solid #dce5e1;
        border-radius: 7px;
        padding: 22px 24px;
    }

    .cleanup-detail-page .quick-actions-title {
        color: #17191c;
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 17px;
    }

    .cleanup-detail-page .quick-actions-list {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .cleanup-detail-page .quick-action-form {
        display: inline-flex;
        margin: 0;
    }

    @media (max-width: 575px) {
        .cleanup-detail-page .quick-actions-section {
            padding: 18px;
        }

        .cleanup-detail-page .quick-actions-list,
        .cleanup-detail-page .quick-action-form,
        .cleanup-detail-page .quick-actions-list .summary-action-btn {
            width: 100%;
        }
    }
    .summary-action-btn i{
        font-size: 13px;
    }



    /*cleanup event view detail new css */
.cleanup-detail-page .event-meta-wrapper{
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.cleanup-detail-page .divider-hr{
        width: 100%;
        height: 1px;
        background-color: #e5e9ec;
        margin: 16px 0;
}
.cleanup-detail-page .schedule-event-grid{
       margin-top: 20px;
}
.cleanup-detail-page .schedule-event-grid .simple-field{
   border: 1px solid #dadadaba;
    padding: 16px;
    border-radius: 8px;
}
.cleanup-detail-page .schedule-event-grid .simple-field .detail-value {
    margin: 0;
}
.cleanup-detail-page .spot-box-content {
    border: 1px solid #dadada;
    padding: 20px;
    border-radius: 5px;
}
.cleanup-detail-page .cleanup-impact-grid .simple-field, .cleanup-detail-page .volunteer-stats-grid .simple-field{
    border: 1px solid #e5e9ec;
    padding: 16px;
    border-radius: 8px;
}
.cleanup-detail-page .cleanup-impact-grid, .cleanup-detail-page .volunteer-stats-grid{
    margin-top: 20px;
}


</style>
@endpush

@section('content')
<div class="wrapper wrapper-body cleanup-detail-page">
    <div class="dashboard-body">
        <div class="container-fluid">
            <div class="d-main-title">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <h3>
                            <i class="fa-solid fa-clipboard-list me-3 mb-3"></i>
                            Cleanup Details
                        </h3>

                        @php
                            $breadcrumbs = [
                                [
                                    'name' => 'Cleanups I\'m Hosting',
                                    'url' => route(
                                        'front-cleanup-hosting',
                                        ['type' => 'organizer']
                                    ),
                                ],
                                [
                                    'name' => $event->event_name,
                                    'url' => route(
                                        'front-cleanup-detail',
                                        ['id' => $event->encrypted_id]
                                    ),
                                ],
                            ];
                        @endphp

                        <x-reusables.frontend.breadcrumb
                            :breadcrumbs="$breadcrumbs"
                        />
                    </div>

                    <div class="d-flex flex-wrap summary-toolbar">
                        
                        @if(!empty($event->slug))
                            <a
                                href="{{ route(
                                    'front-duplicate-an-event',
                                    ['slug' => $event->slug]
                                ) }}"
                                class="summary-action-btn"
                            >
                                <i class="fa-regular fa-clone"></i>
                                <span>Duplicate Event</span>
                            </a>
                        @endif
                        
                        @if($publicEventUrl)
                            <button
                                type="button"
                                class="summary-action-btn"
                                onclick="copyCleanupEventUrl(@js($publicEventUrl), this)"
                            >
                                <i class="fa-solid fa-share-nodes"></i>
                                <span>Share</span>
                            </button>
                        @else
                            <button
                                type="button"
                                class="summary-action-btn disabled"
                                title="The event does not have a public URL"
                            >
                                <i class="fa-solid fa-share-nodes"></i>
                                <span>Share</span>
                            </button>
                        @endif

                        @if($iCalUrl)
                            <a
                                href="{{ $iCalUrl }}"
                                class="summary-action-btn"
                            >
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>Download iCal</span>
                            </a>
                        @else
                            <span
                                class="summary-action-btn disabled"
                                title="The event does not have a slug"
                            >
                                <i class="fa-solid fa-calendar-days"></i>
                                <span>Download iCal</span>
                            </span>
                        @endif

                        @if($canEditEvent)
                            <a
                                href="{{ route(
                                    'front-post-a-event',
                                    ['id' => $event->encrypted_id]
                                ) }}"
                                class="summary-action-btn"
                            >
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>Edit</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <x-reusables.frontend.alert />

            {{-- Overview --}}
            <div class="main-card detail-section mt-4">
                <button
                    type="button"
                    class="detail-section-toggle"
                    data-bs-toggle="collapse"
                    data-bs-target="#cleanupOverviewSection"
                    aria-expanded="true"
                    aria-controls="cleanupOverviewSection"
                >
                    <h4>
                        <i class="fa-solid fa-circle-info step_icon me-3"></i>
                        Overview
                    </h4>

                    <i class="fa-solid fa-chevron-down section-toggle-icon"></i>
                </button>

                <div id="cleanupOverviewSection" class="collapse show">
                    <div class="detail-section-body">
                        <div class="summary-banner mb-4">
                            <img
                                src="{{ $event->formattedBanner() }}"
                                alt="{{ $event->event_name }}"
                            >

                            <div class="summary-status">
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ $runningStatus }}
                                </span>
                            </div>
                        </div>

                       <div class="event-meta-wrapper">
                         <h2 class="event-title">
                            {{ $event->event_name }}
                        </h2>

                        <div class="event-badges">
                            <span class="soft-badge">
                                {{ $text($event->category?->name) }}
                            </span>

                            <span class="soft-badge">
                                {{ $event->is_private
                                    ? 'Private Event'
                                    : 'Public Event' }}
                            </span>
                        </div>

                        <div class="organizer-row">
                            <i class="fa-solid fa-user"></i>

                            <span class="organizer-label">
                                Organizer:
                            </span>

                            <span class="organizer-name">
                                {{ $organizerName }}
                            </span>
                        </div>

                        <div class="description-block">
                            <span class="detail-label">Description</span>

                            <p class="description-text {{ empty(
                                trim(strip_tags($event->description ?? ''))
                            ) ? 'empty-value' : '' }}">
                                {{ $text($event->description) }}
                            </p>
                        </div>
                       </div>
                        <div class="divider-hr"></div>
                        <div class="overview-meta">
                            <div class="simple-grid">
                                <div class="simple-field">
                                    <span class="detail-label">
                                        Event URL Slug
                                    </span>

                                    <p class="detail-value {{ empty(
                                        $event->slug
                                    ) ? 'empty-value' : '' }}">
                                        {{ $text($event->slug) }}
                                    </p>
                                </div>

                                <div class="simple-field">
                                    <span class="detail-label">
                                        {{ $statusDetailLabel }}
                                    </span>

                                    <p class="detail-value">
                                        {{ $statusDetailValue }}
                                    </p>
                                </div>

                                <div class="simple-field">
                                    <span class="detail-label">
                                        Public Event URL
                                    </span>

                                    @if($publicEventUrl)
                                        <a
                                            href="{{ $publicEventUrl }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="detail-value"
                                        >
                                            {{ $publicEventUrl }}
                                        </a>
                                    @else
                                        <p class="detail-value empty-value">
                                            Not Provided
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Schedule and location --}}
            <div class="main-card detail-section mt-4">
                <button
                    type="button"
                    class="detail-section-toggle"
                    data-bs-toggle="collapse"
                    data-bs-target="#cleanupScheduleSection"
                    aria-expanded="true"
                    aria-controls="cleanupScheduleSection"
                >
                    <h4>
                        <i class="fa-solid fa-calendar-days step_icon me-3"></i>
                        Schedule & Location
                    </h4>

                    <i class="fa-solid fa-chevron-down section-toggle-icon"></i>
                </button>

                <div id="cleanupScheduleSection" class="collapse show">
                    <div class="detail-section-body">
                        <h5 class="subsection-title">Event Schedule</h5>

                        <div class="simple-grid schedule-event-grid">
                            @foreach($scheduleItems as $item)
                                <div class="simple-field">
                                    <span class="detail-label">
                                        {{ $item['label'] }}
                                    </span>

                                    <p class="detail-value {{
                                        $item['value'] === 'Not Provided'
                                            ? 'empty-value'
                                            : ''
                                    }}">
                                        {{ $item['value'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        <h5 class="subsection-title mt-4">
                            Event Location
                        </h5>

                        <div class="location-box">
                            <div class="location-heading-row">
                                <div class="location-content">
                                    <span class="detail-label">
                                        Event Address or Venue
                                    </span>

                                    <p class="detail-value {{ empty(
                                        $event->fullAddress
                                    ) ? 'empty-value' : '' }}">
                                        {{ $text($event->fullAddress) }}
                                    </p>
                                </div>

                                @if(
                                    !empty($event->latitude) &&
                                    !empty($event->longitude)
                                )
                                    <a
                                        href="https://www.google.com/maps?q={{ urlencode(
                                            $event->latitude.','.$event->longitude
                                        ) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="map-link"
                                    >
                                        <i class="fa-solid fa-location-dot"></i>
                                        View on Google Maps
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="row g-4 mt-1">
                            <div class="col-md-6 p-3">
                                <div class="spot-box-content">
                                <div>
                                    <span class="detail-label">
                                    Parking Information
                                </span>

                                <p class="detail-value {{ empty(
                                    $event->parking_info_details
                                ) ? 'empty-value' : '' }}">
                                    {{ $text(
                                        $event->parking_info_details
                                    ) }}
                                </p>
                                </div>
                                </div>
                            </div>

                            <div class="col-md-6 p-3">
                                <div class="spot-box-content">
                                <span class="detail-label">
                                    Meeting Spot Information
                                </span>

                                <p class="detail-value {{ empty(
                                    $event->meeting_spot_details
                                ) ? 'empty-value' : '' }}">
                                    {{ $text(
                                        $event->meeting_spot_details
                                    ) }}
                                </p>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Media and preparation --}}
            <div class="main-card detail-section mt-4">
                <button
                    type="button"
                    class="detail-section-toggle"
                    data-bs-toggle="collapse"
                    data-bs-target="#cleanupMediaSection"
                    aria-expanded="true"
                    aria-controls="cleanupMediaSection"
                >
                    <h4>
                        <i class="fa-solid fa-photo-film step_icon me-3"></i>
                        Media & Event Preparation
                    </h4>

                    <i class="fa-solid fa-chevron-down section-toggle-icon"></i>
                </button>

                <div id="cleanupMediaSection" class="collapse show">
                    <div class="detail-section-body">
                        <h5 class="subsection-title">
                            Event Banner ({{ ucfirst($text(
                                $event->banner_image_orientation,
                                'Landscape'
                            )) }})
                        </h5>

                        <div class="media-banner">
                            <img
                                src="{{ $event->formattedBanner() }}"
                                alt="{{ $event->event_name }} banner"
                            >
                        </div>

                        <h5 class="subsection-title mt-4">
                            Tools & Classification
                        </h5>

                        <div class="row g-4 ">
                            <div class="col-md-6 p-3">
                                <div class="spot-box-content">
                                    <span class="detail-label">
                                    What Should Volunteers Bring?
                                </span>

                                @if($event->bringTools->isNotEmpty())
                                    <p class="text-list">
                                        {{ $event->bringTools
                                            ->pluck('name')
                                            ->implode(', ') }}
                                    </p>
                                @else
                                    <p class="text-list empty-value">
                                        Not Provided
                                    </p>
                                @endif
                                </div>
                            </div>

                            <div class="col-md-6 p-3">
                                <div class="spot-box-content">
                                    
                                <span class="detail-label">
                                    What Will Be Provided?
                                </span>

                                @if($event->providedTools->isNotEmpty())
                                    <p class="text-list">
                                        {{ $event->providedTools
                                            ->pluck('name')
                                            ->implode(', ') }}
                                    </p>
                                @else
                                    <p class="text-list empty-value">
                                        Not Provided
                                    </p>
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="main-card detail-section mt-4">
                <button
                    type="button"
                    class="detail-section-toggle"
                    data-bs-toggle="collapse"
                    data-bs-target="#cleanupSeoSection"
                    aria-expanded="true"
                    aria-controls="cleanupSeoSection"
                >
                    <h4>
                        <i class="fa-solid fa-magnifying-glass step_icon me-3"></i>
                        SEO Details
                    </h4>

                    <i class="fa-solid fa-chevron-down section-toggle-icon"></i>
                </button>

                <div id="cleanupSeoSection" class="collapse show">
                    <div class="detail-section-body">
                        <div class="seo-field">
                            <span class="detail-label">Meta Title</span>

                            <p class="detail-value {{ empty(
                                $event->meta_title
                            ) ? 'empty-value' : '' }}">{{ $text($event->meta_title) }}</p>
                        </div>

                        <div class="seo-field">
                            <span class="detail-label">
                                Focus Keywords
                            </span>

                            <p class="detail-value {{ empty(
                                $event->meta_focus_keyword
                            ) ? 'empty-value' : '' }}">{{ $text($event->meta_focus_keyword) }}</p>
                        </div>

                        <div class="seo-field">
                            <span class="detail-label">
                                Meta Description
                            </span>

                            <p class="detail-value {{ empty(
                                $event->meta_description
                            ) ? 'empty-value' : '' }}">{{ $text($event->meta_description) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Registration --}}
            <div class="main-card detail-section mt-4">
                <button
                    type="button"
                    class="detail-section-toggle"
                    data-bs-toggle="collapse"
                    data-bs-target="#cleanupRegistrationSection"
                    aria-expanded="true"
                    aria-controls="cleanupRegistrationSection"
                >
                    <h4>
                        <i class="fa-solid fa-ticket step_icon me-3"></i>
                        Registration, Tickets & Waiver
                    </h4>

                    <i class="fa-solid fa-chevron-down section-toggle-icon"></i>
                </button>

                <div id="cleanupRegistrationSection" class="collapse show">
                    <div class="detail-section-body">
                        <h5 class="subsection-title">
                            Registration Settings
                        </h5>

                        <div class="settings-list">
                            @foreach($registrationItems as $item)
                                <div class="setting-row">
                                    <span class="setting-label">
                                        {{ $item['label'] }}
                                    </span>

                                    <span class="setting-value {{
                                        $item['value'] === 'Not Provided'
                                            ? 'empty-value'
                                            : ''
                                    }}">
                                        {{ $item['value'] }}
                                    </span>
                                </div>
                            @endforeach

                            <div class="setting-row">
                                <span class="setting-label">
                                    Reminder Email Enabled
                                </span>

                                <span class="setting-value">
                                    {{ $yesNo(
                                        $event->is_send_reminder_email
                                    ) }}

                                    @if($hasReminderNote)
                                        <button
                                            type="button"
                                            class="popover-trigger"
                                            data-bs-toggle="popover"
                                            data-bs-trigger="focus"
                                            data-bs-placement="top"
                                            data-bs-html="false"
                                            data-bs-title="Reminder Custom Note"
                                            data-bs-content="{{ $text(
                                                $event->reminder_custom_note
                                            ) }}"
                                            aria-label="View reminder custom note"
                                        >
                                            <i class="fa-regular fa-circle-question"></i>
                                            View
                                        </button>
                                    @endif
                                </span>
                            </div>

                            <div class="setting-row">
                                <span class="setting-label">
                                    Volunteer Questions
                                </span>

                                <span class="setting-value">
                                    {{ $hasVolunteerQuestions ? 'Yes' : 'No' }}

                                    @if($hasVolunteerQuestions)
                                        <button
                                            type="button"
                                            class="popover-trigger"
                                            data-bs-toggle="popover"
                                            data-bs-trigger="focus"
                                            data-bs-placement="top"
                                            data-bs-html="false"
                                            data-bs-title="Volunteer Questions"
                                            data-bs-content="{{ $text(
                                                $event->ticket_questions
                                            ) }}"
                                            aria-label="View volunteer questions"
                                        >
                                            <i class="fa-regular fa-circle-question"></i>
                                            View
                                        </button>
                                    @endif
                                </span>
                            </div>

                            @if((int) $event->participation_type === 4)
                                <div class="setting-row">
                                    <span class="setting-label">
                                        External Registration URL
                                    </span>

                                    <span class="setting-value">
                                        @if(!empty(
                                            $event->participation_type_external_link
                                        ))
                                            <a
                                                href="{{ $event->participation_type_external_link }}"
                                                target="_blank"
                                                rel="noopener"
                                            >
                                                {{ $event->participation_type_external_link }}
                                            </a>
                                        @else
                                            <span class="empty-value">
                                                Not Provided
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>

                        <h5 class="subsection-title mt-4">
                            Ticket Types
                        </h5>

                        <div class="ticket-table-wrap">
                            <table class="table ticket-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Sold</th>
                                        <th>Cancelled</th>
                                        <th>Remaining</th>
                                        <th>Max. Per Order</th>
                                        <th>Sales Date</th>
                                        <th>Passcode</th>
                                    </tr>
                                </thead>

                                @forelse($event->eventTickets as $ticket)
                                    @php
                                        $soldCount = $ticket->soldTickets
                                            ->where(
                                                'rsvp_confirmation',
                                                1
                                            )
                                            ->count();

                                        $cancelledTicketCount =
                                            $ticket->soldTickets
                                                ->where(
                                                    'rsvp_confirmation',
                                                    0
                                                )
                                                ->count();

                                        $remainingCount = max(
                                            0,
                                            (int) $ticket
                                                ->available_quantity -
                                            $soldCount
                                        );
                                    @endphp

                                    <tbody>
                                        <tr>
                                            <td class="ticket-name">
                                                <span
                                                    class="status-dot {{
                                                        $ticket->is_active
                                                            ? 'active'
                                                            : 'inactive'
                                                    }}"
                                                    title="{{ $ticket->is_active
                                                        ? 'Active'
                                                        : 'Inactive' }}"
                                                ></span>

                                                {{ $ticket->ticket_name }}

                                                <span class="ticket-subvalue">
                                                    {{ ucfirst(
                                                        $ticket->ticket_type
                                                    ) }}
                                                </span>
                                            </td>

                                            <td>
                                                {{ $ticket->ticket_type ===
                                                    'paid'
                                                    ? '$'.number_format(
                                                        (float) $ticket
                                                            ->ticket_price,
                                                        2
                                                    )
                                                    : 'Free' }}
                                            </td>

                                            <td>
                                                {{ $soldCount }} /
                                                {{ $ticket->available_quantity }}
                                            </td>

                                            <td>
                                                {{ $cancelledTicketCount }}
                                            </td>

                                            <td>
                                                {{ $remainingCount }}
                                            </td>

                                            <td>
                                                {{ $text(
                                                    $ticket
                                                        ->max_qty_allowed_per_order
                                                ) }}
                                            </td>

                                            <td>
                                                <span class="ticket-date-primary">
                                                    {{ $formatDate(
                                                        $ticket
                                                            ->sell_start_time
                                                    ) }}
                                                </span>

                                                <span class="ticket-date-secondary">
                                                    to
                                                    {{ $formatDate(
                                                        $ticket
                                                            ->sell_end_time
                                                    ) }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="{{ empty(
                                                    $ticket->pass_code
                                                ) ? 'empty-value' : '' }}">
                                                    {{ $text(
                                                        $ticket->pass_code
                                                    ) }}
                                                </span>
                                            </td>
                                        </tr>

                                        <tr class="ticket-description-row">
                                            <td colspan="8">
                                                <span class="ticket-description-label">
                                                    Ticket Description:
                                                </span>

                                                <span class="{{ empty(
                                                    $ticket->ticket_description
                                                ) ? 'empty-value' : '' }}">
                                                    {{ $text(
                                                        $ticket
                                                            ->ticket_description
                                                    ) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                @empty
                                    <tbody>
                                        <tr>
                                            <td
                                                colspan="8"
                                                class="text-center empty-value"
                                            >
                                                No ticket types have been added.
                                            </td>
                                        </tr>
                                    </tbody>
                                @endforelse
                            </table>
                        </div>

                        <h5 class="subsection-title mt-4">
                            Waiver Information
                        </h5>

                        <div class="waiver-row">
                            <div class="waiver-information">
                                <span class="detail-label">
                                    Waiver Type
                                </span>

                                <p class="detail-value">
                                    {{ $waiverTypes[
                                        $event->waiver_form_type
                                    ] ?? 'No Waiver Form' }}
                                </p>
                            </div>

                            @if($hasWaiverContent)
                                <button
                                    type="button"
                                    class="waiver-toggle"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#cleanupWaiverContent"
                                    aria-expanded="false"
                                    aria-controls="cleanupWaiverContent"
                                >
                                    <span class="waiver-toggle-text">
                                        Show Waiver Content
                                    </span>

                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            @endif
                        </div>

                        @if($hasWaiverContent)
                            <div
                                id="cleanupWaiverContent"
                                class="collapse"
                            >
                                <div class="waiver-collapse-content">
                                    @if($waiverPdfPath)
                                        <a
                                            href="{{ $waiverPdfPath }}"
                                            target="_blank"
                                            rel="noopener"
                                            class="summary-action-btn"
                                        >
                                            <i class="fa-solid fa-file-pdf"></i>
                                            View Waiver PDF
                                        </a>
                                    @elseif(
                                        $event->waiver_form_type ===
                                            'external_link'
                                    )
                                        <a
                                            href="{{ $event->waiver_form_content }}"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            {{ $event->waiver_form_content }}
                                        </a>
                                    @else
                                        {{ $text(
                                            $event->waiver_form_content
                                        ) }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Co-hosts --}}
            <div class="main-card detail-section mt-4">
                <button
                    type="button"
                    class="detail-section-toggle"
                    data-bs-toggle="collapse"
                    data-bs-target="#cleanupCohostSection"
                    aria-expanded="true"
                    aria-controls="cleanupCohostSection"
                >
                    <h4>
                        <i class="fa-solid fa-people-group step_icon me-3"></i>
                        Co-Hosts Management
                    </h4>

                    <i class="fa-solid fa-chevron-down section-toggle-icon"></i>
                </button>

                <div id="cleanupCohostSection" class="collapse show">
                    <div class="detail-section-body">
                        <h5 class="subsection-title">
                            Co-Host
                        </h5>

                        <div class="ticket-table-wrap">
                            <table class="table ticket-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Extra Notes</th>
                                    </tr>
                                </thead>

                                @forelse($event->cohostRecords as $cohost)
                                    @php
                                        $soldCount = $ticket->soldTickets
                                            ->where(
                                                'rsvp_confirmation',
                                                1
                                            )
                                            ->count();

                                        $cancelledTicketCount =
                                            $ticket->soldTickets
                                                ->where(
                                                    'rsvp_confirmation',
                                                    0
                                                )
                                                ->count();

                                        $remainingCount = max(
                                            0,
                                            (int) $ticket
                                                ->available_quantity -
                                            $soldCount
                                        );
                                    @endphp

                                    <tbody>
                                        <tr>
                                            <td>
                                                {{ $text($cohost->volunteer?->name) }}
                                            </td>

                                            <td>
                                                {{ $text($cohost->volunteer?->email_address) }}
                                            </td>

                                            <td>
                                                {{ ucfirst($text($cohost->request_status)) }}
                                            </td>

                                            <td>
                                                <p class="content-box-value {{ empty($cohost->extra_notes) ? 'empty-value' : '' }}">
                                                    {{ $text($cohost->extra_notes) }}
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                @empty
                                    <tbody>
                                        <tr>
                                            <td
                                                colspan="4"
                                                class="text-center empty-value"
                                            >
                                                No co-host have been added.
                                            </td>
                                        </tr>
                                    </tbody>
                                @endforelse
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Registration statistics --}}
            <div class="main-card detail-section mt-4">
                <button
                    type="button"
                    class="detail-section-toggle"
                    data-bs-toggle="collapse"
                    data-bs-target="#cleanupStatisticsSection"
                    aria-expanded="true"
                    aria-controls="cleanupStatisticsSection"
                >
                    <h4>
                        <i class="fa-solid fa-chart-column step_icon me-3"></i>
                        Registration Statistics
                    </h4>

                    <i class="fa-solid fa-chevron-down section-toggle-icon"></i>
                </button>

                <div id="cleanupStatisticsSection" class="collapse show">
                    <div class="detail-section-body">
                        <div class="row g-3">
                            <div class="col-6 col-md-4 col-xl">
                                <div class="summary-stat">
                                    <span class="summary-stat-label">
                                        Registered
                                    </span>
                                    <span class="summary-stat-value">
                                        {{ $statistics['registered'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-4 col-xl">
                                <div class="summary-stat">
                                    <span class="summary-stat-label">
                                        Checked In
                                    </span>
                                    <span class="summary-stat-value">
                                        {{ $statistics['checked_in'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-4 col-xl">
                                <div class="summary-stat">
                                    <span class="summary-stat-label">
                                        Waivers Signed
                                    </span>
                                    <span class="summary-stat-value">
                                        {{ $statistics['waivers'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-4 col-xl">
                                <div class="summary-stat">
                                    <span class="summary-stat-label">
                                        Cancelled
                                    </span>
                                    <span class="summary-stat-value text-danger">
                                        {{ $statistics['cancelled'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-4 col-xl">
                                <div class="summary-stat">
                                    <span class="summary-stat-label">
                                        Donations
                                    </span>
                                    <span class="summary-stat-value">
                                        ${{ number_format(
                                            $statistics['donations'],
                                            2
                                        ) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Wrap-up and reports --}}
            <div class="main-card detail-section mt-4 mb-4">
                <button
                    type="button"
                    class="detail-section-toggle"
                    data-bs-toggle="collapse"
                    data-bs-target="#cleanupReportingSection"
                    aria-expanded="true"
                    aria-controls="cleanupReportingSection"
                >
                    <h4>
                        <i class="fa-solid fa-clipboard-check step_icon me-3"></i>
                        Wrap-Up & Reports
                    </h4>

                    <i class="fa-solid fa-chevron-down section-toggle-icon"></i>
                </button>

                <div id="cleanupReportingSection" class="collapse show">
                    <div class="detail-section-body">
                        @if((int) $event->is_completed !== 1)
                            <div class="report-status-alert mb-4">
                                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                Cleanup Report Status: Pending. Please complete
                                this after the event concludes.
                            </div>
                        @endif

                        <div class="row g-5">
                            <div class="col-lg-6">
                                <h5 class="subsection-title">
                                    Volunteer Stats
                                </h5>

                                <div class="simple-grid volunteer-stats-grid">
                                    <div class="simple-field">
                                        <span class="detail-label">
                                            Registered
                                        </span>

                                        <p class="detail-value">
                                            {{ $statistics['registered'] }}
                                        </p>
                                    </div>

                                    <div class="simple-field">
                                        <span class="detail-label">
                                            Other Sites
                                        </span>

                                        <p class="detail-value">
                                            {{ $statistics['other_sites'] }}
                                        </p>
                                    </div>

                                    <div class="simple-field">
                                        <span class="detail-label">
                                            Total
                                        </span>

                                        <p class="detail-value">
                                            {{ $statistics[
                                                'total_volunteers'
                                            ] }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h5 class="subsection-title">
                                    Cleanup Impact
                                </h5>

                                <div class="simple-grid cleanup-impact-grid">
                                    <div class="simple-field">
                                        <span class="detail-label">
                                            Waste Collected
                                        </span>

                                        <p class="detail-value {{
                                            is_null($event->waste_collected)
                                                ? 'empty-value'
                                                : ''
                                        }}">
                                            {{ is_null(
                                                $event->waste_collected
                                            )
                                                ? 'Not Provided'
                                                : $event->waste_collected
                                                    .' Pound (lb)' }}
                                        </p>
                                    </div>

                                    <div class="simple-field">
                                        <span class="detail-label">
                                            Area Cleaned
                                        </span>

                                        <p class="detail-value {{
                                            is_null($event->area_cleaned)
                                                ? 'empty-value'
                                                : ''
                                        }}">
                                            {{ is_null($event->area_cleaned)
                                                ? 'Not Provided'
                                                : $event->area_cleaned
                                                    .' Yards' }}
                                        </p>
                                    </div>

                                    <div class="simple-field">
                                        <span class="detail-label">
                                            Total Hours
                                        </span>

                                        <p class="detail-value {{
                                            is_null($event->volunteer_hours)
                                                ? 'empty-value'
                                                : ''
                                        }}">
                                            {{ $text(
                                                $event->volunteer_hours
                                            ) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="subsection-title mt-4">
                            Reporting Details
                        </h5>

                        <div class="row gx-4 reporting-details-row">
                            <div class="col-md-6">
                                <span class="detail-label">
                                    Cleanup Report Status
                                </span>

                                <p class="detail-value">
                                    {{ (int) $event->is_completed === 1
                                        ? 'Completed'
                                        : 'Pending' }}
                                </p>
                            </div>

                            <div class="col-md-6">
                                <span class="detail-label">
                                    Wrap-Up Completed By
                                </span>

                                <p class="detail-value {{ empty(
                                    $event->wrapup_email_sender
                                ) ? 'empty-value' : '' }}">
                                    {{ $text(
                                        $event->wrapup_email_sender?->name
                                    ) }}
                                </p>
                            </div>

                            <div class="col-12 mt-3">
                                <span class="detail-label">
                                    Post Event Comments
                                </span>

                                <p class="detail-value {{ empty(
                                    $event->post_event_notes
                                ) ? 'empty-value' : '' }}">
                                    {{ $text($event->post_event_notes) }}
                                </p>
                            </div>
                        </div>

                        <h5 class="subsection-title mt-4">
                            Post Event Images
                        </h5>

                        <div class="row g-3">
                            @forelse($afterEventImages as $image)
                                @php
                                    $imagePath = is_array($image)
                                        ? ($image['path'] ?? null)
                                        : $image;
                                @endphp

                                @if($imagePath)
                                    <div class="col-sm-6 col-lg-3">
                                        <a
                                            href="{{ asset($imagePath) }}"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            <img
                                                src="{{ asset($imagePath) }}"
                                                class="after-event-image"
                                                alt="Post event image"
                                            >
                                        </a>
                                    </div>
                                @endif
                            @empty
                                <div class="col-12">
                                    <p class="detail-value empty-value">
                                        Not Provided
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="quick-actions-section mt-4 mb-4">
                <h4 class="quick-actions-title">
                    Quick Actions
                </h4>
                <div class="d-flex flex-wrap summary-toolbar">
                    
                    @if(!empty($event->slug))
                        <a
                            href="{{ route(
                                'front-duplicate-an-event',
                                ['slug' => $event->slug]
                            ) }}"
                            class="summary-action-btn"
                        >
                            <i class="fa-regular fa-clone"></i>
                            <span>Duplicate Event</span>
                        </a>
                    @endif
                    
                    @if($iCalUrl)
                        <a
                            href="{{ $iCalUrl }}"
                            class="summary-action-btn"
                        >
                            <i class="fa-solid fa-calendar-plus"></i>
                            <span>Download iCal</span>
                        </a>
                    @endif
                </div>

            </div>


        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var popoverElements = document.querySelectorAll(
            '[data-bs-toggle="popover"]'
        );

        if (
            typeof bootstrap !== 'undefined' &&
            bootstrap.Popover
        ) {
            Array.prototype.forEach.call(
                popoverElements,
                function (element) {
                    new bootstrap.Popover(element, {
                        container: 'body'
                    });
                }
            );
        }

        var waiverContent = document.getElementById(
            'cleanupWaiverContent'
        );

        if (waiverContent) {
            waiverContent.addEventListener(
                'show.bs.collapse',
                function () {
                    updateWaiverToggleText(
                        'Hide Waiver Content'
                    );
                }
            );

            waiverContent.addEventListener(
                'hide.bs.collapse',
                function () {
                    updateWaiverToggleText(
                        'Show Waiver Content'
                    );
                }
            );
        }
    });

    function updateWaiverToggleText(text) {
        var textElement = document.querySelector(
            '.waiver-toggle-text'
        );

        if (textElement) {
            textElement.textContent = text;
        }
    }

    function copyCleanupEventUrl(url, button) {
        if (!url) {
            return;
        }

        function showCopiedState() {
            var textElement = button.querySelector('span');
            var originalText = textElement.textContent;

            textElement.textContent = 'Link Copied';

            window.setTimeout(function () {
                textElement.textContent = originalText;
            }, 2000);
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url)
                .then(showCopiedState)
                .catch(function () {
                    fallbackCopyCleanupEventUrl(url);
                    showCopiedState();
                });

            return;
        }

        fallbackCopyCleanupEventUrl(url);
        showCopiedState();
    }

    function fallbackCopyCleanupEventUrl(url) {
        var input = document.createElement('textarea');

        input.value = url;
        input.style.position = 'fixed';
        input.style.opacity = '0';

        document.body.appendChild(input);

        input.focus();
        input.select();
        document.execCommand('copy');

        document.body.removeChild(input);
    }
</script>
@endpush