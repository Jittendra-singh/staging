@php
    $pageName = 'Weekly Alerts';
    $tableHead = ['Alert Name', 'Location (Zipcode)', 'Created By', 'Created Date', 'Status', 'Action'];

    $counterCards = [
        ['label' => 'Total Alerts', 'value' => $counters->total_alerts, 'icon' => 'mdi mdi-bell-outline'],
        ['label' => 'New This Week', 'value' => $counters->new_this_week, 'icon' => 'mdi mdi-calendar-week'],
        ['label' => 'New This Month', 'value' => $counters->new_this_month, 'icon' => 'mdi mdi-calendar-month'],
        ['label' => 'New This Year', 'value' => $counters->new_this_year, 'icon' => 'mdi mdi-calendar'],
        ['label' => 'Active Alerts', 'value' => $counters->active_alerts, 'icon' => 'mdi mdi-check-circle-outline'],
        ['label' => 'Disabled Alerts', 'value' => $counters->disabled_alerts, 'icon' => 'mdi mdi-minus-circle-outline'],
        ['label' => 'Disabled This Week', 'value' => $counters->disabled_this_week, 'icon' => 'mdi mdi-calendar-week'],
        ['label' => 'Disabled This Month', 'value' => $counters->disabled_this_month, 'icon' => 'mdi mdi-calendar-month'],
    ];
@endphp

@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $pageName])

