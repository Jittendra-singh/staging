<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Ticket</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 18px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f6fb;
            color: #101828;
            font-size: 16px;
            line-height: 1.5;
        }

        .ticket-page {
            width: 100%;
            padding: 0;
            margin-top:50px;
        }

        .ticket-card {
            width: 760px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 10px;
            overflow: hidden;
        }

        .ticket-header {
            padding: 42px 48px 34px 48px;
            border-bottom: 1px solid #eef2f7;
        }

        .ticket-badge {
            display: inline-block;
            background: #111827;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 8px 18px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .ticket-edition {
            color: #2563eb;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            text-align: right;
            white-space: nowrap;
        }

        .event-title {
            margin-top: 34px;
            font-size: 46px;
            line-height: 1.1;
            font-weight: 800;
            color: #111827;
            text-transform: uppercase;
        }

        .title-line {
            width: 70px;
            height: 5px;
            background: #2563eb;
            margin-top: 22px;
            border-radius: 4px;
        }

        .ticket-body {
            padding: 36px 48px 18px 48px;
        }

        .section {
            padding: 24px 0;
            border-bottom: 1px solid #eef2f7;
        }

        .label {
            color: #94a3b8;
            font-size: 13px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            margin-bottom: 13px;
        }

        .value {
            color: #111827;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.55;
        }

        .details-table td {
            width: 50%;
            vertical-align: top;
            padding: 24px 0;
        }

        .details-table td:first-child {
            padding-right: 28px;
        }

        .details-table td:last-child {
            padding-left: 28px;
        }

        .ticket-footer {
            background: #f8fafc;
            padding: 34px 48px;
            text-align: center;
        }

        .footer-note {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 700;
        }

        .muted {
            color: #64748b;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .header-table td {
            width: 50%;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    @php
        $ticketType = 'N/A';

        if (!empty($ticket->ticket)) {
            $ticketName = $ticket->ticket->ticket_name ?? '';
            $ticketCategory = !empty($ticket->ticket->ticket_type) ? ucfirst($ticket->ticket->ticket_type) : '';

            $ticketType = trim($ticketName . ($ticketCategory ? ' - ' . $ticketCategory : ''));
        }

        $orderNumber = $order->order_number ?? 'N/A';
        $ticketNumber = $ticket->ticket_number ?? 'N/A';
        $editionYear = !empty($event->start_datetime)
            ? \Carbon\Carbon::parse($event->start_datetime)->format('Y')
            : \Carbon\Carbon::now()->format('Y');
    @endphp

    <div class="ticket-page">
        <div class="ticket-card">
            <div class="ticket-header">
                <table class="header-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <span class="ticket-badge">Official Event Ticket</span>
                        </td>
                        <td class="ticket-edition">
                            {{ $editionYear }} Edition
                        </td>
                    </tr>
                </table>

                <div class="event-title">
                    {{ $eventName ?? 'Event Ticket' }}
                </div>
                <div class="title-line"></div>
            </div>

            <div class="ticket-body">
                <div class="section">
                    <div class="label">Venue</div>
                    <div class="value">{{ $eventVenue ?? 'N/A' }}</div>
                </div>

                <div class="section">
                    <div class="label">Date &amp; Time</div>
                    <div class="value">{{ $eventDate ?? 'N/A' }}</div>
                </div>

                <table class="details-table">
                    <tr>
                        <td>
                            <div class="label">Attendee</div>
                            <div class="value">{{ $volunteerName ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="label">Ticket Type</div>
                            <div class="value">{{ $ticketType ?: 'N/A' }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="label">Ticket Number</div>
                            <div class="value">{{ $ticketNumber }}</div>
                        </td>
                        <td>
                            <div class="label">Order ID</div>
                            <div class="value">#{{ $orderNumber }}</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="ticket-footer">
                <div class="footer-note">Please present this ticket at the event entrance</div>
            </div>
        </div>
    </div>
</body>
</html>