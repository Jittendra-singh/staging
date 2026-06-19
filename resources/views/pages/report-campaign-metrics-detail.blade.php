@php
    $pageName = 'Campaign Metrics Detail';
    $tableHead = ['Event Name', 'Date', 'Organizer', 'Tickets Sold', 'Checked-in Volunteers', 'Action'];
@endphp

@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => 'Campaign Analytics'])

@section('css')
    <x-reusables.datatablecss />
    <x-reusables.advanceformcss />

    <style>
        .metrics-detail-row td {
            background: #f8f7ff;
            border-color: #e6e0ff;
            padding: 14px 18px;
        }

        .metrics-detail-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .metrics-detail-label {
            color: #6c757d;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .metrics-detail-value {
            color: #495057;
            font-size: 13px;
            font-weight: 400;
            min-width: 0;
        }

        .metrics-detail-url {
            display: block;
            max-width: 240px;
            color: #4656c9;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .metrics-eye-btn {
            border: 0;
            background: #eff0f6;
            color: #4656c9;
            width: 32px;
            height: 28px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            line-height: 1;
        }

        .metrics-eye-btn:hover {
            background: #e6e8f5;
            color: #3949ab;
        }

        .metrics-empty-state {
            min-height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #6c757d;
            font-size: 13px;
            font-weight: 500;
        }

        .metrics-empty-state i {
            display: block;
            color: #4656c9;
            font-size: 28px;
            margin-bottom: 8px;
            opacity: .75;
        }

        @media (max-width: 991px) {
            .metrics-detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575px) {
            .metrics-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('js')
    <x-reusables.datatablejs />
    <x-reusables.advanceformjs />

    <script>
        $('.select2').select2();

        $('#campaignMetricsDetailFilterForm').on('submit', function(e) {
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
    <div class="row">
        <div class="col-12">
            <x-reusables.frontend.alert />

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" id="campaignMetricsDetailFilterForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4 col-xl-2">
                                <label class="form-label" for="search">Search Events</label>
                                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search events...">
                            </div>

                            <div class="col-md-4 col-xl-2">
                                <label class="form-label" for="tag_id">Event Tag</label>
                                <select class="form-control select2" id="tag_id" name="tag_id">
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" @selected($selectedTagId == $tag->id)>{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 col-xl-2">
                                <label class="form-label" for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                            </div>

                            <div class="col-md-4 col-xl-2">
                                <label class="form-label" for="end_date">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>

                            <div class="col-md-4 col-xl-2">
                                <label class="form-label" for="category_id">Category</label>
                                <select class="form-control select2" id="category_id" name="category_id">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 col-xl-2">
                                <label class="form-label" for="organizer_id">Organizer</label>
                                <select class="form-control select2ajax" datatype="organiser" id="organizer_id" name="organizer_id">
                                    @if(request('organizer_id') && $selected_organizer)
                                        <option value="{{ request('organizer_id') }}" selected>
                                            {{ $selected_organizer->first_name.' '.$selected_organizer->last_name.' ('.$selected_organizer->email_address.')' }}
                                        </option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter-outline me-1"></i> Filter
                                </button>

                                <a href="{{ route('index-campaign-metrics-detail') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-refresh me-1"></i> Reset
                                </a>

                                <a href="{{ route('index-campaign-metrics-detail', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-success">
                                    <i class="mdi mdi-download me-1"></i> Export CSV
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="table-responsive">
                <table id="custom_datatable" sort="1" class="table table-bordered nowrap w-100 volunteer-records" dt-responsive>
                    <x-reusables.table-header :tableHead="$tableHead" />
                    <tbody>
                        @forelse($listingData as $event)
                            @php
                                $totalTickets = (int) ($event->total_tickets_count ?? 0);
                                $soldTickets = (int) ($event->sold_active_tickets_count ?? 0);
                                $checkedInTickets = (int) ($event->checked_in_tickets_count ?? 0);
                                $attendanceRate = $soldTickets > 0 ? round(($checkedInTickets / $soldTickets) * 100) : 0;
                                $eventUrl = $event->slug ? route('view-event-details', ['slug' => $event->slug]) : null;
                                $collapseId = 'eventMetricsDetail' . $event->id;
                            @endphp

                            <tr>
                                <td>{{ $event->event_name }}</td>
                                <td>{{ $event->start_datetime ? $event->start_datetime->format(config('app.dateFormat', 'M d, Y')) : '-' }}</td>
                                <td>
                                    {{ trim(@$event->creator->first_name.' '.@$event->creator->last_name) ?: '-' }}
                                    @if(@$event->creator->email_address)
                                        <br><small class="text-muted">{{ $event->creator->email_address }}</small>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($soldTickets) }}/{{ number_format($totalTickets) }}</td>
                                <td class="text-center"><span class="badge bg-primary">{{ number_format($checkedInTickets) }}</span></td>
                                <td class="text-center">
                                    <button type="button" class="metrics-eye-btn" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </button>
                                </td>
                            </tr>

                            <tr class="collapse metrics-detail-row" id="{{ $collapseId }}">
                                <td colspan="6">
                                    <div class="metrics-detail-grid">
                                        <div>
                                            <div class="metrics-detail-label">Event URL</div>
                                            <div class="metrics-detail-value">
                                                @if($eventUrl)
                                                    <a href="{{ $eventUrl }}" target="_blank" class="metrics-detail-url" title="{{ $eventUrl }}">
                                                        {{ $eventUrl }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </div>

                                        <div>
                                            <div class="metrics-detail-label">Location</div>
                                            <div class="metrics-detail-value">{{ $event->venue ?: '-' }}</div>
                                        </div>

                                        <div>
                                            <div class="metrics-detail-label">Attendance %</div>
                                            <div class="metrics-detail-value">{{ $attendanceRate }}%</div>
                                        </div>

                                        <div>
                                            <div class="metrics-detail-label">Debris (lbs)</div>
                                            <div class="metrics-detail-value text-primary">{{ $event->waste_collected ?: '0' }}</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="metrics-empty-state">
                                        <div>
                                            <i class="mdi mdi-calendar-search-outline"></i>
                                            No campaign metrics found.
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($listingData->hasPages())
                <div class="mt-3">
                    {{ $listingData->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection