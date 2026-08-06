@php
    $pageName = 'Sales Report';
    $tableHead = ['Order Number', 'Donation', 'Order Amount', 'Platform / Processing Fee', 'Returned Order Amount', 'Returned Platform / Processing Fee','Actual Order Amount', 'Actual Platform / Processing Fee', 'Ordered At'];
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
                                <label for="event" class="form-label">Search & Select Organizer</label>
                                <select class="form-control select2ajax" datatype="all_volunteers" id="purchased_by_id" name="org_id">
                                    @if(!empty($selected_org))
                                        <option value="{{ $selected_org->id }}" selected>
                                            {{ $selected_org->first_name.' '.$selected_org->last_name.' ('.$selected_org->email_address.')' }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="event" class="form-label">Search & Select Event</label>
                                <select class="form-control select2ajax" datatype="event" id="event_select" name="event_id">
                                    @if(!empty($selected_event))
                                        <option value="{{ $selected_event->id }}" selected>
                                            {{ $selected_event->event_name }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ $start_date }}">
                            </div>
                            <div class="form-group">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ $end_date }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('index-sales-report') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-h-100">
                        <!-- card body -->
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Amount</span>
                                    <h4>
                                        $<span class="counter-value" data-target="{{ number_format($total_amt_all, 2) }}">{{ number_format($total_amt_all, 2) }}</span>
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
                                    <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Donation</span>
                                    <h4 >
                                        $<span class="counter-value" data-target="{{ number_format($total_donation, 2) }}">{{ number_format($total_donation, 2) }}</span>
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
                                    <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Fee</span>
                                    <h4 >
                                        $<span class="counter-value" data-target="{{ number_format($total_platform_fee_all+$total_processing_fee_all, 2) }}">{{ number_format($total_platform_fee_all+$total_processing_fee_all, 2) }}</span>
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
                                    <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Paid Orders</span>
                                    <h4 >
                                        <span class="counter-value" data-target="{{$orders->count()}}">{{$orders->count()}}</span>
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
                        @forelse ($listingData as $data)
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

                            $hasProcessingFee = !is_null($data->processing_fee_per) && (float) $data->processing_fee_per > 0;
                            foreach($data->tickets as $ticket){
                                $total_amt += $ticket->price;
                                //$total_fee += $ticket->ticket_platform_fee;
                                $total_platform_fee += (float) $ticket->ticket_platform_fee;
                                if ($hasProcessingFee) {
                                    $total_processing_fee += (float) ($ticket->ticket_processing_fee ?? 0);
                                }
                            }
                            $donationProcessingFee = $hasProcessingFee && (float) $data->donation_amount > 0
                                ? round(
                                    ((float) $data->donation_amount * (float) $data->processing_fee_per) / 100,
                                    2
                                )
                                : 0;

                            $total_processing_fee += $donationProcessingFee;
                            @endphp 
                            @if($total_amt<=0 && $data->donation_amount<=0)
                                @continue;
                            @endif
                            @php
                            foreach($data->tickets as $ticket){
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
                                <td><a targe="_blank" href="{{ route('view-order-details',[encrypt(@$data->id)]) }}">{{ @$data->order_number }}</a></td>
                                <td>${{ @$data->donation_amount }}</td>

                                <td>${{round($total_amt,2)}}</td>
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

                                <td>${{round($re_total_amt,2)}}</td>
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

                                <td>${{round($a_total_amt,2)}}</td>
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
                                <td>{{ @$data->createdDateTime }}</td>
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
        </div> <!-- end col -->
    </div>
    <!-- end row -->
@endsection
