@php
$outPages = true;
$ticket_booking = session()->get('ticket_booking', []);
$user = auth()->guard('front')->check() ? auth()->guard('front')->user()->load(['city_state_zip']) : null;
@endphp
@extends('layouts.frontend')
@push('style')
<link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }} ">
<style>
    .select2-container--default .select2-selection--single {
        background: #f9f9f9 !important;
        border-color: #dee2e6;
    }

    .select2-container .select2-selection--single,
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 50px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 50px;
    }
    .form-check-input.waiver_check_age, 
    .form-check-input.has-children-checkbox{
        border-color: black;
        margin: 2px;
    }
    .switch-field label{
        width: calc(20% - 8px);
    }
    .switch-field input:checked+label{
        background-color: var(--linear-gradient-2) !important;
        border-color: var(--linear-gradient-2) !important;
    }
</style>
@endpush

@section('content')
<div class="wrapper" style="background-color: #f5f5f5cf">
    <div class="event-dt-block p-80">
        <form id="checkoutForm" ticketchecked="0" action="{{route('front-create-order')}}" method="POST" class="validateform">
            @csrf
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="main-title checkout-title">
                            <h3>Checkout</h3>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-12 col-md-12">
                        <div class="checkout-block">
                            <div class="main-card">
                                <div class="bp-title">
                                    <h4>Your Information</h4>
                                </div>
                                <div class="bp-content bp-form">
                                    <div class="row parent-container">
                                        <x-reusables.frontend.alert />
                                        <div class="col-lg-12 col-md-12">
                                            <div class="form-group mt-4">
                                                <label class="form-label">Email*</label>
                                                <input class="form-control h_50 checkout-email email_address billing-email" placeholder="Enter your email" type="text" name="checkout_email_address" required
                                                    @if($user && $user->email_address)
                                                value="{{$user->email_address ?? ''}}" readonly
                                                @endif
                                                >
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12">
                                            <div class="form-group mt-4">
                                                <label class="form-label">First Name*</label>
                                                <input class="form-control h_50 first_name" name="checkout_first_name" type="text" required placeholder="Enter your first name"
                                                    @if($user && $user->first_name)
                                                value="{{$user->first_name ?? ''}}" readonly
                                                @endif
                                                >
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12">
                                            <div class="form-group mt-4">
                                                <label class="form-label">Last Name*</label>
                                                <input class="form-control h_50 last_name" name="checkout_last_name" type="text" required placeholder="Enter your last name"
                                                    @if($user && $user->last_name)
                                                value="{{$user->last_name ?? ''}}" readonly
                                                @endif
                                                >
                                            </div>
                                        </div>
                                        <input type="hidden" name="checkout_address" value="">
                                        <!-- <div class="col-lg-12 col-md-12">
                                            <div class="form-group mt-4">
                                                <label class="form-label">Address</label>
                                                <input class="form-control h_50 address_line_1" type="text" name="checkout_address"
                                                    @if($user && $user->address_line_1)
                                                value="{{$user->address_line_1 ?? ''}}" readonly
                                                @endif
                                                >
                                            </div>
                                        </div> -->
                                        <div class="col-lg-6 col-md-12">
                                            <div class="form-group mt-4">
                                                <label class="form-label">Zip/Post Code*</label>
                                                <!-- <input class="form-control h_50 pincode" type="text" name="checkout_pincode" 
                                                        @if($user && $user->pincode)
                                                            value="{{$user->pincode ?? ''}}" readonly required
                                                        @endif
                                                        required
                                                    > -->
                                                <select datatype="pincode" class="form-select select2ajax city_state_zip" id="pincode" name="checkout_pincode" required>
                                                    @if($user && $user->city_state_zip && $user->city_state_zip->id)
                                                    <option selected value="{{ $user->city_state_zip->id }}">{{ $user->city_state_zip->zipcode.' ('.$user->city_state_zip->city.', '.$user->city_state_zip->state.')' }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" name="checkout_organisation_name" value="">
                                        <!-- <div class="col-lg-4 col-md-12">
                                            <div class="form-group mt-4">
                                                <label class="form-label">Company Name</label>
                                                <input class="form-control h_50 organisation_name" name="checkout_organisation_name" type="text"
                                                    @if($user && $user->organisation_name)
                                                value="{{$user->organisation_name ?? ''}}" readonly
                                                @endif
                                                >
                                            </div>
                                        </div> -->
                                        <div class="col-lg-6 col-md-12">
                                            <div class="form-group mt-4">
                                                <label class="form-label">Phone <span class="fs-7 text-info d-none">Start with +1</span></label>
                                                <input class="form-control h_50 phone usphonemask" name="phone" type="text" placeholder="Enter your phone"
                                                    @if($user && $user->phone)
                                                value="{{$user->phone ?? ''}}" readonly
                                                @endif
                                                >
                                            </div>
                                        </div>
                                        @if(!$user)
                                        <div class="col-lg-6 col-md-12">
                                            <div class="form-group mt-4" id="checkout-password" style="display: none;">
                                                <label class="form-label">Password*</label>
                                                <input class="form-control h_50 password" name="checkout_password" type="password">
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-sm-12">
                                            <div class="form-group mt-4">
                                                <label class="form-label" for="copy_buyer_info_to_attendee">
                                                    <input type="checkbox" name="copy_buyer_info_to_attendee" class="form-check-input" id="copy_buyer_info_to_attendee" value="1"> Attendee is the same as buyer
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($event->allow_donations==1 && !empty($event->creator->stripe_email) && !empty($event->creator->stripe_account_id))
                            <!-- <div class="main-card mt-4 pb-4">
                                <div class="bp-title">
                                    <h4>Optional Donation</h4>
                                </div>
                                <div class="bp-content bp-form">
                                    <div class="row parent-container">
                                        <label class="form-label  mt-4">Would you like to make an optional donation to the Organizer to help support this cleanup?</label>
                                        <div class="col-lg-9 col-md-12">
                                            <div class="form-group mt-1">
                                                <div class="switch-field">
                                                    <div class="sw-container">
                                                        <input type="radio" id="donation_type1" class="payment_type" name="donation_type" value="5">
                                                        <label for="donation_type1" class="me-0">$5</label>

                                                        <input type="radio" id="donation_type2" class="payment_type" name="donation_type" value="10">
                                                        <label for="donation_type2" class="me-0">$10</label>

                                                        <input type="radio" id="donation_type3" class="payment_type" name="donation_type" value="25">
                                                        <label for="donation_type3" class="me-0">$25</label>

                                                        <input type="radio" id="donation_type4" class="payment_type" name="donation_type" value="50">
                                                        <label for="donation_type4" class="me-0">$50</label>

                                                        <input type="radio" id="donation_type5" class="payment_type" name="donation_type" value="custom">
                                                        <label for="donation_type5" class="me-0">Custom</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-12">
                                            <div class="form-group mt-1 pt-1" id="donation-price" style="display: none;">
                                                <input inputmode="decimal" autocomplete="off" oninput="this.value = sanitizeDonation(this.value)" class="form-control h_50" placeholder="Custom Amount" type="text" name="donation_amount">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            @endif
                            <div class="main-card mt-4 pb-4">
                                {{-- <div class="bp-title">
                                        <h4>Ticket information</h4>
                                    </div> --}}
                                <div class="bp-content bp-form">
                                    <x-reusables.frontend.ticket-book-info :event="$event" :ticketbooking="$ticket_booking" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-12 col-md-12">
                        <div class="main-card order-summary">
                            <div class="bp-title">
                                <h4>Ticket Summary</h4>
                            </div>
                            <div class="order-summary-content p_30">
                                <div class="event-order-dt">
                                    <div class="event-thumbnail-img">
                                        <img src="{{ $event->formattedBanner() }}" alt="">
                                    </div>
                                    <div class="event-order-dt-content">
                                        <h5>{{$event->event_name}}</h5>
                                        <span>{{$event->startTimeAndEndTime}}</span>
                                    </div>
                                </div>

                                {!! $order_total_view !!}

                                <div class="coupon-code-block mt-4">
                                    <div class="form-group mb-3">
                                        <label for="terms" class="mb-0">
                                            <input name="terms" type="checkbox" id="terms" checked required class="me-2" />
                                            I acknowledge that I have read and agree to the <a href='{{ config('app.frontsite') }}terms' class='' target="_blank">Terms and Conditions</a> of this website <span class="text-danger">*</span>
                                        </label>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="newsletter" class="mb-0">
                                            <input value="1" name="newsletter" type="checkbox" id="newsletter" checked class="me-2" />
                                            Send me your Quarterly Newsletter
                                        </label>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="weekly_alert" class="mb-0">
                                            <input name="weekly_alert" type="checkbox" id="weekly_alert" class="me-2" value="1" />
                                            Alert me of Upcoming Cleanups every Tuesday
                                        </label>
                                    </div>
                                </div>
                                <div class="confirmation-btn ">
                                    <input name="widget_id" type="hidden" />
                                    <button type="submit" class="primary-btn w-100" id="confirm-pay">
                                        Complete Registration
                                    </button>
                                    <span class="d-none">Price is inclusive of all applicable GST</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@push('script')
<script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/libs/imask/imask.min.js') }}"></script>
<script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/validation.js')}}"></script>
<!-- check if widget_id is there on local storage and get the widget_id_datetime also and check if 7 days have passed or not if passed then we don't add value if not passed then we will add value to $('input[name="widget_id"]').val() -->
<script>
    $(document).ready(function() {
        if (localStorage.getItem('widget_id')) {
            const widget_id = localStorage.getItem('widget_id');
            const widget_id_datetime = localStorage.getItem('widget_id_datetime');
            const widget_id_datetime_obj = new Date(widget_id_datetime);
            const widget_id_datetime_obj_7_days = new Date(widget_id_datetime_obj.getTime() + 7 * 24 * 60 * 60 * 1000);
            if (widget_id_datetime_obj_7_days > new Date()) {
                $('input[name="widget_id"]').val(widget_id);
            }
        }
    });
</script>
<script>
$(document).ready(function () {
    const maxChildren = 4;

    function childRow(ticketIndex, childIndex) {
        return `
            <div class="row child-row align-items-start mb-3" data-child-index="${childIndex}">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Child Name*</label>
                        <input type="text"
                            class="form-control"
                            name="ticket[${ticketIndex}][children][${childIndex}][child_name]"
                            placeholder="Enter child name"
                            required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Child Age*</label>
                        <input type="number"
                            class="form-control"
                            name="ticket[${ticketIndex}][children][${childIndex}][child_age]"
                            min="0"
                            max="12"
                            step="1"
                            placeholder="Enter age"
                            required>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label invisible">Remove</label>
                        <button type="button"
                            class="btn btn-outline-danger remove-child-row w-100">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    function refreshChildRows($parent) {
        const count = $parent.find('.child-row').length;

        $parent.find('.add-child-row').prop('disabled', count >= maxChildren);
        $parent.find('.remove-child-row').toggle(count > 1);
    }

    function addChild($parent) {
        const $rows = $parent.find('.children-rows');

        if ($rows.find('.child-row').length >= maxChildren) {
            return;
        }

        const ticketIndex = $parent.data('ticket-index');
        const nextIndex = $rows.find('.child-row').length;

        $rows.append(childRow(ticketIndex, nextIndex));
        refreshChildRows($parent);
    }

    function clearChildren($parent) {
        $parent.find('.children-rows').empty();
        $parent.find('.children-fields-container').addClass('d-none');
        refreshChildRows($parent);
    }

    $(document).on('change', '.has-children-checkbox', function () {
        const $parent = $(this).closest('.parent-container');
        const $under18 = $parent.find('.is_under_18').first();
        const $under18Option = $parent.find('.under-18-option');
        const $guardians = $parent.find('.waiver-guardian');

        if ($(this).is(':checked')) {
            $under18.prop('checked', false).prop('disabled', true).trigger('change');
            $under18Option.addClass('d-none');

            $guardians.addClass('d-none');
            $guardians.find('input').prop('disabled', true).removeAttr('required');

            $parent.find('.children-fields-container').removeClass('d-none');

            if ($parent.find('.child-row').length === 0) {
                addChild($parent);
            }
        } else {
            $under18.prop('disabled', false);
            $under18Option.removeClass('d-none');

            clearChildren($parent);
        }
    });

    $(document).on('change', '.is_under_18', function () {
        const $parent = $(this).closest('.parent-container');
        const $childrenCheckbox = $parent.find('.has-children-checkbox');
        const $childrenOption = $parent.find('.child-option');
        const $guardians = $parent.find('.waiver-guardian');
        const $guardianInputs = $guardians.find('input');

        if ($(this).is(':checked')) {
            // Show guardian fields for an under-18 attendee.
            $guardians.removeClass('d-none');
            $guardianInputs
                .prop('disabled', false)
                .attr('required', true);

            // An under-18 attendee cannot have accompanying children.
            $childrenCheckbox
                .prop('checked', false)
                .prop('disabled', true);

            $childrenOption.addClass('d-none');
            clearChildren($parent);
        } else {
            // Hide and clear guardian information when the attendee is not under 18.
            $guardians.addClass('d-none');
            $guardianInputs
                .prop('disabled', true)
                .removeAttr('required')
                .val('');

            $childrenCheckbox.prop('disabled', false);
            $childrenOption.removeClass('d-none');
        }
    });

    $(document).on('click', '.add-child-row', function () {
        addChild($(this).closest('.parent-container'));
    });

    $(document).on('click', '.remove-child-row', function () {
        const $parent = $(this).closest('.parent-container');

        $(this).closest('.child-row').remove();

        $parent.find('.child-row').each(function (index) {
            const ticketIndex = $parent.data('ticket-index');

            $(this).attr('data-child-index', index);
            $(this).find('input[name*="[child_name]"]').attr(
                'name',
                `ticket[${ticketIndex}][children][${index}][child_name]`
            );
            $(this).find('input[name*="[child_age]"]').attr(
                'name',
                `ticket[${ticketIndex}][children][${index}][child_age]`
            );
        });

        refreshChildRows($parent);
    });
});
</script>
@endpush