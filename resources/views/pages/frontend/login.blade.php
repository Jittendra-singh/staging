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
                                <form action="{{ route('front-login-post') }}" method="POST" class="validateform login-panel-form">
                                    @csrf
                                    @if(request()->filled('redirect_to'))
                                        <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                                    @endif
                                    <div class="login-panel-heading">
                                        <h2 class="registration-title">Welcome back</h2>
                                        <p>Please enter your details to sign in.</p>
                                    </div>

                                    <x-reusables.frontend.alert />

                                    <div class="form-group login-form-group">
                                        <label class="form-label">Your Email</label>
                                        <input class="form-control h_50" type="email" name="email" placeholder="Enter your email" required />
                                    </div>

                                    <div class="form-group login-form-group">
                                        <div class="field-password">
                                            <label class="form-label">Password</label>
                                            <a class="forgot-pass-link" href="{{ route('front-forgot-password') }}">Forgot Password?</a>
                                        </div>

                                        <div class="loc-group position-relative">
                                            <input class="form-control h_50" type="password" name="password" placeholder="Enter your password" required />
                                            <span class="pass-show-eye"><i class="fas fa-eye-slash"></i></span>
                                        </div>
                                    </div>

                                    <button class="primary-btn login-submit-btn mt-4" type="submit">
                                        SIGN IN <i class="fas fa-arrow-right"></i>
                                    </button>

                                    <div class="login-divider">
                                        <span>OR CONTINUE WITH</span>
                                    </div>

                                    <div class="social-login-options">
                                        <a href="javascript:void(0);" class="social-login-btn google-login-btn" id="googleLoginBtn">
                                            <!-- <i class="fa-brands fa-google social-icon google-icon"></i> -->
                                            <img
                                                src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
                                                alt="Google"
                                                class="google-login-icon">
                                            <span>Google</span>
                                        </a>

                                        <a href="javascript:void(0);" class="social-login-btn magic-login-btn" data-bs-toggle="modal" data-bs-target="#magicLinkLoginModal">
                                            <i class="far fa-envelope social-icon"></i>
                                            <span>Magic Link</span>
                                        </a>
                                    </div>

                                    <div class="login-register-text">
                                        Don't have an account?
                                        <a href="{{ route('front-register') }}">Register</a>
                                    </div>

                                    <!-- <div class="login-migration-notice">
                                        <div class="notice-icon">
                                            <i class="fas fa-info"></i>
                                        </div>

                                        <div class="notice-content">
                                            <p>
                                                <strong>Platform Migration Notice:</strong>
                                                On Nov 3, 2025, we moved to a new system. Your data is safe, but you'll need to reset your password for security.
                                            </p>
                                            <a href="{{ route('front-forgot-password') }}">
                                                Reset password now <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div> -->
                                </form>
                                <form id="googleLoginForm" action="{{ route('front-login-google') }}" method="POST" style="display:none;">
                                    @csrf
                                    <input type="hidden" name="credential" id="googleCredential">
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
            <form id="magicLinkLoginForm" action="{{ route('front-login-magic-link') }}" method="POST" novalidate>
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="magicLinkLoginModalLabel">Login with Magic Link</h5>
                        <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="uil uil-multiply"></i>
                        </button>
                    </div>

                    <!-- <div class="modal-body">
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
                                        <label class="form-label">Email*</label>
                                        <input class="form-control h_50" type="email" name="email_address" placeholder="Enter your email" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="modal-body">
                        <div class="model-content main-form">
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="form-group mt-4">
                                        <label class="form-label">Email*</label>
                                        <input class="form-control h_50" type="email" name="email_address" placeholder="Enter your email" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="primary-btn w-fit" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" form="magicLinkLoginForm" class="primary-btn w-fit">Get Magic Link</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@push('script')
    <script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('assets/js/validation.js')}}"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        window.addEventListener('load', function () {
            google.accounts.id.initialize({
                client_id: "{{ config('services.google.client_id') }}",
                callback: handleGoogleCredentialResponse
            });

            var googleLoginBtn = document.getElementById('googleLoginBtn');

            if (googleLoginBtn) {
                googleLoginBtn.addEventListener('click', function () {
                    google.accounts.id.prompt();
                });
            }
        });

        function handleGoogleCredentialResponse(response) {
            var googleCredential = document.getElementById('googleCredential');
            var googleLoginForm = document.getElementById('googleLoginForm');

            if (!googleCredential || !googleLoginForm) {
                console.error('Google login form or credential input is missing.');
                return;
            }

            googleCredential.value = response.credential;
            googleLoginForm.submit();
        }
    </script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var magicLinkForm = document.getElementById('magicLinkLoginForm');

        if (!magicLinkForm) {
            return;
        }

        magicLinkForm.addEventListener('submit', function (event) {
            var emailInput = magicLinkForm.querySelector('[name="email_address"]');

            if (!emailInput || !emailInput.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();

                magicLinkForm.classList.add('was-validated');

                if (emailInput) {
                    emailInput.focus();
                }

                return false;
            }
        });
    });
