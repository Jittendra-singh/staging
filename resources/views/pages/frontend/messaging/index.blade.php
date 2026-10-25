@extends('layouts.frontend')

@section('content')
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 d-flex align-items-center justify-content-between">
                        <div class="d-main-title">
                            <h3><i class="fa-solid fa-inbox me-3 mb-3"></i>Inbox</h3>
                            @php
                                $breadcrumbs = [
                                    ['name' => 'Inbox', 'url' => route('contact-lists')],
                                ];
                            @endphp
                            <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <x-reusables.frontend.alert />
                    </div>

                    <div class="col-lg-8 col-md-12">
                        <!-- Navigation Tabs -->
                        <div class="main-card mt-4">
                            <div class="dashboard-wrap-content p-3">
                                <ul class="nav nav-pills card-header-pills">
                                    <li class="nav-item me-2">
                                        <a class="nav-link {{ $tab === 'all' && $status !== 'archived' ? 'active bg-primary text-white' : 'text-dark' }}"
                                           href="{{ route('contact-lists', array_merge(request()->except(['tab', 'page']), ['tab' => 'all'])) }}">
                                            <i class="fa-solid fa-comments me-1"></i> All Messages
                                            <span class="badge bg-secondary ms-1">{{ $allCount }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $tab === 'archived' || $status === 'archived' ? 'active bg-primary text-white' : 'text-dark' }}"
                                           href="{{ route('contact-lists', array_merge(request()->except(['tab', 'page']), ['tab' => 'archived'])) }}">
                                            <i class="fa-solid fa-box-archive me-1"></i> Archived
                                            <span class="badge bg-secondary ms-1">{{ $archivedCount }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Search and Filter Bar -->
                        <div class="main-card mt-3">
                            <div class="dashboard-wrap-content p-4">
                                <form method="GET" action="{{ route('contact-lists') }}" class="row g-3 align-items-end">
                                    <input type="hidden" name="tab" value="{{ $tab }}">

                                    <div class="col-lg-6 col-md-6">
                                        <label class="form-label">Search</label>
                                        <input class="form-control h_40" type="text" name="search" value="{{ $search }}" placeholder="Search by name, email, or message">
                                    </div>

                                    <div class="col-lg-4 col-md-4">
                                        <label class="form-label">Status</label>
                                        <select class="form-control h_40" name="status" onchange="this.form.submit()">
                                            <option value="all" @selected($status === 'all')>All Statuses</option>
                                            <option value="unread" @selected($status === 'unread')>Unread ({{ $unreadCount }})</option>
                                            <option value="read" @selected($status === 'read')>Read</option>
                                            <option value="archived" @selected($status === 'archived')>Archived</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-2 col-md-2">
                                        <button class="primary-btn w-100" type="submit">Filter</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Bulk Actions & Conversation List -->
                        <div class="main-card mt-3">
                            <div class="dashboard-wrap-content p-4">
                                <form id="bulkActionForm" method="POST" action="{{ route('messaging.conversations.bulk') }}">
                                    @csrf
                                    <input type="hidden" name="action" id="bulkActionInput" value="">

                                    <!-- Bulk Action Controls -->
                                    <div class="d-flex flex-wrap align-items-center justify-content-between pb-3 mb-3 border-bottom">
                                        <div class="d-flex align-items-center me-3 mb-2 mb-md-0">
                                            <div class="form-check me-3 mb-0">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                                <label class="form-check-label text-muted small" for="selectAll">Select All</label>
                                            </div>
                                            <span id="selectedCounter" class="text-muted small fw-semibold">0 selected</span>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary bulk-btn" data-action="read" disabled>
                                                <i class="fa-solid fa-envelope-open me-1"></i> Mark Read
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary bulk-btn" data-action="unread" disabled>
                                                <i class="fa-solid fa-envelope me-1"></i> Mark Unread
                                            </button>
                                            @if($tab === 'archived' || $status === 'archived')
                                                <button type="button" class="btn btn-sm btn-outline-info bulk-btn" data-action="unarchive" disabled>
                                                    <i class="fa-solid fa-box-open me-1"></i> Unarchive
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-warning bulk-btn" data-action="archive" disabled>
                                                    <i class="fa-solid fa-box-archive me-1"></i> Archive
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-danger bulk-btn" data-action="delete" disabled onclick="return confirm('Are you sure you want to remove selected conversation(s) from your inbox?')">
                                                <i class="fa-solid fa-trash me-1"></i> Delete
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Table View -->
                                    <div class="table-card">
                                        <div class="main-table">
                                            <div class="table-responsive">
                                                <table class="table align-middle">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th style="width: 40px;"></th>
                                                            <th>Conversation</th>
                                                            <th>Latest Message</th>
                                                            <th>Updated</th>
                                                            <th>Status</th>
                                                            <th class="text-end">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($conversations as $conversation)
                                                            @php
                                                                $currentUser = auth()->guard('front')->user();
                                                                $other = $conversation->participants->firstWhere('volunteer_id', '!=', $currentUser->id)?->volunteer;
                                                                $unread = $conversation->unreadCountFor($currentUser);
                                                                $participant = $conversation->participantFor($currentUser);
                                                                $isArchived = $participant?->isArchived();
                                                            @endphp
                                                            <tr class="{{ $unread > 0 ? 'table-active fw-bold' : '' }}">
                                                                <td>
                                                                    <input class="form-check-input conversation-checkbox" type="checkbox" name="uuids[]" value="{{ $conversation->uuid }}">
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('messaging.conversations.show', $conversation) }}" class="text-dark text-decoration-none fw-semibold">
                                                                        {{ $conversation->subject ?: 'Conversation' }}
                                                                    </a>
                                                                    <div class="text-muted small">
                                                                        @if($other)
                                                                            {{ trim($other->first_name . ' ' . $other->last_name) }}
                                                                            <span class="text-muted">({{ $other->email_address }})</span>
                                                                        @elseif(!empty($conversation->metadata['is_guest']))
                                                                            <span class="badge bg-secondary me-1"><i class="fa-solid fa-user-clock me-1"></i>Guest</span>
                                                                            {{ $conversation->metadata['guest_name'] ?? 'Guest' }}
                                                                            <span class="text-muted">({{ $conversation->metadata['guest_email'] ?? '' }})</span>
                                                                        @else
                                                                            Participant
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="text-muted small">
                                                                        {{ \Illuminate\Support\Str::limit($conversation->latestMessage?->body ?: 'No messages yet', 70) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <span class="small text-muted">
                                                                        {{ optional($conversation->last_message_at ?: $conversation->updated_at)->format('M d, Y h:i A') }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @if($unread > 0)
                                                                        <span class="badge bg-danger">{{ $unread }} unread</span>
                                                                    @elseif($isArchived)
                                                                        <span class="badge bg-secondary">Archived</span>
                                                                    @else
                                                                        <span class="badge bg-light text-dark border">Read</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-end">
                                                                    <div class="d-inline-flex gap-1">
                                                                        <a href="{{ route('messaging.conversations.show', $conversation) }}" class="btn btn-sm btn-outline-primary" title="Open Conversation">
                                                                            <i class="fa-solid fa-folder-open"></i>
                                                                        </a>

                                                                        @if($unread > 0)
                                                                            <form method="POST" action="{{ route('messaging.conversations.mark-read', $conversation) }}" class="d-inline">
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Mark as Read">
                                                                                    <i class="fa-solid fa-envelope-open"></i>
                                                                                </button>
                                                                            </form>
                                                                        @else
                                                                            <form method="POST" action="{{ route('messaging.conversations.mark-unread', $conversation) }}" class="d-inline">
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Mark as Unread">
                                                                                    <i class="fa-solid fa-envelope"></i>
                                                                                </button>
                                                                            </form>
                                                                        @endif

                                                                        @if($isArchived)
                                                                            <form method="POST" action="{{ route('messaging.conversations.unarchive', $conversation) }}" class="d-inline">
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-sm btn-outline-info" title="Unarchive">
                                                                                    <i class="fa-solid fa-box-open"></i>
                                                                                </button>
                                                                            </form>
                                                                        @else
                                                                            <form method="POST" action="{{ route('messaging.conversations.archive', $conversation) }}" class="d-inline">
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Archive">
                                                                                    <i class="fa-solid fa-box-archive"></i>
                                                                                </button>
                                                                            </form>
                                                                        @endif

                                                                        <form method="POST" action="{{ route('messaging.conversations.delete', $conversation) }}" class="d-inline" onsubmit="return confirm('Remove this conversation from your inbox?')">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                                                <i class="fa-solid fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center py-4 text-muted">
                                                                    <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                                                                    No conversations found matching your criteria.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="mt-3">
                                    {{ $conversations->links() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Settings -->
                    <div class="col-lg-4 col-md-12">
                        <div class="main-card mt-4">
                            <div class="dashboard-wrap-content p-4">
                                <h5 class="mb-3"><i class="fa-solid fa-sliders me-2"></i>Notification Settings</h5>
                                <form method="POST" action="{{ route('messaging.preferences.update') }}">
                                    @csrf
                                    <div class="form-group mt-3">
                                        <label class="form-label">Default Delivery</label>
                                        <select class="form-control h_40" name="default_delivery">
                                            @foreach(['immediate' => 'Immediate', 'batch' => '15-minute batch', 'digest' => 'Digest', 'disabled' => 'Disabled'] as $value => $label)
                                                <option value="{{ $value }}" @selected($preference->default_delivery === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mt-3">
                                        <label class="form-label">Daily Email Limit</label>
                                        <input class="form-control h_40" type="number" name="daily_email_limit" min="0" max="500" value="{{ $preference->daily_email_limit }}">
                                    </div>
                                    <div class="form-group mt-3">
                                        <label class="form-label">Digest Hour</label>
                                        <input class="form-control h_40" type="number" name="digest_hour" min="0" max="23" value="{{ $preference->digest_hour }}">
                                    </div>
                                    <button class="primary-btn w-100 mt-4" type="submit">Save Settings</button>
                                </form>
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
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.conversation-checkbox');
    const counter = document.getElementById('selectedCounter');
    const bulkButtons = document.querySelectorAll('.bulk-btn');
    const bulkForm = document.getElementById('bulkActionForm');
    const bulkActionInput = document.getElementById('bulkActionInput');

    function updateBulkState() {
        const checked = document.querySelectorAll('.conversation-checkbox:checked');
        const count = checked.length;

        counter.textContent = count + ' selected';

        bulkButtons.forEach(btn => {
            btn.disabled = (count === 0);
        });

        if (selectAll) {
            selectAll.checked = (checkboxes.length > 0 && count === checkboxes.length);
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkState();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkState);
    });

    bulkButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const action = this.getAttribute('data-action');
            if (action && bulkForm && bulkActionInput) {
                bulkActionInput.value = action;
                bulkForm.submit();
            }
        });
    });

    updateBulkState();
});
</script>
@endpush
