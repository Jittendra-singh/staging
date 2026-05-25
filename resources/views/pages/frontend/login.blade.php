@php
    $outPages = isset($outPages) ? true : false;
@endphp
@extends('layouts.frontend')
@section('title')
    Login -Find a cleanup near you or in Neighbourhood
@endsection
@section('content')
    <div class="form-wrapper">
        <div class="app-form">
            <div class="app-form-sidebar">
                <div class="sidebar-sign-logo">
                    {{-- <img src="images/sign-logo.svg" alt=""> --}}
                </div>
                <!-- <div class="sign_sidebar_text">
                    <h1>The Easiest Way to Create Events and Sell More Tickets Online</h1>
                </div> -->
            </div>
            <div class="app-form-content justify-content-center">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-7 col-lg-10 col-md-7">
                            <div class="registration">
                                <form action="{{route('front-login-post')}}" method="POST" class="validateform">
                                    @csrf
                                    <h2 class="registration-title pb-3">Log in to Your Dashboard</h2>
                                    
                                    <x-reusables.frontend.alert />
                                    <div class="form-group mt-4">
                                        <label class="form-label">Your Email*</label>
                                        <input class="form-control h_50" type="email" name="email" placeholder="Enter your email" required />
                                    </div>
                                    <div class="form-group mt-4">
                                        <div class="field-password">
                                            <label class="form-label">Password*</label>
                                            <a class="forgot-pass-link" href="{{route('front-forgot-password')}}">Forgot Password?</a>
                                        </div>
                                        <div class="loc-group position-relative">
                                            <input class="form-control h_50" type="password" name="password" placeholder="Enter your password" required />
                                            <span class="pass-show-eye"><i class="fas fa-eye-slash"></i></span>
                                        </div>
                                    </div>
                                    <button class="primary-btn w-fit mt-4" type="submit">Sign In <i class="fas fa-sign-in-alt ms-2"></i></button>

                                    <div class="login-divider">
                                        <span>OR</span>
                                    </div>

                                    <div class="social-login-options">
                                        <!-- <a href="{{ route('front-login-google') }}" class="social-login-btn google-login-btn">
                                            <i class="fa-brands fa-google social-icon"></i>
                                            <span>Login with Google</span>
                                        </a> -->
                                        <a href="javascript:void(0);" class="social-login-btn magic-login-btn" data-bs-toggle="modal" data-bs-target="#magicLinkLoginModal">
                                            <i class="far fa-envelope social-icon"></i>
                                            <span>Login with Magic Link</span>
                                        </a>
                                    </div>

                                    <div class="mt-3">
                                        <label class="text-start form-label h5">
                                            Don't have an account? 
                                            <a href="{{ route('front-register') }}">Register</a>
                                        </label>

                                        <p>
                                            On Nov 3, 2025, we migrated to a new platform. Your information has been moved over, but you will need to reset your password to log in. Please reset your password 
                                            <a href="{{route('front-forgot-password')}}">here</a>.
                                        </p>
                                    </div>
                                    <!-- <div class="mt-3">
                                        <label class="text-start form-label h5">Don't have an account? 
                                            <a href="{{ route('front-register') }}">Register</a>
                                        </label>
                                        <p>On Nov 3, 2025, we migrated to a new platform. Your information has been moved over, but you will need to reset your password to log in. Please reset your password <a href="{{route('front-forgot-password')}}">here</a>.</p>
                                    </div> -->
                                    {{-- @dd(request()->session()) --}}
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="magicLinkLoginModal" tabindex="-1" aria-labelledby="magicLinkLoginModalLabel" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('front-login-magic-link') }}" method="POST" class="validateform">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="magicLinkLoginModalLabel">Login with Magic Link</h5>
                        <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="uil uil-multiply"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="model-content main-form">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group mt-4">
                                        <label class="form-label">First Name*</label>
                                        <input class="form-control h_50" type="text" name="first_name" placeholder="Enter first name" required>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group mt-4">
                                        <label class="form-label">Last Name*</label>
                                        <input class="form-control h_50" type="text" name="last_name" placeholder="Enter last name" required>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-md-12">
                                    <div class="form-group mt-4">
                                        <label class="form-label">Email Id*</label>
                                        <input class="form-control h_50" type="email" name="email_address" placeholder="Enter your email" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="primary-btn w-fit" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="primary-btn w-fit">Get Magic Link</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@push('script')
    <script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('assets/js/validation.js')}}"></script>
@endpush
@push('style')
<style>
    .login-divider {
        display: flex;
        align-items: center;
        gap: 16px;
        margin: 26px 0 18px;
        color: #222;
        font-weight: 600;
        font-size: 15px;
    }

    .login-divider::before,
    .login-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #d7dde6;
    }

    .social-login-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .social-login-btn {
        height: 50px;
        border: 1px solid #d5dbe5;
        border-radius: 6px;
        background: #fff;
        color: #111827;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .social-login-btn:hover {
        border-color: #0d8ff2;
        box-shadow: 0 4px 14px rgba(13, 143, 242, 0.12);
        color: #0d8ff2;
        text-decoration: none;
    }

    .social-icon {
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        color: #0d8ff2;
    }

    .google-icon {
        font-family: Arial, sans-serif;
        font-weight: 700;
        color: #4285f4;
    }

    .magic-link-note {
        color: #667085;
        font-size: 15px;
        margin-top: 14px;
        margin-bottom: 14px;
    }
</style>
@endpush