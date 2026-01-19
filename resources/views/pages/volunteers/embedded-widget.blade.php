@php
    $pageName = 'Embedded Widget';
    $tableHead = ['Volunteer', 'Widget ID', 'Title', 'Location', 'Created Date', 'Status', 'Accessibility','Actions'];
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
                                <label for="from_date" class="form-label">Created From Date</label>
                                <input type="date" class="form-control" id="from_date" name="from_date"
                                    value="{{ $from_date }}">
                            </div>
                            <div class="form-group">
                                <label for="to_date" class="form-label">Created To Date</label>
                                <input type="date" class="form-control" id="to_date" name="to_date"
                                    value="{{ $to_date }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('embedded-widget') }}" class="btn btn-secondary">Reset</a>
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
                                <td>{{$data->title}}</td>
                                <td>{{$data->location}}</td>
                                <td>{{$data->created_at}}</td>
                                <td>{{$data->is_active==1?'Active':'Inactive'}}</td>
                                <td>{{$data->status==1?'Enabled':'Disabled'}}</td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-start action-button">
                                        <a class="showWidgetDetailBtn" url="{{route('view-widget-detail', encrypt($data->id))}}" href="javascript:void(0);">
                                            <i class="fs-5 mdi mdi-eye" title="View"></i>
                                        </a>
                                        <div class="dropdown">
                                            <button class="btn btn-link font-size-16 text-primary py-0 px-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bx bx-dots-horizontal-rounded"></i> 
                                            </button>
                                            <ul class="dropdown-menu drop-menu-end" style="">
                                                @if($data->status==1)
                                                <li class="dropdown-item">
                                                    <a href="{{route('change-status-widget', encrypt($data->id))}}">
                                                        <i class="fs-5 mdi mdi-order-bool-ascending"></i> <span class="text-muted">Disable Accessibility</span>    
                                                    </a>
                                                </li>
                                                @else 
                                                <li class="dropdown-item">
                                                    <a  href="{{route('change-status-widget', encrypt($data->id))}}">
                                                        <i class="fs-5 mdi mdi-order-bool-ascending"></i> <span class="text-muted">Enable Accessibility</span>    
                                                    </a>
                                                </li>
                                                @endif 
                                                <li class="dropdown-item">
                                                    <a href="javascript:void(0);" data-id="{{encrypt($data->id)}}" data-module="widget" data-name="{{$data->title}}" class="delete-record  js-swal-confirm">
                                                        <i class="fs-5 mdi mdi-delete"></i> <span class="text-muted">Delete</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
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
    {{-- Volunteer Details --}}
    <div id="showWidgetDetail" class="modal fade modal-lg detailed-modal-popup" tabindex="-1"
        aria-labelledby="view-report-modalLabel" aria-hidden="true" data-bs-scroll="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="view-report-modalLabel">Embedded Widget Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="showWidgetDetailBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="copyWidgetCode">Copy Code</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection
