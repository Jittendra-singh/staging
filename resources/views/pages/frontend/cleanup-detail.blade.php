@extends('layouts.frontend')

@php
    $event = $form_data;

    $text = function ($value, $fallback = 'Not Provided') {
        if (is_null($value)) {
            return $fallback;
        }

        $cleanValue = is_string($value) ? trim(strip_tags($value)) : $value;

        return $cleanValue === '' ? $fallback : $cleanValue;
    };

    $yesNo = fn ($value) => $value ? 'Yes' : 'No';

    $formatDateTime = function ($value) {
        return empty($value)
            ? 'Not Provided'
            : \Carbon\Carbon::parse($value)->format(config('app.dateTimeFormat'));
    };

    $formatDate = function ($value) {
        return empty($value)
            ? 'Not Provided'
            : \Carbon\Carbon::parse($value)->format(config('app.dateFormat', 'M d, Y'));
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

    if (!empty($event->cancelled_at)) {
        $runningStatus = 'Cancelled';
        $statusClass = 'danger';
    } elseif ((int) $event->is_completed === 1) {
        $runningStatus = 'Completed';
        $statusClass = 'success';
    } elseif (
        !empty($event->start_datetime) &&
        \Carbon\Carbon::parse($event->start_datetime)->isFuture()
    ) {
        $runningStatus = 'Upcoming';
        $statusClass = 'primary';
    } elseif (
        !empty($event->start_datetime) &&
        !empty($event->end_datetime) &&
        now()->between(
            \Carbon\Carbon::parse($event->start_datetime),
            \Carbon\Carbon::parse($event->end_datetime)
        )
    ) {
        $runningStatus = 'Ongoing';
        $statusClass = 'warning';
    } else {
        $runningStatus = 'Ended';
        $statusClass = 'secondary';
    }

    $publicEventUrl = !empty($event->slug)
        ? route('view-event-details', ['slug' => $event->slug])
        : null;

    $iCalUrl = !empty($event->slug)
        ? route('front-registration-calendar', ['slug' => $event->slug])
        : null;

    $afterEventImages = [];

    if (!empty($event->after_event_images)) {
        $afterEventImages = json_decode($event->after_event_images, true) ?: [];
    }

    $waiverPdfPath = null;

    if ($event->waiver_form_type === 'pdf' && !empty($event->waiver_form_content)) {
        $waiverData = json_decode($event->waiver_form_content);

        if (is_object($waiverData) && !empty($waiverData->path)) {
            $waiverPdfPath = asset($waiverData->path);
        } elseif (filter_var($event->waiver_form_content, FILTER_VALIDATE_URL)) {
            $waiverPdfPath = $event->waiver_form_content;
        }
    }

    $cohostPermissionLabels = config('app.cohost_permissions', []);

    $scheduleItems = [
        [
            'label' => 'Start Date & Time',
            'value' => $formatDateTime($event->start_datetime),
            'icon' => 'fa-calendar-day',
        ],
        [
            'label' => 'End Date & Time',
            'value' => $formatDateTime($event->end_datetime),
            'icon' => 'fa-calendar-check',
        ],
        [
            'label' => 'Page Publish Date',
            'value' => $formatDate($event->site_publish_date),
            'icon' => 'fa-eye',
        ],
        [
            'label' => 'Page Expiry Date',
            'value' => $formatDate($event->site_depublish_date),
            'icon' => 'fa-eye-slash',
        ],
        [
            'label' => 'Timezone',
            'value' => ucfirst($text($event->event_timezone)),
            'icon' => 'fa-clock',
        ],
        [
            'label' => 'Country',
            'value' => $text($event->country?->country_name),
            'icon' => 'fa-earth-americas',
        ],
        [
            'label' => 'ZIP/Postcode',
            'value' => $text($event->pincode),
            'icon' => 'fa-location-crosshairs',
        ],
        [
            'label' => 'Latitude',
            'value' => $text($event->latitude),
            'icon' => 'fa-map-pin',
        ],
        [
            'label' => 'Longitude',
            'value' => $text($event->longitude),
            'icon' => 'fa-map-pin',
        ],
    ];

    $registrationItems = [
        [
            'label' => 'Participation Method',
            'value' => $participationTypes[$event->participation_type] ?? 'Not Provided',
        ],
        /* [
            'label' => 'Global Ticket Sale Start',
            'value' => $formatDate($event->ticket_sell_start_time),
        ],
        [
            'label' => 'Global Ticket Sale End',
            'value' => $formatDate($event->ticket_sell_end_time),
        ], */
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
            'label' => 'Sell Tickets Individually',
            'value' => $yesNo($event->sell_tickets_individually),
        ],
        [
            'label' => 'Fees & Processing Paid By',
            'value' => (int) $event->charge_bear_by === 1
                ? 'Added on top of the ticket price'
                : 'Deducted from organizer ticket revenue',
        ],
    ];

    $seoItems = [
        [
            'label' => 'Meta Title',
            'value' => $text($event->meta_title),
            'icon' => 'fa-heading',
        ],
        [
            'label' => 'Focus Keywords',
            'value' => $text($event->meta_focus_keyword),
            'icon' => 'fa-tags',
        ],
    ];
