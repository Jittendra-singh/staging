@extends('layouts.email-body')

@section('content')
    @if($action_type=='cancelled')
        <p>Hi {{$org_name}},</p>
        <p>A registration for {{ $event_name }} has been cancelled by the volunteer.</p>
        <p><strong>Cancellation Summary:</strong></p>
        <ul>
            <li><strong>Cancelled Ticket(s):</strong> {{ $ticket_count }}</li>
            <li><strong>Ticket Amount Refunded:</strong> {{ $ticket_refund_amount }}</li>
        </ul><br/>
        <hr>
        <p>Any applicable refunds and donations have been automatically processed according to your event settings.</p>
        <p>Funds already deposited into your connected Stripe account remain unaffected.</p>
        <p>Updated attendee list and detailed ticket breakdown is available in your Event Dashboard.</p>
        <p>👉 <strong><a href="{{ $event_hosting_url ?? '#' }}">View Event Dashboard</a></strong></p>
        <p>Thank you for continuing to organize impactful cleanups.</p>
        <p>Warm regards,<br/><strong><a href="{{ config('app.frontsite') }}">{{ APPNAMEEMAIL }}</a></strong></p>
    @else 
        <p>Hi {{$org_name}},</p>

        <p>A new registration has been completed for {{ $event_name }}.</p>
        <p><strong>Tickets Registered:</strong></p>
        <ul>
            @foreach ($event_tickets as $ticket)
                <li><strong>{{ $ticket['name'] }}</strong> x {{ $ticket['count'] }}</li>
            @endforeach
        </ul><br/>
        <hr>
        <p><strong>Payment Summary:</strong></p>
        <ul>
            <li><strong>Gross Amount Paid:</strong> {{ round($order->grand_total,2) }}</li>
            <li><strong>Platform Fees:</strong> {{ round($platform_fee,2) }}</li>
            <li><strong>Net Amount Credited:</strong> {{ round($order->grand_total-$platform_fee, 2) }}</li>
        </ul><br/>
        <hr>
        <p>Funds (minus processing and platform fees) have been deposited into your connected account.</p>
        <p>You can view full details in your <a href="{{route('front-cleanup-hosting',['type'=>'organizer'])}}">Event Dashboard</a>.</p>
        <p>Warm regards,<br/><strong><a href="{{ config('app.frontsite') }}">{{ APPNAMEEMAIL }}</a></strong></p>
    @endif
@endsection