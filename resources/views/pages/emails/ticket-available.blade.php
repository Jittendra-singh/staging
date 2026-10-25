@extends('layouts.email-body')
@section('content')
<p>Great news, {{ $waitlist_name ?? 'there' }}!</p>
<p>A ticket just opened up for the event: <strong>{{ $eventTitle }}</strong>.</p>
<p>Because you are on the waitlist, we are letting you know immediately. Tickets are first-come, first-serve, so click the button below to grab yours before someone else does!</p>
<p><a href="{{ $link ?? '#' }}" style="background-color:#4CAF50;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;">Register Now</a></p>
<p>Keep making the planet cleaner,<br>
<b>{{ APPNAMEEMAIL }} 🌎</b></p>
@endsection