</script>

@endpush
@push('style')
<style>
    .registration {
        max-width: 430px;
        margin: 0 auto;
    }

    .login-panel-form {
        width: 100%;
    }

    .login-panel-heading {
        margin-bottom: 24px;
    }

    .login-panel-heading .registration-title {
        margin-top: 0;
        margin-bottom: 6px;
        padding-bottom: 0 !important;
        color: #1f2933;
        font-size: 24px;
        font-weight: 700;
        line-height: 1.25;
    }

    .login-panel-heading p {
        margin-bottom: 0;
        color: #667085;
        font-size: 13px;
        line-height: 1.4;
    }

    .login-form-group {
        margin-top: 20px;
    }

    .login-form-group .form-label {
        margin-bottom: 8px;
        color: #1f2933;
        font-size: 12px;
        font-weight: 600;
    }

    .login-panel-form .form-control {
        height: 48px;
        border: 1px solid #cfd8e3;
        border-radius: 6px;
        background-color: #fff;
        color: #1f2933;
        font-size: 13px;
        font-weight: 400;
        padding: 10px 14px;
        box-shadow: none;
    }

    .login-panel-form .form-control:focus {
        border-color: var(--linear-gradient-2);
        box-shadow: 0 0 0 3px rgba(0, 145, 255, 0.12);
    }

    .login-panel-form .field-password {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .login-panel-form .forgot-pass-link {
        position: static;
        color: var(--linear-gradient-2);
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
    }

    .login-panel-form .forgot-pass-link:hover {
        color: var(--secondary-color) !important;
        text-decoration: underline;
    }

    .login-panel-form .pass-show-eye {
        top: 4px;
        right: 4px;
        color: #667085;
    }

    .login-panel-form .pass-show-eye:hover {
        color: var(--linear-gradient-2);
    }

    .login-submit-btn {
        width: 100%;
        height: 48px;
        max-height: none;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0;
    }

    .login-divider {
        display: flex;
        align-items: center;
        gap: 14px;
        margin: 26px 0 20px;
        color: #667085;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
    }

    .login-divider::before,
    .login-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #d9e2ec;
    }

    .login-divider span {
        white-space: nowrap;
    }

    .social-login-options {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .social-login-btn {
        height: 46px;
        border: 1px solid #cfd8e3;
        border-radius: 6px;
        background: #fff;
        color: #344054;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .social-login-btn:hover {
        border-color: var(--linear-gradient-2);
        color: var(--linear-gradient-2);
        box-shadow: 0 6px 16px rgba(0, 145, 255, 0.12);
        text-decoration: none;
    }

    .social-icon {
        color: var(--linear-gradient-2);
        font-size: 14px;
        line-height: 1;
    }

    .google-icon {
        color: #4285f4;
    }

    .login-register-text {
        margin-top: 26px;
        color: #344054;
        font-size: 13px;
        font-weight: 400;
    }

    .login-register-text a {
        color: var(--linear-gradient-2);
        font-weight: 700;
        text-decoration: none;
    }

    .login-register-text a:hover {
        color: var(--secondary-color) !important;
        text-decoration: underline;
    }

    .login-migration-notice {
        margin-top: 24px;
        padding: 16px 18px;
        border: 1px solid #cfd8e3;
        border-radius: 8px;
        background: #f3f8fc;
        display: flex;
        gap: 12px;
    }

    .notice-icon {
        width: 18px;
        height: 18px;
        min-width: 18px;
        margin-top: 2px;
        border: 1px solid var(--primary-color);
        border-radius: 50%;
        color: var(--primary-color);
        font-size: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .notice-content p {
        margin-bottom: 8px;
        color: #344054;
        font-size: 11px;
        line-height: 1.5;
    }

    .notice-content strong {
        font-weight: 700;
    }

    .notice-content a {
        color: var(--linear-gradient-2);
        font-size: 11px;
        font-weight: 700;
        text-decoration: none;
    }

    .notice-content a:hover {
        color: var(--secondary-color) !important;
        text-decoration: underline;
    }

    @media (max-width: 575px) {
        .registration {
            max-width: 100%;
        }

        .social-login-options {
            grid-template-columns: 1fr;
        }
    }
    .google-login-icon {
        width: 16px;
        height: 16px;
        display: block;
    }
    .app-form-content{
        padding: 130px 0;
    }
</style>
@endpush