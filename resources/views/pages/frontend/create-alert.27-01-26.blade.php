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
</style>
@endpush
@section('content')
    <div class="wrapper mt-0" style="background-color: #f5f5f5cf">
        <div class="hero-banner">
            <div class="container mb-5">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <div class="hero-banner-content" data-aos="fade-right" data-aos-offset="200" data-aos-duration="800"
                            data-aos-easing="ease-in-sine">
                            <h2 class="text-black">Get Notified of Future Cleanups Near You</h2>
                            <p class="text-black">Submit your information below, and we will send you a single email every Tuesday with a list of all upcoming cleanups for the next 3 weeks within a 15-mile radius of your zip code. You will also receive our quarterly newsletter. It’s easy to cancel, and we never sell your information.</p>
                            <p class="text-black">NOTE: Once you fill out the form, you will need to click on a confirmation link in your inbox to confirm.</p>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-12">
                        <div class="search-form main-form" data-aos="fade-left" data-aos-anchor="#example-anchor" data-aos-duration="800">
                                    <x-reusables.frontend.alert />
                            <form id="eventListForm" method="post" action="{{ route('submit-create-alert') }}">
                                    @csrf
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="text form-control h_50" name="first_name" placeholder="First Name" required>
                                    </div>
                                    <div class="col-lg-6">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="text form-control h_50" name="last_name" placeholder="Last Name" required>
                                    </div>
                                    <div class="col-lg-6 mt-0">
                                        <label>Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="text form-control h_50" name="email" placeholder="Email Address" required>
                                    </div>
                                    <!-- <div class="col-lg-6 mt-0">
                                        <label>Zipcode <span class="text-danger">*</span></label>
                                        <input type="text" class="text form-control h_50" name="zipcode" placeholder="Zipcode" required>
                                    </div> -->
                                    <div class="col-lg-6 mt-0">
                                        <div class="form-group">
                                            <label>Zipcode <span class="text-danger">*</span></label>
                                            <select datatype="pincode" class="form-select select2ajax city_state_zip" id="pincode" name="checkout_pincode" required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mt-0">
                                        <div class="form-group">
                                            <label for="newsletter" class="mb-0">
                                                <input name="newsletter" type="checkbox" id="newsletter" class="me-2" value="1" />
                                                Receive our quarterly newsletter
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mt-0">
                                        <div class="form-group mb-3">
                                            <label for="create_account" class="mb-0">
                                                <input name="create_account" type="checkbox" id="create_account" class="me-2" value="1" required />
                                                I understand that an account will be created to send me cleanup alerts.
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start gap-2 pt-2">
                                    <button type="submit" class="primary-btn">Create Alert</button>
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
