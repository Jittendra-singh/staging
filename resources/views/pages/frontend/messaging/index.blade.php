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

                    <div class="col-md-12">
                        <div class="main-card mt-4">
                            <div class="dashboard-wrap-content p-4">
                                <form method="GET" action="{{ route('contact-lists') }}" class="row g-3 align-items-end">
                                    <div class="col-lg-6 col-md-8">
                                        <label class="form-label">Search</label>
                                        <input class="form-control h_40" type="text" name="search" value="{{ $search }}" placeholder="Search conversations">
                                    </div>
                                    <div class="col-lg-3 col-md-4">
                                        <button class="primary-btn w-100" type="submit">Search</button>
                                    </div>
                                    <div class="col-lg-3 col-md-12 text-lg-end">
                                        @if($showArchived)
                                            <a href="{{ route('contact-lists') }}" class="primary-btn btn-hover w-100">Active Inbox</a>
                                        @else
                                            <a href="{{ route('contact-lists', ['archived' => 1]) }}" class="primary-btn btn-hover w-100">Archived</a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-12">
                        <div class="main-card mt-4">
                            <div class="dashboard-wrap-content p-4">
                                <div class="table-card">
                                    <div class="main-table">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Conversation</th>
                                                        <th>Latest Message</th>
                                                        <th>Updated</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($conversations as $conversation)
                                                        @php
                                                            $currentUser = auth()->guard('front')->user();
                                                            $other = $conversation->participants->firstWhere('volunteer_id', '!=', $currentUser->id)?->volunteer;
                                                            $unread = $conversation->unreadCountFor($currentUser);
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <div class="fw-semibold">
                                                                    {{ $conversation->subject ?: 'Conversation' }}
                                                                    @if($unread > 0)
                                                                        <span class="badge bg-primary ms-2">{{ $unread }}</span>
                                                                    @endif
                                                                </div>
                                                                <div class="text-muted small">
                                                                    {{ $other ? trim($other->first_name.' '.$other->last_name) : 'Participant' }}
                                                                </div>
                                                            </td>
                                                            <td>{{ \Illuminate\Support\Str::limit($conversation->latestMessage?->body ?: 'No messages yet', 90) }}</td>
                                                            <td>{{ optional($conversation->last_message_at ?: $conversation->updated_at)->format('M d, Y h:i A') }}</td>
                                                            <td class="text-end">
                                                                <a href="{{ route('messaging.conversations.show', $conversation) }}" class="primary-btn w-fit">Open</a>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center">
                                                                {{ $showArchived ? 'No archived conversations available.' : 'No conversations available.' }}
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    {{ $conversations->links() }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <div class="main-card mt-4">
                            <div class="dashboard-wrap-content p-4">
                                <h5 class="mb-3">Notification Settings</h5>
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
