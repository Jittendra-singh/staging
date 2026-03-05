@extends('layouts.email-body')

@section('content')
    <p>Hi {{ $volunteer ?? 'Volunteer' }},</p>

    <p>Thank you for signing up to be a part of the {{ env('APP_NAME') }} community! We're excited to have you join our mission to protect and clean up our beaches and natural spaces.</p>

    <p>To activate your account, please confirm your email address by clicking the link below:</p>
    <p>👉 <a href="{{ $verification_link ?? '#' }}" style="background-color:#4CAF50;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;">Confirm to Start Volunteering</a></p>

    <p>Verifying your email helps us keep your account secure and ensures you receive important updates about upcoming cleanups and your registrations.</p>

    <p>If you didn’t sign up for VolunteerCleanup, you can safely ignore this email.</p>

    <p>Looking forward to seeing you in action!</p>    

    <p>Warm regards,<br>
    <strong>{{ APPNAMEEMAIL }}</strong><br>
    <em>Small acts. Big impact.</em></p>
@endsection
