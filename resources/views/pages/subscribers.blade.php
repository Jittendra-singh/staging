@php
    $pageName = 'Subscribers';
    $tableHead = ['Subscriber', 'User Type', 'Alert Counts', 'Status'];

    $counterCards = [
        ['label' => 'Total Subscribers', 'value' => $counters->total_subscribers, 'icon' => 'mdi mdi-account-group-outline'],
        ['label' => 'Inactive Subscribers', 'value' => $counters->inactive_subscribers, 'icon' => 'mdi mdi-account-off-outline'],
        ['label' => 'New This Month', 'value' => $counters->new_this_month, 'icon' => 'mdi mdi-calendar-month'],
        ['label' => 'New This Year', 'value' => $counters->new_this_year, 'icon' => 'mdi mdi-calendar'],
    ];
@endphp

@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $pageName])

@section('css')
    <x-reusables.datatablecss />
    <x-reusables.advanceformcss />

    <style>
        .subscriber-counter-card {
            background: linear-gradient(135deg, #5865d9 0%, #3949ab 100%);
            border: 0;
            border-radius: 8px;
            color: #fff;
            /* min-height: 118px; */
            min-height: 100px;
        }

        .subscriber-counter-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .subscriber-counter-label {
            font-size: 13px;
            opacity: .88;
            margin-bottom: 4px;
        }

        .subscriber-counter-value {
            font-size: 26px;
            font-weight: 700;
            line-height: 1.1;
        }

        .subscriber-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #eef1ff;
            color: #4656c9;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex: 0 0 36px;
        }

        .subscriber-title {
            font-weight: 600;
            color: #343a40;
        }

        .subscriber-subtitle {
            font-size: 12px;
            color: #74788d;
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
                        <div class="card subscriber-counter-card">
                            <div class="card-body">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="subscriber-counter-icon">
                                        <i class="{{ $card['icon'] }}"></i>
                                    </span>
                                    <div>
                                        <div class="subscriber-counter-label">{{ $card['label'] }}</div>
                                        <div class="subscriber-counter-value">{{ number_format($card['value']) }}</div>
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
                            <div class="col-md-4">
                                <label for="user_id" class="form-label">Search By User</label>
                                <select class="form-control select2ajax" datatype="all_volunteers" id="user_id" name="user_id">
                                    @if(request('user_id') && $selected_user)
                                        <option value="{{ request('user_id') }}" selected>
                                            {{ $selected_user->first_name.' '.$selected_user->last_name.' ('.$selected_user->email_address.')' }}
                                        </option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="user_type" class="form-label">User Type</label>
                                <select class="form-control select2" id="user_type" name="user_type">
                                    <option value="">All</option>
                                    <option value="volunteer" @selected(request('user_type') == 'volunteer')>Volunteer</option>
                                    <option value="organizer" @selected(request('user_type') == 'organizer')>Organizer</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control select2" id="status" name="status">
                                    <option value="">All</option>
                                    <option value="1" @selected(request('status') === '1')>Active</option>
                                    <option value="0" @selected(request('status') === '0')>Inactive</option>
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter-outline me-1"></i> Apply Filters
                                </button>
                                <a href="{{ route('index-subscribers') }}" class="btn btn-secondary">
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
                <table id="custom_datatable" sort="1" class="table table-bordered nowrap w-100 volunteer-records" dt-responsive>
                    <x-reusables.table-header :tableHead="$tableHead" />
                    <tbody>
                        @forelse($listingData as $data)
                            @php
                                $initial = strtoupper(substr($data->first_name ?: $data->email_address, 0, 1));
                            @endphp

                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="subscriber-avatar">{{ $initial }}</span>
                                        <div>
                                            <div class="subscriber-title">{{ $data->name }}</div>
                                            <div class="subscriber-subtitle">{{ $data->email_address }}</div>
                                        </div>
                                    </div>
                                </td>
                                <!-- <td>{{ $data->email_address }}</td> -->
                                <td>
                                    @if($data->is_organiser == 1)
                                        <span class="text-primary">Organizer</span>
                                    @else
                                        <span class="text-success">Volunteer</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ number_format($data->alerts_count) }}</span>
                                </td>
                                <td>
                                    @if($data->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
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
