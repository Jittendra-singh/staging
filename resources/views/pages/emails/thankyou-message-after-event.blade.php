@extends('layouts.email-body')
@section('content')
<p>Hello {{ $volunteerName }},</p>
<p>Thank you for being a part of <strong>{{ $eventName }}</strong> – your presence truly made a difference 🌿</p>
<p>Here’s a quick snapshot of the impact you have created:</p>
<ul>
    <li><strong>🧍‍♀️ Total Volunteers Checked In: {{ $volunteersCount }}</strong></li>
    <li><strong>🗑️ Total Waste Collected: {{ $wasteCollected }} Pound (lb)</strong></li>
    <li><strong>📍 Total Area Cleaned: {{ $areaCleaned }} Yards</strong></li>
    <li><strong>⏱️ Total Volunteer Hours: {{ $volunteerHours }} Hours</strong></li>
</ul>
<p>Your commitment to protecting our environment and keeping our community clean is inspiring. Every plastic bottle picked, every step taken — it all mattered.<br/>
We’re deeply grateful for your time, effort, and energy.</p>

@if($instaLink!='' || $xLink!='' || $linkedinLink!='')
<h5><strong>💙 Stay Involved:</strong></h5>
<p>Follow us on social media to see event highlights and upcoming cleanups:</p>
<ul>
    @if($instaLink!='')
    <li><a href="{{ $instaLink }}">Instagram</a></li>
    @endif
    @if($facebookLink!='')
    <li><a href="{{ $facebookLink }}">Facebook</a></li>
    @endif
    @if($twitterLink!='')
    <li><a href="{{ $twitterLink }}">Twitter</a></li>
    @endif
    @if($linkedinLink!='')
    <li><a href="{{ $linkedinLink }}">LinkedIn</a></li>
    @endif
</ul>
@endif

<p>Let’s keep this wave of change going 🌊</p>

<p>With gratitude,<br>
{{ APPNAMEEMAIL }}</p>
@endsection
