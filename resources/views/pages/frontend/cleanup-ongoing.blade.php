@extends('layouts.frontend')

@push('style')
<style>
    .ongoing-cleanup-list {
        margin-top: 16px;
    }

    .ongoing-cleanup-card {
        background: #fff;
        border: 1px solid #dce3ee;
        border-radius: 8px;
        padding: 18px 26px;
        margin-bottom: 14px;
    }

    .ongoing-cleanup-card-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .ongoing-cleanup-badges {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        flex-wrap: wrap;
    }

    .ongoing-cleanup-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 700;
        line-height: 1;
        text-transform: uppercase;
    }

    .ongoing-cleanup-badge.host {
        background: #eef3f8;
        color: #69758c;
    }

    .ongoing-cleanup-badge.cohost {
        background: #0d6efd;
        color: #fff;
    }

    .ongoing-cleanup-badge.live {
        background: #eef7ff;
        color: #0d6efd;
    }

    .ongoing-cleanup-name {
        font-size: 17px;
        font-weight: 700;
        color: var(--bs-body-color);
        margin: 0 0 8px;
    }

    .ongoing-cleanup-meta {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
        color: #53627a;
        font-size: 13px;
        font-weight: 500;
    }

    .ongoing-cleanup-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .ongoing-cleanup-meta i {
        color: var(--bs-primary);
    }

    .ongoing-checkin-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-width: 170px;
        min-height: 50px;
        border-radius: 6px;
        background: var(--bs-primary);
        color: white !important;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
    }

    a.ongoing-checkin-btn:hover {
        color: white !important;
        opacity: .92;
    }

    @media (max-width: 767px) {
        .ongoing-cleanup-card {
            padding: 16px;
        }

        .ongoing-cleanup-card-inner {
            display: block;
        }

        .ongoing-cleanup-action {
            margin-top: 16px;
        }

        .ongoing-checkin-btn {
            width: 100%;
            min-width: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="wrapper wrapper-body ongoing-cleanup-page">
    <div class="dashboard-body">
        <div class="container-fluid">
            <div class="ongoing-cleanup-shell">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-main-title ongoing-cleanup-title">
                            <h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>In-progress Cleanups</h3>
                            <p class="mb-2">Check-in volunteers for in-progress and published events.</p>

                            @php
                                $breadcrumbs = [
                                    [
                                        'name' => 'In-progress Cleanups',
                                        'url' => route('front-cleanup-ongoing'),
                                    ],
                                ];
                            @endphp
                            <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <x-reusables.frontend.alert />

                        <div class="event-list ongoing-cleanup-list">
                            @forelse ($hosting_events as $event)
                                @php
                                    $isHost = auth()->guard('front')->user()->id == $event->creator_id;
                                    $roleLabel = $isHost ? 'Host' : 'Co-Host';
                                    $roleClass = $isHost ? 'host' : 'cohost';
                                @endphp

                                <div class="ongoing-cleanup-card">
                                    <div class="ongoing-cleanup-card-inner">
                                        <div class="ongoing-cleanup-info">
                                            <div class="ongoing-cleanup-badges">
                                                <span class="ongoing-cleanup-badge {{ $roleClass }}">{{ $roleLabel }}</span>
                                                <!-- <span class="ongoing-cleanup-badge live">Now Live</span> -->
                                            </div>

                                            <h4 class="ongoing-cleanup-name">{{ $event->event_name }}</h4>

                                            <div class="ongoing-cleanup-meta">
                                                <span>
                                                    <i class="fa-solid fa-location-dot"></i>
                                                    {{ $event->venue ?: $event->fullAddress }}
                                                </span>
                                                <span>
                                                    <i class="fa-solid fa-user-group"></i>
                                                    {{ $event->volunteer_count }} Volunteers
                                                </span>
                                            </div>
                                        </div>

                                        <div class="ongoing-cleanup-action">
                                            <a href="{{ route('front-volunteer-check-in', ['id' => $event->encrypted_id]) }}" class="ongoing-checkin-btn">
                                                <i class="fa-solid fa-user-check"></i>
                                                Start Check-In
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="main-card mt-2">
                                    <div class="contact-list">
                                        <div class="card-top event-top p-4 align-items-center top d-flex flex-wrap justify-content-center">
                                            <div class="text-center py-4">
                                                <div class="empty-icon-wrap mx-auto mb-3">
                                                    <i class="fa-regular fa-image fa-2x text-muted"></i>
                                                </div>
                                                <h5 class="fw-semibold mb-2">No in-progress cleanup found</h5>
                                                <p class="text-muted small mb-0">There are no active in-progress host or co-host cleanups right now.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-3">
                            {{ $hosting_events->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection