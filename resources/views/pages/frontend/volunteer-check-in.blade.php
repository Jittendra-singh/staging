@extends('layouts.frontend')

@section('content')
<div class="wrapper wrapper-body">
    <div class="dashboard-body">
        <div class="container-fluid">
            <div class="row">
                <!-- <div class="col-md-12">
                    <div class="d-main-title">
                        <h3><i class="fa-solid fa-user-check me-3 mb-3"></i>{{ __('messages.front_volunteer_checkin') }}</h3>
                        <p class="mb-2">Check-in volunteers for this cleanup event</p>

                        @php
                            $breadcrumbs = [
                                ['name' => 'Ongoing Cleanups', 'url' => route('front-cleanup-ongoing')],
                                ['name' => $event->event_name, 'url' => route('front-registration-list', ['event' => decrypt($event_id)])],
                            ];
                        @endphp
                        <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                    </div>
                </div> -->
                <div class="col-md-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-main-title">
                        <h3><i class="fa-solid fa-user-check me-3 mb-3"></i>Volunteer Check-in</h3>
                        <p class="mb-2">{{ $event->event_name }} | {{ $event->startTimeAndEndTime }}</p>

                        @php
                            $breadcrumbs = [
                                ['name' => __('messages.front_ongoing_cleanups'), 'url' => route('front-cleanup-ongoing')],
                                ['name' => __('messages.front_volunteer_checkin'), 'url' => route('front-volunteer-check-in', ['id' => $event_id])],
                            ];
                        @endphp
                        <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                    </div>

                    <a href="{{ route('front-volunteer-check-in-export', ['id' => $event_id]) }}" class="primary-btn w-fit">
                        <i class="fa-solid fa-file-pdf me-2"></i>{{ __('messages.front_export_attendees') }}
                    </a>
                </div>

                <div class="col-md-12">
                    <x-reusables.frontend.alert />

                    <div class="filter-card mt-4">
                        <div class="card-body p-4">
                            <form class="filter-form">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-7">
                                        <label class="form-label filter-label">{{ __('messages.front_search_volunteer') }}</label>
                                        <div class="filter-input-wrap">
                                            <i class="fa fa-search filter-icon"></i>
                                            <input class="form-control filter-field" value="{{ request()->search }}" name="search" type="text" placeholder="{{ __('messages.front_name_email_phone') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label filter-label">{{ __('messages.front_status') }}</label>
                                        <select class="selectpicker filter-field w-100" name="status" title="{{ __('messages.front_status_all') }}">
                                            <option value="">{{ __('messages.front_status_all') }}</option>
                                            <option value="signed" @selected(request()->status == 'signed')>{{ __('messages.front_signed') }}</option>
                                            <option value="unsigned" @selected(request()->status == 'unsigned')>{{ __('messages.front_unsigned') }}</option>
                                            <option value="checked_in" @selected(request()->status == 'checked_in')>{{ __('messages.front_checked_in') }}</option>
                                            <option value="not_checked_in" @selected(request()->status == 'not_checked_in')>{{ __('messages.front_not_checked_in') }}</option>
                                            <option value="checked_in_signed" @selected(request()->status == 'checked_in_signed')>{{ __('messages.front_checked_in_signed') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex gap-2">
                                        <button class="btn btn-filter-search flex-grow-1" type="submit">
                                            <i class="fa fa-search me-2"></i>{{ __('messages.front_search') }}
                                        </button>
                                        <a href="{{ route('front-volunteer-check-in', ['id' => $event_id]) }}" class="btn btn-filter-reset">
                                            <i class="fa fa-rotate-left"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="volunteer-checkin-list mt-4">
                        @forelse($registrations as $registration)
                            @php
                                $volunteer = $registration->volunteer;
                                $isSigned = !empty($registration->waiver_filled_at);
                                $isCheckedIn = $registration->checkin == 1;
                                $signatureId = 'signature_'.$registration->id;
                                $signatureInputId = 'signature_input_'.$registration->id;
                            @endphp

                            <div class="main-card volunteer-checkin-card mb-3">
                                <div class="volunteer-checkin-card-body">
                                    <div class="volunteer-checkin-main">
                                        <img class="volunteer-checkin-avatar" src="{{ $volunteer->formattedImage('profile_picture') }}" alt="{{ $volunteer->name }}">

                                        <div class="volunteer-checkin-info">
                                            <h5>{{ $volunteer->name }}</h5>
                                            <p><i class="fa fa-envelope me-1"></i>{{ $volunteer->email_address }}</p>
                                            @if($volunteer->phone)
                                                <p><i class="fa fa-phone me-1"></i>{{ $volunteer->phone }}</p>
                                            @endif

                                            <div class="volunteer-checkin-badges">
                                                @if($isSigned)
                                                    <span class="badge custom-badge-success">{{ __('messages.front_signed') }}</span>
                                                @else
                                                    <span class="badge custom-badge-danger">{{ __('messages.front_unsigned') }}</span>
                                                @endif

                                                @if($isCheckedIn)
                                                    <span class="badge custom-badge-success">{{ __('messages.front_checked_in') }}</span>
                                                @else
                                                    <span class="badge custom-badge-warning">{{ __('messages.front_not_checked_in') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="volunteer-checkin-action">
                                        @if(!$isSigned && !$isCheckedIn)
                                            <form method="POST" action="{{ route('front-volunteer-sign-and-check-in', ['id' => $registration->encrypted_id]) }}" class="volunteer-sign-checkin-form">
                                                @csrf
                                                <label class="form-label fw-semibold">{{ __('messages.front_volunteer_signature') }}</label>
                                                <input type="hidden" name="signature" id="{{ $signatureInputId }}">
                                                <div class="volunteer-sign-pad js-signature" id="{{ $signatureId }}" data-input="#{{ $signatureInputId }}" data-width="420" data-height="115"></div>

                                                <div class="d-flex justify-content-between align-items-center mt-2 gap-2 flex-wrap">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary clear-volunteer-signature" data-pad="#{{ $signatureId }}" data-input="#{{ $signatureInputId }}">
                                                        {{ __('messages.front_clear') }}
                                                    </button>
                                                    <!-- <button type="submit" class="primary-btn w-fit btn-sign-checkin">
                                                        <i class="fa-solid fa-signature me-2"></i>Sign & Check-in
                                                    </button> -->
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <button type="button" class="btn btn-outline-primary btn-show-waiver-qr" data-url="{{ route('front-volunteer-waiver-qr', ['id' => $registration->encrypted_id]) }}">
                                                            <i class="fa-solid fa-qrcode me-2"></i>{{ __('messages.front_show_qr_code') }}
                                                        </button>

                                                        <button type="submit" class="primary-btn w-fit btn-sign-checkin">
                                                            <i class="fa-solid fa-signature me-2"></i>{{ __('messages.front_sign_checkin') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        @elseif($isSigned && !$isCheckedIn)
                                            <form method="POST" action="{{ route('front-volunteer-mark-check-in', ['id' => $registration->encrypted_id]) }}">
                                                @csrf
                                                <div class="volunteer-status-line mb-3">
                                                    <span>{{ __('messages.front_waiver_signed') }}</span>
                                                    <strong>{{ date(config('app.dateTimeFormat'), strtotime($registration->waiver_filled_at)) }}</strong>
                                                </div>
                                                <button type="submit" class="primary-btn w-fit">
                                                    <i class="fa-solid fa-user-check me-2"></i>{{ __('messages.front_checkin') }}
                                                </button>
                                            </form>
                                        @else
                                            <div class="volunteer-status-stack">
                                                <div class="volunteer-status-line">
                                                    <span>{{ __('messages.front_waiver_signed') }}</span>
                                                    <strong>{{ date(config('app.dateTimeFormat'), strtotime($registration->waiver_filled_at)) }}</strong>
                                                </div>
                                                <div class="volunteer-status-line">
                                                    <span>{{ __('messages.front_checked_in') }}</span>
                                                    <strong>{{ !empty($registration->checkin_dt) ? date(config('app.dateTimeFormat'), strtotime($registration->checkin_dt)) : __('messages.front_done') }}</strong>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="main-card mt-2">
                                <div class="contact-list">
                                    <div class="card-top event-top p-4 align-items-center top d-flex flex-wrap justify-content-center">
                                        <div class="text-center py-4">
                                            <div class="empty-icon-wrap mx-auto mb-3">
                                                <i class="fa-regular fa-image fa-2x text-muted"></i>
                                            </div>
                                            <h5 class="fw-semibold mb-2">{{ __('messages.front_no_volunteer_checkin_found') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('messages.front_checkin_empty_desc') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-2">
                        {{ $registrations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="waiverQrModal" tabindex="-1" aria-labelledby="waiverQrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="waiverQrModalLabel">{{ __('messages.front_waiver_qr_code') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center waiver-qr-modal-body">
                <div id="waiverQrLoader" class="py-4">{{ __('messages.front_generating_qr_code') }}</div>
                <div id="waiverQrContent" class="d-none">
                    <div id="waiverQrImage" class="waiver-qr-image py-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets_front/js/jq-signature.min.js') }}"></script>
<script>
    $('.js-signature').each(function () {
        $(this).jqSignature();
    });

    $('.js-signature').on('jq.signature.changed', function () {
        const input = $(this).data('input');
        $(input).val($(this).jqSignature('getDataURL'));
    });

    $('.clear-volunteer-signature').click(function () {
        const pad = $(this).data('pad');
        const input = $(this).data('input');

        $(pad).jqSignature('clearCanvas');
        $(input).val('');
    });

    $('.volunteer-sign-checkin-form').submit(function (e) {
        const signatureInput = $(this).find('input[name="signature"]');

        if (!signatureInput.val()) {
            e.preventDefault();
            showError('Please provide volunteer signature before check-in.', 'error');
            return false;
        }

        $(this).find('.btn-sign-checkin').text('Submitting...').attr('disabled', true);
    });
    $('.btn-show-waiver-qr').click(function () {
        const url = $(this).data('url');

        $('#waiverQrLoader').removeClass('d-none').text('Generating QR code...');
        $('#waiverQrContent').addClass('d-none');
        $('#waiverQrImage').html('');

        const modal = new bootstrap.Modal(document.getElementById('waiverQrModal'));
        modal.show();

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.status && response.data) {
                    $('#waiverQrImage').html(response.data.qr_html);
                    $('#waiverQrLoader').addClass('d-none');
                    $('#waiverQrContent').removeClass('d-none');
                } else {
                    $('#waiverQrLoader').text(response.message || 'Unable to generate QR code.');
                }
            },
            error: function () {
                $('#waiverQrLoader').text('Unable to generate QR code.');
            }
        });
    });
</script>
@endpush

@push('style')
<link rel="stylesheet" href="{{ asset('assets_front/css/filter-css.css') }} ">
<style>
.waiver-qr-modal-body {
    padding-top: 28px;
    padding-bottom: 28px;
}

.waiver-qr-image {
    display: flex;
    justify-content: center;
    align-items: center;
}

.waiver-qr-image svg {
    max-width: 280px;
    width: 100%;
    height: auto;
    display: block;
}

    .volunteer-checkin-card {
        overflow: hidden;
    }

    .volunteer-checkin-card-body {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        padding: 22px;
    }

    .volunteer-checkin-main {
        display: flex;
        gap: 16px;
        min-width: 260px;
        flex: 1;
    }

    .volunteer-checkin-avatar {
        width: 74px;
        height: 74px;
        border-radius: 8px;
        object-fit: cover;
        background: #f1f3f5;
        flex: 0 0 auto;
    }

    .volunteer-checkin-info h5 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--bs-body-color);
    }

    .volunteer-checkin-info p {
        color: #6c757d;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .volunteer-checkin-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .volunteer-checkin-action {
        width: 460px;
        max-width: 100%;
    }

    .volunteer-sign-pad {
        width: 100%;
        height: 120px;
        border: 1px solid #d7dee8;
        border-radius: 8px;
        background: #fff;
        overflow: hidden;
    }

    .volunteer-sign-pad canvas {
        width: 100% !important;
        height: 115px !important;
        border: 0 !important;
        display: block;
    }

    .volunteer-sign-pad .jq-signature {
        border: 0 !important;
    }

    .volunteer-status-stack {
        display: grid;
        gap: 10px;
    }

    .volunteer-status-line {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 14px;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        background: #f8fafc;
    }

    .volunteer-status-line span {
        color: #6c757d;
        font-size: 13px;
        font-weight: 600;
    }

    .volunteer-status-line strong {
        color: var(--bs-body-color);
        font-size: 13px;
        text-align: right;
    }

    @media (max-width: 991px) {
        .volunteer-checkin-card-body {
            display: block;
        }

        .volunteer-checkin-action {
            width: 100%;
            margin-top: 18px;
        }
    }

    @media (max-width: 575px) {
        .volunteer-checkin-card-body {
            padding: 16px;
        }

        .volunteer-checkin-main {
            display: block;
        }

        .volunteer-checkin-avatar {
            margin-bottom: 12px;
        }

        .volunteer-status-line {
            display: block;
        }

        .volunteer-status-line strong {
            display: block;
            text-align: left;
            margin-top: 4px;
        }

        .primary-btn.w-fit {
            width: 100%;
        }
    }
</style>
@endpush
