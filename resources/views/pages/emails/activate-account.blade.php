@extends('layouts.email-body')
@section('content')
<p>Hi {{ $volunteer ?? 'Volunteer' }},</p>
<p>You're just one step away from activating your account.</p>
<p>Please confirm your email address by clicking the button below:</p>
<p><a href="{{ $verification_link ?? '#' }}" style="background-color:#4CAF50;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;">Activate My Account</a></p>
<p>Need help or have questions? Just hit reply. We’re here for you!</p>
<p>Keep making the planet cleaner,<br>
<b>Team {{ env('APP_NAME') }} 🌎</b></p>
@endsection