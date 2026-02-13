@php
    $outPages = true;
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
    .receive-type-box{
        background:#F1F5F9;
    }
    .receive-type-box a{
        background: transparent;
        color: var(--secondary-color);
        border:none;
        border-radius: var(--bs-btn-border-radius)!important;
        font-weight: 500;
    }
    .receive-type-box a.active{
        background: white!important;
        color: var(--secondary-color)!important;
    }
.receive-box {
    /* background: #f3f4f7; */
    border-radius: 10px;
}

.option-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    border: 1px solid #dcdfea;
    border-radius: 10px;
    background: #fff;
    cursor: pointer;
    transition: all .2s ease;
    height: 100%;
}

.option-card input {
    width: 18px;
    height: 18px;
    accent-color: #4a6cf7;
    margin: 0;
}

.option-card.active {
    border: 1px solid #4a6cf7;
    background: #eef2ff;
}

.option-card:hover {
    border-color: #4a6cf7;
}


</style>
@endpush
@section('content')
    <div class="wrapper mt-0" style="background-color: #f5f5f5cf">
        <div class="hero-banner">
            <div class="container mb-5">
                <div class="row align-items-center">
                    <div class="col-sm-12 col-lg-12">
                        <div class="search-form main-form" data-aos="fade-left" data-aos-anchor="#example-anchor" data-aos-duration="800">
                            <form id="weekly_alert_box" method="post" action="{{ route('submit-create-weekly-newsletter-alert') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-8 offset-lg-2 col-sm-12 text-center">
                                    <h3 class="text-black"><b>Get Weekly Cleanup Alerts Near You</b></h3>
                                    <p class="text-black">Sign up to receive weekly cleanup updates near you (within 15 miles of your ZIP code). You can also opt in to our quarterly newsletter. <br/>Unsubscribe anytime - we never sell your data.</p>
                                    <p class="text-primary m-0">NOTE: Check your inbox to confirm your subscription.</p>
                                </div>
                                <div class="col-lg-8 offset-lg-2 col-sm-12 rs mb-3">
                                <x-reusables.frontend.alert />
                                    <div class="receive-box">

                                        <div class="d-flex align-items-center mb-3">
                                            <span class="me-2 fa fa-bell"></span>
                                            <h6 class="mb-0 fw-semibold">What would you like to receive?</h6>
                                        </div>

                                        <div class="row g-3">

                                            <!-- Weekly Cleanup Alerts -->
                                            <div class="col-6">
                                                <label class="option-card active w-100">
                                                    <input value="1" class="create_alert_checkbox" type="checkbox" name="weekly_alert" checked>
                                                    <span>Weekly Cleanup Alerts</span>
                                                </label>
                                            </div>

                                            <!-- Quarterly Newsletter -->
                                            <div class="col-6">
                                                <label class="option-card w-100">
                                                    <input value="1" class="create_alert_checkbox" type="checkbox" name="newsletter_alert">
                                                    <span>Quarterly Newsletter</span>
                                                </label>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                                <!-- <div class="col-lg-8 offset-lg-2 col-sm-12 rs">
                                    <label class="mb-2">What would you like to receive?</label>
                                    <div class="btn-group w-100 receive-type-box" role="group" > 
                                        <a href="javascript:void(0);" onclick="changeCreateAlertForm('weeklyalerts',this);" class="btn btn-outline-primary px-3 py-2 active text-white m-2">
                                            Weekly Cleanup Alerts
                                        </a>
                                        <a href="javascript:void(0);" onclick="changeCreateAlertForm('quarterlynewsletter',this);" class="btn btn-outline-primary px-3 py-2 m-2">
                                            Quarterly Newsletter
                                        </a>
                                    </div>
                                </div> -->
                                <div class="col-lg-8 offset-lg-2 col-sm-12 mt-3">
                                    <div class="row">
                                        <div class="col-sm-6 mt-0 mb-2">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" class="text form-control h_50 specialtextonlymask" name="first_name" placeholder="First Name" required>
                                        </div>
                                        <div class="col-sm-6 mt-0 mb-2">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" class="text form-control h_50 specialtextonlymask" name="last_name" placeholder="Last Name" required>
                                        </div>
                                        <div class="col-lg-6 mt-0 mb-2 email-div">
                                            <label>Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="text form-control h_50" name="email" placeholder="Email Address" required>
                                        </div>
                                        <!-- <div class="col-lg-6 mt-0">
                                            <label>Zipcode <span class="text-danger">*</span></label>
                                            <input type="text" class="text form-control h_50" name="zipcode" placeholder="Zipcode" required>
                                        </div> -->
                                        <div class="col-lg-6 mt-0 mb-2 zipcode-div">
                                            <label>Zipcode <span class="text-danger">*</span></label>
                                            <div class="form-group">
                                                <select datatype="pincode" class="form-select select2ajax city_state_zip" id="pincode" name="checkout_pincode" required>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-12 mt-0">
                                            <div class="form-group">
                                                <label for="newsletter" class="mb-0">
                                                    <input name="newsletter" type="checkbox" id="newsletter" class="me-2" value="1" />
                                                    Receive our quarterly newsletter
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-0">
                                            <div class="form-group">
                                                <label for="create_account" class="mb-0">
                                                    <input name="create_account" type="checkbox" id="create_account" class="me-2" value="1" required />
                                                    I understand that an account will be created to send me cleanup alerts.
                                                </label>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center gap-2 pt-3 mt-3">
                                <button type="submit" class="primary-btn">Subscribe Now</button>
                            </div>
                            </form>
                            <form id="quarterly_newsletter_box" method="post" action="{{ route('submit-create-newsletter') }}" style="display: none;">
                            @csrf
                            <div class="row g-3 mt-3">
                                <div class="col-lg-8 offset-lg-2 col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-6 mt-0 mb-2">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" class="text form-control h_50" name="first_name" placeholder="First Name" required>
                                        </div>
                                        <div class="col-sm-6 mt-0 mb-2">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" class="text form-control h_50" name="last_name" placeholder="Last Name" required>
                                        </div>
                                        <div class="col-sm-12 mt-0 mb-2">
                                            <label>Email Address <span class="text-danger">*</span></label>
                                            <input type="email" class="text form-control h_50" name="email" placeholder="Email Address" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center gap-2 pt-3 mt-3">
                                <button type="submit" class="primary-btn">Get Quarterly Newsletter</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
            
@endsection
@push('script')
<script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/libs/imask/imask.min.js') }}"></script>
<script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/validation.js')}}"></script>
@endpush
