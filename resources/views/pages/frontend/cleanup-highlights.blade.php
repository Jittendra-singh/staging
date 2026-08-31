@php
    $outPages = true;

    $checkedInVolunteerCount = $event->tickets()->active()->where('checkin', 1)->count();
    $registeredVolunteerCount = $event->tickets()->active()->where('rsvp_confirmation', 1)->count();
    $hasNarrative = !empty(trim($event->post_event_notes ?? ''));

    $images = [];
    if (!empty($event->after_event_images)) {
        $decodedImages = json_decode($event->after_event_images);
        if (!empty($decodedImages)) {
            $images = $decodedImages;
        }
    }

    $hasImages = !empty($images);

    $wasteCollected = !empty($event->waste_collected) ? $event->waste_collected . ' lbs' : '-- lbs';
    $volunteerHours = !empty($event->volunteer_hours) ? $event->volunteer_hours . ' hrs' : '-- hrs';
@endphp

@extends('layouts.frontend')

@push('style')
<style>
    .cleanup-highlights-page {
        background: #f8fafc;
    }

    .cleanup-highlights-wrap {
        padding: 70px 0 56px;
    }

    .cleanup-highlights-card {
        background: #fff;
        border: 1px solid #edf1f5;
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .cleanup-highlights-head {
        padding: 28px 28px 22px;
        border-bottom: 1px solid #edf1f5;
        background: #fff;
    }

    .cleanup-highlights-body {
        padding: 28px;
    }

    .cleanup-back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #667085;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        margin-bottom: 16px;
    }

    .cleanup-back-link:hover {
        color: var(--bs-primary);
    }

    .cleanup-page-title {
        margin: 0;
        color: #101828;
        font-size: 2.25rem;
        line-height: 1.15;
        font-weight: 700;
    }

    .cleanup-meta-row {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    .cleanup-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #667085;
        font-size: 15px;
        font-weight: 500;
    }

    .cleanup-meta-item i {
        color: #337af6;
        font-size: 14px;
    }

    .cleanup-share-btn {
        min-width: 118px;
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 0;
        border-radius: 10px;
        background: #f2f4f7;
        color: #1d2939;
        font-size: 15px;
        font-weight: 500;
        padding: 10px 18px;
    }

    .cleanup-share-btn:hover {
        background: #e9edf2;
        color: #101828;
    }

    .cleanup-side-panel {
        background: #f8fafc;
        border: 1px solid #e8edf3;
        border-radius: 16px;
        padding: 20px;
        height: 100%;
    }

    .cleanup-side-title {
        margin: 0 0 18px;
        color: #2051b7;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .cleanup-stat-card {
        display: flex;
        align-items: center;
        gap: 14px;
        background: #fff;
        border: 1px solid #edf1f5;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        padding: 16px;
    }

    .cleanup-stat-card + .cleanup-stat-card {
        margin-top: 14px;
    }

    .cleanup-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #eaf2ff;
        color: #2158c9;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex: 0 0 48px;
    }

    .cleanup-stat-label {
        margin: 0 0 4px;
        color: #98a2b3;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        line-height: 1.2;
    }

    .cleanup-stat-value {
        margin: 0;
        color: #101828;
        font-size: 15px;
        line-height: 1.35;
        font-weight: 700;
    }

    .cleanup-stat-value.is-muted {
        color: #344054;
    }

    .cleanup-gallery-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .cleanup-gallery-item {
        background: #fff;
        border: 1px solid #edf1f5;
        border-radius: 16px;
        overflow: hidden;
        min-height: 240px;
    }

    .cleanup-gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .cleanup-empty-media {
        min-height: 420px;
        border: 2px dashed #d6dbe4;
        border-radius: 20px;
        background: #fbfcfe;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 28px;
    }

    .cleanup-empty-media-icon {
        width: 68px;
        height: 68px;
        margin: 0 auto 16px;
        border-radius: 18px;
        background: #eef2f7;
        color: #c4cad4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .cleanup-empty-media-title {
        margin: 0 0 6px;
        color: #98a2b3;
        font-size: 18px;
        line-height: 1.3;
        font-weight: 500;
    }

    .cleanup-empty-media-text {
        margin: 0;
        color: #98a2b3;
        font-size: 14px;
        line-height: 1.6;
    }

    .cleanup-narrative {
        margin-top: 32px;
    }

    .cleanup-narrative-title {
        margin: 0 0 10px;
        color: #101828;
        font-size: 28px;
        line-height: 1.2;
        font-weight: 700;
    }

    .cleanup-narrative-text {
        margin: 0;
        color: #667085;
        font-size: 15px;
        line-height: 1.8;
    }

    .cleanup-narrative-text.is-empty {
        font-style: italic;
        color: #98a2b3;
    }

    @media (max-width: 991.98px) {
        .cleanup-page-title {
            font-size: 1.9rem;
        }

        .cleanup-gallery-grid {
            grid-template-columns: 1fr;
        }

        .cleanup-empty-media {
            min-height: 320px;
        }
    }

    @media (max-width: 767.98px) {
        .cleanup-highlights-wrap {
            padding: 24px 0 40px;
        }

        .cleanup-highlights-head,
        .cleanup-highlights-body {
            padding: 20px;
        }

        .cleanup-page-title {
            font-size: 1.55rem;
        }

        .cleanup-meta-item {
            font-size: 14px;
        }

        .cleanup-share-btn {
            width: 100%;
            min-width: 100%;
        }

        .cleanup-empty-media {
            min-height: 260px;
            padding: 20px;
        }

        .cleanup-empty-media-title {
            font-size: 16px;
        }

        .cleanup-empty-media-text {
            font-size: 13px;
        }

        .cleanup-narrative-title {
            font-size: 24px;
        }

        .cleanup-narrative-text {
            font-size: 14px;
        }
    }
