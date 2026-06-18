@php
    $pageName = 'Campaign Analytics';

    $counterCards = [
        ['label' => 'Total Events', 'value' => $counters->total_events, 'icon' => 'mdi mdi-calendar-month-outline', 'unit' => ''],
        ['label' => 'Total Registrations', 'value' => $counters->total_registrations, 'icon' => 'mdi mdi-account-plus-outline', 'unit' => ''],
        ['label' => 'Total Check-ins', 'value' => $counters->total_checkins, 'icon' => 'mdi mdi-check-circle-outline', 'unit' => ''],
        ['label' => 'Total Debris Removed', 'value' => $counters->total_debris_removed, 'icon' => 'mdi mdi-delete-variant', 'unit' => 'lbs'],
    ];
@endphp

@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $pageName])

@section('css')
    <x-reusables.advanceformcss/>
    <style>
        .campaign-card {
            border: 0;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .06);
        }

        .campaign-stat-card {
            background: linear-gradient(135deg, #5865d9 0%, #3949ab 100%);
            color: #fff;
            min-height: 116px;
        }

        .campaign-stat-card.light {
            background: #e9e9e9;
            color: #1f2937;
        }

        .campaign-stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .campaign-stat-card.light .campaign-stat-icon {
            background: #e8ecff;
            color: #4656c9;
        }

        .campaign-stat-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .03em;
            opacity: .78;
            margin-bottom: 5px;
        }

        .campaign-stat-value {
            font-size: 26px;
            font-weight: 700;
            line-height: 1.1;
        }

        .campaign-stat-unit {
            font-size: 13px;
            font-weight: 600;
            margin-left: 4px;
        }

        .campaign-stat-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 12px;
            width: 100%;
        }

        .campaign-detail-link {
            color: rgba(255, 255, 255, .82);
            font-size: 11px;
            font-weight: 600;
            text-decoration: underline !important;
            white-space: nowrap;
        }

        .campaign-detail-link:hover {
            color: #fff;
        }

        .campaign-stat-card.light .campaign-detail-link {
            color: #4656c9;
        }

        .campaign-stat-card.light .campaign-detail-link:hover {
            color: #25339b;
        }

        .campaign-section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .campaign-chart-title,
        .campaign-table-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            color: #495057;
            margin-bottom: 12px;
        }

        .campaign-filter-card .form-label {
            color: #495057;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .campaign-trend-total {
            color: #4656c9;
            font-size: 18px;
            font-weight: 700;
            line-height: 1;
            text-align: right;
        }

        .campaign-trend-subtitle {
            color: #6b7280;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: right;
        }

        .attendance-rate {
            color: #4656c9;
            font-size: 44px;
            font-weight: 700;
            line-height: 1;
        }

        .attendance-subtitle {
            color: #4656c9;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .attendance-status-label {
            color: #6b7280;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .attendance-status-value {
            color: #1f2937;
            font-size: 12px;
            font-weight: 700;
        }

        .campaign-progress {
            height: 11px;
            background: #e5e7ff;
            border-radius: 50px;
        }

        .campaign-progress .progress-bar {
            background: #4656c9;
            border-radius: 50px;
        }

        .attendance-mini-box {
            background: #f8f9fc;
            border: 1px solid #eef1f6;
            border-radius: 6px;
            padding: 14px;
        }

        .attendance-mini-box.active {
            background: #eef1ff;
        }

        .attendance-mini-label {
            color: #6b7280;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .attendance-mini-value {
            color: #1f2937;
            font-size: 18px;
            font-weight: 700;
        }

        .attendance-mini-box.active .attendance-mini-value {
            color: #4656c9;
        }

        .campaign-table {
            margin-bottom: 0;
        }

        .campaign-table th {
            color: #6b7280;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            border-top: 0;
            border-bottom: 1px solid #eef1f6;
            padding: 8px;
        }

        .campaign-table td {
            color: #4b5563;
            font-size: 13px;
            font-weight: 600;
            border-bottom: 0;
            padding: 10px 8px;
            vertical-align: middle;
        }

        .campaign-table .text-primary {
            color: #4656c9 !important;
            font-weight: 700;
        }
    </style>
    <style>
        .campaign-trend-summary {
            min-width: 110px;
            text-align: right;
        }

        .campaign-trend-subtitle {
            white-space: nowrap;
        }

        .registration-trend-wrapper {
            width: 100%;
            overflow: visible;
            padding: 0 6px;
            min-height: 315px;
        }

        #registration-trend-chart {
            width: 100%;
            min-height: 315px;
            overflow: visible;
        }

        #registration-trend-chart .apexcharts-canvas,
        #registration-trend-chart svg,
        #registration-trend-chart foreignObject {
            overflow: visible !important;
        }

        #registration-trend-chart .apexcharts-xaxis-label {
            font-size: 10px;
            font-weight: 700;
        }
        .campaign-empty-state {
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            font-weight: 600;
        }

        .campaign-empty-state i {
            display: block;
            color: #4656c9;
            font-size: 24px;
            margin-bottom: 8px;
            opacity: .75;
        }
    </style>
