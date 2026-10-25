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
                                        <div class="mb-4 {{ $isMine ? 'text-end' : '' }}" data-message-id="{{ $message->id }}">
                                            <div class="d-inline-block text-start p-3 rounded {{ $isMine ? 'bg-light' : 'border' }}" style="max-width: 760px;">
                                                <div class="fw-semibold">
                                                    @if($message->sender)
                                                        {{ trim($message->sender->first_name.' '.$message->sender->last_name) }}
                                                    @elseif(!empty($message->metadata['guest_name']))
                                                        {{ $message->metadata['guest_name'] }} <span class="badge bg-secondary ms-1">Guest</span>
                                                    @elseif(!empty($conversation->metadata['guest_name']))
                                                        {{ $conversation->metadata['guest_name'] }} <span class="badge bg-secondary ms-1">Guest</span>
                                                    @else
                                                        Guest Visitor <span class="badge bg-secondary ms-1">Guest</span>
                                                    @endif
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
                                        <textarea class="form-control" name="message" rows="5" maxlength="2000" placeholder="Write your reply (max 2,000 characters)"></textarea>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label class="form-label">Attachments</label>
                                        <input class="form-control h_40" type="file" name="attachments[]" accept=".jpg,.jpeg,.png,.gif,.pdf" multiple>
                                        <div class="text-muted small mt-1">Allowed: JPG, JPEG, PNG, GIF, PDF. Max 2,000 characters. Max 10MB per file.</div>
                                    </div>
                                    <button class="primary-btn mt-4" type="submit">Send Reply</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        @if(!empty($conversation->metadata['is_guest']))
                            <div class="main-card mt-4">
                                <div class="dashboard-wrap-content p-4">
                                    <h5 class="mb-3 text-secondary"><i class="fa-solid fa-user-clock me-2"></i>Guest Inquiry Info</h5>
                                    <div class="small">
                                        <div class="mb-2"><strong>Name:</strong> {{ $conversation->metadata['guest_name'] ?? 'N/A' }}</div>
                                        <div class="mb-2"><strong>Email:</strong> {{ $conversation->metadata['guest_email'] ?? 'N/A' }}</div>
                                        <div class="mb-2"><strong>Phone:</strong> {{ $conversation->metadata['guest_phone'] ?? 'N/A' }}</div>
                                        <div class="text-muted text-wrap">Replies sent from this thread will be delivered directly to the guest's email.</div>
                                    </div>
                                </div>
                            </div>
                        @endif

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

@push('script')
<script>
(function () {
    let lastMessageId = {{ $conversation->messages->last()?->id ?: 0 }};
    let isPolling = false;

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function appendMessage(msg) {
        if (document.querySelector('[data-message-id="' + msg.id + '"]')) {
            return;
        }

        const threadContainer = document.querySelector('.conversation-thread');
        if (!threadContainer) return;

        const isMine = msg.is_mine;
        const msgDiv = document.createElement('div');
        msgDiv.className = 'mb-4 ' + (isMine ? 'text-end' : '');
        msgDiv.setAttribute('data-message-id', msg.id);

        let bodyHtml = '';
        if (msg.body && msg.body.trim() !== '') {
            bodyHtml = '<div class="mt-2" style="white-space: pre-wrap;">' + escapeHtml(msg.body) + '</div>';
        }

        let attachmentsHtml = '';
        if (msg.attachments && msg.attachments.length > 0) {
            attachmentsHtml = '<div class="mt-3">';
            msg.attachments.forEach(function (att) {
                attachmentsHtml += '<a href="' + escapeHtml(att.url) + '" target="_blank" class="d-block">' +
                    '<i class="fa-solid fa-paperclip me-1"></i>' + escapeHtml(att.original_name) +
                    '</a>';
            });
            attachmentsHtml += '</div>';
        }

        msgDiv.innerHTML = '<div class="d-inline-block text-start p-3 rounded ' + (isMine ? 'bg-light' : 'border') + '" style="max-width: 760px;">' +
            '<div class="fw-semibold">' + escapeHtml(msg.sender_name) +
            ' <span class="text-muted small ms-2">' + escapeHtml(msg.created_at) + '</span></div>' +
            bodyHtml +
            attachmentsHtml +
            '</div>';

        threadContainer.appendChild(msgDiv);

        if (msg.id > lastMessageId) {
            lastMessageId = msg.id;
        }
    }

    function pollDelta() {
        if (isPolling) return;
        isPolling = true;

        const url = "{{ route('messaging.conversations.delta', $conversation) }}?after_id=" + lastMessageId;

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(function (data) {
            if (data && data.status && Array.isArray(data.messages) && data.messages.length > 0) {
                data.messages.forEach(function (msg) {
                    appendMessage(msg);
                });
            }
        })
        .catch(function (error) {
            // Silently handle polling network errors without breaking UI
        })
        .finally(function () {
            isPolling = false;
        });
    }

    setInterval(pollDelta, 30000);
})();
</script>
@endpush
