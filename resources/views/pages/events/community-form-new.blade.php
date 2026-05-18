@php
    $imgDir = public_path('assets_front/img/community-form');
    $topWave = file_exists($imgDir . '/top-wave.png') ? base64_encode(file_get_contents($imgDir . '/top-wave.png')) : '';
    $bottomWave = file_exists($imgDir . '/bottom-wave.png') ? base64_encode(file_get_contents($imgDir . '/bottom-wave.png')) : '';
    $footerLogos = file_exists($imgDir . '/footer-logos.png') ? base64_encode(file_get_contents($imgDir . '/footer-logos.png')) : '';
    $signImgPath = public_path('assets_front/images/sign_img.png');
    $signImg = file_exists($signImgPath) ? base64_encode(file_get_contents($signImgPath)) : '';
    $gradientPath = $imgDir . '/page-gradient.jpg';
    if (!file_exists($gradientPath) && file_exists($imgDir . '/page-gradient.svg')) {
        $gradientPath = $imgDir . '/page-gradient.svg';
    }
    $gradientBg = file_exists($gradientPath) ? base64_encode(file_get_contents($gradientPath)) : '';
    $gradientMime = str_ends_with($gradientPath, '.png') ? 'image/png' : 'image/svg+xml';
    $gradientUrl = $gradientBg ? 'data:' . $gradientMime . ';base64,' . $gradientBg : '';
    $eventDate = $event->eventStartDateTime ?? '--';
    $eventType = ($event->category->name ?? 'Community') . ' Cleanup';
    $organizerDisplay = trim(($organization_name ?? '') . ($organizer_name && $organization_name ? ', ' : '') . ($organizer_name ?? ''));
    $pad = '85px';
    $textDark = '#2d3436';
    $textMuted = '#4a5568';
    $blueTitle = '#1a5f9e';
    $greenTitle = '#2d8f5f';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Community Service Certificate</title>
    <style>
        @page {
            margin: 0;
            size: letter portrait;
            background-color: #d4eaf5;
            @if($gradientUrl)
            background-image: url('{{ $gradientUrl }}');
            background-repeat: no-repeat;
            background-size: 100% 100%;
            @endif
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #d4eaf5;
        }
        table { border-collapse: collapse; }
        .page-bg-layer {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            z-index: -1000;
            line-height: 0;
            font-size: 0;
        }
        .page-bg-layer img {
            display: block;
            width: 100%;
            height: 100%;
            border: 0;
            margin: 0;
            padding: 0;
        }
        .page-content {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#d4eaf5;">

@if($gradientUrl)
<div class="page-bg-layer">
    <img src="{{ $gradientUrl }}" width="816" height="1056" alt="">
</div>
@endif

@if($bottomWave)
<div style="position:fixed;bottom:0;right:0;margin:0;padding:0;line-height:0;font-size:0;z-index:2;">
    <img src="data:image/png;base64,{{ $bottomWave }}" width="235" alt="" style="display:block;width:235px;height:auto;border:0;margin:0;padding:0;">
</div>
@endif

<table class="page-content" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;border-collapse:collapse;">

    <tr>
        <td style="padding:0;background:transparent;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="30%" valign="top" align="left" style="padding:0;line-height:0;font-size:0;background:transparent;">
                        @if($topWave)
                        <img src="data:image/png;base64,{{ $topWave }}" width="200" alt="" style="width:200px;height:auto;display:block;border:0;">
                        @endif
                    </td>
                    <td width="40%" valign="middle" align="center" style="padding:12px 0 10px;text-align:center;background:transparent;">
                        <img src="https://volunteercleanup.org/wp-content/uploads/2021/02/logo.webp" width="158" alt="" style="width:158px;height:auto;border:0;">
                    </td>
                    <td width="30%" style="background:transparent;">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td align="center" style="padding:4px {{ $pad }} 4px;font-size:32px;font-weight:bold;color:{{ $blueTitle }};letter-spacing:1px;line-height:1.1;background:transparent;">
            COMMUNITY
        </td>
    </tr>
    <tr>
        <td align="center" style="padding:0 {{ $pad }} 16px;font-size:26px;font-weight:bold;color:{{ $greenTitle }};letter-spacing:1px;line-height:1.1;background:transparent;">
            SERVICE CERTIFICATE
        </td>
    </tr>

    <tr>
        <td style="padding:0 {{ $pad }} 0;background:transparent;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td align="center" style="font-size:20px;color:{{ $textMuted }};padding:0 0 14px;line-height:1.45;">This certifies that</td>
                </tr>
                <tr>
                    <td align="center" style="font-size:30px;font-weight:bold;color:{{ $textDark }};padding:0 0 12px;line-height:1.15;">{{ $volunteerName ?? 'Volunteer Name' }}</td>
                </tr>
                <tr>
                    <td align="center" style="font-size:20px;color:{{ $textMuted }};padding:0 0 12px;line-height:1.55;">
                        has completed <strong style="color:{{ $textDark }};">{{ $hours ?? '--' }}</strong> hours of community service
                    </td>
                </tr>
                <tr>
                    <td align="center" style="font-size:20px;color:{{ $textMuted }};padding:0 0 20px;line-height:1.55;">
                        at <strong style="color:{{ $textDark }};">{{ $event->event_name }}</strong> on <strong style="color:{{ $textDark }};">{{ $eventDate }}</strong>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding:6px 120px 0;background:transparent;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="23%" style="font-size:15px;color:{{ $textMuted }};padding:8px 14px 8px 0;vertical-align:top;line-height:1.45;">Event Type:</td>
                    <td width="77%" style="font-size:15px;color:{{ $textDark }};padding:8px 0;vertical-align:top;line-height:1.45;">{{ $eventType }}</td>
                </tr>
                <tr>
                    <td style="font-size:15px;color:{{ $textMuted }};padding:8px 14px 8px 0;vertical-align:top;line-height:1.45;">Location:</td>
                    <td style="font-size:15px;color:{{ $textDark }};padding:8px 0;vertical-align:top;line-height:1.45;">{{ $event->fullAddress ?? '--' }}</td>
                </tr>
                <tr>
                    <td style="font-size:15px;color:{{ $textMuted }};padding:8px 14px 8px 0;vertical-align:top;line-height:1.45;">Organizer:</td>
                    <td style="font-size:15px;color:{{ $textDark }};padding:8px 0;vertical-align:top;line-height:1.45;">{{ $organizerDisplay ?: '--' }}</td>
                </tr>
                <tr>
                    <td style="font-size:15px;color:{{ $textMuted }};padding:8px 14px 8px 0;vertical-align:top;line-height:1.45;">Contact:</td>
                    <td style="font-size:15px;color:{{ $textDark }};padding:8px 0;vertical-align:top;line-height:1.45;"><a href="mailto:{{ $organization_email ?? '--' }}">{{ $organization_email ?? '--' }}</a></td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding:22px {{ $pad }} 12px;background:transparent;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="38%">&nbsp;</td>
                    <td width="62%" align="right" valign="top" style="text-align:right;">
                        <table cellpadding="0" cellspacing="0" border="0" align="right">
                            @if($signImg)
                            <tr>
                                <td align="right" style="padding:0 0 10px;text-align:right;">
                                    <img src="data:image/png;base64,{{ $signImg }}" width="130" alt="" style="width:130px;height:auto;border:0;display:block;margin-left:auto;">
                                </td>
                            </tr>
                            @else
                            <tr><td align="right" style="height:30px;font-size:1px;">&nbsp;</td></tr>
                            @endif
                            <tr>
                                <td align="right" style="padding:0 0 10px;text-align:right;">
                                    <table cellpadding="0" cellspacing="0" border="0" align="right" width="220" style="width:220px;">
                                        <tr><td style="border-bottom:1px solid #333;font-size:1px;line-height:1px;">&nbsp;</td></tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" style="font-size:14px;color:{{ $textDark }};line-height:1.6;text-align:right;">
                                    Dave Doebler, Executive Director<br>dave@volunteercleanup.org
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    @if($footerLogos)
    <tr>
        <td align="center" style="padding:12px {{ $pad }} 48px;text-align:center;background:transparent;">
            <img src="data:image/png;base64,{{ $footerLogos }}" width="275" alt="" style="width:275px;height:auto;border:0;display:inline-block;">
        </td>
    </tr>
    @endif

</table>

</body>
</html>
