<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Volunteer Cleanup Email')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Bootstrap-like minimal email styles */
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #212529;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .email-header {
            background: #0052CC;
            color: #fff;
            padding: 24px 32px;
            text-align: center;
        }
        .email-header img {
            max-height: 48px;
            margin-bottom: 8px;
        }
        .email-body {
            padding: 32px;
        }
        .email-footer {
            background: #f1f3f4;
            color: #6c757d;
            font-size: 13px;
            text-align: center;
            padding: 18px 32px;
        }
        @media (max-width: 600px) {
            .email-container, .email-body, .email-header, .email-footer {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }
            .email-body {
                padding: 18px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            {{-- Optional: Place your logo below --}}
            <img src="{{ asset($settings->logo) }}" alt="{{env('APP_NAME')}}">
            <!-- <h2 style="margin: 0; font-weight: 600;">{{env('APP_NAME')}}</h2> -->
        </div>
        <div class="email-body">
            @yield('content')
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} {{env('APP_NAME')}}. All rights reserved.<br>
            If you did not expect this email, please ignore it.
        </div>
    </div>
    @stack('script')
</body>
</html>