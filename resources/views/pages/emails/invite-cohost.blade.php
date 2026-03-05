@extends('layouts.email-body')
@section('content')
@if($is_new_user)
    <p>Hi there,</p>
    <p>{{ $organizerName }} has invited you to co-host the cleanup event <strong>“{{ $eventName }}”</strong> To accept, please create your VolunteerCleanup.org organizer account — it only takes a minute. Once complete, this event will appear on your dashboard so you can help manage volunteers, edit details, and more.</p>
    <p style="text-align:center;">
        <a href="{{ $acceptLink }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; margin: 10px 20px; text-decoration: none; border-radius: 4px;">
            Create Account & Accept Co-Host Invitation
        </a>
    </p>
    <p>If you prefer not to co-host, you can safely ignore this email.<br/>
    Thanks for stepping up to help keep our community clean!</p>
    <p>{{ APPNAMEEMAIL }}</p>
@else 
    <p>Hi {{ $cohostName }},</p>
    <p>{{ $organizerName }} has invited you to co-host the cleanup event <strong>“{{ $eventName }}”</strong> on VolunteerCleanup.org.</p>
    <p>As a co-host, you’ll be able to help manage the event — view and contact volunteers, update event details, mark attendance, and support community-service form approvals (based on the permissions set by the organizer).
    </p>
    <p>Click below to accept the invitation and add this event to your dashboard:</p>
    <p style="text-align:center;">
        <a href="{{ $acceptLink }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; margin: 10px 20px; text-decoration: none; border-radius: 4px;">
            Accept Co-Host Invitation
        </a>
    </p>
    <p>If you’re not available to co-host, you can simply ignore this email or click <a href="{{ $cancelLink }}"><strong>Decline Invitation</strong></a>.</p>
    <p>Thank you for helping make a difference for our shoreline and community!<br/>
    {{ APPNAMEEMAIL }}</p>
@endif
@endsection