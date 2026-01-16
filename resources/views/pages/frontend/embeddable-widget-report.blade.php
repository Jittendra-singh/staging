@extends('layouts.frontend')
@section('title')
    Registrations
@endsection
@push('style')
<style>
.bs-searchbox input::placeholder {
    content: "Start typing to search...";
}
.d-none {
    display: none !important;
}

</style>
@endpush
@section('content')
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            <div class="container-fluid">
                <!-- <div class="text-center mb-4">
                    <h1 class="d-main-title">Upcoming Feature</h1>
                    <p class="mb-0">This feature is under development as we will launch this feature soon.</p>
                </div> -->
                @if(true)
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-between">
                        <div class="d-main-title">
                            <h3><i class="fa-solid fa-calendar-days me-3"></i>Embedded Widget Report</h3>
                            <!-- <p class="mb-0 mt-3">You can send a message to your RSVP’s in the ‘Registration List’.</p> -->
                        </div>
                    </div>
                </div>
                <x-reusables.frontend.alert />
                <div class="col-md-12">
                    <div class="conversion-setup">
                        <div class="main-card mt-5">
                            <div class="dashboard-wrap-content p-4">
                                <div class="table-card mt-4">
                                    <div class="main-table">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">Widget ID</th>
                                                        <th scope="col">Total View</th>
                                                        <th scope="col">Total Click</th>
                                                        <th scope="col">Total Order</th>
                                                        <th scope="col">Last Active Date</th>
                                                        <th scope="col">Created Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($listing_data as $data)
                                                        @php 
                                                        $last_view_log = $data->view_logs->last();
                                                        $lastActiveDate = isset($last_view_log->id)?date(config('app.dateFormat'),strtotime($last_view_log->created_at)):'-';
                                                        @endphp 
                                                        <tr class="single-tr">										
                                                            <td>{{$data->wid_number}}</td>								
                                                            <td>{{$data->view_logs->count()}}</td>	
                                                            <td>{{$data->click_logs->count()}}</td>	
                                                            <td>{{$data->order_logs->count()}}</td>	
                                                            <td>{{$lastActiveDate}}</td>
                                                            <td>{{date(config('app.dateFormat'),strtotime($data->created_at))}}</td>
                                                        </tr>
                                                    @endforeach
                                                    @if($listing_data->isEmpty())
                                                        <tr>
                                                            <td colspan="6" class="text-center">No widget found.</td>
                                                        </tr>
                                                    @endif
                                                </tbody>									
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>							
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
<x-reusables.google :locationarray="[]"/>
@endsection
@push('script')
    <x-reusables.advanceformjs />

@endpush