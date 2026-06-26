@extends('layouts.frontend')

@push('style')
<link rel="stylesheet" href="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.css') }}">
<style>
    .date-helper-text {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: #6c757d;
        font-weight: 500;
    }

    .recurring-ticket-table th,
    .recurring-ticket-table td {
        vertical-align: middle;
    }

    .recurring-ticket-table .form-control {
        min-width: 170px;
    }
</style>
@endpush

@section('content')
<div class="wrapper wrapper-body">
    <div class="dashboard-body">
        <div class="container-fluid">
            <div class="d-main-title">
                <h3><i class="fa-solid fa-repeat me-3 mb-3"></i>Recurring Event: {{ $event->event_name }}</h3>

                @php
                    $breadcrumbs = [
                        [
                            'name' => 'Cleanups I\'m Hosting',
                            'url' => route('front-cleanup-hosting', ['type' => 'organizer']),
                        ],
                        [
                            'name' => $event->event_name,
                            'url' => route('front-post-a-event', [$event->encrypted_id]),
                        ],
                        [
                            'name' => 'Recurring Event',
                            'url' => route('front-recurring-event', [
                                'event' => $event->encrypted_id,
                                'duration' => $duration,
                                'numbers' => $numbers,
                            ]),
                        ],
                    ];
                @endphp

                <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs" />
            </div>

            <x-reusables.frontend.alert />

            <form method="POST" action="{{ route('front-recurring-event-store', ['event' => $event->encrypted_id]) }}">
                @csrf

                @for($i = 1; $i <= $numbers; $i++)
                    @php
                        $parentStart = \Carbon\Carbon::parse($event->start_datetime);
                        $parentEnd = \Carbon\Carbon::parse($event->end_datetime);
                        $isUpcomingEvent = $parentStart->gt(now());

                        if ($isUpcomingEvent) {
                            // Existing logic for upcoming events: do not change behavior.
                            $days = $intervalDays * $i;

                            $start = \Carbon\Carbon::parse($event->start_datetime)->addDays($days);
                            $end = \Carbon\Carbon::parse($event->end_datetime)->addDays($days);
                            $publish = \Carbon\Carbon::parse($event->site_publish_date)->addDays($days);
                            $expiry = \Carbon\Carbon::parse($event->site_depublish_date)->addDays($days);
                        } else {
                            // New logic for past / ongoing events.
                            $days = $intervalDays * ($i - 1);
                            $today = \Carbon\Carbon::today();

                            $parentDurationSeconds = $parentStart->diffInSeconds($parentEnd);

                            $start = $today->copy()
                                ->addDays(7)
                                ->addDays($days)
                                ->setTime($parentStart->hour, $parentStart->minute, $parentStart->second);

                            $end = $start->copy()->addSeconds($parentDurationSeconds);

                            $publish = $today->copy()->addDays($days);
                            $expiry = $end->copy();
                        }
                    @endphp

                    <div class="main-card p-4 mt-4 recurring-event-box">
                        <div class="bp-title">
                            <h4 class="px-0">
                                <i class="fa-solid fa-calendar-days step_icon me-3"></i>
                                Event #{{ $i }}
                            </h4>
                        </div>

                        <div class="row g-4 main-form">
                            <div class="col-md-6">
                                <label class="form-label fs-6">Start Date Time*</label>
                                <div class="loc-group position-relative">
                                    <input
                                        autocomplete="off"
                                        name="recurrences[{{ $i }}][start_datetime]"
                                        class="form-control h_50 recurring-datetime-picker recurring-date-input recurring-start-datetime"
                                        value="{{ $start->format('Y-m-d, H:i') }}"
                                        required
                                    >
                                    <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                </div>
                                <small class="date-helper-text"></small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-6">End Date Time*</label>
                                <div class="loc-group position-relative">
                                    <input
                                        autocomplete="off"
                                        name="recurrences[{{ $i }}][end_datetime]"
                                        class="form-control h_50 recurring-datetime-picker recurring-date-input recurring-end-datetime"
                                        value="{{ $end->format('Y-m-d, H:i') }}"
                                        required
                                    >
                                    <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                </div>
                                <small class="date-helper-text"></small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-6">Event Page Publish Date*</label>
                                <div class="loc-group position-relative">
                                    <input
                                        autocomplete="off"
                                        name="recurrences[{{ $i }}][site_publish_date]"
                                        class="form-control h_50 recurring-date-picker recurring-date-input recurring-publish-date"
                                        value="{{ $publish->format('Y-m-d') }}"
                                        required
                                    >
                                    <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                </div>
                                <small class="date-helper-text"></small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-6">Event Page Expiry Date*</label>
                                <div class="loc-group position-relative">
                                    <input
                                        autocomplete="off"
                                        name="recurrences[{{ $i }}][site_depublish_date]"
                                        class="form-control h_50 recurring-date-picker recurring-date-input recurring-expiry-date"
                                        value="{{ $expiry->format('Y-m-d') }}"
                                        required
                                    >
                                    <span class="absolute-icon"><i class="fa-solid fa-calendar-days"></i></span>
                                </div>
                                <small class="date-helper-text"></small>
                            </div>
                        </div>

                        <div class="bp-title mt-4">
                            <h4 class="px-0 mb-0">
                                <i class="fa-solid fa-ticket step_icon me-3"></i>
                                Tickets
                            </h4>
                        </div>

                        @if($event->eventTickets->count() > 0)
                            <div class="table-responsive mt-3">
                                <table class="table recurring-ticket-table">
                                    <thead>
                                        <tr>
                                            <th>Ticket Name</th>
                                            <th>Type</th>
                                            <th>Price</th>
                                            <th>Total Tickets</th>
                                            <th>Sell Start Date</th>
                                            <th>Sell End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($event->eventTickets as $ticketIndex => $ticket)
                                            @php
                                                if ($isUpcomingEvent) {
                                                    // Existing logic for upcoming events.
                                                    $sellStart = \Carbon\Carbon::parse($ticket->sell_start_time)->addDays($days);
                                                    $sellEnd = \Carbon\Carbon::parse($ticket->sell_end_time)->addDays($days);
                                                } else {
                                                    // New logic for past / ongoing parent events.
                                                    $sellStart = \Carbon\Carbon::today()->addDays($days);

                                                    $parentTicketSellStart = \Carbon\Carbon::parse($ticket->sell_start_time);
                                                    $parentTicketSellEnd = \Carbon\Carbon::parse($ticket->sell_end_time);

                                                    if ($parentTicketSellEnd->isSameDay($parentEnd)) {
                                                        // If parent ticket sell end date is same as parent event end date,
                                                        // recurring ticket sell end date should be same as recurring event end date.
                                                        $sellEnd = $end->copy();
                                                    } else {
                                                        // Otherwise maintain the same difference between parent ticket sell start and sell end.
                                                        $ticketSellDurationDays = $parentTicketSellStart->diffInDays($parentTicketSellEnd);
                                                        $sellEnd = $sellStart->copy()->addDays($ticketSellDurationDays);
                                                    }
                                                }
                                            @endphp

                                            <tr>
                                                <td>{{ $ticket->ticket_name }}</td>
                                                <td>{{ ucfirst($ticket->ticket_type) }}</td>
                                                <td>{{ config('app.currency') . formatAmt($ticket->ticket_price) }}</td>
                                                <td>{{ $ticket->available_quantity }}</td>
                                                <td>
                                                    <input
                                                        autocomplete="off"
                                                        name="recurrences[{{ $i }}][tickets][{{ $ticketIndex }}][sell_start_time]"
                                                        class="form-control h_50 recurring-date-picker recurring-date-input recurring-ticket-sell-start"
                                                        value="{{ $sellStart->format('Y-m-d') }}"
                                                        required
                                                    >
                                                    <small class="date-helper-text"></small>
                                                </td>
                                                <td>
                                                    <input
                                                        autocomplete="off"
                                                        name="recurrences[{{ $i }}][tickets][{{ $ticketIndex }}][sell_end_time]"
                                                        class="form-control h_50 recurring-date-picker recurring-date-input recurring-ticket-sell-end"
                                                        value="{{ $sellEnd->format('Y-m-d') }}"
                                                        required
                                                    >
                                                    <small class="date-helper-text"></small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 mt-3 border rounded">
                                <h6 class="mb-1">Tickets not found</h6>
                                <p class="text-muted small mb-0">This parent event does not have any ticket types.</p>
                            </div>
                        @endif
                    </div>
                @endfor
                <div class="main-card p-4 mt-4">
                    <div class="d-flex align-items-start">
                        <span class="step_icon me-3">
                            <i class="fa-solid fa-circle-info"></i>
                        </span>

                        <div>
                            <h5 class="mb-2">How these dates were calculated</h5>

                            <p class="text-muted mb-0">
                                @if($duration === 'weekly')
                                    This preview was created by adding 7 days to the parent event dates for each occurrence. Event dates, publish/expiry dates, and ticket sell dates follow the same weekly interval. You can still edit any date before creating the events.
                                @else
                                    This preview was created by adding 30 days to the parent event dates for each occurrence. Event dates, publish/expiry dates, and ticket sell dates follow the same monthly interval. You can still edit any date before creating the events.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 d-flex mt-4 justify-content-end">
                    <button type="submit" class="primary-btn w-fit">Create Recurring Events</button>
                    <a href="{{ route('front-cleanup-hosting', ['type' => 'organizer']) }}" class="primary-btn w-fit ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.full.min.js') }}"></script>