@endphp

@push('style')
<style>
    .cleanup-detail-page {
        min-width: 0;
    }

    .cleanup-detail-page .dashboard-body,
    .cleanup-detail-page .container-fluid,
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
        gap: 10px;
    }

    .cleanup-detail-page .summary-action-btn {
        align-items: center;
        background: #fff;
        border: 1px solid var(--bs-primary);
        border-radius: 6px;
        color: var(--bs-primary);
        display: inline-flex;
        font-size: 14px;
        font-weight: 600;
        gap: 8px;
        justify-content: center;
        min-height: 43px;
        padding: 9px 17px;
        transition: all .2s ease;
    }

    .cleanup-detail-page .summary-action-btn:hover {
        background: var(--bs-primary);
        color: #fff;
    }

    .cleanup-detail-page .summary-action-btn.disabled {
        cursor: not-allowed;
        opacity: .55;
        pointer-events: none;
    }

    .cleanup-detail-page .detail-section {
        border: 1px solid #e6e9ec;
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
        padding: 17px 20px;
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
        font-size: 17px;
        font-weight: 600;
        margin: 0;
    }

    .cleanup-detail-page .detail-section-toggle .section-toggle-icon {
        color: #6c757d;
        font-size: 13px;
        transition: transform .2s ease;
    }

    .cleanup-detail-page .detail-section-toggle[aria-expanded="true"] .section-toggle-icon {
        transform: rotate(180deg);
    }

    .cleanup-detail-page .detail-section-toggle[aria-expanded="false"] {
        border-bottom-color: transparent;
    }

    .cleanup-detail-page .detail-section-body {
        padding: 24px;
    }

    .cleanup-detail-page .summary-banner {
        background: #f3f5f6;
        border-radius: 8px;
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
        right: 16px;
        top: 16px;
    }

    .cleanup-detail-page .event-title {
        color: #17191c;
        font-size: 25px;
        font-weight: 700;
        line-height: 1.35;
        margin: 0;
    }

    .cleanup-detail-page .event-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .cleanup-detail-page .soft-badge {
        background: rgba(var(--bs-primary-rgb), .08);
        border: 1px solid rgba(var(--bs-primary-rgb), .18);
        border-radius: 20px;
        color: var(--bs-primary);
        display: inline-flex;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 11px;
    }

    .cleanup-detail-page .organizer-card {
        align-items: center;
        background: #f8fafb;
        border: 1px solid #e6e9ec;
        border-radius: 8px;
        display: flex;
        gap: 13px;
        padding: 14px 16px;
    }

    .cleanup-detail-page .organizer-icon {
        align-items: center;
        background: rgba(var(--bs-primary-rgb), .10);
        border-radius: 50%;
        color: var(--bs-primary);
        display: flex;
        flex: 0 0 42px;
        height: 42px;
        justify-content: center;
        width: 42px;
    }

    .cleanup-detail-page .organizer-caption {
        color: #75808a;
        display: block;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .04em;
        margin-bottom: 2px;
        text-transform: uppercase;
    }

    .cleanup-detail-page .organizer-name {
        color: #17191c;
        font-size: 15px;
        font-weight: 600;
        margin: 0;
    }

    .cleanup-detail-page .content-box {
        background: #f8fafb;
        border: 1px solid #e5e9ec;
        border-left: 3px solid var(--bs-primary);
        border-radius: 7px;
        padding: 18px 20px;
    }

    .cleanup-detail-page .content-box-label {
        color: #66717c;
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .05em;
        margin-bottom: 9px;
        text-transform: uppercase;
    }

    .cleanup-detail-page .content-box-value {
        color: #272b2f;
        font-size: 14px;
        line-height: 1.75;
        margin: 0;
        overflow-wrap: anywhere;
        white-space: pre-line;
    }

    .cleanup-detail-page .info-card {
        background: #fff;
        border: 1px solid #e7eaed;
        border-radius: 7px;
        height: 100%;
        padding: 15px 16px;
    }

    .cleanup-detail-page .info-card-head {
        align-items: center;
        display: flex;
        gap: 9px;
        margin-bottom: 8px;
    }

    .cleanup-detail-page .info-card-icon {
        align-items: center;
        background: rgba(var(--bs-primary-rgb), .08);
        border-radius: 5px;
        color: var(--bs-primary);
        display: flex;
        flex: 0 0 30px;
        height: 30px;
        justify-content: center;
        width: 30px;
    }

    .cleanup-detail-page .info-card-label {
        color: #68737d;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .04em;
        line-height: 1.4;
        text-transform: uppercase;
    }

    .cleanup-detail-page .info-card-value {
        color: #202428;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.55;
        margin: 0;
        overflow-wrap: anywhere;
    }

    .cleanup-detail-page .empty-value {
        color: #929aa2 !important;
        font-style: italic;
        font-weight: 400 !important;
    }

    .cleanup-detail-page .subsection-title {
        align-items: center;
        color: #23272b;
        display: flex;
        font-size: 16px;
        font-weight: 650;
        gap: 9px;
        margin: 0 0 16px;
    }

    .cleanup-detail-page .subsection-title i {
        color: var(--bs-primary);
    }

    .cleanup-detail-page .location-card {
        background: #f8fafb;
        border: 1px solid #e5e9ec;
        border-radius: 8px;
        padding: 18px;
    }

    .cleanup-detail-page .location-address {
        color: #212529;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.6;
        margin: 0;
    }

    .cleanup-detail-page .map-link {
        align-items: center;
        display: inline-flex;
        font-size: 13px;
        font-weight: 600;
        gap: 7px;
        margin-top: 11px;
    }

    .cleanup-detail-page .media-banner {
        background: #f3f5f6;
        border: 1px solid #e5e9ec;
        border-radius: 8px;
        height: 270px;
        overflow: hidden;
    }

    .cleanup-detail-page .media-banner img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }

    .cleanup-detail-page .tool-chip {
        background: rgba(var(--bs-primary-rgb), .08);
        border: 1px solid rgba(var(--bs-primary-rgb), .18);
        border-radius: 20px;
        color: var(--bs-primary);
        display: inline-flex;
        font-size: 12px;
        font-weight: 600;
        margin: 0 6px 6px 0;
        padding: 6px 11px;
    }

    .cleanup-detail-page .ticket-card {
        background: #fff;
        border: 1px solid #e3e7ea;
        border-radius: 8px;
        height: 100%;
        overflow: hidden;
    }

    .cleanup-detail-page .ticket-card-header {
        align-items: flex-start;
        background: #f8fafb;
        border-bottom: 1px solid #e5e9ec;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        padding: 15px 17px;
    }

    .cleanup-detail-page .ticket-card-title {
        color: #202428;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.4;
        margin: 0;
    }

    .cleanup-detail-page .ticket-card-body {
        padding: 10px 17px 17px;
    }

    .cleanup-detail-page .ticket-row {
        align-items: flex-start;
        border-bottom: 1px solid #edf0f2;
        display: flex;
        gap: 16px;
        justify-content: space-between;
        padding: 9px 0;
    }

    .cleanup-detail-page .ticket-row:last-child {
        border-bottom: 0;
    }

    .cleanup-detail-page .ticket-row-label {
        color: #747d85;
        font-size: 12px;
    }

    .cleanup-detail-page .ticket-row-value {
        color: #24282c;
        font-size: 12px;
        font-weight: 600;
        max-width: 60%;
        overflow-wrap: anywhere;
        text-align: right;
    }

    .cleanup-detail-page .waiver-box {
        background: #f8fafb;
        border: 1px solid #e5e9ec;
        border-radius: 8px;
        padding: 20px;
    }

    .cleanup-detail-page .waiver-type {
        align-items: center;
        background: rgba(var(--bs-primary-rgb), .08);
        border-radius: 6px;
        color: var(--bs-primary);
        display: inline-flex;
        font-size: 13px;
        font-weight: 600;
        gap: 8px;
        padding: 8px 12px;
    }

    .cleanup-detail-page .waiver-content {
        color: #30353a;
        font-size: 14px;
        line-height: 1.8;
        margin: 16px 0 0;
        overflow-wrap: anywhere;
        white-space: pre-line;
    }

    .cleanup-detail-page .summary-stat {
        background: #fff;
        border: 1px solid #e4e8eb;
        border-radius: 8px;
        height: 100%;
        padding: 19px 12px;
        text-align: center;
    }

    .cleanup-detail-page .summary-stat-icon {
        align-items: center;
        background: rgba(var(--bs-primary-rgb), .08);
        border-radius: 50%;
        color: var(--bs-primary);
        display: flex;
        height: 38px;
        justify-content: center;
        margin: 0 auto 10px;
        width: 38px;
    }

    .cleanup-detail-page .summary-stat-label {
        color: #75808a;
        display: block;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .cleanup-detail-page .summary-stat-value {
        color: #17191c;
        display: block;
        font-size: 22px;
        font-weight: 700;
        margin-top: 5px;
    }

    .cleanup-detail-page .cohost-card {
        background: #fff;
        border: 1px solid #e4e8eb;
        border-radius: 8px;
        height: 100%;
        padding: 18px;
    }

    .cleanup-detail-page .cohost-header {
        align-items: center;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .cleanup-detail-page .cohost-person {
        align-items: center;
        display: flex;
        gap: 11px;
        min-width: 0;
    }

    .cleanup-detail-page .cohost-avatar {
        align-items: center;
        background: rgba(var(--bs-primary-rgb), .09);
        border-radius: 50%;
        color: var(--bs-primary);
        display: flex;
        flex: 0 0 40px;
        height: 40px;
        justify-content: center;
        width: 40px;
    }

    .cleanup-detail-page .cohost-name {
        color: #202428;
        font-size: 14px;
        font-weight: 700;
        margin: 0 0 2px;
    }

    .cleanup-detail-page .cohost-email {
        color: #737c84;
        display: block;
        font-size: 12px;
        overflow-wrap: anywhere;
    }

    .cleanup-detail-page .cohost-status {
        flex-shrink: 0;
        white-space: nowrap;
    }

    .cleanup-detail-page .report-metric {
        align-items: center;
        background: #f8fafb;
        border: 1px solid #e5e9ec;
        border-radius: 8px;
        display: flex;
        gap: 13px;
        height: 100%;
        padding: 16px;
    }

    .cleanup-detail-page .report-metric-icon {
        align-items: center;
        background: rgba(var(--bs-primary-rgb), .09);
        border-radius: 7px;
        color: var(--bs-primary);
        display: flex;
        flex: 0 0 40px;
        height: 40px;
        justify-content: center;
        width: 40px;
    }

    .cleanup-detail-page .report-metric-label {
        color: #75808a;
        display: block;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .cleanup-detail-page .report-metric-value {
        color: #202428;
        display: block;
        font-size: 15px;
        font-weight: 700;
        margin-top: 3px;
    }

    .cleanup-detail-page .after-event-image {
        border: 1px solid #e5e9ec;
        border-radius: 8px;
        height: 190px;
        object-fit: cover;
        width: 100%;
    }

    @media (max-width: 991px) {
        .cleanup-detail-page .summary-banner {
            height: 280px;
        }

        .cleanup-detail-page .media-banner {
            height: 230px;
        }
    }

    @media (max-width: 767px) {
        .cleanup-detail-page .summary-toolbar {
            width: 100%;
        }

        .cleanup-detail-page .summary-action-btn {
            flex: 1;
        }

        .cleanup-detail-page .detail-section-toggle {
            padding: 15px 16px;
        }

        .cleanup-detail-page .detail-section-toggle h4 {
            font-size: 15px;
        }

        .cleanup-detail-page .detail-section-body {
            padding: 17px;
        }

        .cleanup-detail-page .summary-banner {
            height: 220px;
        }

        .cleanup-detail-page .event-title {
            font-size: 21px;
        }

        .cleanup-detail-page .ticket-row-value {
            max-width: 55%;
        }
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
                                    'url' => route('front-cleanup-hosting', [
                                        'type' => 'organizer',
                                    ]),
                                ],
                                [
                                    'name' => $event->event_name,
                                    'url' => route('front-cleanup-detail', [
                                        'id' => $event->encrypted_id,
                                    ]),
                                ],
                            ];
                        @endphp

                        <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs" />
                    </div>

                    <div class="d-flex flex-wrap summary-toolbar">
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
                            <a href="{{ $iCalUrl }}" class="summary-action-btn">
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

                        <div class="row g-4 align-items-start">
                            <div class="col-lg-8">
                                <h2 class="event-title">{{ $event->event_name }}</h2>

                                <div class="event-badges mt-3">
                                    <span class="soft-badge">
                                        {{ $text($event->category?->name) }}
                                    </span>

                                    <span class="soft-badge">
                                        {{ ucfirst($text($event->status)) }}
                                    </span>

                                    <span class="soft-badge">
                                        {{ $event->is_private ? 'Private Event' : 'Public Event' }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="organizer-card">
                                    <div class="organizer-icon">
                                        <i class="fa-solid fa-user"></i>
                                    </div>

                                    <div>
                                        <span class="organizer-caption">Organizer</span>
                                        <p class="organizer-name">
                                            {{ $text($event->creator?->name) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="content-box">
                                    <span class="content-box-label">Description</span>

                                    <p class="content-box-value {{ empty(trim(strip_tags($event->description ?? ''))) ? 'empty-value' : '' }}">
                                        {{ $text($event->description) }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-link"></i>
                                        </span>

                                        <span class="info-card-label">Event URL Slug</span>
                                    </div>

                                    <p class="info-card-value {{ empty($event->slug) ? 'empty-value' : '' }}">
                                        {{ $text($event->slug) }}
                                    </p>
                                </div>
                            </div>

                            <!-- <div class="col-md-6 col-xl-3">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-file-circle-check"></i>
                                        </span>

                                        <span class="info-card-label">Community Service Form</span>
                                    </div>

                                    <p class="info-card-value">
                                        {{ $yesNo($event->allow_community_service_form) }}
                                    </p>
                                </div>
                            </div> -->

                            <div class="col-md-6 col-xl-4">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-ban"></i>
                                        </span>

                                        <span class="info-card-label">Cancelled At</span>
                                    </div>

                                    <p class="info-card-value {{ empty($event->cancelled_at) ? 'empty-value' : '' }}">
                                        {{ $formatDateTime($event->cancelled_at) }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-globe"></i>
                                        </span>

                                        <span class="info-card-label">Public Event URL</span>
                                    </div>

                                    @if($publicEventUrl)
                                        <a
                                            href="{{ $publicEventUrl }}"
                                            target="_blank"
                                            class="info-card-value"
                                        >
                                            Open public event page
                                            <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                                        </a>
                                    @else
                                        <p class="info-card-value empty-value">
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
                        <h5 class="subsection-title">
                            <i class="fa-solid fa-clock"></i>
                            Event Schedule
                        </h5>

                        <div class="row g-3">
                            @foreach(array_slice($scheduleItems, 0, 6) as $item)
                                <div class="col-md-6 col-xl-4">
                                    <div class="info-card">
                                        <div class="info-card-head">
                                            <span class="info-card-icon">
                                                <i class="fa-solid {{ $item['icon'] }}"></i>
                                            </span>

                                            <span class="info-card-label">
                                                {{ $item['label'] }}
                                            </span>
                                        </div>

                                        <p class="info-card-value {{ $item['value'] === 'Not Provided' ? 'empty-value' : '' }}">
                                            {{ $item['value'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-4">

                        <h5 class="subsection-title">
                            <i class="fa-solid fa-location-dot"></i>
                            Event Location
                        </h5>

                        <div class="location-card mb-3">
                            <span class="content-box-label">Event Address or Venue</span>

                            <p class="location-address {{ empty($event->venue) ? 'empty-value' : '' }}">
                                {{ $text($event->venue) }}
                            </p>

                            @if(!empty($event->latitude) && !empty($event->longitude))
                                <a
                                    href="https://www.google.com/maps?q={{ urlencode($event->latitude.','.$event->longitude) }}"
                                    target="_blank"
                                    class="map-link"
                                >
                                    <i class="fa-solid fa-map-location-dot"></i>
                                    View on Google Maps
                                </a>
                            @endif
                        </div>

                        <div class="row g-3">
                            @foreach(array_slice($scheduleItems, 6) as $item)
                                <div class="col-md-4">
                                    <div class="info-card">
                                        <div class="info-card-head">
                                            <span class="info-card-icon">
                                                <i class="fa-solid {{ $item['icon'] }}"></i>
                                            </span>

                                            <span class="info-card-label">
                                                {{ $item['label'] }}
                                            </span>
                                        </div>

                                        <p class="info-card-value {{ $item['value'] === 'Not Provided' ? 'empty-value' : '' }}">
                                            {{ $item['value'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-lg-6">
                                <div class="content-box h-100">
                                    <span class="content-box-label">
                                        Parking Information
                                    </span>

                                    <p class="content-box-value {{ empty($event->parking_info_details) ? 'empty-value' : '' }}">
                                        {{ $text($event->parking_info_details) }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="content-box h-100">
                                    <span class="content-box-label">
                                        Meeting Spot Information
                                    </span>

                                    <p class="content-box-value {{ empty($event->meeting_spot_details) ? 'empty-value' : '' }}">
                                        {{ $text($event->meeting_spot_details) }}
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
                            <i class="fa-solid fa-image"></i>
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

                        <!-- <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-crop-simple"></i>
                                        </span>

                                        <span class="info-card-label">
                                            Banner Orientation
                                        </span>
                                    </div>

                                    <p class="info-card-value">
                                        {{ ucfirst($text(
                                            $event->banner_image_orientation,
                                            'Landscape'
                                        )) }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-repeat"></i>
                                        </span>

                                        <span class="info-card-label">
                                            Event Recurrence
                                        </span>
                                    </div>

                                    <p class="info-card-value {{ empty($event->event_reoccurance) ? 'empty-value' : '' }}">
                                        {{ $text($event->event_reoccurance) }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-video"></i>
                                        </span>

                                        <span class="info-card-label">Video URL</span>
                                    </div>

                                    @if(!empty($event->video_url))
                                        <a
                                            href="{{ $event->video_url }}"
                                            target="_blank"
                                            class="info-card-value"
                                        >
                                            Open video
                                            <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                                        </a>
                                    @else
                                        <p class="info-card-value empty-value">
                                            Not Provided
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div> -->

                        <hr class="my-4">

                        <h5 class="subsection-title">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                            Tools & Classification
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-hand"></i>
                                        </span>

                                        <span class="info-card-label">
                                            What Should Volunteers Bring?
                                        </span>
                                    </div>

                                    <div>
                                        @forelse($event->bringTools as $tool)
                                            <span class="tool-chip">
                                                {{ $tool->name }}
                                            </span>
                                        @empty
                                            <p class="info-card-value empty-value">
                                                Not Provided
                                            </p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-box-open"></i>
                                        </span>

                                        <span class="info-card-label">
                                            What Will Be Provided?
                                        </span>
                                    </div>

                                    <div>
                                        @forelse($event->providedTools as $tool)
                                            <span class="tool-chip">
                                                {{ $tool->name }}
                                            </span>
                                        @empty
                                            <p class="info-card-value empty-value">
                                                Not Provided
                                            </p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-users"></i>
                                        </span>

                                        <span class="info-card-label">Volunteer Tags</span>
                                    </div>

                                    <div>
                                        @forelse($event->volunteertags as $tag)
                                            <span class="tool-chip">
                                                {{ $tag->name }}
                                            </span>
                                        @empty
                                            <p class="info-card-value empty-value">
                                                Not Provided
                                            </p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-tags"></i>
                                        </span>

                                        <span class="info-card-label">Event Tags</span>
                                    </div>

                                    <div>
                                        @forelse($event->tags as $tag)
                                            <span class="tool-chip">
                                                {{ $tag->name }}
                                            </span>
                                        @empty
                                            <p class="info-card-value empty-value">
                                                Not Provided
                                            </p>
                                        @endforelse
                                    </div>
                                </div>
                            </div> -->

                            <div class="col-md-4">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-bell"></i>
                                        </span>

                                        <span class="info-card-label">
                                            Reminder Email Enabled
                                        </span>
                                    </div>

                                    <p class="info-card-value">
                                        {{ $yesNo($event->is_send_reminder_email) }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="content-box h-100">
                                    <span class="content-box-label">
                                        Reminder Custom Note
                                    </span>

                                    <p class="content-box-value {{ empty($event->reminder_custom_note) ? 'empty-value' : '' }}">
                                        {{ $text($event->reminder_custom_note) }}
                                    </p>
                                </div>
                            </div>
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
                            <i class="fa-solid fa-sliders"></i>
                            Registration Settings
                        </h5>

                        <div class="row g-3">
                            @foreach($registrationItems as $item)
                                <div class="col-md-6 col-xl-4">
                                    <div class="info-card">
                                        <div class="info-card-head">
                                            <span class="info-card-icon">
                                                <i class="fa-solid fa-circle-check"></i>
                                            </span>

                                            <span class="info-card-label">
                                                {{ $item['label'] }}
                                            </span>
                                        </div>

                                        <p class="info-card-value {{ $item['value'] === 'Not Provided' ? 'empty-value' : '' }}">
                                            {{ $item['value'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            @if($event->participation_type==4)
                            <div class="col-md-8">
                                <div class="info-card">
                                    <div class="info-card-head">
                                        <span class="info-card-icon">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </span>

                                        <span class="info-card-label">
                                            External Registration URL
                                        </span>
                                    </div>

                                    @if(!empty($event->participation_type_external_link))
                                        <a
                                            href="{{ $event->participation_type_external_link }}"
                                            target="_blank"
                                            class="info-card-value"
                                        >
                                            {{ $event->participation_type_external_link }}
                                        </a>
                                    @else
                                        <p class="info-card-value empty-value">
                                            Not Provided
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <div class="col-md-12">
                                <div class="content-box h-100">
                                    <span class="content-box-label">
                                        Volunteer Questions
                                    </span>

                                    <p class="content-box-value {{ empty($event->ticket_questions) ? 'empty-value' : '' }}">
                                        {{ $text($event->ticket_questions) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="subsection-title">
                            <i class="fa-solid fa-ticket-simple"></i>
                            Ticket Types
                        </h5>

                        <div class="row g-3">
                            @forelse($event->eventTickets as $ticket)
                                @php
                                    $soldCount = $ticket->soldTickets
                                        ->where('rsvp_confirmation', 1)
                                        ->count();

                                    $cancelledTicketCount = $ticket->soldTickets
                                        ->where('rsvp_confirmation', 0)
                                        ->count();

                                    $remainingCount = max(
                                        0,
                                        (int) $ticket->available_quantity - $soldCount
                                    );

                                    $ticketRows = [
                                        ['Status', $ticket->is_active ? 'Active' : 'Inactive'],
                                        [
                                            'Price',
                                            $ticket->ticket_type === 'paid'
                                                ? '$'.number_format((float) $ticket->ticket_price, 2)
                                                : 'Free',
                                        ],
                                        ['Available Quantity', $ticket->available_quantity],
                                        ['Sold', $soldCount],
                                        ['Cancelled', $cancelledTicketCount],
                                        ['Remaining', $remainingCount],
                                        [
                                            'Maximum Per Order',
                                            $text($ticket->max_qty_allowed_per_order),
                                        ],
                                        ['Sale Start', $formatDate($ticket->sell_start_time)],
                                        ['Sale End', $formatDate($ticket->sell_end_time)],
                                        ['Passcode', $text($ticket->pass_code)],
                                    ];
                                @endphp

                                <div class="col-lg-6">
                                    <div class="ticket-card">
                                        <div class="ticket-card-header">
                                            <h6 class="ticket-card-title">
                                                {{ $ticket->ticket_name }}
                                            </h6>

                                            <span class="badge bg-light text-dark border">
                                                {{ ucfirst($ticket->ticket_type) }}
                                            </span>
                                        </div>

                                        <div class="ticket-card-body">
                                            @foreach($ticketRows as $ticketRow)
                                                <div class="ticket-row">
                                                    <span class="ticket-row-label">
                                                        {{ $ticketRow[0] }}
                                                    </span>

                                                    <span class="ticket-row-value {{ $ticketRow[1] === 'Not Provided' ? 'empty-value' : '' }}">
                                                        {{ $ticketRow[1] }}
                                                    </span>
                                                </div>
                                            @endforeach

                                            <div class="content-box mt-3">
                                                <span class="content-box-label">
                                                    Ticket Description
                                                </span>

                                                <p class="content-box-value {{ empty($ticket->ticket_description) ? 'empty-value' : '' }}">
                                                    {{ $text($ticket->ticket_description) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="content-box">
                                        <p class="content-box-value empty-value">
                                            No ticket types have been added.
                                        </p>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <hr class="my-4">

                        <h5 class="subsection-title">
                            <i class="fa-solid fa-file-signature"></i>
                            Waiver Information
                        </h5>

                        <div class="waiver-box">
                            <span class="waiver-type">
                                <i class="fa-solid fa-file-shield"></i>

                                {{ $waiverTypes[$event->waiver_form_type]
                                    ?? 'No Waiver Form' }}
                            </span>

                            <div class="mt-3">
                                <span class="content-box-label">
                                    Waiver Content
                                </span>

                                @if($waiverPdfPath)
                                    <a
                                        href="{{ $waiverPdfPath }}"
                                        target="_blank"
                                        class="summary-action-btn"
                                    >
                                        <i class="fa-solid fa-file-pdf"></i>
                                        View Waiver PDF
                                    </a>
                                @elseif(
                                    $event->waiver_form_type === 'external_link' &&
                                    !empty($event->waiver_form_content)
                                )
                                    <a
                                        href="{{ $event->waiver_form_content }}"
                                        target="_blank"
                                        class="waiver-content d-block"
                                    >
                                        {{ $event->waiver_form_content }}
                                    </a>
                                @else
                                    <p class="waiver-content {{ empty($event->waiver_form_content) ? 'empty-value' : '' }}">
                                        {{ $text($event->waiver_form_content) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistics --}}
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
                                    <span class="summary-stat-icon">
                                        <i class="fa-solid fa-user-group"></i>
                                    </span>

                                    <span class="summary-stat-label">Registered</span>
                                    <span class="summary-stat-value">
                                        {{ $statistics['registered'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-4 col-xl">
                                <div class="summary-stat">
                                    <span class="summary-stat-icon">
                                        <i class="fa-solid fa-user-check"></i>
                                    </span>

                                    <span class="summary-stat-label">Checked In</span>
                                    <span class="summary-stat-value">
                                        {{ $statistics['checked_in'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-4 col-xl">
                                <div class="summary-stat">
                                    <span class="summary-stat-icon">
                                        <i class="fa-solid fa-file-signature"></i>
                                    </span>

                                    <span class="summary-stat-label">Waivers Signed</span>
                                    <span class="summary-stat-value">
                                        {{ $statistics['waivers'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-4 col-xl">
                                <div class="summary-stat">
                                    <span class="summary-stat-icon">
                                        <i class="fa-solid fa-user-xmark"></i>
                                    </span>

                                    <span class="summary-stat-label">Cancelled</span>
                                    <span class="summary-stat-value">
                                        {{ $statistics['cancelled'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-6 col-md-4 col-xl">
                                <div class="summary-stat">
                                    <span class="summary-stat-icon">
                                        <i class="fa-solid fa-hand-holding-dollar"></i>
                                    </span>

                                    <span class="summary-stat-label">Donations</span>
                                    <span class="summary-stat-value">
                                        ${{ number_format($statistics['donations'], 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
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
                        Co-Hosts
                    </h4>

                    <i class="fa-solid fa-chevron-down section-toggle-icon"></i>
                </button>

                <div id="cleanupCohostSection" class="collapse show">
                    <div class="detail-section-body">
                        <div class="row g-3">
                            @forelse($event->cohostRecords as $cohost)
                                <div class="col-lg-6">
                                    <div class="cohost-card">
                                        <div class="cohost-header">
                                            <div class="cohost-person">
                                                <span class="cohost-avatar">
                                                    <i class="fa-solid fa-user"></i>
                                                </span>

                                                <div>
                                                    <h6 class="cohost-name">
                                                        {{ $text($cohost->volunteer?->name) }}
                                                    </h6>

                                                    <span class="cohost-email">
                                                        {{ $text($cohost->volunteer?->email_address) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <span class="badge bg-light text-dark border cohost-status">
                                                {{ ucfirst($text($cohost->request_status)) }}
                                            </span>
                                        </div>

                                        <div class="content-box">
                                            <span class="content-box-label">
                                                Extra Notes
                                            </span>

                                            <p class="content-box-value {{ empty($cohost->extra_notes) ? 'empty-value' : '' }}">
                                                {{ $text($cohost->extra_notes) }}
                                            </p>
                                        </div>

                                        <!-- <div class="mt-3">
                                            <span class="content-box-label">
                                                Permissions
                                            </span>

                                            @forelse($cohost->permissions as $permission)
                                                <span class="tool-chip">
                                                    {{ $cohostPermissionLabels[$permission->permission]
                                                        ?? 'Permission '.$permission->permission }}
                                                </span>
                                            @empty
                                                <p class="info-card-value empty-value">
                                                    Not Provided
                                                </p>
                                            @endforelse
                                        </div> -->
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="content-box">
                                        <p class="content-box-value empty-value">
                                            No co-hosts have been added.
                                        </p>
                                    </div>
                                </div>
                            @endforelse
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
                        <div class="row g-3">
                            @foreach($seoItems as $seoItem)
                                <div class="col-lg-6">
                                    <div class="info-card">
                                        <div class="info-card-head">
                                            <span class="info-card-icon">
                                                <i class="fa-solid {{ $seoItem['icon'] }}"></i>
                                            </span>

                                            <span class="info-card-label">
                                                {{ $seoItem['label'] }}
                                            </span>
                                        </div>

                                        <p class="info-card-value {{ $seoItem['value'] === 'Not Provided' ? 'empty-value' : '' }}">
                                            {{ $seoItem['value'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-12">
                                <div class="content-box">
                                    <span class="content-box-label">
                                        Meta Description
                                    </span>

                                    <p class="content-box-value {{ empty($event->meta_description) ? 'empty-value' : '' }}">
                                        {{ $text($event->meta_description) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cleanup reporting --}}
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
                        Cleanup Reporting
                    </h4>

                    <i class="fa-solid fa-chevron-down section-toggle-icon"></i>
                </button>

                <div id="cleanupReportingSection" class="collapse show">
                    <div class="detail-section-body">
                        <h5 class="subsection-title">
                            <i class="fa-solid fa-users"></i>
                            Volunteer Summary
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="report-metric">
                                    <span class="report-metric-icon">
                                        <i class="fa-solid fa-user-group"></i>
                                    </span>

                                    <div>
                                        <span class="report-metric-label">
                                            Registered Volunteers
                                        </span>

                                        <span class="report-metric-value">
                                            {{ $statistics['registered'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="report-metric">
                                    <span class="report-metric-icon">
                                        <i class="fa-solid fa-globe"></i>
                                    </span>

                                    <div>
                                        <span class="report-metric-label">
                                            Registered From Other Sites
                                        </span>

                                        <span class="report-metric-value">
                                            {{ $statistics['other_sites'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="report-metric">
                                    <span class="report-metric-icon">
                                        <i class="fa-solid fa-users"></i>
                                    </span>

                                    <div>
                                        <span class="report-metric-label">
                                            Total Volunteers
                                        </span>

                                        <span class="report-metric-value">
                                            {{ $statistics['total_volunteers'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="subsection-title">
                            <i class="fa-solid fa-leaf"></i>
                            Cleanup Impact
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="report-metric">
                                    <span class="report-metric-icon">
                                        <i class="fa-solid fa-trash"></i>
                                    </span>

                                    <div>
                                        <span class="report-metric-label">
                                            Waste Collected
                                        </span>

                                        <span class="report-metric-value {{ is_null($event->waste_collected) ? 'empty-value' : '' }}">
                                            {{ is_null($event->waste_collected)
                                                ? 'Not Provided'
                                                : $event->waste_collected.' Pound (lb)' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="report-metric">
                                    <span class="report-metric-icon">
                                        <i class="fa-solid fa-ruler-combined"></i>
                                    </span>

                                    <div>
                                        <span class="report-metric-label">
                                            Area Cleaned
                                        </span>

                                        <span class="report-metric-value {{ is_null($event->area_cleaned) ? 'empty-value' : '' }}">
                                            {{ is_null($event->area_cleaned)
                                                ? 'Not Provided'
                                                : $event->area_cleaned.' Yards' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="report-metric">
                                    <span class="report-metric-icon">
                                        <i class="fa-solid fa-clock"></i>
                                    </span>

                                    <div>
                                        <span class="report-metric-label">
                                            Total Volunteer Hours
                                        </span>

                                        <span class="report-metric-value {{ is_null($event->volunteer_hours) ? 'empty-value' : '' }}">
                                            {{ $text($event->volunteer_hours) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="report-metric">
                                    <span class="report-metric-icon">
                                        <i class="fa-solid fa-clipboard-check"></i>
                                    </span>

                                    <div>
                                        <span class="report-metric-label">
                                            Cleanup Report Status
                                        </span>

                                        <span class="report-metric-value">
                                            {{ (int) $event->is_completed === 1
                                                ? 'Completed'
                                                : 'Pending' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="report-metric">
                                    <span class="report-metric-icon">
                                        <i class="fa-solid fa-user-check"></i>
                                    </span>

                                    <div>
                                        <span class="report-metric-label">
                                            Wrap-Up Completed By
                                        </span>

                                        <span class="report-metric-value {{ empty($event->wrapup_email_sender) ? 'empty-value' : '' }}">
                                            {{ $text($event->wrapup_email_sender?->name) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="content-box">
                                    <span class="content-box-label">
                                        Post Event Comments
                                    </span>

                                    <p class="content-box-value {{ empty($event->post_event_notes) ? 'empty-value' : '' }}">
                                        {{ $text($event->post_event_notes) }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-12">
                                <h5 class="subsection-title mt-2">
                                    <i class="fa-solid fa-images"></i>
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
                                            <div class="content-box">
                                                <p class="content-box-value empty-value">
                                                    Not Provided
                                                </p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
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