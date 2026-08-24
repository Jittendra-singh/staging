@extends('layouts.email-body')

@section('content')
    <p>Hi {{ $name ?? 'there' }},</p>
    <p>Please verify your email address before we deliver your message to {{ trim(($organizer->first_name ?? '').' '.($organizer->last_name ?? '')) }}.</p>
    <p>
        <a href="{{ $verification_link }}"
           style="background-color:#2196F3;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
            Verify and Send Message
        </a>
    </p>
    <p>This link expires at {{ $expires_at->format('M d, Y h:i A') }}.</p>
    <p>Thank you,<br>{{ env('APP_NAME') }}</p>
@endsection
