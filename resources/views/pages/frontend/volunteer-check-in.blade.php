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
                                    ['name' => __('messages.front_ongoing_cleanups'), 'url' => route('front-cleanup-ongoing')],
                                    ['name' => __('messages.front_volunteer_checkin'), 'url' => route('front-volunteer-check-in', ['id' => $event_id])],
                                ];
                            @endphp
                            <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                        </div>
                    </div>

                    <a href="{{ route('front-volunteer-check-in-export', ['id' => $event_id]) }}"
                       class="primary-btn w-fit d-none d-sm-flex align-items-center">
                        <i class="fa-solid fa-file-pdf me-2"></i>{{ __('messages.front_export_attendees') }}
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
                                            <label class="form-label filter-label">{{ __('messages.front_search_volunteer') }}</label>
                                            <div class="filter-input-wrap">
                                                <i class="fa fa-search filter-icon"></i>
                                                <input class="form-control filter-field" value="{{ request()->search }}" name="search" type="text" placeholder="{{ __('messages.front_name_email_phone') }}">
                                            </div>
                                        </div>
                                        <button class="btn btn-primary d-sm-none" type="submit" style="height: 48px; width: 48px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-3 d-none d-sm-block">
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

                                $activeChildren = $registration->children ? $registration->children->whereNull('cancelled_at') : collect();
                                $hasChildren = $activeChildren->isNotEmpty();
                                $childrenCount = $activeChildren->count();
                                $checkedInChildrenCount = $activeChildren->where('checkin', 1)->count();
                            @endphp

                            <div class="main-card volunteer-checkin-card mb-3" id="volunteer-card-{{$registration->id}}">
                                <div class="volunteer-checkin-card-body">
                                    <div class="volunteer-checkin-main">
                                        <img class="volunteer-checkin-avatar d-none d-sm-block" src="{{ $volunteer->formattedImage('profile_picture') }}" alt="{{ $volunteer->name }}">

                                        <div class="volunteer-checkin-info">
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <h5 class="mb-0">{{ $volunteer->name }}</h5>
                                                @if($hasChildren)
                                                    <span class="badge" style="background-color: #e8f4fd; color: #0d6efd; font-size: 11px; font-weight: 600; padding: 4px 8px; border-radius: 6px;">
                                                        <i class="fa-solid fa-people-roof me-1"></i> Family (1 Adult + {{ $childrenCount }} {{ $childrenCount === 1 ? 'Child' : 'Children' }})
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="mt-1 mb-1"><i class="fa fa-envelope me-1 text-muted"></i>{{ $volunteer->email_address }}</p>
                                            @if($volunteer->phone)
                                                <p class="mb-1"><i class="fa fa-phone me-1 text-muted"></i>{{ $volunteer->phone }}</p>
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
                                                    <span class="badge custom-badge-success">{{ __('messages.front_checked_in') }}</span>
                                                @else
                                                    <span class="badge custom-badge-warning">{{ __('messages.front_not_checked_in') }}</span>
                                                @endif

                                                @if($hasChildren)
                                                    <span class="badge bg-light text-success border border-success" id="parent-badge-kids-{{ $registration->id }}" style="{{ $checkedInChildrenCount > 0 ? '' : 'display:none;' }}">
                                                        <i class="fa-solid fa-check me-1"></i><span class="parent-in-counter-{{ $registration->id }}">{{ $checkedInChildrenCount }}</span>/{{ $childrenCount }} Kids In
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="volunteer-checkin-action d-none d-sm-block">
                                        @if(!empty($event->waiver_form_type) && !$isSigned && !$isCheckedIn)
                                            <form method="POST" action="{{ route('front-volunteer-sign-and-check-in', ['id' => $registration->encrypted_id]) }}" class="volunteer-sign-checkin-form">
                                                @csrf
                                                <input type="hidden" name="checkin_scope" value="family">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label fw-semibold mb-0">{{ __('messages.front_volunteer_signature') }}</label>
                                                    <a href="{{ route('view-waiver-form', ['ticket' => encrypt($registration->id)]) }}" target="_blank" class="text-decoration-none" style="color: #0d6efd; font-size: 14px; font-weight: 500;">View Waiver</a>
                                                </div>
                                                <input type="hidden" name="signature" id="{{ $signatureInputId }}">
                                                <div class="volunteer-sign-pad js-signature" id="{{ $signatureId }}" data-input="#{{ $signatureInputId }}" data-width="420" data-height="115"></div>

                                                <div class="d-flex justify-content-between align-items-center mt-2 gap-2 flex-wrap">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary clear-volunteer-signature" data-pad="#{{ $signatureId }}" data-input="#{{ $signatureInputId }}">
                                                        {{ __('messages.front_clear') }}
                                                    </button>
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        @if(!empty($event->waiver_form_type))
                                                        <button type="button" class="btn btn-outline-primary btn-show-waiver-qr" data-url="{{ route('front-volunteer-waiver-qr', ['id' => $registration->encrypted_id]) }}">
                                                            <i class="fa-solid fa-qrcode me-2"></i>{{ __('messages.front_show_qr_code') }}
                                                        </button>
                                                        @endif

                                                        <button type="submit" class="primary-btn w-fit btn-sign-checkin">
                                                            @if($hasChildren)
                                                                <i class="fa-solid fa-signature me-2"></i>Sign & Check-in Family
                                                            @else
                                                                <i class="fa-solid fa-signature me-2"></i>{{ __('messages.front_sign_checkin') }}
                                                            @endif
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
                                                @if($hasChildren)
                                                    <input type="hidden" name="checkin_scope" id="scope-{{ $registration->id }}" value="family">
                                                    <div class="d-flex flex-column gap-2 align-items-end">
                                                        <button type="submit" class="primary-btn w-fit" onclick="document.getElementById('scope-{{ $registration->id }}').value='family';">
                                                            <i class="fa-solid fa-users me-2"></i>Check-in Family ({{ 1 + $childrenCount }})
                                                        </button>
                                                        <button type="submit" class="btn btn-sm btn-link text-decoration-none text-muted p-0" onclick="document.getElementById('scope-{{ $registration->id }}').value='adult_only';">
                                                            <small><i class="fa-solid fa-user me-1"></i>Check-in Adult Only</small>
                                                        </button>
                                                    </div>
                                                @else
                                                    <button type="submit" class="primary-btn w-fit">
                                                        <i class="fa-solid fa-user-check me-2"></i>{{ __('messages.front_checkin') }}
                                                    </button>
                                                @endif
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
                                                    <span>{{ __('messages.front_checked_in') }}</span>
                                                    <strong>{{ !empty($registration->checkin_dt) ? date(config('app.dateTimeFormat'), strtotime($registration->checkin_dt)) : __('messages.front_done') }}</strong>
                                                </div>
                                                @if($hasChildren)
                                                    <div class="volunteer-status-line">
                                                        <span>Kids Check-in</span>
                                                        <strong><span class="parent-in-counter-{{ $registration->id }}">{{ $checkedInChildrenCount }}</span>/{{ $childrenCount }} In</strong>
                                                    </div>
                                                @endif
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
                                                    @if($hasChildren)
                                                        <input type="hidden" name="checkin_scope" value="family">
                                                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-2" style="font-size: 14px;">
                                                            <i class="fa-solid fa-users me-2"></i>CHECK-IN FAMILY ({{ 1 + $childrenCount }})
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-2" style="font-size: 14px;">
                                                            <i class="fa-solid fa-user me-2"></i>CHECK-IN
                                                        </button>
                                                    @endif
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-primary w-100 fw-bold py-2 shadow-sm rounded-2 btn-mobile-unsigned-checkin" data-url="{{ route('front-volunteer-waiver-qr', ['id' => $registration->encrypted_id]) }}" style="font-size: 14px;">
                                                    <i class="fa-solid fa-user me-2"></i>CHECK-IN
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                {{-- Inline Family Box for Accompanying Children --}}
                                @if($hasChildren)
                                    <div class="volunteer-children-box px-4 py-3 border-top" style="background-color: #fbfcfd;" id="children-box-{{ $registration->id }}">
                                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold small text-dark">
                                                    <i class="fa-solid fa-children text-primary me-1"></i> Accompanying Children ({{ $childrenCount }})
                                                </span>
                                                <span class="badge bg-light text-secondary border" style="font-size: 10.5px;">
                                                    <span class="parent-in-counter-{{ $registration->id }}">{{ $checkedInChildrenCount }}</span>/{{ $childrenCount }} Checked-in
                                                </span>
                                            </div>
                                            <span class="text-muted" style="font-size: 11.5px;">
                                                <i class="fa-solid fa-shield-halved text-success me-1"></i>Waiver covered by {{ $volunteer->name }}
                                            </span>
                                        </div>

                                        <div class="d-flex flex-column gap-2 mt-2">
                                            @foreach($activeChildren as $child)
                                                @php
                                                    $isChildCheckedIn = ($child->checkin == 1);
                                                @endphp
                                                <div class="child-checkin-row d-flex align-items-center justify-content-between p-2 rounded" 
                                                     id="child-row-{{ $child->id }}" 
                                                     style="background-color: #ffffff; border: 1px solid #e9ecef;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 30px; height: 30px; font-size: 13px;">
                                                            <i class="fa-solid fa-child"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-semibold text-dark small">{{ $child->child_name }}</span>
                                                            <span class="badge bg-secondary ms-1" style="font-size: 10px; padding: 2px 6px;">Age {{ $child->child_age }}</span>
                                                            @if(!empty($child->ticket_answer))
                                                                <div class="text-muted small mt-1" style="font-size: 11px;">
                                                                    <i class="fa-regular fa-comment-dots me-1 text-primary"></i><strong>Ans:</strong> {{ $child->ticket_answer }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <form method="POST" action="{{ route('front-child-mark-check-in', ['id' => encrypt($child->id)]) }}" class="child-toggle-form" data-child-id="{{ $child->id }}" data-parent-id="{{ $registration->id }}">
                                                            @csrf
                                                            @if($isChildCheckedIn)
                                                                <button type="submit" class="btn btn-sm btn-outline-success py-1 px-3 fw-semibold child-checkin-btn" style="font-size: 11px;" title="Click to undo check-in">
                                                                    <i class="fa-solid fa-circle-check text-success me-1"></i> Checked-in @if($child->checkin_dt)({{ date('h:i A', strtotime($child->checkin_dt)) }})@endif
                                                                </button>
                                                            @else
                                                                <button type="submit" class="btn btn-sm btn-outline-primary py-1 px-3 fw-semibold child-checkin-btn" style="font-size: 11px;">
                                                                    <i class="fa-solid fa-user-check me-1"></i> Check-in Minor
                                                                </button>
                                                            @endif
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
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
                <div id="waiverQrWarning" class="alert alert-warning d-none text-start mb-4" style="font-size: 14px;">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    Waiver has not been signed. Please have the volunteer scan the QR code to sign the waiver.
                </div>
                <div id="waiverQrLoader" class="py-4">
                    {{ __('messages.front_generating_qr_code') }}
                </div>
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
                _token: '{{ csrf_token() }}',
                is_mobile: 1
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

    $(document).on('submit', '.child-toggle-form', function (e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $form.find('.child-checkin-btn');
        const parentId = $form.data('parent-id');
        const originalHtml = $btn.html();
        
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                if (response.status) {
                    const $counter = $('.parent-in-counter-' + parentId);
                    let currentCount = parseInt($counter.first().text(), 10) || 0;
                    const newCount = response.checkin == 1 ? currentCount + 1 : Math.max(0, currentCount - 1);

                    if (response.checkin == 1) {
                        const timeStr = response.checkin_time ? ` (${response.checkin_time})` : '';
                        $btn.removeClass('btn-outline-primary').addClass('btn-outline-success')
                            .html(`<i class="fa-solid fa-circle-check text-success me-1"></i> Checked-in${timeStr}`)
                            .attr('title', 'Click to undo check-in');
                    } else {
                        $btn.removeClass('btn-outline-success').addClass('btn-outline-primary')
                            .html('<i class="fa-solid fa-user-check me-1"></i> Check-in Minor')
                            .removeAttr('title');
                    }
                    $counter.text(newCount);
                    if (newCount > 0) {
                        $('#parent-badge-kids-' + parentId).show();
                    } else {
                        $('#parent-badge-kids-' + parentId).hide();
                    }
                    if (typeof showError === 'function') {
                        showError(response.message, 'success');
                    }
                } else {
                    $btn.html(originalHtml);
                    if (typeof showError === 'function') {
                        showError(response.message || 'Error updating check-in.', 'error');
                    }
                }
            },
            error: function () {
                $btn.html(originalHtml);
                if (typeof showError === 'function') {
                    showError('Error updating child check-in.', 'error');
                }
            },
            complete: function () {
                $btn.prop('disabled', false);
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