</style>
@endpush

@section('content')
<div class="wrapper cleanup-highlights-page">
    <div class="event-dt-block cleanup-highlights-wrap">
        <div class="container">
            <div class="cleanup-highlights-card">
                <div class="cleanup-highlights-head">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div class="flex-grow-1">
                            @if(!empty($event->slug))
                                <a href="{{ route('view-event-details', ['slug' => $event->slug]) }}" class="cleanup-back-link">
                                    <i class="fa-solid fa-arrow-left"></i>
                                    <span>Back to Event</span>
                                </a>
                            @endif
                            <button type="button" class="cleanup-share-btn float-end mb-4" onclick="copyText('{{ route('cleanup-highlights', $event->encrypted_id) }}')">
                                <i class="fa-solid fa-share-nodes"></i>
                                <span>Share</span>
                            </button>

                            <h1 class="cleanup-page-title">{{ $event->event_name }}


                            </h1>

                            <div class="cleanup-meta-row">
                                <div class="cleanup-meta-item">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>{{ $event->eventStartDateTime }}</span>
                                </div>
                                <div class="cleanup-meta-item">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span>{{ $event->fullAddress }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="w-100 w-md-auto">
                            <button type="button" class="cleanup-share-btn" onclick="copyText('{{ route('cleanup-highlights', $event->encrypted_id) }}')">
                                <i class="fa-solid fa-share-nodes"></i>
                                <span>Share</span>
                            </button>
                        </div> -->
                    </div>
                </div>

                <div class="cleanup-highlights-body">
                    <div class="row g-4 align-items-start">
                        <div class="col-lg-4">
                            <div class="cleanup-side-panel">
                                <h3 class="cleanup-side-title">Cleanup Highlights</h3>

                                <div class="cleanup-stat-card">
                                    <div class="cleanup-stat-icon">
                                        <i class="fa-solid fa-trash"></i>
                                    </div>
                                    <div>
                                        <p class="cleanup-stat-label">Total Debris</p>
                                        <h4 class="cleanup-stat-value">{{ $wasteCollected }}</h4>
                                    </div>
                                </div>


                                <div class="cleanup-stat-card">
                                    <div class="cleanup-stat-icon">
                                        <i class="fa-solid fa-flag"></i>
                                    </div>
                                    <div>
                                        <p class="cleanup-stat-label">Volunteer Hours</p>
                                        <h4 class="cleanup-stat-value">{{ $volunteerHours }}</h4>
                                    </div>
                                </div>

                                <div class="cleanup-stat-card">
                                    <div class="cleanup-stat-icon">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div>
                                        <p class="cleanup-stat-label">Registered Volunteers</p>
                                        <h4 class="cleanup-stat-value {{ $registeredVolunteerCount ? '' : 'is-muted' }}">
                                            {{ $registeredVolunteerCount ? $registeredVolunteerCount : '--' }}
                                        </h4>
                                    </div>
                                </div>

                                <div class="cleanup-stat-card">
                                    <div class="cleanup-stat-icon">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div>
                                        <p class="cleanup-stat-label">Attended Volunteers</p>
                                        <h4 class="cleanup-stat-value {{ $checkedInVolunteerCount ? '' : 'is-muted' }}">
                                            {{ $checkedInVolunteerCount ? $checkedInVolunteerCount : '--' }}
                                        </h4>
                                    </div>
                                </div>

                                @if(!empty($event->volunteer_registered_from_other_sites))
                                <div class="cleanup-stat-card">
                                    <div class="cleanup-stat-icon">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div>
                                        <p class="cleanup-stat-label">Volunteers From Other Platforms</p>
                                        <h4 class="cleanup-stat-value {{ $event->volunteer_registered_from_other_sites ? '' : 'is-muted' }}">
                                            {{ $event->volunteer_registered_from_other_sites ? $event->volunteer_registered_from_other_sites : '--' }}
                                        </h4>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-8">
                            @if($hasImages)
                                <div class="cleanup-gallery-grid">
                                    @foreach($images as $image)
                                        @php
                                            $imagePath = is_object($image) ? $image->path : $image;
                                        @endphp
                                        <div class="cleanup-gallery-item">
                                            <img src="{{ asset($imagePath) }}" alt="{{ $event->event_name }}">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="cleanup-empty-media">
                                    <div>
                                        <div class="cleanup-empty-media-icon">
                                            <i class="fa-regular fa-image"></i>
                                        </div>
                                        <h4 class="cleanup-empty-media-title">No images available yet</h4>
                                        <p class="cleanup-empty-media-text">
                                            Photos will appear here once uploaded by the coordinator.
                                        </p>
                                    </div>
                                </div>
                            @endif

                            <div class="cleanup-narrative">
                                <h2 class="cleanup-narrative-title">Post Event Comments</h2>

                                @if($hasNarrative)
                                    <p class="cleanup-narrative-text">{{ $event->post_event_notes }}</p>
                                @else
                                    <p class="cleanup-narrative-text is-empty">
                                        The event summary will appear here once the report is finalized.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
@endsection
