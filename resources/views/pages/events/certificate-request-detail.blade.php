@php
    $pageName = 'Certificate Request Details';
    $parentRoute = route('certificate-requests');
    $parentPageName = $module;
@endphp
@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $parentPageName, 'parentRoute' => $parentRoute,'breadcrumb' => [
    ['name' => 'Certificate Requests', 'route' => route('certificate-requests')],
    ['name' => 'Certificate Requests Details', 'route' => ''],
]])
@section('content')
    <div class="row">
        <div class="col-sm-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Order #{{ $order->order_number }}</strong>
                <span class="text-muted">Created At: <b>{{ $order->createdDateTime }}</b></span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8 mb-2">
                        <div class="mb-3">
                            <table class="table table-borderless mb-0"> 
                                <tbody>
                                    <tr>
                                        <th class="fw-bold" style="width: 30%;">Event:</th>
                                        <td>{{ $order->event->event_name ?? '-' }}</td>
                                    </tr> 
                                    <tr>
                                        <th class="fw-bold">Payment Type:</th>
                                        <td>
                                            <span class="badge bg-info text-dark">{{ ucfirst($order->payment_type) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="fw-bold">Transaction Id:</th>
                                        <td>
                                            {{ ucfirst($order->payment_transaction) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="fw-bold">Payment Status:</th>
                                        <td>
                                            <span class="badge 
                                                @if($order->payment_status == 'paid') bg-success
                                                @elseif($order->payment_status == 'pending') bg-warning text-dark
                                                @else bg-secondary
                                                @endif
                                            ">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <!-- <tr>
                                        <th class="fw-bold">Extra Notes</th>
                                        <td>
                                            {{ "Extra Notes Extra Notes" }}
                                        </td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <table class="table table-sm mb-0 table-bordered">
                            <tbody>
                                <tr>
                                    <th class="text-end p-2">Total Tickets</th>
                                    <td class="text-end p-2">{{ $order->total_tickets }}</td>
                                </tr>
                                <tr>
                                    <th class="text-end p-2">Subtotal</th>
                                    <td class="text-end p-2 text-primary">${{ number_format($order->sub_total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-end p-2">Donation</th>
                                    <td class="text-end p-2 text-primary">${{ number_format($order->donation_amount, 2) }}</td>
                                </tr>
                                <!-- <tr>
                                    <th class="text-end p-2">Discount ({{ $order->discount_pert }}%)</th>
                                    <td class="text-end p-2 text-danger">-${{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-end p-2">Tax ({{ $order->tax_pert }}%)</th>
                                    <td class="text-end p-2 text-primary">${{ number_format($order->tax_amount, 2) }}</td>
                                </tr> -->
                                <tr class="border-top">
                                    <th class="text-end p-2">Grand Total</th>
                                    <td class="text-end p-2 fw-bold text-success">${{ number_format($order->grand_total, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <h4 class="mb-3  font-size-18">Tickets</h4>
        <div class="card">
            <div class="card-body p-0"></div>
                <table class="table table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Ticket Number</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Volunteer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <!-- <th>Extra Notes</th> -->
                            <th>Created At</th>
                            <th>RSVP Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->tickets as $index => $ticket)
                            <tr>
                                <td>{{ $index + 1 }}
                                    <div class="d-none tr-detail">
                                        @json($ticket)
                                    </div>
                                </td>
                                <td>#{{ $ticket->ticket_number }}
                                    @if ($ticket->checkin == 1 ) 
                                        <i class="mdi mdi-sticker-check-outline text-primary" title="Checked In"></i>
                                    @endif
                                </td>
                                <td>{{ $ticket->ticket->ticket_type }}</td>
                                <td>${{ $ticket->price }}</td>
                                <td>{{ $ticket->volunteer->name ?? '-' }}</td>
                                <td>{{ $ticket->volunteer->email_address ?? '-' }}</td>
                                <td>{{ $ticket->volunteer->phone ?? '-' }}</td>
                                <!-- <td>{{ $ticket->extra_notes }}</td> -->
                                <td>{{ $ticket->created_at }}</td>
                                <td>
                                    @if($ticket->rsvp_confirmation===0 && $ticket->is_amt_donated==1)
                                    Cancelled & Amt Donated
                                    @elseif($ticket->rsvp_confirmation===0)
                                    Cancelled
                                    @elseif($ticket->rsvp_confirmation==1)
                                    Registered
                                    @else
                                    Pending
                                    @endif 
                                </td>
                                <td>    
                                    <div class="d-flex gap-2 justify-content-start action-button">
                                        <a target="_blank" href="{{ route('index-volunteers','name='.$ticket->volunteer->email_address) }}">
                                            <i class="fs-5 mdi mdi-eye" title="View Details"></i>
                                        </a>
                                    </div>
                                </td>
                                <!-- <td>    
                                    <div class="d-flex gap-2 justify-content-start action-button">
                                        <a class="detailed-modal-popup" 
                                            data-bs-toggle="modal" data-bs-target="#ticketDetails"
                                            data-name="">
                                            <i class="fs-5 mdi mdi-arrow-expand" title="View Details"></i>
                                        </a>
                                    </div>
                                </td> -->
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No tickets found for this order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- modal detail popup --}}
    <div class="modal fade detailed-modal-popup" id="ticketDetails" tabindex="-1" aria-labelledby="ticketDetailsLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketDetailsLabel">Ticket Details</h5>
                    <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i
                            class="uil uil-multiply"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mt-3">
                        <label for="ticketNumber" class="form-label">Extra Notes </label>
                        <input type="text" class="form-control extra_notes " name="extra_notes" id="extra_notes" value="" readonly>
                    </div>
                    <div class="form-group mt-3">
                        <label for="ticketNumber" class="form-label">
                            {{$order->event->ticket_questions}}    
                        </label>
                        <input type="text" class="form-control ticket_answer " name="ticket_answer" id="ticket_answer" value="" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-sm-12 mt-4 text-center">
                            <button type="button" class="btn btn-light waves-effect me-3"
                                data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
