@php
    $outPages = isset($outPages) ? true : false;
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
</style>
@endpush
@section('content')
    <div class="form-wrapper">
        <div class="app-form">
            <div class="app-form-sidebar">
                <div class="sidebar-sign-logo">
                    {{-- <img src="images/sign-logo.svg" alt=""> --}}
                </div>
                <div class="sign_sidebar_text">
                    <h1>The Easiest Way to Create Events and Sell More Tickets Online</h1>
                </div>
            </div>
            <div class="app-form-content justify-content-center">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-7 col-lg-10 col-md-7">
                            <div class="registration py-5">
                                <form method="POST" action="{{ route('front-register-post') }}" class="registerValidateform">
                                    @csrf
                                    <h1>Register To Events</h1>
                                    <h6 class="mt-0">Once you create your account and confirm your email address, <span
                                            class="">you will be able to:</span></h6>
                                    <ul>
                                        <li>Setup weekly alerts of upcoming cleanups near you</li>
                                        <li>
                                            Manage cleanup events you are attending or organizing via your dashboard
                                        </li>
                                        <li>
                                            Post Cleanups and Recruit Volunteers
                                        </li>
                                    </ul>
                                    <div class="d-flex align-items-center gap-2 mt-5">
                                        <div class="form-group w-100">
                                            <label class="form-label">First Name*</label>
                                            <input class="form-control h_50" name="first_name" type="text" placeholder="Enter your first name" required />
                                        </div>
                                        <div class="form-group w-100">
                                            <label class="form-label">Last Name*</label>
                                            <input class="form-control h_50" name="last_name" type="text" placeholder="Enter your last name" required />
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-4">
                                        <div class="form-group w-100">
                                            <label class="form-label">Email*</label>
                                            <input class="form-control h_50" name="email_address" type="email" placeholder="Enter your email" required />
                                        </div>
                                        <div class="form-group w-100">
                                            <label class="form-label">Password*</label>
                                            <input class="form-control h_50" name="password" type="password" placeholder="Enter your password" minlength="4" required />
                                        </div>
                                    </div>
                                    <div id="passwordFeedback" style="font-size:14px;color:red;"></div>
                                    <!-- <div class="form-group mt-4">
                                        <label class="form-label">Are You Associated with An Organization that Plans to Host
                                            a Cleanup?</label>
                                        <div class="d-flex gap-3">
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input class="organization_association" id="is_org_1" name="organization_association" type="radio" value="1" required />
                                                    <label class="form-label m-0 p-0" for="is_org_1">Yes</label>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                <input class="organization_association_deselect" id="is_org_0" name="organization_association" type="radio" value="0" required />
                                                <label class="form-label m-0 p-0" for="is_org_0">No</label>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-4 organization_association_name d-none">
                                        <label class="form-label">Organization Name</label>
                                        <input class="form-control h_50" name="organization_association_name" type="text" 
                                            placeholder="Enter your organization name"
                                            value="">
                                    </div>

                                    <div class="form-group mt-4">
                                        <label class="form-label">Receive Our Quarterly Newsletter</label>
                                        <div class="d-flex gap-3">
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input class="" name="newsletter" id="newsletter" type="radio" value="1" required />
                                                    <label class="form-label m-0 p-0" for="newsletter">Yes</label>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <input class="" name="newsletter" id="newsletter-2" type="radio" value="0" required />
                                                    <label class="form-label m-0 p-0" for="newsletter-2">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="coupon-code-block mt-4">
                                        <div class="form-group mb-3">
                                            <label for="is_newsletter" class="mb-0">
                                                <input value="1" name="is_newsletter" type="checkbox" id="is_newsletter" class="me-2" /> Receive Our Quarterly Newsletter
                                            </label>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label for="weekly_alerts" class="mb-0">
                                                <input value="1" name="weekly_alerts" type="checkbox" id="weekly_alerts" class="me-2" /> Receive Our Weekly Alerts
                                            </label>
                                        </div>
                                        <div class="col-lg-6 register_pincode_div" style="display:none;">
                                            <div class="form-group mt-2">
                                                <label class="form-label">Zip/Post Code*</label>
                                                <select datatype="pincode" class="form-select select2ajax city_state_zip" id="pincode" name="register_pincode">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <x-reusables.frontend.alert />
                                    <button class="primary-btn w-fit mt-4" type="submit">Create My Account</button>
                                    <div class="mt-3">
                                        <p class="text-start">Already have an account? <a
                                                href="{{ route('front-login') }}">Login</a></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
<script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('assets/js/validation.js')}}"></script>
@endpush