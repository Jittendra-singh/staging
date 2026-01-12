@extends('layouts.email-body')

@section('content')
    <p>Hi Outreach Team,</p>

    <p>A new event has just been <strong>published</strong> on VolunteerCleanup.org.</p>
    <hr>
    <p><strong>🗓️ Event Details:</strong></p>
    <ul>
        <li><strong>Event Title:</strong> {{ $eventName }}</li>
        <li><strong>Start Date & Time:</strong> {{ $date }}</li>
        <li><strong>Public URL:</strong> {{ $eventUrl }}</li>
    </ul><br/>
    <hr>
    <p><strong>👤 Organizer Information:</strong></p>
    <ul>
        <li><strong>Name:</strong> {{ $orgName }}</li>
        <li><strong>Email:</strong> {{ $orgEmail }}</li>
        <li><strong>Phone:</strong> {{ $orgPhone }}</li>
    </ul><br/>
    <hr>
    <p><strong>🔧 Admin Access:</strong></p>
    <p>To review or edit this event, click here:</p>
    <p>👉 Open in <a href="{{ $eventEditUrl }}">Admin Panel</a></p>
    <hr>

    <p>🌱 Thank you<br>
    VolunteerCleanup.org System</p>
    <p><i>(This is an automated message – please do not reply.)</i></p>
@endsection