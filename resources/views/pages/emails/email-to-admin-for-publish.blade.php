@extends('layouts.email-body')

@section('content')
    <p>Hi Outreach Team,</p>

    <p>A new event has just been <strong>published</strong> on VolunteerCleanup.org.</p>
    <hr>
    <p><strong>🗓️ Event Details:</strong></p>
    <ul>
        <li><strong>Event Title:</strong> {{ $eventName }}</li>
        <li><strong>Start Date & Time:</strong> {{ $date }}</li>
        <li><strong>End Date & Time:</strong> {{ $end_date }}</li>
        <li><strong>Location:</strong> {{ $location }}</li>
        <li><strong>Is Meeting Private:</strong> {{ $is_meeting_private }}</li>
        <li><strong>Public URL:</strong> {{ $eventUrl }}</li>
        <li><strong>Description:</strong> {{ $description }}</li>
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
    {{ APPNAMEEMAIL }}</p>
    <p><i>(This is an automated message – please do not reply.)</i></p>
@endsection