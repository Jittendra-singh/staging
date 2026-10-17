<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Volunteer Check-In List</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #172033;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .pdf-wrap {
            padding: 24px 28px;
        }

        .pdf-top {
            display: table;
            width: 100%;
            border-bottom: 1px solid #d9dee8;
            padding-bottom: 12px;
            margin-bottom: 22px;
        }

        .pdf-top-left,
        .pdf-top-right {
            display: table-cell;
            vertical-align: top;
            font-size: 12px;
            font-weight: 700;
        }

        .pdf-top-right {
            text-align: right;
        }

        h1 {
            font-size: 24px;
            margin: 0 0 20px;
            font-weight: 800;
        }

        .event-meta {
            display: table;
            width: 100%;
            border-top: 1px solid #e1e5ec;
            border-bottom: 1px solid #e1e5ec;
            padding: 12px 0;
            margin-bottom: 22px;
        }

        .event-meta-item {
            display: table-cell;
            width: 33.333%;
            vertical-align: top;
        }

        .meta-label {
            display: block;
            font-size: 8px;
            color: #6b7588;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: .5px;
            margin-bottom: 4px;
        }

        .meta-value {
            font-size: 11px;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            font-size: 8px;
            text-transform: uppercase;
            color: #172033;
            letter-spacing: .4px;
            padding: 10px 8px;
            border-bottom: 2px solid #5c6470;
            text-align: left;
        }

        tbody td {
            padding: 14px 8px;
            border-bottom: 1px solid #d9dee8;
            vertical-align: top;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .small-muted {
            color: #6b7588;
        }

        .nowrap {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="pdf-wrap">
        <div class="pdf-top">
            <div class="pdf-top-left">VolunteerCleanup.org</div>
            <div class="pdf-top-right">{{ $event->event_name }}</div>
        </div>

        <h1>Volunteer Check-In List</h1>

        <div class="event-meta">
            <div class="event-meta-item">
                <span class="meta-label">Event</span>
                <span class="meta-value">{{ $event->event_name }}</span>
            </div>
            <div class="event-meta-item">
                <span class="meta-label">Location</span>
                <span class="meta-value">{{ $event->venue ?: $event->fullAddress }}</span>
            </div>
            <div class="event-meta-item">
                <span class="meta-label">Date</span>
                <span class="meta-value">{{ $event->start_datetime ? $event->start_datetime->format('F d, Y') : 'N/A' }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 17%;">Volunteer Name</th>
                    <th style="width: 20%;">Email</th>
                    <th style="width: 14%;">Ticket Type</th>
                    <th style="width: 10%;">Waiver</th>
                    <th style="width: 11%;">Status</th>
                    <th style="width: 13%;">Check-in Time</th>
                    <th style="width: 11%;">Reg. Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrations as $key => $registration)
                    @php
                        $volunteer = $registration->volunteer;
                        $ticket = $registration->ticket;
                        $isSigned = !empty($registration->waiver_filled_at);
                        $isCheckedIn = $registration->checkin == 1;
                    @endphp
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            <strong>{{ $volunteer ? $volunteer->name : 'N/A' }}</strong>
                            @php
                                $activeChildren = $registration->children ? $registration->children->whereNull('cancelled_at') : collect();
                            @endphp
                            @if($activeChildren->isNotEmpty())
                                <div style="margin-top: 4px; padding-top: 4px; border-top: 1px dashed #d9dee8; font-size: 8.5px; color: #495057;">
                                    <strong>Accompanying Children ({{ $activeChildren->count() }}):</strong>
                                    @foreach($activeChildren as $child)
                                        <div>• {{ $child->child_name }} (Age {{ $child->child_age }}) — <em>{{ $child->checkin == 1 ? 'Checked In' : 'Pending' }}</em>@if(!empty($child->ticket_answer)) | <strong>Ans:</strong> {{ $child->ticket_answer }}@endif</div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td>{{ $volunteer ? $volunteer->email_address : 'N/A' }}</td>
                        <td>{{ $ticket ? $ticket->ticket_name : 'N/A' }}</td>
                        <td>{{ $isSigned ? 'Signed' : 'Need Sign' }}</td>
                        <td>{{ $isCheckedIn ? 'Checked In' : 'Pending' }}</td>
                        <td>
                            @if($isCheckedIn && !empty($registration->checkin_dt))
                                {{ date('M d, Y h:i A', strtotime($registration->checkin_dt)) }}
                            @else
                                <span class="small-muted">—</span>
                            @endif
                        </td>
                        <td class="nowrap">
                            {{ $registration->createdDateTimeStr ? \Carbon\Carbon::parse($registration->createdDateTimeStr)->format('M d, Y') : 'N/A' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No attendees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>