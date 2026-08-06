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
        <p><strong>Buyer Information :</strong></p>
        <ul>
            <li><strong>Buyer Name:</strong> {{ $buyer_name??'-' }}</li>
            <li><strong>Email:</strong> {{ $buyer_email??'-' }}</li>
            <li><strong>Phone:</strong> {{ $buyer_phone??'-' }}</li>
        </ul><br/>
        <hr>
        <p><strong>Registration Summary :</strong></p>
        <ul>
            <li><strong>Total Attendees:</strong> {{ $total_tickets??'-' }}</li>
            <li><strong>Under 18 Attendees:</strong> {{ $total_18_tickets??'-' }}</li>
            <li><strong>Accompanying Children:</strong> {{ $total_children ?? 0 }}</li>
        </ul><br/>
        <hr>
        <p><strong>Tickets Registered:</strong></p>
        <ul>
            @foreach ($event_tickets as $ticket)
                <li><strong>{{ $ticket['name'] }}</strong> x {{ $ticket['count'] }}</li>
            @endforeach
        </ul><br/>
        <hr>
        <p><strong>Payment Summary:</strong></p>
        <ul>
            <li>
                <strong>Gross Amount Paid:</strong>
                ${{ round($order->grand_total, 2) }}
            </li>

            <li>
                <strong>Platform Fees:</strong>
                ${{ round($platform_fee, 2) }}
            </li>

            @if($has_processing_fee)
                <li>
                    <strong>Processing Fees:</strong>
                    ${{ round($processing_fee, 2) }}
                    ({{ round($order->processing_fee_per, 2) }}%)
                </li>
            @endif

            <li>
                <strong>Total Fees:</strong>
                ${{ round($total_application_fee, 2) }}
            </li>

            <li>
                <strong>Net Amount Credited:</strong>
                ${{ round($organizer_payout, 2) }}
            </li>
        </ul><br/>
        <hr>
        <p>Your organizer payout reflects the platform and processing fees saved for this order.</p>
        <p>You can view full details in your <a href="{{$cleanup_hosting_link}}">Event Dashboard</a>.</p>
        <p>View full attendee details in your registration list : <a href="{{$registration_list_link}}">Registration List</a>.</p>
        <p>Warm regards,<br/><strong><a href="{{ config('app.frontsite') }}">{{ APPNAMEEMAIL }}</a></strong></p>
    @endif
@endsection