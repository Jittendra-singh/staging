@php
    $pageName = 'Embedded Widget Report';
    $tableHead = ['Volunteer','Widget ID', 'Total View', 'Total Click', 'Total Order', 'Last Active Date', 'Widget Created Date'];
@endphp
@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $pageName])
@section('css')
    <x-reusables.datatablecss />
    <x-reusables.advanceformcss/>
@endsection
@section('js')
    <x-reusables.datatablejs />
    <x-reusables.advanceformjs />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <x-reusables.frontend.alert />
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET">
                        <div class="d-flex justify-content-start gap-3 align-items-end">
                            <div class="form-group">
                                <label for="event" class="form-label">Search & Select Volunteer</label>
                                <select class="form-control select2ajax" datatype="all_volunteers" id="volunteer_id" name="volunteer_id">
                                    @if(!empty($selected_volunteer))
                                        <option value="{{ $selected_volunteer->id }}" selected>
                                            {{ $selected_volunteer->first_name.' '.$selected_volunteer->last_name.' ('.$selected_volunteer->email_address.')' }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="from_date" class="form-label">Log From Date</label>
                                <input type="date" class="form-control" id="from_date" name="from_date"
                                    value="{{ $from_date }}">
                            </div>
                            <div class="form-group">
                                <label for="to_date" class="form-label">Log To Date</label>
                                <input type="date" class="form-control" id="to_date" name="to_date"
                                    value="{{ $to_date }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('embedded-widget-report') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @php 
            $total_views = 0;
            $total_clicks = 0;
            $total_orders = 0;
            $total_widgets = 0;
            if($listing_data->count()>0){
                foreach ($listing_data as $listing){
                    $total_views += $listing->view_logs->count();
                    $total_clicks += $listing->click_logs->count();
                    $total_orders += $listing->order_logs->count();
                    $total_widgets += 1;
                }
            }
        @endphp
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-h-100">
                        <!-- card body -->
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Widgets</span>
                                    <h4>
                                        <span class="counter-value" data-target="{{ $total_widgets }}">{{ $total_widgets }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->

                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-h-100">
                        <!-- card body -->
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Views</span>
                                    <h4>
                                        <span class="counter-value" data-target="{{ $total_views }}">{{ $total_views }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->

                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-h-100">
                        <!-- card body -->
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Clicks</span>
                                    <h4 >
                                        <span class="counter-value" data-target="{{ $total_clicks }}">{{ $total_clicks }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col-->

                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-h-100">
                        <!-- card body -->
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Orders</span>
                                    <h4 >
                                        <span class="counter-value" data-target="{{ $total_orders }}">{{ $total_orders }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->   
            </div>
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table id="custom_datatable" sort="1" class="table table-bordered nowrap w-100 volunteer-records"
                    dt-responsive>
                    <x-reusables.table-header :tableHead="$tableHead" />
                    <tbody>
                        @forelse ($listing_data as $data)
                            @php 
                            $last_view_log = $data->view_logs->last();
                            $lastActiveDate = isset($last_view_log->id)?date(config('app.dateFormat'),strtotime($last_view_log->created_at)):'-';
                            @endphp 
                            <tr>
                                <td>{{$data->volunteer->first_name}} {{$data->volunteer->last_name}}<br>
                                    ({{$data->volunteer->email_address}})
                                </td>
                                <td>{{$data->wid_number}}</td>
                                <td>{{$data->view_logs->count()}}</td>
                                <td>{{$data->click_logs->count()}}</td>
                                <td>{{$data->order_logs->count()}}</td>
                                <td>{{$lastActiveDate}}</td>
                                <td>{{$data->created_at}}</td>
                            </tr>
                        @empty
                            <x-reusables.empty-data :colspan="count($tableHead)" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div> <!-- end col -->
    </div>
    <!-- end row -->
@endsection
