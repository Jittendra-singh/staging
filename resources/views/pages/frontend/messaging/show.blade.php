@extends('layouts.frontend')

@section('content')
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 d-flex align-items-center justify-content-between">
                        <div class="d-main-title">
                            <h3><i class="fa-solid fa-comments me-3 mb-3"></i>{{ $conversation->subject ?: 'Conversation' }}</h3>
                            @php
                                $breadcrumbs = [
                                    ['name' => 'Inbox', 'url' => route('contact-lists')],
                                    ['name' => 'Conversation', 'url' => route('messaging.conversations.show', $conversation)],
                                ];
                            @endphp
                            <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                        </div>
                        <a href="{{ route('contact-lists') }}" class="primary-btn w-fit">Back</a>
                    </div>

                    <div class="col-md-12">
                        <x-reusables.frontend.alert />
                    </div>

                    <div class="col-lg-8 col-md-12">
                        <div class="main-card mt-4">
                            <div class="dashboard-wrap-content p-4">
                                <div class="conversation-thread">
                                    @foreach($conversation->messages as $message)
                                        @php
                                            $isMine = (int) $message->sender_id === (int) auth()->guard('front')->id();
                                        @endphp
                                        <div class="mb-4 {{ $isMine ? 'text-end' : '' }}">
                                            <div class="d-inline-block text-start p-3 rounded {{ $isMine ? 'bg-light' : 'border' }}" style="max-width: 760px;">
                                                <div class="fw-semibold">
                                                    {{ $message->sender ? trim($message->sender->first_name.' '.$message->sender->last_name) : 'System' }}
                                                    <span class="text-muted small ms-2">{{ $message->created_at->format('M d, Y h:i A') }}</span>
                                                </div>
                                                @if(trim($message->body) !== '')
                                                    <div class="mt-2" style="white-space: pre-wrap;">{{ $message->body }}</div>
                                                @endif
                                                @if($message->attachments->count())
                                                    <div class="mt-3">
                                                        @foreach($message->attachments as $attachment)
                                                            <a href="{{ $attachment->url }}" target="_blank" class="d-block">
                                                                <i class="fa-solid fa-paperclip me-1"></i>{{ $attachment->original_name }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <form method="POST" action="{{ route('messaging.messages.store', $conversation) }}" enctype="multipart/form-data" class="mt-4">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Reply</label>
                                        <textarea class="form-control" name="message" rows="5" placeholder="Write your reply"></textarea>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label class="form-label">Attachments</label>
                                        <input class="form-control h_40" type="file" name="attachments[]" multiple>
                                        <div class="text-muted small mt-1">Allowed: images, PDF, DOC, DOCX, TXT. Max 10MB each.</div>
                                    </div>
                                    <button class="primary-btn mt-4" type="submit">Send Reply</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <div class="main-card mt-4">
                            <div class="dashboard-wrap-content p-4">
                                <h5 class="mb-3">Conversation Controls</h5>
                                <div class="mb-3">
                                    <div class="text-muted small">Reply Alias</div>
                                    <div style="word-break: break-all;">{{ $conversation->reply_alias }}</div>
                                </div>

                                <form method="POST" action="{{ route('messaging.conversations.notifications', $conversation) }}">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Thread Notifications</label>
                                        <select class="form-control h_40" name="notification_preference">
                                            <option value="" @selected(empty($participant?->notification_preference))>Use default</option>
                                            @foreach(['immediate' => 'Immediate', 'batch' => '15-minute batch', 'digest' => 'Digest', 'disabled' => 'Disabled'] as $value => $label)
                                                <option value="{{ $value }}" @selected($participant?->notification_preference === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button class="primary-btn w-100 mt-3" type="submit">Save Thread Setting</button>
                                </form>

                                @if($participant?->isArchived())
                                    <form method="POST" action="{{ route('messaging.conversations.unarchive', $conversation) }}" class="mt-3">
                                        @csrf
                                        <button class="primary-btn w-100" type="submit">Restore Conversation</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('messaging.conversations.archive', $conversation) }}" class="mt-3">
                                        @csrf
                                        <button class="primary-btn w-100" type="submit">Archive Conversation</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