<script>
    function parseRecurringDate(value) {
        if (!value) {
            return null;
        }

        var cleanValue = value.replace(',', '');
        var parts = cleanValue.split(' ');
        var dateParts = parts[0] ? parts[0].split('-') : [];
        var timeParts = parts[1] ? parts[1].split(':') : ['00', '00'];

        if (dateParts.length !== 3) {
            return null;
        }

        var year = parseInt(dateParts[0], 10);
        var month = parseInt(dateParts[1], 10) - 1;
        var day = parseInt(dateParts[2], 10);
        var hour = parseInt(timeParts[0] || 0, 10);
        var minute = parseInt(timeParts[1] || 0, 10);

        if (!year || month < 0 || !day) {
            return null;
        }

        return new Date(year, month, day, hour, minute);
    }

    function formatReadableDate(date) {
        var weekday = date.toLocaleDateString('en-US', { weekday: 'long' });
        var day = String(date.getDate()).padStart(2, '0');
        var month = date.toLocaleDateString('en-US', { month: 'long' });
        var year = date.getFullYear();

        return weekday + ' ' + day + ' ' + month + ', ' + year;
    }

    function updateReadableDate(input) {
        var date = parseRecurringDate($(input).val());
        var text = date ? formatReadableDate(date) : '';

        $(input).closest('td, .col-md-6').find('.date-helper-text').first().text(text);
    }

    function getDateOnlyValue(value) {
        if (!value) {
            return '';
        }

        return value.split(',')[0].trim();
    }

    function parseDateOnly(value) {
        if (!value) {
            return null;
        }

        var dateParts = getDateOnlyValue(value).split('-');

        if (dateParts.length !== 3) {
            return null;
        }

        return new Date(
            parseInt(dateParts[0], 10),
            parseInt(dateParts[1], 10) - 1,
            parseInt(dateParts[2], 10)
        );
    }

    function formatPickerDate(date) {
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');

        return date.getFullYear() + '-' + month + '-' + day;
    }

    function setDateIfInvalid($input, minValue, maxValue) {
        if (!$input.length || !$input.val()) {
            return;
        }

        var currentDate = parseDateOnly($input.val());
        var minDate = parseDateOnly(minValue);
        var maxDate = parseDateOnly(maxValue);

        if (minDate && currentDate && currentDate < minDate) {
            $input.val(formatPickerDate(minDate));
            updateReadableDate($input[0]);
            return;
        }

        if (maxDate && currentDate && currentDate > maxDate) {
            $input.val(formatPickerDate(maxDate));
            updateReadableDate($input[0]);
        }
    }

    function setDateTimeIfBefore($input, minValue) {
        if (!$input.length || !$input.val() || !minValue) {
            return;
        }

        var currentDate = parseRecurringDate($input.val());
        var minDate = parseRecurringDate(minValue);

        if (currentDate && minDate && currentDate < minDate) {
            $input.val(minValue);
            updateReadableDate($input[0]);
        }
    }

    function getPreviousEventEndDate($box) {
        var $previousBox = $box.prevAll('.recurring-event-box').first();

        if (!$previousBox.length) {
            return '';
        }

        return $previousBox.find('.recurring-end-datetime').val();
    }

    $('.recurring-datetime-picker').datetimepicker({
        format: 'Y-m-d, H:i',
        step: 30,
        scrollMonth: false,
        scrollInput: false,
        onShow: function (ct, input) {
            var $input = $(input);
            var $box = $input.closest('.recurring-event-box');

            if ($input.hasClass('recurring-start-datetime')) {
                var previousEventEndDate = getPreviousEventEndDate($box);

                this.setOptions({
                    minDate: previousEventEndDate ? getDateOnlyValue(previousEventEndDate) : 0,
                    maxDate: false
                });
            }

            if ($input.hasClass('recurring-end-datetime')) {
                this.setOptions({
                    minDate: getDateOnlyValue($box.find('.recurring-start-datetime').val()) || 0,
                    maxDate: false
                });
            }
        },
        onChangeDateTime: function (currentDateTime, input) {
            var $input = $(input);
            var $box = $input.closest('.recurring-event-box');

            updateReadableDate(input);

            if ($input.hasClass('recurring-start-datetime')) {
                var startValue = $input.val();

                setDateTimeIfBefore($box.find('.recurring-end-datetime'), startValue);
                setDateIfInvalid($box.find('.recurring-publish-date'), '', getDateOnlyValue(startValue));
                setDateIfInvalid($box.find('.recurring-ticket-sell-start'), $box.find('.recurring-publish-date').val(), getDateOnlyValue(startValue));
            }

            if ($input.hasClass('recurring-end-datetime')) {
                var endValue = $input.val();

                setDateTimeIfBefore($input, $box.find('.recurring-start-datetime').val());
                setDateIfInvalid($box.find('.recurring-expiry-date'), getDateOnlyValue(endValue), '');
                setDateIfInvalid($box.find('.recurring-ticket-sell-end'), '', getDateOnlyValue(endValue));

                var $nextBox = $box.nextAll('.recurring-event-box').first();
                if ($nextBox.length) {
                    setDateTimeIfBefore($nextBox.find('.recurring-start-datetime'), endValue);
                }
            }
        }
    });

    $('.recurring-date-picker').datetimepicker({
        format: 'Y-m-d',
        timepicker: false,
        scrollMonth: false,
        scrollInput: false,
        onShow: function (ct, input) {
            var $input = $(input);
            var $box = $input.closest('.recurring-event-box');

            if ($input.hasClass('recurring-publish-date')) {
                this.setOptions({
                    minDate: false,
                    maxDate: getDateOnlyValue($box.find('.recurring-start-datetime').val()) || false
                });
            }

            if ($input.hasClass('recurring-expiry-date')) {
                this.setOptions({
                    minDate: getDateOnlyValue($box.find('.recurring-end-datetime').val()) || false,
                    maxDate: false
                });
            }

            if ($input.hasClass('recurring-ticket-sell-start')) {
                this.setOptions({
                    minDate: $box.find('.recurring-publish-date').val() || false,
                    maxDate: getDateOnlyValue($box.find('.recurring-start-datetime').val()) || false
                });
            }

            if ($input.hasClass('recurring-ticket-sell-end')) {
                var $row = $input.closest('tr');

                this.setOptions({
                    minDate: $row.find('.recurring-ticket-sell-start').val() || false,
                    maxDate: getDateOnlyValue($box.find('.recurring-end-datetime').val()) || false
                });
            }
        },
        onChangeDateTime: function (currentDateTime, input) {
            var $input = $(input);
            var $box = $input.closest('.recurring-event-box');

            updateReadableDate(input);

            if ($input.hasClass('recurring-publish-date')) {
                $box.find('.recurring-ticket-sell-start').each(function () {
                    setDateIfInvalid($(this), $input.val(), getDateOnlyValue($box.find('.recurring-start-datetime').val()));
                });
            }

            if ($input.hasClass('recurring-ticket-sell-start')) {
                var $row = $input.closest('tr');
                setDateIfInvalid($row.find('.recurring-ticket-sell-end'), $input.val(), getDateOnlyValue($box.find('.recurring-end-datetime').val()));
            }

            if ($input.hasClass('recurring-expiry-date')) {
                setDateIfInvalid($input, getDateOnlyValue($box.find('.recurring-end-datetime').val()), '');
            }

            if ($input.hasClass('recurring-ticket-sell-end')) {
                var $ticketRow = $input.closest('tr');
                setDateIfInvalid($input, $ticketRow.find('.recurring-ticket-sell-start').val(), getDateOnlyValue($box.find('.recurring-end-datetime').val()));
            }
        }
    });

    $('.recurring-date-input').each(function () {
        updateReadableDate(this);
    });

    $(document).on('keyup change blur', '.recurring-date-input', function () {
        updateReadableDate(this);
    });
</script>
@endpush