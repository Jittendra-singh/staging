@extends('layouts.frontend')

@push('style')
<style>
    /* Styling for the new Waitlist Management mockup */
    .waitlist-page-container {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
        min-height: 100vh;
        padding-top: 30px;
        padding-bottom: 60px;
        border-radius: 12px;
    }
    .page-title {
        color: #1e3a8a; /* dark blue */
        font-weight: 700;
        font-size: 28px;
        margin-bottom: 4px;
    }
    .page-subtitle {
        color: #475569;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 12px;
    }
    .breadcrumb-text {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }
    .breadcrumb-text a {
        color: #64748b;
        text-decoration: none;
    }
    .breadcrumb-text span {
        margin: 0 6px;
    }
    .breadcrumb-text .active {
        color: #1e293b;
        font-weight: 600;
    }
    
    .card-custom {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .capacity-card {
        padding: 24px;
        height: 100%;
    }
    .capacity-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .capacity-title i {
        color: #3b82f6;
    }
    .capacity-stat {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        color: #475569;
        font-weight: 500;
        margin-bottom: 16px;
    }
    .capacity-stat .value {
        color: #3b82f6;
        font-weight: 700;
    }
    .capacity-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 16px 0;
    }
    .waitlist-stat {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
    }
    .waitlist-stat i {
        color: #ef4444;
    }
    
    .filter-card {
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .form-control-custom, .form-select-custom {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
        color: #475569;
        padding: 10px 16px;
    }
    
    .btn-search {
        background-color: #2563eb;
        color: white;
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 500;
        border: none;
    }
    .btn-export {
        background-color: #1e3a8a;
        color: white !important;
        border-radius: 8px;
        padding: 8px 20px;
        font-weight: 500;
        border: none;
    }
    
    /* Table Styles */
    .table-container {
        margin-top: 24px;
        overflow: hidden;
    }
    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }
    .table-custom th {
        background-color: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }
    .table-custom td {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #475569;
        vertical-align: middle;
    }
    .table-custom tr:last-child td {
        border-bottom: none;
    }
    
    .volunteer-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        color: white;
    }
    .volunteer-name {
        font-weight: 600;
        color: #1e293b;
    }
    
    .badge-ticket {
        background-color: #f1f5f9;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .badge-ticket.student {
        background-color: #eff6ff;
        color: #3b82f6;
    }
    
    .badge-status {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-status::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .badge-status.waiting {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    .badge-status.waiting::before {
        background-color: #ef4444;
    }
    .badge-status.registered {
        background-color: #dcfce3;
        color: #166534;
    }
    .badge-status.registered::before {
        background-color: #22c55e;
    }
    
    .pagination-footer {
        padding: 16px 24px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .pagination-text {
        font-size: 13px;
        color: #64748b;
    }
    .pagination-controls {
        display: flex;
        gap: 4px;
    }
    .page-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 6px;
        color: #475569;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
    }
    .page-btn.active {
        background-color: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }
</style>
@endpush

@section('content')
<div class="wrapper wrapper-body">
    <div class="dashboard-body">
        <div class="container-fluid">
            <div class="waitlist-page-container pt-4">
                <div class="container">
        
        <!-- Header Section -->
        <div class="mb-4">
            <h1 class="page-title">Waitlist Management</h1>
            <div class="page-subtitle">{{ $event->event_name ?? 'None' }}</div>
            <div class="breadcrumb-text">
                <a href="{{ route('front-dashboard') }}">Dashboard</a> <span>&gt;</span>
                <a href="{{ route('front-cleanup-hosting', 'organizer') }}">Cleanups I'm Hosting</a> <span>&gt;</span>
                <span class="active">Waitlist Management</span>
            </div>
        </div>
        
        <!-- Top Cards -->
        <div class="row g-4 mb-4">
            <!-- Capacity Card -->
            <div class="col-md-4">
                <div class="card-custom capacity-card">
                    <div class="capacity-title">
                        Event Capacity <i class="fa-solid fa-user-group"></i>
                    </div>
                    <div class="capacity-stat">
                        <span>Registered</span>
                        <span class="value">{{ $registeredCount }} / {{ $totalCapacity }}</span>
                    </div>
                    <div class="capacity-divider"></div>
                    <div class="waitlist-stat">
                        <i class="fa-solid fa-hourglass-half"></i> <span><strong style="color: #ef4444;">{{ $totalWaitlistCount }}</strong> volunteers on waitlist</span>
                    </div>
                </div>
            </div>
            
            <!-- Filters Card -->
            <div class="col-md-8">
                <div class="card-custom filter-card">
                    <form id="waitlistDetailSearchForm" method="GET" action="{{ route('waitlist.detail', ['event_id' => $selectedEventId]) }}">
                        <input type="hidden" name="event_id" value="{{ $selectedEventId }}">
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <input type="text" id="waitlistDetailSearchInput" name="search" value="{{ request('search') }}" class="form-control form-control-custom w-100" placeholder="Search Volunteers by name or email id">
                            </div>
                            <div class="col-md-4">
                                <select class="form-select form-select-custom w-100" onchange="window.location.href='{{ url('dashboard/waitlist-detail') }}/' + this.value">
                                    @foreach($events as $ev)
                                        <option value="{{ $ev->id }}" {{ $selectedEventId == $ev->id ? 'selected' : '' }}>{{ $ev->event_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="ticketFilter" name="ticket_id" class="form-select form-select-custom w-100">
                                    <option value="all">All Ticket Types</option>
                                    @foreach($tickets as $ticket)
                                        <option value="{{ $ticket->id }}" {{ request('ticket_id') == $ticket->id ? 'selected' : '' }}>{{ $ticket->ticket_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn-search"><i class="fa-solid fa-search me-1"></i> Search</button>
                            <a id="waitlist-export-btn" href="{{ route('export.waitlist', ['event_id' => $selectedEventId, 'search' => request('search')]) }}" class="btn-export text-decoration-none d-inline-flex align-items-center"><i class="fa-solid fa-download me-1"></i> Export CSV</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Table Card -->
        <div id="waitlist-table-card" class="card-custom table-container">
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>VOLUNTEER</th>
                            <th>EMAIL</th>
                            <th>TICKET TYPE</th>
                            <th>STATUS</th>
                            <th>JOINED WAITLIST</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($waitlists as $entry)
                        <tr>
                            <td>
                                <div class="volunteer-cell">
                                    @php
                                        $initials = strtoupper(substr($entry->first_name, 0, 1) . substr($entry->last_name, 0, 1));
                                        $colors = ['#6366f1', '#6ee7b7', '#7dd3fc', '#818cf8', '#f87171', '#fbbf24'];
                                        $bgColor = $colors[strlen($entry->first_name) % count($colors)];
                                    @endphp
                                    <div class="avatar" style="background-color: {{ $bgColor }};">{{ $initials }}</div>
                                    <span class="volunteer-name">{{ $entry->first_name }} {{ $entry->last_name }}</span>
                                </div>
                            </td>
                            <td>{{ $entry->email }}</td>
                            <td>
                                <span class="badge-ticket {{ strtolower($entry->ticket->ticket_name ?? '') == 'vip' ? 'student' : '' }}" {!! strtolower($entry->ticket->ticket_name ?? '') == 'vip' ? 'style="background-color: #e0e7ff; color: #4338ca;"' : '' !!}>
                                    {{ $entry->ticket->ticket_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if(strtolower($entry->status) == 'waiting')
                                    <span class="badge-status waiting">Waiting</span>
                                @else
                                    <span class="badge-status registered">{{ ucfirst($entry->status) }}</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($entry->joined_at)->format('M d, Y - h:i A') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">No volunteers found on the waitlist.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination Footer -->
            @if($waitlists->hasPages() || $waitlists->total() > 0)
            <div class="pagination-footer">
                <div class="pagination-text">
                    Showing <strong>{{ $waitlists->firstItem() ?? 0 }}</strong> to <strong>{{ $waitlists->lastItem() ?? 0 }}</strong> of <strong>{{ $waitlists->total() }}</strong> volunteers
                </div>
                <div class="pagination-controls">
                    {{ $waitlists->links('pagination::bootstrap-4') }}
                </div>
            </div>
            @endif
        </div>
        
        
        <!-- AJAX Script for filtering -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const searchInput = document.getElementById('waitlistDetailSearchInput');
                const searchForm = document.getElementById('waitlistDetailSearchForm');
                const ticketFilter = document.getElementById('ticketFilter');
                
                if (searchInput && searchForm) {
                    let timeout = null;
                    
                    function performSearch() {
                        const url = new URL(searchForm.action);
                        url.searchParams.set('event_id', searchForm.querySelector('[name="event_id"]').value);
                        if (searchInput.value) {
                            url.searchParams.set('search', searchInput.value);
                        }
                        if (ticketFilter && ticketFilter.value && ticketFilter.value !== 'all') {
                            url.searchParams.set('ticket_id', ticketFilter.value);
                        }
                        
                        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                
                                // Replace table card content
                                const newTableCard = doc.getElementById('waitlist-table-card');
                                const oldTableCard = document.getElementById('waitlist-table-card');
                                if (newTableCard && oldTableCard) {
                                    oldTableCard.innerHTML = newTableCard.innerHTML;
                                }
                                
                                // Update export link
                                const newExport = doc.getElementById('waitlist-export-btn');
                                const oldExport = document.getElementById('waitlist-export-btn');
                                if (newExport && oldExport) {
                                    oldExport.href = newExport.href;
                                }
                            });
                    }
                    
                    // Remove the auto-submit 'input' listener
                    // Search will only trigger on form submission (pressing Enter or clicking the Search button)

                    // Trigger search on ticket filter change
                    if (ticketFilter) {
                        ticketFilter.addEventListener('change', performSearch);
                    }

                    // Prevent full page reload on Enter
                    searchForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        clearTimeout(timeout);
                        performSearch();
                    });
                }
            });
        </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
