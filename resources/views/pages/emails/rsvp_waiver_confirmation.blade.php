@extends('layouts.email-body')
@section('content')
    @if($is_under18)
    <p>Hi {{ $guardian_name ?? 'Guardian' }},</p>

    <p><b>{{$primary_user_name}}</b> has registered <b>{{$volunteer_name}}</b> for <b>{{$event_name}}</b> on {{$event_date}} at {{$event_location}}.<br>
    Because <b>{{$volunteer_name}}</b> 
    @if($is_waiver_signed) 
    is under 18, your confirmation was required — and we’ve received it successfully. 
    @else 
    is under 18, your confirmation is required. 
    @endif
    </p>

    
    @if(!$is_rsvp_confirmed)
    <p>👉 Please review and confirm RSVP: <a href="{{ $rsvp_waiver_link }}">Confirm Now</a></p>
    @elseif(!$is_waiver_signed)
    <p>👉 Please review and sign the waiver: <a href="{{ $rsvp_waiver_link }}">Sign the Waiver</a></p>
    @else
    <h5>✅ Thank You!</h5>
    <p>You’ve confirmed the RSVP and signed the waiver.</p>
    @endif

    <hr>
    <h5><strong>🧽 Event Info</strong></h5>
    <ul>
        <li><strong>Event:</strong> {{ $event_name ?? 'Event Title' }}</li>
        <li><strong>Date & Time:</strong> {{ $event_date ?? 'Event Date' }}</li>
        <li><strong>Location:</strong> {{ $event_location ?? 'Event Location' }}</li>
        @if($event->bringTools->count())
            <li><strong>What To Bring:</strong> {{$event->bringTools->pluck('name')->implode(', ')}}</li>
        @else 
            <li><strong>What to Bring:</strong> - </li>
        @endif
        @if($event->providedTools->count())
            <li><strong>What Will Be Provided:</strong> {{$event->providedTools->pluck('name')->implode(', ')}}</li>
        @else 
            <li><strong>What Will Be Provided:</strong> - </li>
        @endif
    </ul>

    <hr>
    <h5><strong>📝 Your Confirmation Status</strong></h5>
    <ul>
        @if($is_rsvp_confirmed)
        <li>RSVP: ✅ Confirmed</li>
        @else    
        <li>RSVP: ❌ Pending</li>
        @endif 
        @if($is_waiver_signed)
        <li>Waiver: ✅ Signed 
            @if($is_rsvp_confirmed)
            <small><a target="_blank" href="{{ route('view-waiver-pdf', ['ticket' => encrypt($ticket->id)]) }}">Waiver PDF</a></small>
            @endif 
        </li>
        @else    
        <li>Waiver: ❌ Pending</li>
        @endif 
        <li>Ticket ID: {{ $event_ticket_id}}</li>
    </ul>

    <p>Thank you for supporting our young volunteers! 🌱</p>
    <p>{{ APPNAMEEMAIL }}</p>
    @else 
    <p>Hi {{ $volunteer_name ?? 'Volunteer' }},</p>
    <p>We’re excited to have you for <b>{{$event_name}}</b> on <b>{{$event_date}} at {{$event_location}}</b>! 🐚🌊</p>
        @if($event->waiverFormContentFilter())
            @if($is_rsvp_confirmed && $is_waiver_signed)
            <p>✅ Your RSVP and Waiver are both confirmed – you’re officially ready to join the cleanup crew! 🌍💚</p>
            <p>Please wear appropriate clothing and footwear, and arrive on time so you don't miss the short orientation.</p>
            @else 
                @if(!$is_rsvp_confirmed)
                <p>🚨 You haven’t confirmed your RSVP yet!<br>
                👉 <a href="{{ $rsvp_waiver_link }}">RSVP Now</a></p>
                @elseif(!$is_waiver_signed) 
                <p>✅ RSVP Confirmed!</p>
                <p>⚠️ One last step – please sign your waiver to complete registration:<br>
                👉 <a href="{{ $rsvp_waiver_link }}">Sign the Waiver</a></p>
                @endif
            @endif 
        @else
            @if($is_rsvp_confirmed)
                <p>✅ Your RSVP is confirmed – you’re officially ready to join the cleanup crew! 🌍💚</p>
            @else 
                <p>🚨 You haven’t confirmed your RSVP yet!<br>
                👉 <a href="{{ $rsvp_waiver_link }}">RSVP Now</a></p>
            @endif 
        @endif 
        <hr>
        <h5><strong>🧽 Event Info</strong></h5>
        <ul>
            <li><strong>Event:</strong> {{ $event_name ?? 'Event Title' }}</li>
            <li><strong>Date & Time:</strong> {{ $event_date ?? 'Event Date' }}</li>
            <li><strong>Location:</strong> {{ $event_location ?? 'Event Location' }}</li>
            @if($event->bringTools->count())
                <li><strong>What To Bring:</strong> {{$event->bringTools->pluck('name')->implode(', ')}}</li>
            @else 
                <li><strong>What to Bring:</strong> - </li>
            @endif
            @if($event->providedTools->count())
                <li><strong>What Will Be Provided:</strong> {{$event->providedTools->pluck('name')->implode(', ')}}</li>
            @else 
                <li><strong>What Will Be Provided:</strong> - </li>
            @endif
        </ul>

        @if($event->waiverFormContentFilter())
        <hr>
        <h5><strong>📝 Your Status</strong></h5>
        <ul>
            @if($is_rsvp_confirmed)
            <li>RSVP: ✅ Confirmed</li>
            @else    
            <li>RSVP: ❌ Pending</li>
            @endif 
            @if($is_waiver_signed)
            <li>Waiver: ✅ Signed
                @if($is_rsvp_confirmed)
                <small><a target="_blank" href="{{ route('view-waiver-pdf', ['ticket' => encrypt($ticket->id)]) }}">Waiver PDF</a></small>
                @endif 
            </li>
            @else    
            <li>Waiver: ❌ Pending</li>
            @endif 
            <li>Ticket ID: {{ $event_ticket_id}}</li>
        </ul>
        @endif

    <hr>
    <h5><strong>🔗 Manage Your RSVP</strong></h5>
    <p>👉 <a href="{{ $dashboard_link }}">Go to Dashboard</a></p>

    <p>Thanks for being part of this powerful movement – your time and energy truly make a difference! 💪🌊</p>
    <p>See you at the shoreline,<br>
    {{ APPNAMEEMAIL }}</p>
    @endif 
@endsection
