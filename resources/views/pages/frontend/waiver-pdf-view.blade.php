<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Liability Waiver &amp; Release</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #ffffff;
            color: #1a1a1a;
            font-size: 12px;
            line-height: 1.5;
        }

        /* ── HEADER ── */
        .header-table {
            width: 100%;
            background-color: #1a1a1a;
            padding: 12px 36px;
        }

        .header-logo {
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .header-logo-green {
            color: #4caf8c;
        }

        .header-badge {
            color: #aaaaaa;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: right;
            line-height: 1.6;
        }

        /* ── TITLE ── */
        .title-wrap {
            padding: 22px 36px 14px 36px;
            border-bottom: 3px solid #1a1a1a;
        }

        .doc-label {
            font-size: 7.5px;
            font-weight: bold;
            color: #888888;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .doc-title {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 26px;
            font-weight: bold;
            color: #1a1a1a;
            letter-spacing: -0.5px;
            line-height: 1.1;
            margin-bottom: 4px;
        }

        .doc-subtitle {
            font-size: 7.5px;
            color: #999999;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* ── EVENT DETAIL STRIP ── */
        .detail-strip {
            width: 100%;
            background-color: #f8f8f8;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-strip td {
            padding: 12px 16px;
            border-right: 1px solid #e0e0e0;
            vertical-align: top;
            width: 25%;
        }

        .detail-strip td:first-child {
            padding-left: 36px;
        }

        .detail-strip td:last-child {
            border-right: none;
            padding-right: 36px;
        }

        .detail-label {
            font-size: 7px;
            font-weight: bold;
            color: #999999;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 11.5px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .detail-value-green {
            font-size: 11.5px;
            font-weight: bold;
            color: #2e7d5e;
        }

        /* ── BODY ── */
        .body-wrap {
            padding: 22px 36px;
        }

        .intro-text {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 11.5px;
            color: #333333;
            line-height: 1.75;
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e8e8e8;
        }

        .intro-strong {
            color: #2e7d5e;
            font-weight: bold;
        }

        /* Waiver terms wrapper — for dynamic content from DB */
        .waiver-terms-wrap {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 11px;
            color: #444444;
            line-height: 1.75;
        }

        /* Override any inline styles the rich-text editor may inject */
        .waiver-terms-wrap p {
            margin-bottom: 10px;
        }

        .waiver-terms-wrap ul,
        .waiver-terms-wrap ol {
            margin-left: 18px;
            margin-bottom: 10px;
        }

        .waiver-terms-wrap li {
            margin-bottom: 4px;
        }

        .waiver-terms-wrap strong,
        .waiver-terms-wrap b {
            color: #1a1a1a;
        }

        /* Fallback section blocks */
        .section-block {
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        .section-number {
            font-size: 7px;
            font-weight: bold;
            color: #4caf8c;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .section-heading {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-body {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 11px;
            color: #444444;
            line-height: 1.75;
        }

        /* ── SIGNATURE SECTION ── */
        .sig-outer {
            margin: 6px 36px 0 36px;
            padding: 18px 20px;
            background-color: #f8f8f8;
            border: 1px solid #e0e0e0;
        }

        .sig-table {
            width: 100%;
        }

        .sig-table td {
            vertical-align: top;
            width: 50%;
            padding-right: 24px;
        }

        .sig-table td:last-child {
            padding-right: 0;
            padding-left: 24px;
            border-left: 1px solid #e8e8e8;
        }

        .sig-label {
            font-size: 7px;
            font-weight: bold;
            color: #999999;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .sig-name-text {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            border-bottom: 1.5px solid #1a1a1a;
            padding-bottom: 3px;
        }

        .sig-image {
            max-height: 60px;
            max-width: 200px;
            display: block;
            border-bottom: 1.5px solid #1a1a1a;
            padding-bottom: 3px;
        }

        .sig-image-fallback {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 24px;
            font-style: italic;
            color: #2e7d5e;
            border-bottom: 1.5px solid #1a1a1a;
            padding-bottom: 3px;
        }

        .sig-spacer {
            height: 12px;
        }

        .verified-badge {
            display: inline-block;
            background-color: #e8f5f0;
            border: 1px solid #4caf8c;
            padding: 3px 9px;
            margin-top: 10px;
        }

        .verified-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            background-color: #4caf8c;
            border-radius: 50%;
            margin-right: 4px;
            vertical-align: middle;
        }

        .verified-text {
            font-size: 7.5px;
            font-weight: bold;
            color: #2e7d5e;
            letter-spacing: 1px;
            text-transform: uppercase;
            vertical-align: middle;
        }

        .sig-date-value {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a1a;
            border-bottom: 1.5px solid #1a1a1a;
            padding-bottom: 3px;
        }

        .sig-disclosure {
            margin-top: 12px;
            font-size: 7.5px;
            color: #aaaaaa;
            line-height: 1.65;
        }

        /* ── FOOTER ── */
        .footer-table {
            width: 100%;
            margin-top: 24px;
            background-color: #f2f2f2;
            /* border-top: 1px solid #e0e0e0; */
            padding: 12px 36px;
        }

        .footer-left {
            font-size: 7.5px;
            color: #aaaaaa;
            line-height: 1.6;
        }

        .footer-right {
            font-size: 7.5px;
            color: #aaaaaa;
            text-align: right;
            line-height: 1.6;
        }
    </style>
</head>
<body>

    <!-- ══ HEADER ══ -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header-logo">
                <span class="header-logo-green">VolunteerCleanup.org</span>
            </td>
            <td class="header-badge">
                {{ \Carbon\Carbon::now()->format('M d, Y') }} at {{ \Carbon\Carbon::now()->format('H:i:s') }} UTC
            </td>
        </tr>
    </table>

    <!-- ══ TITLE ══ -->
    <div class="title-wrap">
        <div class="doc-label">Signer Agreement</div>
        <div class="doc-title">Liability Waiver &amp; Release</div>
        <div class="doc-subtitle">Official Record &nbsp;&middot;&nbsp; Executed Electronically &nbsp;&middot;&nbsp; Legal Binding</div>
    </div>

    <!-- ══ EVENT DETAIL STRIP ══ -->
    <table class="detail-strip" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="detail-label">Event Name</div>
                <div class="detail-value">{{ $eventName ?? '' }}</div>
            </td>
            <td>
                <div class="detail-label">Event Date</div>
                <div class="detail-value">{{ $eventDateTime ?? '' }}</div>
            </td>
            <td>
                <div class="detail-label">Location</div>
                <div class="detail-value">{{ $eventVenue ?? '' }}</div>
            </td>
            <td>
                <div class="detail-label">Signer Name</div>
                {{-- Strip any HTML tags that may be in the name string --}}
                <div class="detail-value-green">{{ strip_tags($signerName ?? '') }}</div>
            </td>
        </tr>
    </table>

    <!-- ══ BODY ══ -->
    <div class="body-wrap">

        <!-- Dynamic waiver terms from DB -->
        @if(!empty($waiverTerms))
            <div class="waiver-terms-wrap">
                {!! $waiverTerms !!}
            </div>
        @else
            <div class="section-block">
                <div class="section-number" style="text-align:center;">No Content</div>
            </div>
        @endif

    </div><!-- /.body-wrap -->

    <!-- ══ SIGNATURE ══ -->
    <div class="sig-outer">
        <table class="sig-table" cellpadding="0" cellspacing="0">
            <tr>
                <!-- Left: name + drawn signature -->
                <td>
                    <div class="sig-label">Participant Full Name</div>
                    <div class="sig-name-text">{{ strip_tags($signerName ?? '') }}</div>

                    <div class="sig-spacer"></div>

                    <div class="sig-label">Date Signed</div>
                    <div class="sig-date-value">
                        {{ !empty($ticket->waiver_filled_at)
                            ? \Carbon\Carbon::parse($ticket->waiver_filled_at)->format('M d, Y')
                            : \Carbon\Carbon::now()->format('M d, Y') }}
                    </div>

                </td>

                <!-- Right: date + disclosure -->
                <td class="text-center" style="text-align: center;">
                    <div class="sig-label">Digital Signature Certificate</div>
                    @if(!empty($ticket->signature))
                        <img src="{{ $ticket->signature }}" class="sig-image" alt="Signature">
                    @else
                        <div class="sig-image-fallback">{{ strip_tags($signerName ?? '') }}</div>
                    @endif

                    <div class="sig-spacer"></div>

                    <div class="verified-badge">
                        <span class="verified-dot"></span>
                        <span class="verified-text">Verified</span>
                    </div>
                    <div class="sig-disclosure">
                        Identity authenticated and document sealed via VolunteerCleanup.org secure portal. Hand-drawn signature facsimile generated upon authorization.
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ══ FOOTER ══ -->
    <table class="footer-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="footer-left">
                {{ config('app.name') }}<br>
                This document is an official record generated on
                {{ \Carbon\Carbon::now()->format('M d, Y') }} at {{ \Carbon\Carbon::now()->format('H:i:s') }} UTC
            </td>
        </tr>
    </table>

</body>
</html>