@section('css')
    <x-reusables.datatablecss />
    <x-reusables.advanceformcss />

    <style>
        .alert-counter-card {
            background: linear-gradient(135deg, #5865d9 0%, #3949ab 100%);
            border: 0;
            border-radius: 8px;
            color: #fff;
            /* min-height: 118px;*/
            min-height: 100px;
        }

        .alert-counter-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .alert-counter-label {
            font-size: 13px;
            opacity: .88;
            margin-bottom: 4px;
        }

        .alert-counter-value {
            font-size: 26px;
            font-weight: 700;
            line-height: 1.1;
        }

        .alert-status-meta {
            display: block;
            margin-top: 5px;
            font-size: 11px;
            color: #6c757d;
        }

        .alert-disabled-by {
            border-bottom: 1px dotted #6c757d;
            cursor: help;
        }
    </style>
@endsection

@section('js')
    <x-reusables.datatablejs />
    <x-reusables.advanceformjs />
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-reusables.frontend.alert />

            <div class="row g-3 mb-3">
                @foreach($counterCards as $card)
                    <div class="col-sm-6 col-xl-3">
                        <div class="card alert-counter-card">
                            <div class="card-body">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="alert-counter-icon">
                                        <i class="{{ $card['icon'] }}"></i>
                                    </span>
                                    <div>
                                        <div class="alert-counter-label">{{ $card['label'] }}</div>
                                        <div class="alert-counter-value">{{ number_format($card['value']) }}</div>
                                        <!-- <small class="text-white-50">View Details <i class="mdi mdi-arrow-right"></i></small> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4 col-xl-2">
                                <label for="alert_name" class="form-label">Search By Alert Name</label>
                                <input type="text" class="form-control" id="alert_name" name="alert_name" value="{{ request('alert_name') }}" placeholder="Enter alert name">
                            </div>

                            <div class="col-md-4 col-xl-2">
                                <label for="user_id" class="form-label">Search By User</label>
                                <select class="form-control select2ajax" datatype="all_volunteers" id="user_id" name="user_id">
                                    @if(request('user_id') && $selected_user)
                                        <option value="{{ request('user_id') }}" selected>
                                            {{ $selected_user->first_name.' '.$selected_user->last_name.' ('.$selected_user->email_address.')' }}
                                        </option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4 col-xl-2">
                                <label for="user_type" class="form-label">User Type</label>
                                <select class="form-control select2" id="user_type" name="user_type">
                                    <option value="">All</option>
                                    <option value="volunteer" @selected(request('user_type') == 'volunteer')>Volunteer</option>
                                    <option value="organizer" @selected(request('user_type') == 'organizer')>Organizer</option>
                                </select>
                            </div>

                            <div class="col-md-4 col-xl-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control select2" id="status" name="status">
                                    <option value="">All</option>
                                    <option value="1" @selected(request('status') === '1')>Enable</option>
                                    <option value="0" @selected(request('status') === '0')>Disabled</option>
                                </select>
                            </div>

                            <div class="col-md-4 col-xl-2">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                            </div>

                            <div class="col-md-4 col-xl-2">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter-outline me-1"></i> Apply Filters
                                </button>
                                <a href="{{ route('index-weekly-alerts') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-refresh me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="table-responsive">
                <table id="custom_datatable" sort="3" class="table table-bordered nowrap w-100 volunteer-records" dt-responsive>
                    <x-reusables.table-header :tableHead="$tableHead" />
                    <tbody>
                        @forelse($listingData as $data)
                            @php
                                $creator = $data->volunteer;
                                $creatorName = $creator ? $creator->name : 'N/a';
                                $creatorEmail = $creator ? $creator->email_address : null;
                                $cityZip = $data->location ? $data->location : null;
                                $locationText = $cityZip
                                    ? $cityZip->zipcode.' ('.$cityZip->city.', '.$cityZip->state.')'
                                    : $data->zipcode;
                            @endphp

                            <tr>
                                <td>{{ $data->alert_name }}</td>
                                <td>{{ $locationText }}</td>
                                <td>
                                    {{ $creatorName }}
                                    @if($creatorEmail)
                                        <br><small class="text-muted">{{ $creatorEmail }}</small>
                                    @endif
                                    @if($creator)
                                        <br>
                                        <small class="{{ $creator->is_organiser ? 'text-primary' : 'text-success' }}">
                                            {{ $creator->is_organiser ? 'Organizer' : 'Volunteer' }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    {{ $data->created_at ? date(config('app.dateTimeFormat'), strtotime($data->created_at)) : '' }}
                                </td>
                                <td>
                                    @php
                                        $disabledByName = 'N/a';

                                        if (!$data->status) {
                                            if ($data->status_updated_by_role == 2) {
                                                $disabledByName = @$data->statusUpdatedByAdmin->name ?: 'Admin';
                                            } else {
                                                $disabledByName = @$data->statusUpdatedByVolunteer->name ?: @$data->volunteer->name ?: 'Subscriber';
                                            }
                                        }

                                        $canToggleStatus = $data->status == 1 || ($data->status == 0 && $data->status_updated_by_role == 2);
                                    @endphp

                                    @if($canToggleStatus)
                                        <x-reusables.list-switch
                                            :onlabel="'Enable'"
                                            :offlabel="'Disabled'"
                                            :data="$data"
                                            url="{{ route('toggle-weekly-alert-status', ['placeholder' => $data->encrypted_id]) }}"
                                        />
                                    @else
                                        <div class="form-check form-switch form-switch-md mb-1" dir="ltr">
                                            <input class="form-check-input" type="checkbox" disabled>
                                        </div>
                                    @endif

                                    @if(!$data->status)
                                        <span class="alert-status-meta">
                                            By
                                            <span class="alert-disabled-by" data-bs-toggle="tooltip" data-bs-original-title="Disabled at {{ $data->updated_at ? date(config('app.dateTimeFormat'), strtotime($data->updated_at)) : 'N/a' }}">
                                                {{ $disabledByName }}
                                            </span>
                                        </span>
                                    @endif
                                </td>

                                <!-- <td>
                                    @if($data->status)
                                        <span class="badge bg-success">Enable</span>
                                    @else
                                        <span class="badge bg-danger">Disabled</span>
                                        <span class="alert-status-meta">
                                            by
                                            <span class="alert-disabled-by" data-bs-toggle="tooltip" data-bs-original-title="Disabled at {{ $data->updated_at ? date(config('app.dateTimeFormat'), strtotime($data->updated_at)) : 'N/a' }}">
                                                {{ $creatorName }}
                                            </span>

                                        </span>
                                    @endif
                                </td> -->
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" target="_blank" href="{{ route('view-events', ['sort_by_date' => 1, 'location' => $data->zipcode, 'latitude' => $data->location_lat, 'longitude' => $data->location_long, 'distance' => '15', 'unit' => 'mi']) }}">
                                                <i class="mdi mdi-eye-outline me-1"></i> View Events
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <x-reusables.empty-data :colspan="count($tableHead)" />
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-2" data-aos="fade-up" data-aos-duration="800">
                    {{ $listingData->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
