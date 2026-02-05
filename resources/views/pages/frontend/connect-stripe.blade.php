@extends('layouts.frontend')
@push('style')
<link rel="stylesheet" href="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.css') }} ">
<style>
.custom-bg-color{
    background:#F3F7FE!important;
    border-color:#F3F7FE!important;
}
.box-icon-custom{
    font-size: 45px;
}
.help-box {
    background-color: #f8f9fb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
}

.help-box .icon {
    /* width: 22px;
    height: 22px; */
    border-radius: 50%;
    background-color: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-weight: 600;
    font-size: 13px;
}

.help-box a {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 500;
}

.help-box a:hover {
    text-decoration: underline;
}
.email-box {
    /* background-color: #f8f9fb;
    border: 1px solid #e5e7eb; */
    border-radius: 8px;
    /* padding: 14px 18px; */
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.email-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.email-icon {
    width: 34px;
    height: 34px;
    background-color: #e9f0ff;
    color: #0d6efd;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.email-text strong {
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}
.email-text small {
    display: block;
    font-size: 12px;
    color: #6b7280;
}
.disconnect-box {
    background-color: #fff5f5;
    border: 1px solid #fed7d7;
    border-radius: 8px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.disconnect-text h6 {
    color: #dc2626;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 4px;
}

.disconnect-text p {
    margin: 0;
    font-size: 12px;
    color: #b91c1c;
}

.btn-disconnect {
    background-color: #fff;
    border: 1px solid #fca5a5;
    color: #dc2626;
    font-size: 13px;
    padding: 6px 16px;
    border-radius: 6px;
}

.btn-disconnect:hover {
    background-color: #fee2e2;
    color: #b91c1c;
}
</style>
@endpush
@push('script')
    <script src="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datetimepicker/jquery.datetimepicker.min.js') }}"></script>
@endpush
@section('content')
<div class="wrapper wrapper-body">
	<div class="dashboard-body orders-page">
		<div class="container-fluid">
			<div class="col-md-12 d-flex align-items-center justify-content-between mb-5">
				<div class="d-main-title">
					<h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>Stripe Connect</h3>
                    @php
                        $breadcrumbs = [
                            [
                                'name' => 'Stripe Connect', 
                                'url' => route('front-stripe-connect')
                            ],
                        ]
                    @endphp     
                    <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                </div>
			</div>
		</div>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
                    <x-reusables.frontend.alert />
                    <div class="main-card">
                        <div class="dashboard-wrap-content">
                            <div class="dashboard-report-content">
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                        <div class="dashboard-report-card success custom-bg-color">
                                            <div class="card-content">
                                                <div class="card-content">
                                                    <div class="col-sm-12 text-center my-3">
                                                        <i class="fa fa-university text-primary box-icon-custom mb-3"></i>
                                                        <h5 class="text-primary">Secure Payouts</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if(!empty(auth()->guard('front')->user()->stripe_email) && !empty(auth()->guard('front')->user()->stripe_account_id))
                                    <div class="col-sm-12 pt-4">
                                        <h4>Stripe Connection Active</h4>
                                        <p>Your account is successfully inked. Onboarding and payout preferences are managed externally.</p>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="email-box pb-3">
                                            <div class="email-left">
                                                <div class="email-icon">
                                                    <i class="fa fa-envelope"></i>
                                                </div>
                                                <div class="email-text">
                                                    <strong>{{auth()->guard('front')->user()->stripe_email}}</strong>
                                                    <small>Primary Connected Account</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <a class="primary-btn w-fit" target="_blank" href="https://dashboard.stripe.com/login">Go to Stripe Dashboard</a>
                                            </div>
                                        </div>
                                    </div>
                                    @else 
                                    <div class="col-sm-12 pt-4">
                                        <h4>Set up your payouts</h4>
                                        <p>To start receiving revenue from your events, you'll need to link a Stripe account. This allows for secure, automated transfers directly to your bank account with industry-standard encryption.</p>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Stripe Account Email</label>
                                                    <input class="form-control h_40 w-100" id="stripe_email" name="stripe_email" required type="text" placeholder="Enter email address">
                                                    <small>Use the email associated with your existing Stripe account, or the one you'd like to use to create a new one. We'll use this to pre-fill your onboarding form.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 pt-4">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <button id="stripe-connect-btn" url="{{ route('connect-stripe') }}" class="primary-btn w-fit" type="button">Connect with Stripe</button>
                                            </div>
                                        </div>
                                    </div>
                                    @endif 
                                </div>
                            </div>
                        </div>
                    </div>
					<div class="conversion-setup mt-4">
						<div class="main-card" style="border: none;">
                                <div class="row">
                                    <div class="col-sm-12">
                                        @if(!empty(auth()->guard('front')->user()->stripe_email) && !empty(auth()->guard('front')->user()->stripe_account_id))
                                        <div class="disconnect-box">
                                            <div class="disconnect-text">
                                                <h6>Disconnect Account</h6>
                                                <p>Removing your connection will pause all future payouts until a new account is connected.</p>
                                            </div>

                                            <button url="{{route('front-disconnect-stripe-connect')}}" onclick="disconnectStripeAccount(this);" type="button" class="btn btn-disconnect">Disconnect</button>
                                        </div>
                                        @else 
                                        <div class="help-box">
                                            <div class="icon"><i class="fa fa-question-circle"></i></div>
                                            <div>
                                                <strong>Need help setting up payouts?</strong><br>
                                                Our support team is available 24/7 to help you with your financial setup.
                                                <a href="{{config('app.frontsite')}}contact-us">Contact Support</a>
                                            </div>
                                        </div>
                                        @endif 
                                    </div>
                                </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection