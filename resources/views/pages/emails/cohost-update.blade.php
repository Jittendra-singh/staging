@extends('layouts.email-body')
@section('content')
@if($is_deleted)
    <p>Hi {{ $cohost_first_name }},</p>
    <p>{{ $organizer_name }} has removed you as a co-host from <strong>“{{ $event_name }}.”</strong></p>
    <p>You’ll no longer see or be able to manage this event from your dashboard. If this was unexpected or an error, please contact the organizer directly.</p>
    <p>Organizer Details:</p>
    <ul>
        <li><strong>Email: <a href="mailto:{{ $organizer_email }}">{{ $organizer_email }}</a></strong></li>
    </ul>
    <p>VolunteerCleanup.org Team</p>
@else 
    <p>Hi {{ $cohost_first_name }},</p>
    <p>{{ $organizer_name }} has updated your access settings for <strong>“{{ $event_name }}”.</strong></p> 
    <p>Your new permission level:</p>
    <ul>
        @foreach ($permissions as $permission)
            <li><strong>{{ config('app.cohost_permissions')[$permission->permission] }}</strong></li>
        @endforeach
    </ul>

    <p>You can view or manage the event anytime from your dashboard:</p>
    <p style="text-align:center;">
        <a href="{{ $dashboardLink }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; margin: 10px 20px; text-decoration: none; border-radius: 4px;">
             Go to My Dashboard
        </a>
    </p>
    <p>Thank you for supporting this cleanup!</p>
    <p>VolunteerCleanup.org Team</p>
@endif
@endsection