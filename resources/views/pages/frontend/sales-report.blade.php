@extends('layouts.frontend')
@push('style')
<link rel="stylesheet" href="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.css') }} ">
	<link rel="stylesheet" href="{{ asset('assets_front/css/filter-css.css') }} ">
@endpush
@push('script')
    <script src="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.js') }}"></script>
@endpush
@section('content')
<div class="wrapper wrapper-body">
	<div class="dashboard-body orders-page">
		<div class="container-fluid">
			<div class="col-md-12 d-flex align-items-center justify-content-between">
				<div class="d-main-title">
					<h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>Sales Report</h3>
                    @php
                        $breadcrumbs = [
                            [
                                'name' => 'Sales Report', 
                                'url' => route('front-sales-report')
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
                    <div class="filter-card my-4">
                        <div class="card-body p-4">
                            <div class="filter-card-header mb-3">
                                <i class="fa fa-filter me-2"></i>
                                <span>Filters</span>
                            </div>
                            <form class="filter-form">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label filter-label">Select Event</label>
                                        <select class="selectpicker filter-field w-100" name="event" title="Select Event">
                                            @foreach($events as $event)
                                                <option value="{{ $event->id }}" @selected(request()->event == $event->id)>
                                                    {{ $event->event_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label filter-label">Start Date</label>
                                        <div class="filter-input-wrap">
                                            <i class="fa fa-calendar filter-icon"></i>
                                            <input class="form-control filter-field only_date_picker" value="{{ $start_date }}" name="start_date" type="text" placeholder="Order Start Date">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label filter-label">End Date</label>
                                        <div class="filter-input-wrap">
                                            <i class="fa fa-calendar filter-icon"></i>
                                            <input class="form-control filter-field only_date_picker" value="{{ $end_date }}" name="end_date" type="text" placeholder="Order End Date">
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex gap-2">
                                        <button class="btn btn-filter-search flex-grow-1" type="submit">
                                            <i class="fa fa-filter me-2"></i>Filter
                                        </button>
                                        <a href="{{ url()->current() }}" class="btn btn-filter-reset" title="Reset">
                                            <i class="fa fa-rotate-left"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                            <small class="text-muted mt-3 d-block">This report includes only paid orders (tickets or donations). Cancelled or refunded tickets are excluded.</small>
                        </div>
                    </div>
                    <!-- <div class="main-card mt-0">
                        <div class="dashboard-wrap-content p-4">
                            <form class="filter-form">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group dropdown bootstrap-select dropdown"> 
                                            <select class="selectpicker" name="event">
                                                <option disabled selected>Select Event</option>
                                                @foreach($events as $event)
                                                    <option value="{{ $event->id }}" @selected(request()->event == $event->id)>
                                                        {{ $event->event_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input class="form-control h_40 w-100 only_date_picker" value="{{$start_date}}" name="start_date" type="text" placeholder="Order Start Date">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input class="form-control h_40 w-100 only_date_picker" value="{{$end_date}}" name="end_date" type="text" placeholder="Order End Date">
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
                            <div class="col-sm-12">
                                <small>This report includes only paid orders (tickets or donations). Cancelled or refunded tickets are excluded.</small>
                            </div>
                        </div>
                    </div> -->
                    @php 
                        /* $total_amt_all = 0;
                        $total_fee_all = 0;
                        $total_donation = 0; */

                        $total_amt_all = 0;
                        $total_platform_fee_all = 0;
                        $total_processing_fee_all = 0;
                        $total_donation = 0;
                        $has_old_or_zero_processing_orders = false;
                        if($orders->count()>0){
                            foreach ($orders as $order){
                                $total_donation += $order->donation_amount>0?$order->donation_amount:0;
                                foreach($order->tickets as $ticket){
                                    if($ticket->rsvp_confirmation==1){
                                        $total_amt_all += $ticket->price;
                                        //$total_fee_all += $ticket->ticket_platform_fee;

                                        $total_platform_fee_all += (float) $ticket->ticket_platform_fee;
                                        if (!is_null($order->processing_fee_per) && (float) $order->processing_fee_per > 0) {
                                            $total_processing_fee_all += (float) ($ticket->ticket_processing_fee ?? 0);
                                        } else {
                                            $has_old_or_zero_processing_orders = true;
                                        }
                                    }
                                }
                            }
                        }
                    @endphp
                    <div class="main-card mt-4">
                        <div class="dashboard-wrap-content">
                            <div class="dashboard-report-content">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-6 col-md-6">
                                        <div class="dashboard-report-card purple">
                                            <div class="card-content">
                                                <div class="card-content">
                                                    <span class="card-title fs-6">Total Amount</span>
                                                    <span class="card-sub-title fs-3">${{round($total_amt_all,2)}}</span>
                                                </div>
                                                <div class="card-media">
                                                    <i class="fa-solid fa-money-bill"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-md-6">
                                        <div class="dashboard-report-card red">
                                            <div class="card-content">
                                                <div class="card-content">
                                                    <span class="card-title fs-6">Total Donation</span>
                                                    <span class="card-sub-title fs-3">${{round($total_donation,2)}}</span>
                                                </div>
                                                <div class="card-media">
                                                    <i class="fa-solid fa-box"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-md-6">
                                        <div class="dashboard-report-card info">
                                            <div class="card-content">
                                                <div class="card-content">
                                                    <span class="card-title fs-6">Total Fee</span>
                                                    <span class="card-sub-title fs-3">${{ round($total_platform_fee_all+$total_processing_fee_all, 2) }}</span>
                                                </div>
                                                <div class="card-media">
                                                    <i class="fa-solid fa-eye"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-6 col-md-6">
                                        <div class="dashboard-report-card success">
                                            <div class="card-content">
                                                <div class="card-content">
                                                    <span class="card-title fs-6">Total Paid Orders</span>
                                                    <span class="card-sub-title fs-3">{{$orders->count()}}</span>
                                                </div>
                                                <div class="card-media">
                                                    <i class="fa-solid fa-ticket"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                        <th scope="col">Donation</th>
                                                        <th scope="col">Order Amount</th>
                                                        <th scope="col">Platform / Processing Fee</th>
                                                        <th scope="col">Returned Order Amount</th>
                                                        <th scope="col">Returned Platform / Processing Fee</th>
                                                        <th scope="col">Actual Order Amount</th>
                                                        <th scope="col">Actual Platform / Processing Fee</th>
                                                        <th scope="col">Ordered At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($orders) && $orders->count()>0)
                                                    @foreach ($orders as $order)
                                                        @php
                                                        $total_amt = 0;
                                                        $re_total_amt = 0;
                                                        $a_total_amt = 0;
                                                        /* $total_fee = 0;
                                                        $re_total_fee = 0;
                                                        $a_total_fee = 0; */
                                                        $total_platform_fee = 0;
                                                        $total_processing_fee = 0;

                                                        $returned_platform_fee = 0;
                                                        $returned_processing_fee = 0;

                                                        $actual_platform_fee = 0;
                                                        $actual_processing_fee = 0;

                                                        $hasProcessingFee = !is_null($order->processing_fee_per)
                                                            && (float) $order->processing_fee_per > 0;
                                                        foreach($order->tickets as $ticket){
                                                            $total_amt += $ticket->price;
                                                            //$total_fee += $ticket->ticket_platform_fee;
                                                            $total_platform_fee += (float) $ticket->ticket_platform_fee;
                                                            if ($hasProcessingFee) {
                                                                $total_processing_fee += (float) ($ticket->ticket_processing_fee ?? 0);
                                                            }
                                                        }
                                                        $donationProcessingFee = $hasProcessingFee && (float) $order->donation_amount > 0
                                                            ? round(
                                                                ((float) $order->donation_amount * (float) $order->processing_fee_per) / 100,
                                                                2
                                                            )
                                                            : 0;

                                                        $total_processing_fee += $donationProcessingFee;
                                                        @endphp 
                                                        @if($total_amt<=0 && $order->donation_amount<=0)
                                                            @continue;
                                                        @endif
                                                        @php
                                                        foreach($order->tickets as $ticket){
                                                            if($ticket->rsvp_confirmation!=1){
                                                                $re_total_amt += $ticket->price;
                                                                //$re_total_fee += $ticket->ticket_platform_fee;
                                                                $returned_platform_fee += (float) $ticket->ticket_platform_fee;
                                                                if ($hasProcessingFee) {
                                                                    $returned_processing_fee += (float) ($ticket->ticket_processing_fee ?? 0);
                                                                }
                                                            }
                                                        }
                                                        $actual_platform_fee = $total_platform_fee - $returned_platform_fee;
                                                        $actual_processing_fee = $hasProcessingFee
                                                            ? $total_processing_fee - $returned_processing_fee
                                                            : 0;
                                                        $a_total_amt = $total_amt - $re_total_amt;
                                                        //$a_total_fee = $total_fee - $re_total_fee;
                                                        @endphp 
                                                    <tr>
                                                        <td><a href="{{route('front-order-details',[encrypt($order->id)])  }}" target="_blank">{{$order->order_number}}</a></td>
                                                        <td>{{config('app.currency').formatAmt($order->donation_amount)}}</td>
                                                        <td>{{config('app.currency').formatAmt($total_amt)}}</td>
                                                        
                                                        <td>
                                                            <small>
                                                            Platform Fee: {{ config('app.currency') }}{{ formatAmt($total_platform_fee) }} 
                                                            @if($hasProcessingFee)
                                                                <br/>
                                                                Processing Fee: {{ config('app.currency') }}{{ formatAmt($total_processing_fee) }} <br/>
                                                                Total Fees: {{ config('app.currency') }}{{ formatAmt($total_platform_fee + $total_processing_fee) }}
                                                            @endif
                                                            </small>
                                                        </td>

                                                        <td>{{config('app.currency').formatAmt($re_total_amt)}}</td>
                                                        
                                                        <td>
                                                            <small>
                                                            Return Platform Fee : {{ config('app.currency') }}{{ formatAmt($returned_platform_fee) }}
                                                            @if($hasProcessingFee)
                                                                <br/>
                                                                Return Processing Fee : {{ config('app.currency') }}{{ formatAmt($returned_processing_fee) }} <br/>
                                                                Return Total Fees : {{ config('app.currency') }}{{ formatAmt($returned_platform_fee + $returned_processing_fee) }}
                                                            @endif
                                                            </small>
                                                        </td>

                                                        <td>{{config('app.currency').formatAmt($a_total_amt)}}</td>
                                                        
                                                        <td>
                                                            <small>
                                                            Actual Platform Fee: {{ config('app.currency') }}{{ formatAmt($actual_platform_fee) }}
                                                            @if($hasProcessingFee)
                                                                <br/>
                                                                Actual Processing Fee: {{ config('app.currency') }}{{ formatAmt($actual_processing_fee) }} <br/>
                                                                Total Actual Fees: {{ config('app.currency') }}{{ formatAmt($actual_platform_fee + $actual_processing_fee) }}
                                                            @endif
                                                            </small>
                                                        </td>

                                                        <td>{{$order->createdDateTime}}</td>
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