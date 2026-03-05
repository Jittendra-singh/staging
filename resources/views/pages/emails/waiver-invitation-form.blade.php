@extends('layouts.email-body')
@section('content')
    <p>Hi {{ $name ?? 'Volunteer' }},</p>
    <p><strong>{{ $purchase_by_name ?? 'Volunteer' }}</strong> has registered <strong>{{ $childName ?? 'Child' }}</strong> for <strong>{{ $eventName ?? 'Event Name' }}</strong> happening on {{ $eventDate ?? 'Event Date' }}.</p>
    <p>Because <strong>{{ $childName ?? 'Child' }}</strong> is under 18, we need your confirmation and waiver signature for their participation.</p>
    @if($event->waiverFormContentFilter())
    <p>To complete participation, please sign the waiver before the event using the link below:</p>
    <p>👉 <a href="{{ $waiverUrl }}">Sign Waiver</a></p>
    @endif
    <p>If you wish to cancel this registration, please click below:</p>
    <p>❌ <a href="{{ $cancelRegistrationUrl }}">Cancel Registration</a></p>
    
    <hr>
    <h3><strong>🧽 Event Details:</strong></h3>
    <ul>
        <li><strong>Event:</strong> {{ $eventName ?? 'Event Title' }}</li>
        <li><strong>Date & Time:</strong> {{ $eventDate ?? 'Event Date' }}</li>
        <li><strong>Location:</strong> {{ $eventLocation ?? 'Event Location' }}</li>
    </ul><br/>

    <p>We appreciate your support in getting youth involved in protecting our environment 🌱</p>
    <p>Thank you,<br>
    {{ APPNAMEEMAIL }}</p>
@endsection