@endsection

@section('js')
    <x-reusables.advanceformjs />
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        $('.select2').select2();
    </script>
    <script>
        var registrationTrendMonths = @json($registrationTrendMonths);
        var defaultTrendMonth = @json($defaultTrendMonth);
        var registrationTrendChart = null;

        function formatCampaignNumber(value) {
            return Number(value || 0).toLocaleString();
        }

        function getTrendMonthData(monthKey) {
            return registrationTrendMonths[monthKey] || {
                label: '',
                total: 0,
                labels: [],
                data: []
            };
        }

        function renderRegistrationTrendChart(monthKey) {
            var chartElement = document.querySelector("#registration-trend-chart");

            if (!chartElement) {
                return;
            }

            var wrapper = chartElement.closest('.registration-trend-wrapper');
            var chartWidth = Math.floor(wrapper.getBoundingClientRect().width);
            var monthData = getTrendMonthData(monthKey);

            $('#registration_trend_total').text(formatCampaignNumber(monthData.total));

            if (chartWidth <= 0) {
                window.setTimeout(function() {
                    renderRegistrationTrendChart(monthKey);
                }, 100);
                return;
            }

            if (registrationTrendChart) {
                registrationTrendChart.destroy();
            }

            chartElement.innerHTML = '';

            registrationTrendChart = new ApexCharts(chartElement, {
                series: [{ name: 'Registrations', data: monthData.data }],
                chart: {
                    type: 'area',
                    height: 315,
                    width: chartWidth,
                    toolbar: { show: false },
                    parentHeightOffset: 0,
                    redrawOnParentResize: true,
                    redrawOnWindowResize: true,
                    animations: { enabled: false }
                },
                stroke: {
                    curve: 'smooth',
                    width: 4
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        opacityFrom: .16,
                        opacityTo: .02
                    }
                },
                colors: ['#4656c9'],
                xaxis: {
                    categories: monthData.labels,
                    tickPlacement: 'on',
                    labels: {
                        rotate: 0,
                        trim: false,
                        hideOverlappingLabels: false,
                        minHeight: 34,
                        offsetY: 4,
                        style: {
                            colors: '#9aa3b2',
                            fontSize: '10px',
                            fontWeight: 700
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    tooltip: { enabled: false }
                },
                yaxis: {
                    show: true,
                    min: 0,
                    labels: {
                        formatter: function(value) {
                            if (value >= 1000) {
                                return (value / 1000).toFixed(value % 1000 === 0 ? 0 : 1) + 'K';
                            }

                            return value.toFixed(0);
                        },
                        style: {
                            colors: '#9aa3b2',
                            fontSize: '11px',
                            fontWeight: 700
                        }
                    }
                },
                grid: {
                    show: true,
                    borderColor: '#eef1f6',
                    strokeDashArray: 4,
                    padding: {
                        left: 10,
                        right: 18,
                        top: 10,
                        bottom: 8
                    }
                },
                dataLabels: { enabled: false },
                markers: {
                    size: 0,
                    hover: { size: 5 }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return formatCampaignNumber(value) + ' registrations';
                        }
                    }
                }
            });

            registrationTrendChart.render();
        }

        $('#registration_trend_month').on('change', function() {
            renderRegistrationTrendChart($(this).val());
        });

        window.setTimeout(function() {
            renderRegistrationTrendChart(defaultTrendMonth);
        }, 250);

        var registrationTrendResizeTimer = null;
        window.addEventListener('resize', function () {
            clearTimeout(registrationTrendResizeTimer);
            registrationTrendResizeTimer = setTimeout(function() {
                renderRegistrationTrendChart($('#registration_trend_month').val());
            }, 250);
        });
    </script>
    <script>
        $('#campaignAnalyticsFilterForm').on('submit', function(e) {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();

            $('#start_date, #end_date').removeClass('is-invalid');

            if ((startDate && !endDate) || (!startDate && endDate)) {
                e.preventDefault();

                $('#start_date, #end_date').addClass('is-invalid');
                showError('Please select both start date and end date.', 'error');

                return false;
            }

            if (startDate && endDate && endDate < startDate) {
                e.preventDefault();

                $('#start_date, #end_date').addClass('is-invalid');
                showError('End date should be greater than or equal to start date.', 'error');

                return false;
            }

            return true;
        });

        $('#start_date, #end_date').on('change', function() {
            $('#start_date, #end_date').removeClass('is-invalid');
        });
    </script>
@endsection

