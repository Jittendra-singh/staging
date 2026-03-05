@extends('layouts.email-body')
@section('content')
@if($is_accepted==true)
    <p>Hi {{$organizer_first_name}},</p>
    <p>Good news! {{ $cohost_first_name }} has accepted your invitation to co-host “{{ $event_name }}.”</p>
    <p>They now have access to manage the event according to the permissions you set. You can adjust or remove co-host access anytime under your event’s <strong>Co-Hosts</strong> tab!</p>
    <p style="text-align:center;">
        <a href="{{ $eventEditLink }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; margin: 10px 20px; text-decoration: none; border-radius: 4px;">
            Manage Co-Hosts
        </a>
    </p>
    <p>Thank you for organizing together!<br/>
    {{ APPNAMEEMAIL }}</p>
@else 
    <p>Hi {{$organizer_first_name}},</p>
    <p>{{ $cohost_first_name }} has declined your invitation to co-host “{{ $event_name }}.”</p>
    <p>If this was unintentional or you’d like to invite someone else, you can resend or add a new co-host from your event management page.</p>
    <p style="text-align:center;">
        <a href="{{ $eventEditLink }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; margin: 10px 20px; text-decoration: none; border-radius: 4px;">
            Manage Co-Hosts
        </a>
    </p>
    <p>Thank you for organizing together!<br/>
    {{ APPNAMEEMAIL }}</p>
@endif
@endsection