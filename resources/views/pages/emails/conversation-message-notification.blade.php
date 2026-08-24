@extends('layouts.email-body')

@section('content')
    <p>Hi {{ trim(($recipient->first_name ?? '').' '.($recipient->last_name ?? '')) }},</p>
    <p>{{ $is_batch ? 'You have new messages' : 'You have a new message' }} in {{ $conversation->subject ?: 'a conversation' }}.</p>

    @foreach($messages as $message)
        <hr>
        <p><strong>{{ $message->sender ? trim($message->sender->first_name.' '.$message->sender->last_name) : 'Someone' }}</strong></p>
        <p>{{ \Illuminate\Support\Str::limit($message->body, 500) }}</p>
    @endforeach

    <p>
        <a href="{{ $conversation_url }}"
           style="background-color:#2196F3;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
            View Conversation
        </a>
    </p>
    <p>You can also reply directly to this email.</p>
@endsection