@section('content')
    <div class="card campaign-card campaign-filter-card mb-3">
        <div class="card-body py-3">
            <form method="GET" id="campaignAnalyticsFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3 col-xl-2">
                        <label class="form-label" for="tag_id">Event Tag</label>
                        <select class="form-control select2" id="tag_id" name="tag_id">
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" @selected($selectedTagId == $tag->id)>{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-xl-2">
                        <label class="form-label" for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>

                    <div class="col-md-3 col-xl-2">
                        <label class="form-label" for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>

                    <div class="col-md-3 col-xl-2">
                        <label class="form-label" for="category_id">Category</label>
                        <select class="form-control select2" id="category_id" name="category_id">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <label class="form-label" for="organizer_id">Organizer</label>
                        <select class="form-control select2ajax" datatype="organiser" id="organizer_id" name="organizer_id">
                            @if(request('organizer_id') && $selected_organizer)
                                <option value="{{ request('organizer_id') }}" selected>
                                    {{ $selected_organizer->first_name.' '.$selected_organizer->last_name.' ('.$selected_organizer->email_address.')' }}
                                </option>
                            @endif
                        </select>
                    </div>

                    <div class="col-md-6 col-xl-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('index-campaign-analytics') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        @foreach($counterCards as $index => $card)
            <div class="col-sm-6 col-xl-3">
                <div class="card campaign-card campaign-stat-card {{ $index == 2 ? 'light' : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="campaign-stat-label">{{ $card['label'] }}</div>
                                <div class="campaign-stat-value">
                                    {{ number_format($card['value']) }}@if($card['unit'])<span class="campaign-stat-unit">{{ $card['unit'] }}</span>@endif
                                </div>
                            </div>

                            <span class="campaign-stat-icon">
                                <i class="{{ $card['icon'] }}"></i>
                            </span>
                        </div>

                        <div class="campaign-stat-footer">
                            <a href="javascript:void(0);" class="campaign-detail-link">Details</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="campaign-section-title">Campaign Trends</div>

    <div class="row g-3 mb-3">
        <div class="col-xl-6">
            <div class="card campaign-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-3">
                        <div class="campaign-trend-subtitle text-nowrap">Total Registrations</div>

                        <div class="d-flex align-items-start gap-3 ms-auto">
                            <select class="form-control select2" id="registration_trend_month" style="min-width: 180px;">
                                @foreach($registrationTrendMonths as $monthKey => $monthData)
                                    <option value="{{ $monthKey }}" @selected($monthKey == $defaultTrendMonth)>
                                        {{ $monthData['label'] }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="campaign-trend-summary">
                                <div class="campaign-trend-total" id="registration_trend_total">
                                    {{ number_format($registrationTrendMonths[$defaultTrendMonth]['total'] ?? 0) }}
                                </div>
                                <div class="campaign-trend-subtitle text-nowrap">Total Registrations</div>
                            </div>
                        </div>
                    </div>

                    <div class="registration-trend-wrapper">
                        <div id="registration-trend-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card campaign-card h-100">
                <div class="card-body">
                    <div class="campaign-chart-title">Attendance Rate</div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="attendance-rate">{{ $counters->attendance_rate }}%</div>
                            <div class="attendance-subtitle">Efficiency Rate</div>
                        </div>
                        <div class="text-end">
                            <div class="attendance-status-label">Status</div>
                            <div class="attendance-status-value">{{ $counters->attendance_status }}</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-1">
                        <div class="attendance-status-label">Progress</div>
                        <div class="attendance-status-label">{{ number_format($counters->total_checkins) }} / {{ number_format($counters->total_registrations) }}</div>
                    </div>

                    <div class="progress campaign-progress mb-4">
                        <div class="progress-bar" style="width: {{ $counters->attendance_rate }}%"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="attendance-mini-box">
                                <div class="attendance-mini-label">Registrations</div>
                                <div class="attendance-mini-value">{{ number_format($counters->total_registrations) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="attendance-mini-box active">
                                <div class="attendance-mini-label">Checked-in</div>
                                <div class="attendance-mini-value">{{ number_format($counters->total_checkins) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-6">
            <div class="card campaign-card h-100">
                <div class="card-body">
                    <div class="campaign-table-title">Top Organizers (by Events Hosted)</div>
                    <div class="table-responsive">
                        <table class="table campaign-table">
                            <thead>
                                <tr>
                                    <th>Organizer</th>
                                    <th class="text-end">Events</th>
                                    <th class="text-end">Debris (lbs)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topEventOrganizers as $item)
                                    <tr>
                                        <td>{{ @$item->organizer->first_name }} {{ @$item->organizer->last_name }}</td>
                                        <td class="text-end">{{ number_format($item->total_events) }}</td>
                                        <td class="text-end text-primary">{{ number_format($item->debris_removed) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="campaign-empty-state">
                                                <div>
                                                    <i class="mdi mdi-account-group-outline"></i>
                                                    No organizer data found.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card campaign-card h-100">
                <div class="card-body">
                    <div class="campaign-table-title">Organizer Participation (by Registrations)</div>
                    <div class="table-responsive">
                        <table class="table campaign-table">
                            <thead>
                                <tr>
                                    <th>Organizer</th>
                                    <th class="text-end">Registrations</th>
                                    <th class="text-end">Conv. Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topRegistrationOrganizers as $item)
                                    <tr>
                                        <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                                        <td class="text-end">{{ number_format($item->total_registrations) }}</td>
                                        <td class="text-end text-primary">{{ $item->conversion_rate }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="campaign-empty-state">
                                                <div>
                                                    <i class="mdi mdi-chart-line"></i>
                                                    No registration data found.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection