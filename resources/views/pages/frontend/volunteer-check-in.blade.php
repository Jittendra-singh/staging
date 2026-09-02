@extends('layouts.frontend')

@section('content')
<div class="wrapper wrapper-body">
    <div class="dashboard-body">
        <div class="container-fluid">
            <div class="row">
                <!-- <div class="col-md-12">
                    <div class="d-main-title">
                        <h3><i class="fa-solid fa-user-check me-3 mb-3"></i>Volunteer Check-in</h3>
                        <p class="mb-2">Check-in volunteers for this cleanup event</p>

                        @php
                            $breadcrumbs = [
                                ['name' => 'In-progress Cleanups', 'url' => route('front-cleanup-ongoing')],
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

                        <div class="d-none d-sm-block">
                            @php
                                $breadcrumbs = [
                                    ['name' => 'In-progress Cleanups', 'url' => route('front-cleanup-ongoing')],
                                    ['name' => 'Volunteer Check-in', 'url' => route('front-volunteer-check-in', ['id' => $event_id])],
                                ];
                            @endphp
                            <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                        </div>
                    </div>

                    <a href="{{ route('front-volunteer-check-in-export', ['id' => $event_id]) }}" class="primary-btn w-fit d-none d-sm-flex align-items-center">
                        <i class="fa-solid fa-file-pdf me-2"></i>Export Attendees
                    </a>
                </div>

                <div class="col-md-12">
                    <x-reusables.frontend.alert />

                    <div class="filter-card mt-4">
                        <div class="card-body p-4">
                            <form class="filter-form">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4 d-flex gap-2 align-items-end">
                                        <div class="flex-grow-1">
                                            <label class="form-label filter-label">Search Volunteer</label>
                                            <div class="filter-input-wrap">
                                                <i class="fa fa-search filter-icon"></i>
                                                <input class="form-control filter-field" value="{{ request()->search }}" name="search" type="text" placeholder="Name, email, phone">
                                            </div>
                                        </div>
                                        <button class="btn btn-primary d-sm-none" type="submit" style="height: 48px; width: 48px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-3 d-none d-sm-block">
                                        <label class="form-label filter-label">Status</label>
                                        <select class="selectpicker filter-field w-100" name="status" title="Status: All">
                                            <option value="">Status: All</option>
                                            <option value="signed" @selected(request()->status == 'signed')>Signed</option>
                                            <option value="unsigned" @selected(request()->status == 'unsigned')>Unsigned</option>
                                            <option value="checked_in" @selected(request()->status == 'checked_in')>Checked-in</option>
                                            <option value="not_checked_in" @selected(request()->status == 'not_checked_in')>Not Checked-in</option>
                                            <option value="checked_in_signed" @selected(request()->status == 'checked_in_signed')>Checked-in & Signed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label filter-label">Sort By</label>
                                        <select class="selectpicker filter-field w-100" name="sort" title="Sort By: Default">
                                            <option value="">Sort By: Default</option>
                                            <option value="name_asc" @selected(request()->sort == 'name_asc')>Name (A-Z)</option>
                                            <option value="name_desc" @selected(request()->sort == 'name_desc')>Name (Z-A)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 gap-2 d-none d-sm-flex">
                                        <button class="btn btn-filter-search flex-grow-1" type="submit">
                                            <i class="fa fa-search me-2"></i>Search
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

                            <div class="main-card volunteer-checkin-card mb-3" id="volunteer-card-{{$registration->id}}">
                                <div class="volunteer-checkin-card-body">
                                    <div class="volunteer-checkin-main">
                                        <img class="volunteer-checkin-avatar d-none d-sm-block" src="{{ $volunteer->formattedImage('profile_picture') }}" alt="{{ $volunteer->name }}">

                                        <div class="volunteer-checkin-info">
                                            <h5>{{ $volunteer->name }}</h5>
                                            <p><i class="fa fa-envelope me-1 text-muted"></i>{{ $volunteer->email_address }}</p>
                                            @if($volunteer->phone)
                                                <p><i class="fa fa-phone me-1 text-muted"></i>{{ $volunteer->phone }}</p>
                                            @endif

                                            <div class="volunteer-checkin-badges d-none d-sm-flex">
                                                @if(!empty($event->waiver_form_type))
                                                    @if($isSigned)
                                                        <span class="badge custom-badge-success">Signed</span>
                                                    @else
                                                        <span class="badge custom-badge-danger">Unsigned</span>
                                                    @endif
                                                @else 
                                                    <span class="badge custom-badge-success">No Waiver</span>
                                                @endif

                                                @if($isCheckedIn)
                                                    <span class="badge custom-badge-success">Checked-in</span>
                                                @else
                                                    <span class="badge custom-badge-warning">Not Checked-in</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="volunteer-checkin-action d-none d-sm-block">
                                        @if(!empty($event->waiver_form_type) && !$isSigned && !$isCheckedIn)
                                            <form method="POST" action="{{ route('front-volunteer-sign-and-check-in', ['id' => $registration->encrypted_id]) }}" class="volunteer-sign-checkin-form">
                                                @csrf
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-semibold mb-0">Volunteer Signature</label>
                                                    <a href="{{ route('view-waiver-form', ['ticket' => encrypt($registration->id)]) }}" target="_blank" class="text-decoration-none" style="color: #0d6efd; font-size: 14px; font-weight: 500;">View Waiver</a>
                                                </div>
                                                <input type="hidden" name="signature" id="{{ $signatureInputId }}">
                                                <div class="volunteer-sign-pad js-signature" id="{{ $signatureId }}" data-input="#{{ $signatureInputId }}" data-width="420" data-height="115"></div>

                                                <div class="d-flex justify-content-between align-items-center mt-2 gap-2 flex-wrap">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary clear-volunteer-signature" data-pad="#{{ $signatureId }}" data-input="#{{ $signatureInputId }}">
                                                        Clear
                                                    </button>
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        @if(!empty($event->waiver_form_type))
                                                        <button type="button" class="btn btn-outline-primary btn-show-waiver-qr" data-url="{{ route('front-volunteer-waiver-qr', ['id' => $registration->encrypted_id]) }}">
                                                            <i class="fa-solid fa-qrcode me-2"></i>Show QR Code
                                                        </button>
                                                        @endif

                                                        <button type="submit" class="primary-btn w-fit btn-sign-checkin">
                                                            <i class="fa-solid fa-signature me-2"></i>Sign & Check-in
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        @elseif((!empty($event->waiver_form_type) && $isSigned && !$isCheckedIn) || (empty($event->waiver_form_type) && !$isCheckedIn))
                                            <form method="POST" action="{{ route('front-volunteer-mark-check-in', ['id' => $registration->encrypted_id]) }}">
                                                @csrf
                                                <div class="volunteer-status-line mb-3">
                                                    <span>Waiver Signed</span>
                                                    @if(!empty($event->waiver_form_type))
                                                    <strong>{{ date(config('app.dateTimeFormat'), strtotime($registration->waiver_filled_at)) }}</strong>
                                                    @else 
                                                    <strong>No Waiver</strong>
                                                    @endif
                                                </div>
                                                <button type="submit" class="primary-btn w-fit">
                                                    <i class="fa-solid fa-user-check me-2"></i>Check-in
                                                </button>
                                            </form>
                                        @else
                                            <div class="volunteer-status-stack">
                                                <div class="volunteer-status-line">
                                                    <span>Waiver Signed</span>
                                                    @if(!empty($event->waiver_form_type))
                                                    <strong>{{ date(config('app.dateTimeFormat'), strtotime($registration->waiver_filled_at)) }}</strong>
                                                    @else 
                                                    <strong>No Waiver</strong>
                                                    @endif
                                                </div>
                                                <div class="volunteer-status-line">
                                                    <span>Checked-in</span>
                                                    <strong>{{ !empty($registration->checkin_dt) ? date(config('app.dateTimeFormat'), strtotime($registration->checkin_dt)) : 'Done' }}</strong>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Mobile Action Area -->
                                    <div class="volunteer-checkin-action-mobile d-block d-sm-none mt-3">
                                        @if($isCheckedIn)
                                            <div class="p-3 text-center rounded-3" style="background-color: #f3f4f6; border: 1px solid #e5e7eb;">
                                                <div class="fw-bold mb-1" style="color: #007bff; font-size: 14px;">
                                                    <i class="fa-solid fa-circle-check me-1"></i> Waiver Signed & Checked In
                                                </div>
                                                <div class="text-muted" style="font-size: 12px;">
                                                    {{ !empty($registration->checkin_dt) ? date(config('app.dateTimeFormat'), strtotime($registration->checkin_dt)) : 'Done' }}
                                                </div>
                                            </div>
                                        @else
                                            @if($isSigned || empty($event->waiver_form_type))
                                                <form method="POST" action="{{ route('front-volunteer-mark-check-in', ['id' => $registration->encrypted_id]) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-2" style="font-size: 14px;">
                                                        <i class="fa-solid fa-user me-2"></i>CHECK-IN
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-2 btn-mobile-unsigned-checkin" data-url="{{ route('front-volunteer-waiver-qr', ['id' => $registration->encrypted_id]) }}" style="font-size: 14px;">
                                                    <i class="fa-solid fa-user me-2"></i>CHECK-IN
                                                </button>
                                            @endif
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
                                            <h5 class="fw-semibold mb-2">No volunteer found for check-in</h5>
                                            <p class="text-muted small mb-0">Confirmed volunteers will appear here for check-in.</p>
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
                <h5 class="modal-title" id="waiverQrModalLabel">Waiver QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center waiver-qr-modal-body">
                <div id="waiverQrWarning" class="alert alert-warning d-none text-start mb-4" style="font-size: 14px;">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Waiver has not been signed. Please have the volunteer scan the QR code to sign the waiver.
                </div>
                <div id="waiverQrLoader" class="py-4">Generating QR code...</div>
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

        $('#waiverQrWarning').addClass('d-none');
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

    $('.btn-mobile-unsigned-checkin').click(function () {
        const url = $(this).data('url');

        $('#waiverQrWarning').removeClass('d-none');
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

    // Listen for waiver signed events from other tabs
    window.addEventListener('storage', function(e) {
        // e.newValue ensures we only react to the key being added/updated, not when it's deleted during cleanup
        if (e.key && e.key.startsWith('waiver_signed_update_') && e.newValue) {
            const ticketId = e.key.split('waiver_signed_update_')[1];
            
            // Fetch the current page in the background (preserves search/filters in URL)
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Find the updated card in the response and the old card on the screen
                    const oldCard = document.getElementById('volunteer-card-' + ticketId);
                    const newCard = doc.getElementById('volunteer-card-' + ticketId);
                    
                    // Swap them seamlessly
                    if (oldCard && newCard) {
                        oldCard.innerHTML = newCard.innerHTML;
                    }
                    
                    // Clean up localStorage to prevent keys from accumulating forever
                    try {
                        localStorage.removeItem(e.key);
                    } catch (err) {}
                })
                .catch(err => console.error('Error fetching updated volunteer card:', err));
        }
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