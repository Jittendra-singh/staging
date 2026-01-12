@extends('layouts.frontend')
@push('style')
<link rel="stylesheet" href="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.css') }} ">
@endpush
@push('script')
    <script src="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.js') }}"></script>
@endpush
@section('content')
<div class="wrapper wrapper-body">
	<div class="dashboard-body orders-page">
		<div class="container-fluid">
			<div class="col-md-12 d-flex align-items-center justify-content-between mb-5">
				<div class="d-main-title">
					<h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>Orders</h3>
                    @php
                        $breadcrumbs = [
                            [
                                'name' => 'Orders', 
                                'url' => route('front-orders')
                            ],
                        ]
                    @endphp     
                    <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                            {{-- <p class="mb-0 mt-2">Here you will see the order which has been placed by you.</p> --}}
				    
                </div>
			</div>
		</div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
                    <x-reusables.frontend.alert />
                    <div class="main-card mt-0">
                        <div class="dashboard-wrap-content p-4">
                            <form class="filter-form">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group dropdown bootstrap-select dropdown"> 
                                            <select class="selectpicker" name="event_id">
                                                <option disabled selected value="">Select Event</option>
                                                @php 
                                                if(isset($events) && isset($events[0])){
                                                    foreach($events as $event){
                                                        echo '<option '.(request()->event_id == $event['id']?'selected':'').' value="'.$event['id'].'">'.$event['name'].'</option>';
                                                    }
                                                }
                                                @endphp 
                                            </select>
                                        </div> 
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input class="form-control h_40 w-100 only_date_picker" value="{{request()->start_date}}" name="start_date" type="text" placeholder="Order Start Date">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input class="form-control h_40 w-100 only_date_picker" value="{{request()->end_date}}" name="end_date" type="text" placeholder="Order End Date">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="rs mt_r4 ps-3">
                                            <div class="nav custom2-tabs btn-group justify-content-start gap-2 row-gap-3" >
                                                <button class="primary-btn w-fit" type="submit" >Filter</button>
                                                <a href="{{ url()->current() }}" class="primary-btn w-fit">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
					<div class="conversion-setup mt-4">
						<div class="main-card">
							<div class="dashboard-wrap-content p-4">
                                <div class="table-card mt-4">
                                    <div class="main-table">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">Order Number</th>
                                                        <th scope="col">Event Name</th>
                                                        <th scope="col">Tickets</th>
                                                        <th scope="col">Ordered At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($orders->count()>0)
                                                    @foreach ($orders as $order)
                                                    <tr>
                                                        <td>{{$order->order_number}}</td>
                                                        <td>{{$order->event->event_name}}</td>
                                                        <td>{{$order->total_tickets}} Ticket{{ $order->total_tickets>1?'s':'' }}</td>
                                                        <td>{{$order->createdDateTime}}</td>
                                                        <td>
                                                            <a title="View Order Details" href="{{route('front-order-details',[
                                                            'placeholder'=>$order->encryptedId])}}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" />
                                                                    <circle cx="12" cy="12" r="3" />
                                                                </svg>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center">No order available</td>
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
			</div>
		</div>
	</div>
</div>

@endsection