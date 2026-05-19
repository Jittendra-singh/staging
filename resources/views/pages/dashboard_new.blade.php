@php
    $pageName = 'Dashboard';

    $dashboardCards = [
        [
            'label' => 'Total Events',
            'value' => $details->total_events,
            'url' => route('index-events'),
            'icon' => 'mdi mdi-calendar-star',
        ],
        [
            'label' => 'Upcoming Events',
            'value' => $details->upcoming_events,
            'url' => route('index-events', ['start_date' => date('Y-m-d')]),
            'icon' => 'mdi mdi-calendar-clock',
        ],
        [
            'label' => 'Total Volunteers Registered',
            'value' => $details->total_volunteers_registered,
            'url' => route('index-volunteers'),
            'icon' => 'mdi mdi-account-group-outline',
        ],
        [
            'label' => 'Total Organizers',
            'value' => $details->total_organizers,
            'url' => route('index-volunteers'),
            'icon' => 'mdi mdi-account-tie-outline',
        ],
        [
            'label' => 'New Registrations This Month',
            'value' => $details->total_registration_month,
            'url' => route('index-volunteers', ['start_date' => date('Y-m-01')]),
            'icon' => 'mdi mdi-account-plus-outline',
        ],
        [
            'label' => 'Total Orders (Tickets/Donations)',
            'value' => $details->total_orders,
            'url' => route('index-orders'),
            'icon' => 'mdi mdi-ticket-confirmation-outline',
        ],
        [
            'label' => 'Community Service Requests',
            'value' => $details->pending_community_service_req,
            'url' => '#',
            'icon' => 'mdi mdi-clipboard-alert-outline',
        ],
    ];
@endphp

@extends('layouts.backend_new')

@section('css')
    <style>
        .dashboard-counter-card {
            background: linear-gradient(135deg, #5865d9 0%, #3949ab 100%);
            border: 0;
            border-radius: 8px;
            color: #fff;
            min-height: 118px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .08);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .dashboard-counter-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(15, 23, 42, .12);
        }

        .dashboard-counter-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex: 0 0 42px;
        }

        .dashboard-counter-label {
            font-size: 13px;
            opacity: .88;
            margin-bottom: 4px;
            min-height: 32px;
        }

        .dashboard-counter-value {
            font-size: 26px;
            font-weight: 700;
            line-height: 1.1;
            color: #fff;
        }

        .dashboard-counter-link {
            color: rgba(255, 255, 255, .78);
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 8px;
        }

        .dashboard-counter-link:hover {
            color: #fff;
        }
    </style>
@endsection

@section('content')
    <div class="row g-3">
        @foreach($dashboardCards as $card)
            <div class="col-sm-6 col-xl-3">
                <div class="card dashboard-counter-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <span class="dashboard-counter-icon">
                                <i class="{{ $card['icon'] }}"></i>
                            </span>
                            <div>
                                <div class="dashboard-counter-label">{{ $card['label'] }}</div>
                                <div class="dashboard-counter-value">{{ number_format($card['value']) }}</div>
                                <a class="dashboard-counter-link" href="{{ $card['url'] }}">
                                    View Details <i class="mdi mdi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
