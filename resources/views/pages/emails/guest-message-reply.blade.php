@extends('layouts.email-body')
@section('content')
    <p>Hi {{ $guest_name ?? 'there' }},</p>
    <p>You received a reply from <strong>{{ $organizer_name ?? 'the organizer' }}</strong> regarding your inquiry:</p>
    <hr>
    <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #2196F3; margin: 15px 0;">
        {!! nl2br(e($reply_message ?? '')) !!}
    </div>
    <hr>
    <p>To respond, simply reply directly to this email.</p>
    <p>Thank you,<br>
    {{ APPNAMEEMAIL }}</p>
@endsection
