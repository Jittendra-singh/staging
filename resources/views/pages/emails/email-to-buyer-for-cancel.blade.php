@extends('layouts.email-body')

@section('content')
    <p>Hi {{$name}},</p>

    <p>A registration for {{ $event_name }} has been cancelled.</p>

    <p><strong>Cancellation Summary :</strong></p>
    <ul>
        <li><strong>Total Tickets Cancelled:</strong> {{ $ticket_count }}</li>
    </ul><br/>
    <hr>

    @if($ticket_refund_amount>0)
    <p>A refund has been processed for your cancelled ticket(s).</p>
    <ul>
        <li><strong>Ticket Amount:</strong> {{ $ticket_refund_amount }}</li>
        <li><strong>Refund Amount:</strong> {{ $ticket_refund_amount }}</li>
        <li><strong>Payment Method:</strong> Stripe</li>
    </ul><br/>
    <hr>

    <p>If a donation was included, please note that <b>donations are non-refundable.</b></p>

    <p>Refunds may take 5–10 business days to appear in your account, depending on your payment provider.</p>
    @else
    <p>There are <b>no payment or refund was involved</b> with these ticket(s).</p>
    @endif 

    <p>You can view the complete transaction details in your <b>Volunteer Dashboard.</b></p>

    <p>👉 <strong><a href="{{ $order_url ?? '#' }}">View My Order</a></strong></p>

    <p>Thank you for supporting cleaner communities.</p>

    <p>Warm regards,<br/>
    <strong>{{ APPNAMEEMAIL }}</strong></p>
@endsection