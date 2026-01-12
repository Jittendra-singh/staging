@extends('layouts.email-body')
@section('content')
    @if($is_under18 && false)
        <p>Hi {{ $volunteer_name ?? 'Volunteer' }},</p>

        <p>Your guardian has cancelled your RSVP for <b>{{$event_name}}</b> on <b>{{$event_date}} at {{$event_location}}</b>!</p>

        <p>We’ll miss having you at this cleanup 🌊, but we hope to see you at a future event!</p>

        <p>If plans change, your guardian can re-confirm your spot anytime.</p>

        <hr>
        <h5><strong>🧽 Event Info</strong></h5>
        <ul>
            <li><strong>Event:</strong> {{ $event_name ?? 'Event Title' }}</li>
            <li><strong>Date & Time:</strong> {{ $event_date ?? 'Event Date' }}</li>
            <li><strong>Location:</strong> {{ $event_location ?? 'Event Location' }}</li>
            <li><strong>Ticket ID:</strong> {{ $event_ticket_id}}</li>
        </ul>

        <p>Thank you for supporting our mission to protect our shorelines. Even if you can’t make it this time, your care and effort truly matter 💙</p>
        <p>See you at a future cleanup,<br>
        The {{ env('APP_NAME') }} Team</p>
    @else 
        <p>Hi {{ $volunteer_name ?? 'Volunteer' }},</p>

        <p>Your registration for <b>{{$event_name}}</b>, scheduled on <b>{{$event_date}} at {{$event_location}}</b>, has been successfully cancelled. We’ll miss having you at this cleanup 🌊.</p>

        <hr>
        <p>Here are the event details for your reference:</p>
        <ul>
            <li><strong>Event:</strong> {{ $event_name ?? 'Event Title' }}</li>
            <li><strong>Date & Time:</strong> {{ $event_date ?? 'Event Date' }}</li>
            <li><strong>Location:</strong> {{ $event_location ?? 'Event Location' }}</li>
            <li><strong>Ticket ID:</strong> {{ $event_ticket_id}}</li>
        </ul>

        <p>Thank you for supporting our mission to protect our shorelines. Even if you can’t make it this time, your care and effort truly matter 💙</p>

        <p>You can explore and join other upcoming cleanup events anytime through <a href="{{ config('app.frontsite') }}">VolunteerCleanup.org</a>.</p>

        <p>We hope to see you at a future cleanup.</p>
        
        <p>Warm regards,<br>
        The {{ env('APP_NAME') }} Team</p>
    @endif 
@endsection
