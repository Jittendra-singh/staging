@extends('layouts.email-body')

@section('content')
    <p>Hi {{ $name ?? 'Participant' }},</p>

    <p>Thank you for registering for <b>{{ $event_name ?? 'Event' }}</b>. Your purchase has been successfully completed, and the registration details are confirmed.</p>

    <p>Below is a summary of your order:</p>
    <p><strong>Tickets Purchased:</strong></p>
    <ul>
        @foreach ($event_tickets as $ticket)
            <li><strong>{{ $ticket['name'] }}</strong> x {{ $ticket['count'] }}</li>
        @endforeach
    </ul><br/>

    <p><strong>Order Summary:</strong></p>
    <ul>
        <li><strong>Total Ticket Amount:</strong> {{ round($order->grand_total-$order->donation_amount,2) }}</li>
        <li><strong>Donation Amount:</strong> {{ round($order->donation_amount,2) }}</li>
        <li><strong>Total Paid:</strong> {{ round($order->grand_total,2) }}</li>
        <li><strong>Accompanying Children:</strong> {{ $total_children ?? 0 }}</li>
    </ul><br/>

    <p>If you purchased tickets for other attendees, they will receive their own confirmation emails with event details.You can view your full order details anytime from your dashboard:</p>

    <p>👉 <strong>View Order: <a href="{{ $order_url ?? '#' }}">Order Link</a></strong></p>
    
    <p>Thank you for supporting our mission to protect our shorelines 💙</p>

    <p>We look forward to seeing everyone at the cleanup.</p>

    <p>Warm regards,<br>
    {{ APPNAMEEMAIL }}</p>
@endsection
