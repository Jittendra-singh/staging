@extends('layouts.email-body')
@section('content')
<p>Hello {{ $cohostName }},</p>
<p>We’re excited to invite you to be a co-host for the upcoming event:</p>
<p><b>Event Name:</b> {{ $eventName }}</p>
<p><b>Date & Time:</b> {{ $eventDate }}</p>
<p><b>Location:</b> {{ $eventLocation }}</p>
<p><b>Organised by:</b> {{ $eventOrganizer }}</p>
<p>Please let us know if you'd like to accept this co-hosting role:</p>
<p style="text-align:center;">
    <a href="{{ $acceptLink }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; margin: 10px 20px; text-decoration: none; border-radius: 4px;">
        ✅ Accept Invitation
    </a>
    <a href="{{ $cancelLink }}" style="background-color: #0052CC; color: white; padding: 10px 20px; margin: 10px 20px; text-decoration: none; border-radius: 4px;">
        ❌ Decline Invitation
    </a>
</p>
@if($password)
<p>To make things easier, we’ve created an account for you. You can use the credentials below to log in and manage your co-host responsibilities.</p>
<p>
    <b>Your Login Details:</b><br>
    Email: <strong>{{ $email }}</strong><br>
    Temporary Password: <strong>{{ $password }}</strong><br>
</p>
<p>Access your account by clicking this <a href="{{ $login_link ?? '#' }}">login link</a>.</p>
@endif
<p>If you have any questions about your role or the event, feel free to contact our support team at <a href="mailto:{{ SUPPORT_EMAIL }}">{{ SUPPORT_EMAIL }}</a>.</p>
<p>Thank you for being part of our community!</p>
<p>Best regards,<br>
{{ APPNAMEEMAIL }}</p>
@endsection