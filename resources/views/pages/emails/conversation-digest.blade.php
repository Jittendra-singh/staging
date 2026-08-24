@extends('layouts.email-body')

@section('content')
    <p>Hi {{ trim(($recipient->first_name ?? '').' '.($recipient->last_name ?? '')) }},</p>
    <p>Here is your unread VolunteerCleanup message digest.</p>

    @foreach($logs->groupBy('conversation_id') as $conversationLogs)
        @php
            $conversation = $conversationLogs->first()->conversation;
        @endphp
        <hr>
        <p><strong>{{ $conversation?->subject ?: 'Conversation' }}</strong></p>
        <p>{{ $conversationLogs->count() }} new {{ \Illuminate\Support\Str::plural('message', $conversationLogs->count()) }}</p>
        @if($conversation)
            <p><a href="{{ route('messaging.conversations.show', $conversation) }}">Open conversation</a></p>
        @endif
    @endforeach
@endsection
