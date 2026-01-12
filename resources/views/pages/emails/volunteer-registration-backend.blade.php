@extends('layouts.email-body')

@section('content')
    <p>Dear {{ $volunteer ?? 'Volunteer' }},</p>

    <p>We’re excited to welcome you at <strong>{{ env('APP_NAME') }}</strong>. Your account has been successfully created by our team.</p>

    <p>You can now log in to your dashboard using the credentials below:</p>
    <p><b>Login Email:</b> {{ $email }}</p>
    <p><b>Temporary Password:</b> {{ $password }}</p>
    <p>Access your account by clicking this <a href="{{ $login_link ?? '#' }}">login link</a>.</p>

    <p>We recommend logging in and updating your password right away for security purposes.</p>

    <p>If you have any questions or need support, feel free to reply to this email or contact us at <a href="mailto:{{ SUPPORT_EMAIL }}">{{ SUPPORT_EMAIL }}</a>.</p>

    <p>Thank you for joining us in making a difference!</p>

    <p>Best regards,<br>
    <strong>{{ env('APP_NAME') }} Team</strong><br>
@endsection
