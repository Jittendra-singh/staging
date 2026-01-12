<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Appreciation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 40px 0;
            box-sizing: border-box;
        }
        .certificate-card {
            max-width: 700px;
            border: 4px solid #4CAF50;
            background: #fff;
            margin: 40px auto;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.15);
            border-radius: 16px;
            padding: 32px 32px 16px 32px;
        }
        .certificate-title {
            font-family: 'Georgia', serif;
            color: #2E7D32;
            margin-top: 16px;
            margin-bottom: 8px;
        }
        .recipient-name {
            font-family: 'Georgia', serif;
            font-weight: bold;
            font-size: 2rem;
            margin-bottom: 16px;
        }
        .footer-note {
            font-size: 0.95rem;
            color: #888;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto 24px auto;
        }
        .details-table td {
            padding: 8px 6px;
            vertical-align: top;
        }
        .details-label {
            font-weight: bold;
            color: #2E7D32;
            width: 45%;
            white-space: nowrap;
        }
        .details-value {
            width: 55%;
        }
        .text-center {
            text-align: center;
        }
        .text-end {
            text-align: right;
        }
        .btn-outline-success {
            display: inline-block;
            padding: 6px 16px;
            border: 2px solid #4CAF50;
            background: transparent;
            color: #2E7D32;
            border-radius: 6px;
            text-decoration: none;
            font-size: 1rem;
            transition: background 0.2s, color 0.2s;
        }
        .btn-outline-success:hover {
            background: #4CAF50;
            color: #fff;
        }
        .card-footer {
            margin-top: 16px;
        }
        hr {
            border: none;
            border-top: 1px solid #e0e0e0;
            margin: 32px 0;
        }
        @media (max-width: 600px) {
            .certificate-card {
                padding: 16px 4vw 8px 4vw;
            }
            .details-label, .details-value {
                font-size: 0.98rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="certificate-card">
            <div class="text-center">
                <img src="{{ $settings->logo }}" alt="Certificate Badge" style="width:80px;">
                <h2 class="certificate-title">Certificate of Appreciation</h2>
                <p style="font-size: 1.1rem;">This certificate is proudly presented to</p>
                <h3 class="recipient-name">{{ $volunteerName ?? 'Volunteer Name' }}</h3>
                <p>in recognition of your valuable contribution and dedicated efforts during the community cleanup event.</p>
            </div>
            <table class="details-table">
                <tr>
                    <td class="details-label">Event Name:</td>
                    <td class="details-value">
                        <a href="{{route('view-event-details',['slug'=> $event->slug])}}" >{{ $event->event_name }}</a>
                    </td>
                </tr>
                <tr>
                    <td class="details-label">Total Hours Completed:</td>
                    <td class="details-value">{{ $hours ?? '--' }}</td>
                </tr>
                <tr>
                    <td class="details-label">Task/Activity Description:</td>
                    <td class="details-value">{{ $activity ?? '--' }}</td>
                </tr>
                <tr>
                    <td class="details-label">Event Organizer’s Name:</td>
                    <td class="details-value">{{ $organizer_name ?? '--' }}</td>
                </tr>
                <tr>
                    <td class="details-label">Event Organizer’s Contact:</td>
                    <td class="details-value">{{ $organizer_contact ?? '--' }}</td>
                </tr>
                <tr>
                    <td class="details-label">Additional Notes:</td>
                    <td class="details-value">{{ $notes ?? '--' }}</td>
                </tr>
            </table>
            @if(!empty($proof_url))
                <div class="text-center" style="margin-bottom: 24px;">
                    <a href="{{ $proof_url }}" target="_blank" class="btn-outline-success">View Proof of Participation</a>
                </div>
            @endif
            <hr>
            <div class="text-center footer-note">
                Thank you for making a difference in our community!<br>
                <span style="font-style: italic;">This certificate acknowledges your commitment and service.</span>
            </div>
            <div class="card-footer text-end" style="background: transparent; border: none;">
                <span style="font-size: 1rem; color: #2E7D32;">
                    {{ $date ?? \Carbon\Carbon::now()->format('F d, Y') }}
                </span>
            </div>
        </div>
    </div>
</body>
</html>