@extends('layouts.email-body')
@section('content')
<p>Hi {{ $volunteer ?? 'Volunteer' }},</p>

<p>Thank you for participating in <strong>{{$event_title}}.</strong><br>
Your attendance has been confirmed by the event organizer, and your <strong>Community Service Certificate</strong> is now ready.</p>

<p>You can download your certificate here:<br>
 👉 <a href="{{$certificate_download_link}}">Certificate Link</a></p>

<p>You can also view all your certificates anytime from your Volunteer Dashboard:<br>
 👉 <a href="{{$dashboard_link}}">Dashboard Link</a></p>

<p>Thank you again for helping keep our community clean.<br>
{{ APPNAMEEMAIL }}</p>
@endsection