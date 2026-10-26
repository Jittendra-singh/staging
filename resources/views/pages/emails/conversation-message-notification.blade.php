
@extends('layouts.email-body')

@section('content')
    <p>Hi {{ $recipient_name ?? 'there' }},</p>

    @if (!empty($is_digest))
        <p>You have new messages in a VolunteerCleanup conversation:</p>
    @else
        <p>You have received a new message in a VolunteerCleanup conversation.</p>
    @endif

    <p><strong>Subject:</strong> {{ $conversation_subject ?? 'Conversation' }}</p>

    @foreach ($messages as $message)
        <hr>
        <p>
            <strong>From:</strong>
            {{ $message->sender ? trim($message->sender->first_name . ' ' . $message->sender->last_name) : 'VolunteerCleanup user' }}
        </p>
        <p>{!! nl2br(e($message->body ?? '')) !!}</p>
    @endforeach

    <hr>
    <p>Sign in to VolunteerCleanup to view and reply to your conversation.</p>

    <p>Thank you,<br>{{ APPNAMEEMAIL }}</p>
@endsection
