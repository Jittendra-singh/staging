@php
    $outPages = true;

    $organiserEventIds = $organiser->events()->pluck('id');

    $cleanupCount = $organiserEventIds->count();

    $volunteerCount = \App\Models\EventOrderTicket::whereIn('event_id', $organiserEventIds)
        ->where('rsvp_confirmation', 1)
        ->count();

    $poundsRemoved = $organiser->events()->get()->sum(function ($event) {
        return is_numeric($event->waste_collected) ? (float) $event->waste_collected : 0;
    });

    $cleanupCount = $cleanupCount + ($organiser->organisation_prev_cleanups_count ?? 0);
    $volunteerCount = $volunteerCount + ($organiser->organisation_prev_volunteers_count ?? 0);
    $poundsRemoved = $poundsRemoved + ($organiser->organisation_prev_waste_collected_pounds_count ?? 0);

    $joinedYear = \Carbon\Carbon::parse($organiser->created_at)->format('Y');
@endphp

@extends('layouts.frontend')

@push('style')
<style>
    .organizer-detail-page{
        background: linear-gradient(180deg, #f7f9fc 0%, #f3f6fa 100%);
        padding: 70px 0 72px;
    }

    .organizer-shell{
        max-width: 980px;
        margin: 0 auto;
    }

    .organizer-card{
        background: #ffffff;
        border: 1px solid #eef2f6;
        border-radius: 28px;
        box-shadow: 0 18px 48px rgba(19, 35, 67, 0.08);
        overflow: hidden;
    }

    .organizer-hero{
        position: relative;
        padding: 34px 34px 28px;
        background:
            radial-gradient(circle at top left, rgba(41, 214, 181, 0.08), transparent 30%),
            linear-gradient(180deg, #ffffff 0%, #fbfcfd 100%);
    }

    .organizer-hero::after{
        content: "";
        position: absolute;
        left: 34px;
        right: 34px;
        bottom: 0;
        height: 1px;
        background: linear-gradient(90deg, rgba(22, 34, 57, 0.08), rgba(22, 34, 57, 0.02));
    }

    .organizer-avatar{
        width: 94px;
        height: 94px;
        border-radius: 50%;
        object-fit: cover;
        background: #f3f5f8;
        border: 1px solid #eef2f6;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.7);
    }

    .organizer-title{
        font-size: 2rem;
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: #1f2937;
        margin: 0;
    }

    .organizer-subtitle{
        margin: 10px 0 0;
        font-size: 0.96rem;
        font-weight: 600;
        color: #5f6b7a;
    }

    .organizer-social{
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 14px;
        flex-wrap: wrap;
    }

    .organizer-social-link{
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #1f2937!important;
        text-decoration: none;
        transition: all .2s ease;
    }

    .organizer-social-link:hover{
        color: var(--bs-primary);
        background: rgba(13, 110, 253, 0.08);
    }

    .organizer-contact-btn{
        min-width: 132px;
        padding: 12px 22px;
        border: 0;
        border-radius: 10px;
        background: #f5f7fa;
        color: #222b38;
        font-size: 0.86rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        transition: all .2s ease;
    }

    .organizer-contact-btn:hover{
        background: #ebf0f4;
    }

    .organizer-stats{
        padding: 0 34px;
    }

    .organizer-stats-row{
        border-bottom: 1px solid #eef2f6;
    }

    .organizer-stat{
        padding: 26px 18px;
        text-align: center;
        position: relative;
    }

    .organizer-stat + .organizer-stat::before{
        content: "";
        position: absolute;
        top: 22px;
        bottom: 22px;
        left: 0;
        width: 1px;
        background: #eef2f6;
    }

    .organizer-stat-icon{
        font-size: 0.95rem;
        color: #9aa4b2;
        margin-bottom: 10px;
    }

    .organizer-stat-value{
        font-size: 2rem;
        line-height: 1;
        font-weight: 500;
        letter-spacing: -0.04em;
        color: #27d3b7;
        margin: 0;
    }

    .organizer-stat-value small{
        font-size: 0.55em;
        font-weight: 500;
    }

    .organizer-stat-label{
        margin-top: 8px;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #7b8794;
    }

    .organizer-content{
        padding: 28px 34px 36px;
    }

    .organizer-main-tabs{
        border-bottom: 1px solid #eef2f6;
        gap: 24px;
        margin-bottom: 24px!important;
    }

    .organizer-main-tabs .nav-link{
        border: 0;
        background: transparent;
        color: #8b95a3;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 0 0 12px;
        border-bottom: 2px solid transparent;
        border-radius: 0;
        padding-left: 20px;
        padding-right: 20px;
    }

    .organizer-main-tabs .nav-link.active,
    .organizer-main-tabs .nav-link:hover{
        color: #1f2937;
        border-bottom-color: #1f2937;
    }

    .organizer-about{
        color: #667085;
        font-size: 1rem;
        line-height: 1.9;
    }

    .organizer-about p:last-child{
        margin-bottom: 0;
    }

    .organizer-joined{
        margin-top: 24px;
        font-size: 1rem;
        font-weight: 800;
        color: #1f2937;
    }

    .organizer-events-head{
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .organizer-events-title{
        margin: 0;
        font-size: 1.35rem;
        font-weight: 800;
        color: #1f2937;
    }

    .organizer-sub-tabs{
        gap: 10px;
        border: 0;
    }

    .organizer-sub-tabs .nav-link{
        border: 1px solid #e8edf3;
        background: #f8fafc;
        color: #607084;
        font-size: 0.86rem;
        font-weight: 700;
        padding: 10px 16px;
        border-radius: 5px;
    }

    .organizer-sub-tabs .nav-link.active,
    .organizer-sub-tabs .nav-link:hover{
        background: var(--bs-primary) !important;
        border-color: var(--bs-primary) !important;
        color: #fff!important;
    }

    .organizer-empty{
        padding: 36px 20px;
        border: 1px dashed #dfe6ee;
        border-radius: 18px;
        background: #fbfcfe;
        text-align: center;
        color: #7b8794;
    }

    @media (max-width: 991.98px){
        .organizer-hero,
        .organizer-stats,
        .organizer-content{
            padding-left: 24px;
            padding-right: 24px;
        }

        .organizer-hero::after{
            left: 24px;
            right: 24px;
        }

        .organizer-title{
            font-size: 1.7rem;
        }
    }

    @media (max-width: 767.98px){
        .organizer-detail-page{
            padding: 28px 0 48px;
        }

        .organizer-card{
            border-radius: 20px;
        }

        .organizer-hero{
            padding-top: 26px;
            padding-bottom: 22px;
        }

        .organizer-hero-top{
            text-align: center;
        }

        .organizer-hero-top .organizer-identity{
            width: 100%;
            justify-content: center;
            text-align: center;
        }

        .organizer-hero-top .organizer-identity-copy{
            width: 100%;
        }

        .organizer-social{
            justify-content: center;
        }

        .organizer-contact-wrap{
            width: 100%;
        }

        .organizer-contact-btn{
            width: 100%;
        }

        .organizer-stat{
            padding: 20px 12px;
        }

        .organizer-stat + .organizer-stat::before{
            display: none;
        }

        .organizer-stat-value{
            font-size: 1.6rem;
        }

        .organizer-main-tabs{
            gap: 18px;
        }

        .organizer-events-head{
            flex-direction: column;
            align-items: stretch;
        }

        .organizer-sub-tabs{
            width: 100%;
        }

        .organizer-sub-tabs .nav-item{
            flex: 1 1 auto;
        }

        .organizer-sub-tabs .nav-link{
            width: 100%;
        }
    }
    #upcoming-events-tab-btn.active,#past-events-tab-btn.active,#upcoming-events-tab-btn:hover,#past-events-tab-btn:hover{
        color:white !important;
    }
    #upcoming-events-tab-btn,#past-events-tab-btn{
        color:var(--linear-gradient-2) !important
    }
</style>
@endpush

@section('content')
    <div class="wrapper">
        <main class="main organizer-detail-page">
            <div class="container">
                <div class="organizer-shell">
                    <x-reusables.frontend.alert />

                    <div class="organizer-card">
                        <div class="organizer-hero">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-4 organizer-hero-top">
                                <div class="d-flex align-items-center gap-4 flex-wrap organizer-identity">
                                    <img
                                        src="{{ $organiser->formattedImage('organisation_logo') }}"
                                        alt="{{ $organiser->organisation_name }}"
                                        class="organizer-avatar"
                                    />

                                    <div class="organizer-identity-copy">
                                        <h1 class="organizer-title">{{ $organiser->organisation_name }}</h1>
                                        <p class="organizer-subtitle">{{ $organiser->name }}</p>

                                        <div class="organizer-social">
                                            @if ($organiser->organisation_website)
                                                <a href="{{ $organiser->organisation_website }}" target="_BLANK" class="organizer-social-link" aria-label="Website">
                                                    <i class="fa fa-link"></i>
                                                </a>
                                            @endif
                                            @if ($organiser->organisation_instagram)
                                                <a href="{{ $organiser->organisation_instagram }}" target="_BLANK" class="organizer-social-link" aria-label="Instagram">
                                                    <i class="fab fa-instagram"></i>
                                                </a>
                                            @endif
                                            @if ($organiser->organisation_facebook)
                                                <a href="{{ $organiser->organisation_facebook }}" target="_BLANK" class="organizer-social-link" aria-label="Facebook">
                                                    <i class="fab fa-facebook-square"></i>
                                                </a>
                                            @endif
                                            @if ($organiser->organisation_linkedin)
                                                <a href="{{ $organiser->organisation_linkedin }}" target="_BLANK" class="organizer-social-link" aria-label="LinkedIn">
                                                    <i class="fab fa-linkedin-in"></i>
                                                </a>
                                            @endif
                                            @if ($organiser->organisation_twitter)
                                                <a href="{{ $organiser->organisation_twitter }}" target="_BLANK" class="organizer-social-link" aria-label="X">
                                                    <i class="fab">𝕏</i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="organizer-contact-wrap">
                                    <button class="organizer-contact-btn" data-bs-toggle="modal" data-bs-target="#contactOrgraniser">
                                        Contact
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="organizer-stats">
                            <div class="row organizer-stats-row g-0">
                                <div class="col-12 col-md-4">
                                    <div class="organizer-stat">
                                        <div class="organizer-stat-icon">
                                            <i class="fa-regular fa-calendar"></i>
                                        </div>
                                        <h3 class="organizer-stat-value">{{ $cleanupCount }}</h3>
                                        <div class="organizer-stat-label">Cleanups</div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="organizer-stat">
                                        <div class="organizer-stat-icon">
                                            <i class="fa-regular fa-user"></i>
                                        </div>
                                        <h3 class="organizer-stat-value">{{ $volunteerCount }}</h3>
                                        <div class="organizer-stat-label">Volunteers</div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="organizer-stat">
                                        <div class="organizer-stat-icon">
                                            <i class="fa-solid fa-arrow-trend-down"></i>
                                        </div>
                                        <h3 class="organizer-stat-value">{{ rtrim(rtrim(number_format($poundsRemoved, 2, '.', ''), '0'), '.') ?: 0 }}<small> lbs</small></h3>
                                        <div class="organizer-stat-label">Pounds Removed</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="organizer-content">
                            <ul class="nav nav-tabs organizer-main-tabs" id="eventTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link {{ request()->get('tab')=='upcoming_event' || request()->get('tab')=='past_event' || request()->get('tab')=='ongoing_event' ? '' : 'active' }}"
                                        id="current-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#current-events"
                                        type="button"
                                        role="tab"
                                        aria-controls="current-events"
                                        aria-selected="true"
                                    >
                                        About
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link {{ request()->get('tab')=='upcoming_event' || request()->get('tab')=='past_event' || request()->get('tab')=='ongoing_event' ? 'active' : '' }}"
                                        id="upcoming-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#upcoming-events"
                                        type="button"
                                        role="tab"
                                        aria-controls="upcoming-events"
                                        aria-selected="false"
                                    >
                                        Events
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="eventTabsContent">
                                <div
                                    class="tab-pane fade {{ request()->get('tab')=='upcoming_event' || request()->get('tab')=='past_event' || request()->get('tab')=='ongoing_event' ? '' : 'show active' }}"
                                    id="current-events"
                                    role="tabpanel"
                                    aria-labelledby="current-tab"
                                >
                                    <div class="organizer-about">
                                        @if(!empty(trim(strip_tags($organiser->organisation_short_description ?? ''))))
                                            {!! $organiser->organisation_short_description !!}
                                        @else
                                            <p class="mb-0">This organizer has not added an about description yet.</p>
                                        @endif
                                    </div>

                                    <div class="organizer-joined">
                                        Joined in {{ $joinedYear }}
                                    </div>
                                </div>

                                <div
                                    class="tab-pane fade {{ request()->get('tab')=='upcoming_event' || request()->get('tab')=='past_event' || request()->get('tab')=='ongoing_event' ? 'show active' : '' }}"
                                    id="upcoming-events"
                                    role="tabpanel"
                                    aria-labelledby="upcoming-tab"
                                >
                                    <div class="organizer-events-head">
                                        <h3 class="organizer-events-title">Events</h3>

                                        <!-- <ul class="nav nav-tabs organizer-sub-tabs" id="extra-tab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button
                                                    class="nav-link {{ request()->get('tab')=='upcoming_event' ? 'active' : (!request()->get('tab') ? 'active' : '') }}"
                                                    id="upcoming-events-tab-btn"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#upcoming-events-tab"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="upcoming-events-tab"
                                                    aria-selected="false"
                                                >
                                                    Upcoming Events
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button
                                                    class="nav-link {{ request()->get('tab')=='past_event' ? 'active' : '' }}"
                                                    id="past-events-tab-btn"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#past-events-tab"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="past-events-tab"
                                                    aria-selected="false"
                                                >
                                                    Past Events
                                                </button>
                                            </li>
                                        </ul> -->
                                        <div class="btn-group nav nav-tabs" role="group">
                                            <button
                                                    class="btn btn-outline-primary {{ request()->get('tab')=='upcoming_event' ? 'active text-white' : (!request()->get('tab') ? 'active text-white' : '') }}"
                                                    id="upcoming-events-tab-btn"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#upcoming-events-tab"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="upcoming-events-tab"
                                                    aria-selected="false">
                                                Upcoming Event
                                            </button>
                                            <button
                                                    class="btn btn-outline-primary px-3 py-2 {{ request()->get('tab')=='past_event' ? 'active text-white' : '' }}"
                                                    id="past-events-tab-btn"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#past-events-tab"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="past-events-tab"
                                                    aria-selected="false">
                                                Past Event
                                            </button>
                                        </div>
                                    </div>

                                    <div class="tab-content w-100" id="organizerInnerTabsContent">
                                        <div
                                            class="tab-pane fade {{ request()->get('tab')=='upcoming_event' ? 'show active' : (!request()->get('tab') ? 'show active' : '') }}"
                                            id="upcoming-events-tab"
                                            role="tabpanel"
                                            aria-labelledby="upcoming-events-tab-btn"
                                        >
                                            @if($future_events->count())
                                                <x-reusables.frontend.event-list-view :events="$future_events" />
                                                <div class="mt-5" data-aos="fade-up" data-aos-duration="800">
                                                    {{ $future_events->links() }}
                                                </div>
                                            @else
                                                <div class="organizer-empty">
                                                    No upcoming events found for this organizer.
                                                </div>
                                            @endif
                                        </div>

                                        <div
                                            class="tab-pane fade {{ request()->get('tab')=='past_event' ? 'show active' : '' }}"
                                            id="past-events-tab"
                                            role="tabpanel"
                                            aria-labelledby="past-events-tab-btn"
                                        >
                                            @if($past_events->count())
                                                <x-reusables.frontend.event-list-view :events="$past_events" />
                                                <div class="mt-5" data-aos="fade-up" data-aos-duration="800">
                                                    {{ $past_events->links() }}
                                                </div>
                                            @else
                                                <div class="organizer-empty">
                                                    No past events found for this organizer.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-reusables.frontend.organiser-contact :organiser="$organiser->id" />
                </div>
            </div>
        </main>
    </div>
@endsection

@push('script')
<script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/validation.js')}}"></script>
<script src="{{ asset('assets/libs/imask/imask.min.js') }}"></script>
@endpush
