@php
    $pageName = 'Dashboard'; 
    $tableHead = ['Full Name' , 'Machine Name' , 'Reading Number' , 'Fuel in Liters'   ];
    $tableHeadSecond = ['Full Name' , 'Machine Name' ,  'Working Hours'  ];
@endphp
@extends('layouts.backend')
@section('content')
<x-reusables.app-header pageName="{{ $pageName }}"  />
    <div class="content  mx-0 w-100">
        <div class="block block-rounded">
            <div class="block-content block-content-full">
                <div class="row items-push">
                    <div class="col-sm-4 col-xxl-4">
                        <!-- New Customers -->
                        <div class="block block-rounded d-flex flex-column h-100 mb-0">
                            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                                <dl class="mb-0">
                                    <dt class="fs-3 fw-bold">{{$total_groups}}</dt>
                                    <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">Total Groups</dd>
                                </dl>
                                <div class="item item-rounded-lg bg-body-light">
                                    <i class="fa-solid fa-people-line text-danger"></i>
                                </div>
                            </div>
                            <div class="bg-body-light rounded-bottom">
                                <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between text-danger" href="{{route('index-group')}}">
                                    <span class="text-secondary">View all Groups</span>
                                    <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                                </a>
                            </div>
                        </div>
                        <!-- END New Customers -->
                    </div>
                    <div class="col-sm-4 col-xxl-4">
                        <!-- Conversion Rate -->
                        <div class="block block-rounded d-flex flex-column h-100 mb-0">
                            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                                <dl class="mb-0">
                                    <dt class="fs-3 fw-bold">{{$total_machines}}</dt>
                                    <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">Total Machines</dd>
                                </dl>
                                <div class="item item-rounded-lg bg-body-light">
                                    <i class="fa-solid fa-chart-simple text-danger"></i>
                                </div>
                            </div>
                            <div class="bg-body-light rounded-bottom">
                                <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between text-danger" href="{{route('index-machinery')}}">
                                    <span class="text-secondary">View all Machines</span>
                                    <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                                </a>
                            </div>
                        </div>
                        <!-- END Conversion Rate-->
                    </div>
                    <div class="col-sm-4 col-xxl-4">
                        <!-- Messages -->
                        <div class="block block-rounded d-flex flex-column h-100 mb-0">
                            <div class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                                <dl class="mb-0">
                                    <dt class="fs-3 fw-bold">{{$total_users}}</dt>
                                    <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">Total Users </dd>
                                </dl>
                                <div class="item item-rounded-lg bg-body-light">
                                    <i class="fa-solid fa-users text-danger"></i>
                                </div>
                            </div>
                            <div class="bg-body-light rounded-bottom">
                                <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between text-danger" href="{{route('index-user')}}">
                                    <span class="text-secondary">View all Users</span>
                                    <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                                </a>
                            </div>
                        </div>
                        <!-- END Messages -->
                    </div>
                </div>
                <div class="row mt-4 ">
                    <div class="col-sm-7">
                        <div class="table-responsive">
                            <!-- DataTables init on table by adding .js-dataTable-full class, functionality is initialized in js/pages/tables_datatables.js -->
                            <p class="text-center text-danger">LATEST FUEL CONSUMPTION</p>
                            <table class="table table-bordered table-striped  fs-sm">
                               {{-- <h5 class="text-center">Latest Fuel Consumtion</h5>  --}}
                                <x-reusables.table-header :tableHead="$tableHead"/>
                                <tbody>
                                        @foreach ($latest_fuel_consumption as $data)
                                            <tr>
                                                <td class="whitespace-nowrap ">
                                                    {{ $data->user->name ?? ''}}
                                                </td>
                                                <td class="whitespace-nowrap ">
                                                    {{ $data->machinery->machine_name ?? '' }}
                                                </td>
                                                <td class="whitespace-nowrap ">
                                                    {{ $data->reading_number }}
                                                </td>
                                                <td class="whitespace-nowrap ">
                                                    {{ $data->fuel_in_liters }}
                                                </td>
                                            </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        {{-- <h5 class="text-center">Latest Working Hours</h5>  --}}
                        <div class="table-responsive">
                            <!-- DataTables init on table by adding .js-dataTable-full class, functionality is initialized in js/pages/tables_datatables.js -->
                            <p class="text-center text-danger">LATEST WORKING HOURS</p>
                            <table class="table table-bordered table-striped  fs-sm">
                                <x-reusables.table-header :tableHead="$tableHeadSecond"/>
                                <tbody>
                                        @foreach ($machine_working_hours as $data)
                                            <tr>
                                                <td class="whitespace-nowrap ">
                                                    {{ $data->user->name ?? ''}}
                                                </td>
                                                <td class="whitespace-nowrap ">
                                                    {{ $data->machinery->machine_name ?? '' }}
                                                </td>
                                                <td class="whitespace-nowrap">
                                                    {{ $data->closing_hours - $data->opening_hours  }}
                                                </td>
                                            </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
