@extends('layouts.email-body')
@section('content')
<p>Hi {{ $volunteer ?? 'Volunteer' }},</p>
<p>We received a request to reset your {{ env('APP_NAME') }} password.<br/>No worries — it happens!</p>
<p>👉 Click the button below to create a new password:</p>
<p><a href="{{ $link ?? '#' }}" style="background-color:#4CAF50;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;">Reset My Password</a></p>
<p>This link will expire in 30 minutes for your security.</p>
<p>If you didn’t request a password reset, you can ignore this message — your account is safe and nothing has changed.</p>
<p>Need help or have questions? Just hit reply. We’re here for you!</p>
<p>Keep making the planet cleaner,<br>
<b>Team {{ env('APP_NAME') }} 🌎</b></p>
@endsection