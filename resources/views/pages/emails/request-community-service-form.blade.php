@extends('layouts.email-body')
@section('content')
    <p>Hi {{ $org_name ?? 'Organizer' }},</p>

    <p>{{$volunteer}} has requested a <strong>Community Service Certificate</strong> for the event <strong>{{$event_name}}</strong>.</p>

    <p>To issue the certificate, please confirm their attendance by marking them as <strong>Checked In</strong> on your event dashboard:</p>

    <p> 👉 <a href="{{$dashboard_link}}">Certificate Management</a></p>

    <p>Once you check them in, the certificate will be generated automatically and emailed to the volunteer.</p>

    <p>Thank you for your support.<br>
     {{ APPNAMEEMAIL }}</p>
@endsection
