@extends('layouts.email-body')

@section('content')
    <p>Hi Volunteer,</p>

    <p>You requested an email notification when tickets become available.</p>

    <p>Please verify your email address by clicking the button below:</p>

    <p>
        <a href="{{ $verification_link }}"
            style="background-color:#0d6efd;color:#ffffff;padding:10px 20px;text-decoration:none;border-radius:4px;margin-top:10px;margin-bottom:10px;display:inline-block;">
            Verify Email Address
        </a>
    </p>

    <p>This verification link expires in 24 hours.</p>

    <p>If you did not request this notification, you can safely ignore this email.</p>

    <p>
        Warm regards,<br>
        <strong>{{ APPNAMEEMAIL }}</strong>
    </p>
@endsection