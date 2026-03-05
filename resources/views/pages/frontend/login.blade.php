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
                                    <div class="mt-3">
                                        <label class="text-start form-label h5">Don't have an account? 
                                            <a href="{{ route('front-register') }}">Register</a>
                                        </p>
                                        <p>On Nov 3, 2025, we migrated to a new platform. Your information has been moved over, but you will need to reset your password to log in. Please reset your password <a href="{{route('front-forgot-password')}}">here</a>.</p>
                                    </div>
                                    {{-- @dd(request()->session()) --}}
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
    <script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('assets/js/validation.js')}}"></script>
@endpush