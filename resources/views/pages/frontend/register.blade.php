@php
    $outPages = isset($outPages) ? true : false;
@endphp
@extends('layouts.frontend')

@push('style')
<link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }} ">
<style>
    .registration {
        max-width: 560px;
        margin: 0 auto;
    }

    .registration.py-5 {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    .register-panel-form {
        width: 100%;
    }

    .register-panel-heading {
        margin-bottom: 18px;
    }

    .register-panel-heading h1 {
        margin: 0 0 14px;
        color: #1f2933;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.3;
    }

    .register-panel-heading p {
        max-width: 480px;
        margin: 0;
        color: #344054;
        font-size: 13px;
        line-height: 1.55;
    }

    .register-benefits-list {
        display: grid;
        gap: 14px;
        margin: 22px 0 28px !important;
        padding-left: 0 !important;
        list-style: none !important;
    }

    .register-benefits-list li {
        position: relative;
        padding-left: 26px;
        color: #344054 !important;
        font-size: 13px;
        line-height: 1.45;
    }

    .register-benefits-list li::before {
        content: "\f00c";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        top: 1px;
        left: 0;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: var(--primary-color);
        color: #fff;
        font-size: 8px;
        line-height: 15px;
        text-align: center;
    }

    .register-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
    }

    .register-form-group {
        margin-top: 22px;
    }

    .register-panel-form .form-label {
        margin-bottom: 8px;
        color: #1f2933;
        font-size: 12px;
        font-weight: 700;
    }

    .register-panel-form .form-control {
        height: 48px;
        border: 1px solid #edf2f7;
        border-radius: 3px;
        background-color: #f4f8fb;
        color: #1f2933;
        font-size: 13px;
        font-weight: 400;
        padding: 10px 14px;
        box-shadow: none;
    }

    .register-panel-form .form-control:focus {
        border-color: var(--linear-gradient-2);
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(0, 145, 255, 0.12);
    }

    .register-panel-form .pass-show-eye {
        top: 4px;
        right: 4px;
        color: #667085;
    }

    .register-panel-form .pass-show-eye:hover {
        color: var(--linear-gradient-2);
    }

    .register-password-feedback {
        margin-top: 8px;
        color: #dc3545;
        font-size: 12px;
    }

    .register-options-block {
        display: grid;
        gap: 12px;
        margin-top: 28px;
    }

    .register-checkbox-label {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 0;
        color: #344054;
        font-size: 13px;
        line-height: 1.4;
        cursor: pointer;
    }

    .register-checkbox-label input {
        width: 16px;
        height: 16px;
        margin: 0;
        accent-color: var(--linear-gradient-2);
    }

    .register-submit-btn {
        width: fit-content;
        min-width: 245px;
        height: 48px;
        max-height: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0;
    }

    .register-login-text {
        margin-top: 20px;
        color: #344054;
        font-size: 13px;
        font-weight: 400;
    }

    .register-login-text a {
        color: var(--linear-gradient-2);
        font-weight: 700;
        text-decoration: none;
    }

    .register-login-text a:hover {
        color: var(--secondary-color) !important;
        text-decoration: underline;
    }

    .select2-container--default .select2-selection--single {
        height: 48px;
        border: 1px solid #edf2f7;
        border-radius: 3px;
        background: #f4f8fb !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 48px;
        color: #1f2933;
        font-size: 13px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px;
    }

    @media (max-width: 575px) {
        .registration {
            max-width: 100%;
        }

        .register-form-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .register-submit-btn {
            width: 100%;
            min-width: 0;
        }
    }
    .app-form-content{
        padding: 130px 0;
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
                                <form method="POST" action="{{ route('front-register-post') }}" class="registerValidateform register-panel-form">
                                    @csrf

                                    <div class="register-panel-heading">
                                        <h1>Register To Events</h1>
                                        <p>Once you create your account and confirm your email address, you will be able to:</p>
                                    </div>

                                    <ul class="register-benefits-list">
                                        <li>Setup weekly alerts of upcoming cleanups near you</li>
                                        <li>Manage cleanup events you are attending or organizing via your dashboard</li>
                                        <li>Post Cleanups and Recruit Volunteers</li>
                                    </ul>

                                    <x-reusables.frontend.alert />

                                    <div class="register-form-grid">
                                        <div class="form-group">
                                            <label class="form-label">First Name*</label>
                                            <input class="form-control h_50" name="first_name" type="text" placeholder="Enter your first name" required />
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Last Name*</label>
                                            <input class="form-control h_50" name="last_name" type="text" placeholder="Enter your last name" required />
                                        </div>
                                    </div>

                                    <div class="form-group register-form-group">
                                        <label class="form-label">Email*</label>
                                        <input class="form-control h_50" name="email_address" type="email" placeholder="Enter your email" required />
                                    </div>

                                    <div class="form-group register-form-group">
                                        <label class="form-label">Password*</label>
                                        <div class="loc-group position-relative">
                                            <input class="form-control h_50" name="password" type="password" placeholder="Enter your password" minlength="4" required />
                                            <span class="pass-show-eye"><i class="fas fa-eye-slash"></i></span>
                                        </div>
                                    </div>

                                    <div id="passwordFeedback" class="register-password-feedback"></div>

                                    <div class="register-options-block">
                                        <div class="form-group">
                                            <label for="is_newsletter" class="register-checkbox-label">
                                                <input value="1" name="is_newsletter" type="checkbox" id="is_newsletter" />
                                                <span>Receive Our Quarterly Newsletter</span>
                                            </label>
                                        </div>

                                        <div class="form-group">
                                            <label for="weekly_alerts" class="register-checkbox-label">
                                                <input value="1" name="weekly_alerts" type="checkbox" id="weekly_alerts" />
                                                <span>Receive Our Weekly Alerts</span>
                                            </label>
                                        </div>

                                        <div class="register_pincode_div" style="display:none;">
                                            <div class="form-group mt-2">
                                                <label class="form-label">Zip/Post Code*</label>
                                                <select datatype="pincode" class="form-select select2ajax city_state_zip" id="pincode" name="register_pincode"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="primary-btn register-submit-btn mt-4" type="submit">Create My Account</button>

                                    <div class="register-login-text">
                                        Already have an account?
                                        <a href="{{ route('front-login') }}">Login</a>
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