@php
    $pageName = 'Weekly Alert Analytics';

    $counterCards = [
        ['label' => 'Total Alerts', 'value' => $counters->total_alerts, 'icon' => 'mdi mdi-bell-outline'],
        ['label' => 'Disabled Alerts', 'value' => $counters->disabled_alerts, 'icon' => 'mdi mdi-bell-off-outline'],
        ['label' => 'New Alerts', 'value' => $counters->new_alerts, 'icon' => 'mdi mdi-bell-plus-outline'],
        ['label' => 'Subscribers', 'value' => $counters->subscribers, 'icon' => 'mdi mdi-account-group-outline'],
    ];
@endphp

@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $pageName])

@section('css')
    <link href="{{ asset('assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .alert-analytics-card {
            border: 0;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .06);
        }

        .alert-stat-card {
            background: linear-gradient(135deg, #5865d9 0%, #3949ab 100%);
            color: #fff;
            min-height: 116px;
        }

        .alert-stat-card.light {
            background: #f4f6fb;
            color: #1f2937;
        }

        .alert-stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .alert-stat-card.light .alert-stat-icon {
            background: #e8ecff;
            color: #4656c9;
        }

        .alert-stat-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .03em;
            opacity: .78;
            margin-bottom: 5px;
        }

        .alert-stat-value {
            font-size: 26px;
            font-weight: 700;
            line-height: 1.1;
        }

        .alert-chart-box {
            min-height: 270px;
        }

        .alert-chart-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            color: #495057;
            margin-bottom: 12px;
        }

        #weekly-alert-map {
            height: 340px;
        }

        .country-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 0;
            border-bottom: 1px solid #eef1f6;
        }

        .country-row:last-child {
            border-bottom: 0;
        }

        .country-count {
            font-weight: 700;
            color: #4656c9;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-us-merc-en.js') }}"></script>

    <script>
        var weeklyLabels = @json($weeklyLabels);
        var weeklyData = @json($weeklyData);
        var monthlyLabels = @json($monthlyLabels);
        var monthlyData = @json($monthlyData);
        var quarterlyLabels = @json($quarterlyLabels);
        var quarterlyData = @json($quarterlyData);
        var yearlyLabels = @json($yearlyLabels);
        var yearlyData = @json($yearlyData);
        var mapData = @json($mapData);

        new ApexCharts(document.querySelector("#weekly-alert-chart"), {
            series: [{ name: 'Alerts', data: weeklyData }],
            chart: { type: 'line', height: 210, toolbar: { show: false } },
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 4 },
            colors: ['#4656c9'],
            xaxis: { categories: weeklyLabels },
            dataLabels: { enabled: false },
            grid: { borderColor: '#eef1f6' }
        }).render();

        new ApexCharts(document.querySelector("#monthly-alert-chart"), {
            series: [{ name: 'Alerts', data: monthlyData }],
            chart: { type: 'bar', height: 210, toolbar: { show: false } },
            plotOptions: { bar: { borderRadius: 5, columnWidth: '45%' } },
            colors: ['#34c38f'],
            xaxis: { categories: monthlyLabels },
            dataLabels: { enabled: false },
            grid: { borderColor: '#eef1f6' }
        }).render();

        new ApexCharts(document.querySelector("#quarterly-alert-chart"), {
            series: quarterlyData,
            chart: { type: 'donut', height: 210 },
            labels: quarterlyLabels,
            colors: ['#4656c9', '#50a5f1', '#f1b44c', '#f46a6a'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true }
        }).render();

        new ApexCharts(document.querySelector("#yearly-alert-chart"), {
            series: [{ name: 'Alerts', data: yearlyData }],
            chart: { type: 'area', height: 210, toolbar: { show: false } },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .35, opacityTo: .05 } },
            colors: ['#f46a6a'],
            xaxis: { categories: yearlyLabels },
            dataLabels: { enabled: false },
            grid: { borderColor: '#eef1f6' }
        }).render();

        $('#weekly-alert-map').vectorMap({
            map: 'us_merc_en',
            backgroundColor: 'transparent',
            normalizeFunction: 'polynomial',
            hoverOpacity: 0.75,
            hoverColor: false,
            regionStyle: {
                initial: {
                    fill: '#e9edf5',
                    'fill-opacity': 1,
                    stroke: 'none',
                    'stroke-width': 0,
                    'stroke-opacity': 1
                },
                hover: {
                    fill: '#4656c9',
                    cursor: 'pointer'
                }
            },
            series: {
                regions: [{
                    values: mapData,
                    scale: ['#dfe5ff', '#4656c9'],
                    normalizeFunction: 'polynomial'
                }]
            },
            onRegionTipShow: function(e, el, code) {
                var count = mapData[code] || 0;
                el.html(el.html() + ' - ' + count + ' alerts');
            }
        });
    </script>
@endsection

@section('content')
    <div class="row g-3 mb-3">
        @foreach($counterCards as $index => $card)
            @php 
            $boxLink = route('index-weekly-alerts');
            if($index==1){
                $boxLink = route('index-weekly-alerts',['status'=>'0']);
            } elseif($index==2){
                $boxLink = route('index-weekly-alerts', ['start_date'=>date('Y-m-d', strtotime('-7 days')),'end_date'=>date('Y-m-d')]);
            } elseif($index==3){
                $boxLink = route('index-subscribers');
            }
            @endphp 
            <div class="col-sm-6 col-xl-3">
                <div class="card alert-analytics-card alert-stat-card {{ $index == 3 ? 'light' : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="alert-stat-label">{{ $card['label'] }}</div>
                                <div class="alert-stat-value">{{ number_format($card['value']) }}</div>
                                <a href="{{ $boxLink }}"><small class="{{ $index == 3 ? 'text-muted' : 'text-white-50' }}">
                                    View Details <i class="mdi mdi-arrow-right"></i>
                                </small></a>
                            </div>
                            <span class="alert-stat-icon">
                                <i class="{{ $card['icon'] }}"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6 col-xl-3">
            <div class="card alert-analytics-card alert-chart-box">
                <div class="card-body">
                    <div class="alert-chart-title">Weekly</div>
                    <div id="weekly-alert-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card alert-analytics-card alert-chart-box">
                <div class="card-body">
                    <div class="alert-chart-title">Monthly</div>
                    <div id="monthly-alert-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card alert-analytics-card alert-chart-box">
                <div class="card-body">
                    <div class="alert-chart-title">Quarterly</div>
                    <div id="quarterly-alert-chart"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card alert-analytics-card alert-chart-box">
                <div class="card-body">
                    <div class="alert-chart-title">Yearly</div>
                    <div id="yearly-alert-chart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-4">
            <div class="card alert-analytics-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="alert-chart-title mb-0">Highest Interest States (Top 5)</div>
                    </div>

                    @forelse($topStates as $state)
                        <div class="country-row">
                            <span>{{ $state['name'] }}</span>
                            <span class="country-count">{{ number_format($state['count']) }}</span>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4">No alert location data found.</div>
                    @endforelse

                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card alert-analytics-card h-100">
                <div class="card-body">
                    <div class="alert-chart-title">Regional Hub</div>
                    <div id="weekly-alert-map"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
