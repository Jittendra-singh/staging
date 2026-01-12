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
    .form-check-input.waiver_check_age{
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
                                                <input class="form-control h_50 checkout-email email_address billing-email" type="text" name="checkout_email_address" required
                                                    @if($user && $user->email_address)
                                                value="{{$user->email_address ?? ''}}" readonly
                                                @endif
                                                >
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12">
                                            <div class="form-group mt-4">
                                                <label class="form-label">First Name*</label>
                                                <input class="form-control h_50 first_name" name="checkout_first_name" type="text" placeholder="" required
                                                    @if($user && $user->first_name)
                                                value="{{$user->first_name ?? ''}}" readonly
                                                @endif
                                                >
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12">
                                            <div class="form-group mt-4">
                                                <label class="form-label">Last Name*</label>
                                                <input class="form-control h_50 last_name" name="checkout_last_name" type="text" required
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
                                                <input class="form-control h_50 phone usphonemask" name="phone" type="text"
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
@endpush