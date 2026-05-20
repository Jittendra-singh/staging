@php
    $pageName = 'Weekly Alert Analytics';

    $counterCards = [
        [
            'label' => 'Total Alerts',
            'value' => $counters->total_alerts,
            'icon' => 'mdi mdi-bell-outline',
            'url' => route('index-weekly-alerts'),
            'items' => [
                'Enable' => $counters->active_alerts,
                'Disabled' => $counters->inactive_alerts,
            ],
        ],
        [
            'label' => 'Disabled Alerts',
            'value' => $counters->disabled_alerts,
            'icon' => 'mdi mdi-bell-off-outline',
            'url' => route('index-weekly-alerts',['status'=>'0']),
            'items' => [
                'W' => $counters->disabled_week,
                'M' => $counters->disabled_month,
                'Q' => $counters->disabled_quarter,
                'Y' => $counters->disabled_year,
            ],
        ],
        [
            'label' => 'New Alerts',
            'value' => $counters->new_alerts,
            'icon' => 'mdi mdi-bell-plus-outline',
            'url' => route('index-weekly-alerts', ['start_date' => date('Y-01-01')]),
            'items' => [
                'W' => $counters->new_alerts_week,
                'M' => $counters->new_alerts_month,
                'Q' => $counters->new_alerts_quarter,
                'Y' => $counters->new_alerts_year,
            ],
        ],
        [
            'label' => 'Subscribers',
            'value' => $counters->subscribers,
            'icon' => 'mdi mdi-account-group-outline',
            'url' => route('index-subscribers'),
            'items' => [
                'W' => $counters->subscribers_week,
                'M' => $counters->subscribers_month,
                'Q' => $counters->subscribers_quarter,
                'Y' => $counters->subscribers_year,
            ],
        ],
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
        #weekly-alert-map {
            position: relative;
            overflow: hidden;
        }

        .state-count-label {
            position: absolute;
            z-index: 5;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, .92);
            border: 1px solid rgba(70, 86, 201, .18);
            border-radius: 6px;
            color: #1f2937;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.15;
            padding: 3px 6px;
            white-space: nowrap;
            pointer-events: none;
            box-shadow: 0 4px 10px rgba(15, 23, 42, .08);
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .alert-stat-bottom {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 12px;
            font-size: 11px;
            color: rgba(255, 255, 255, .78);
        }

        .alert-stat-bottom span {
            white-space: nowrap;
        }

        .alert-stat-card.light .alert-stat-bottom {
            color: #6b7280;
        }

        .alert-stat-bottom strong {
            color: inherit;
            font-weight: 700;
        }

        .alert-stat-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 12px;
        }

        .alert-stat-footer .alert-stat-bottom {
            margin-top: 0;
        }

        .alert-stat-detail-link {
            color: rgba(255, 255, 255, .82);
            font-size: 11px;
            font-weight: 600;
            text-decoration: underline;
            white-space: nowrap;
        }

        .alert-stat-detail-link:hover {
            color: #fff;
        }

        .alert-stat-card.light .alert-stat-detail-link {
            color: #4656c9;
        }

        .alert-stat-card.light .alert-stat-detail-link:hover {
            color: #25339b;
        }
        .alert-stat-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 12px;
            width: 100%;
        }

        .alert-stat-footer .alert-stat-bottom {
            margin-top: 0;
            flex: 1;
            min-width: 0;
        }

        .alert-stat-detail-link {
            margin-left: auto;
            text-align: right;
            color: rgba(255, 255, 255, .82);
            font-size: 11px;
            font-weight: 600;
            text-decoration: underline;
            white-space: nowrap;
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
        var stateLabels = @json($stateLabels);

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
            },
            onViewportChange: function() {
                window.setTimeout(renderStateLabels, 80);
            }
        });

        function renderStateLabels() {
            var $map = $('#weekly-alert-map');
            var mapObject = $map.vectorMap('get', 'mapObject');
            var mapWidth = $map.width();
            var mapHeight = $map.height();
            var padding = 8;

            $map.find('.state-count-label').remove();

            stateLabels.forEach(function(item) {
                if (!item.lat || !item.lng) {
                    return;
                }

                var point = mapObject.latLngToPoint(item.lat, item.lng);

                if (!point) {
                    return;
                }
                if (item.count<=10) {
                    return;
                }

                if (
                    point.x < padding ||
                    point.y < padding ||
                    point.x > (mapWidth - padding) ||
                    point.y > (mapHeight - padding)
                ) {
                    return;
                }

                var $label = $('<div class="state-count-label"></div>')
                    .text(item.name + ': ' + item.count)
                    .appendTo($map);

                var labelWidth = $label.outerWidth();
                var labelHeight = $label.outerHeight();

                var left = Math.max(labelWidth / 2 + padding, Math.min(point.x, mapWidth - (labelWidth / 2) - padding));
                var top = Math.max(labelHeight / 2 + padding, Math.min(point.y, mapHeight - (labelHeight / 2) - padding));

                $label.css({
                    left: left + 'px',
                    top: top + 'px'
                });
            });
        }


        window.setTimeout(renderStateLabels, 300);

        $(window).on('resize', function() {
            window.setTimeout(renderStateLabels, 200);
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
                                <!-- <a href="{{ $boxLink }}"><small class="{{ $index == 3 ? 'text-muted' : 'text-white-50' }}">
                                    View Details <i class="mdi mdi-arrow-right"></i>
                                </small></a> -->


                            </div>
                            <span class="alert-stat-icon">
                                <i class="{{ $card['icon'] }}"></i>
                            </span>
                        </div>
                        <div class="alert-stat-footer">
                            <div class="alert-stat-bottom">
                                @foreach($card['items'] as $itemLabel => $itemValue)
                                    <span>{{ $itemLabel }}: <strong>{{ number_format($itemValue) }}</strong></span>
                                @endforeach
                            </div>

                            <a target="_blank" href="{{ $card['url'] }}" class="alert-stat-detail-link">Details</a>
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
