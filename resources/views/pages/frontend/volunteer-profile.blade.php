@php
	$organisation_detail_page_url = route('organizer-detail', ['slug' => $user->organisation_landing_slug] ); 
@endphp
@extends('layouts.frontend')
@section('content')
   	<div class="wrapper wrapper-body">
		<div class="dashboard-body">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="d-main-title">
							<h3><i class="fa-solid fa-circle-info me-3"></i>My Organization Details</h3>
							@php						
								$breadcrumbs = [
										[
											'name' => 'My Organization Details', 
											'url' => route('front-profile')
										],
									]
							@endphp     
							<x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
						</div>
					</div>
                        <x-reusables.frontend.alert />
					<div class="col-lg-6 col-md-12">
						<div class="conversion-setup">
							<div class="main-card mt-5">
								<div class="bp-title position-relative">
									<h4>Personal Information</h4>									
									<div class="profile-edit-btn">
										{{-- <a href="#" data-bs-toggle="modal" data-bs-target="#orgSettings" class="btn"><i class="fa-solid fa-user-gear"></i></a> --}}
										<a href="#" data-bs-toggle="modal" data-bs-target="#org-profile-update-pop" class="btn"><i class="fa-solid fa-pen"></i></a>
									</div>
								</div>
								<div class="about-details">
									<div class="about-step text-center">
										<div class="user-avatar-img  mb-3">
											<img  src="{{$user->formattedImage('profile_picture')}}" alt="{{$user->name}}">
										</div>
										<div class="user-dts">
											<h4 class="user-name">{{$user->name}}<span class="verify-badge"><i class="fa-solid fa-circle-check"></i></span></h4>
											<span class="user-email">{{$user->email_address}}</span>
										</div>
									</div>
									
									<div class="about-step">
										<h5>Address</h5>
										<p class="mb-0">{{$user->fullAddress}}</p>
									</div>																												
								</div>
							</div>
						</div>
					</div>
					@if($user->is_organiser == 1)
						<div class="col-lg-6 col-md-12 organisation-details-div">
							<div class="conversion-setup h-100">
								<div class="main-card mt-5 h-100 overflow-hidden">
									<div class="bp-title position-relative">
										<h4>Organization Information</h4>	
										<div class="profile-edit-btn">
											<!-- <a href="{{ !empty($user->organisation_website) ? $user->organisation_website : '#' }}" target="_blank" class="btn"><i class="fa-solid fa-arrow-up-right-from-square"></i></a> -->
											<a href="#" data-bs-toggle="modal" data-bs-target="#orgPrivacySettings" class="btn"><i class="fa-solid fa-pen"></i></a>
										</div>							
									</div>
									<div class="about-details">
										<div class="d-flex align-items-top justify-content-start mb-4 about-step ">
											<div class="user-avatar-img organiser-avatar me-3">
												<img height="50" width="50" 
													src="{{ 
															$user->formattedImage('organisation_logo') 
														}}" 
														alt="{{ $user->organisation_name ?? 'Organization Logo' }}">
											</div>
											<div class="user-dts">
												<h4 class="user-name mt-0">
													{{ $user->organisation_name ?? 'Organization Name' }}
													@if(!empty($user->organisation_website))
														<span class="verify-badge"><i class="fa-solid fa-circle-check"></i></span>
													@endif
												</h4>
												<a class="user-email" href="{{ !empty($user->organisation_website) ? $user->organisation_website : '#' }}" target="_blank">
													View Website
													<i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
												</a>
												<a class="user-email mt-2" href="{{ $organisation_detail_page_url }}" target="_blank">
													View Public Profile
													<i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
												</a>
											</div>
										</div>
										<div class="about-step">
											<h5>Short Description</h5>
											<p class="mb-0">{!! $user->organisation_short_description ?? 'No description provided.' !!}</p>
										</div>
										<!-- <div class="about-step">
											<h5>Connect Stripe</h5>
											<div class="form-group">
												@if(!empty($user->stripe_email) && !empty($user->stripe_account_id))
												<label class="form-label" for="stripe_email">{{$user->stripe_email}} <span class="verify-badge"><i class="fa-solid fa-circle-check"></i></span> </label>
												@else 
												<label class="form-label" for="stripe_email">Stripe Account Email*</label>
												<div class="loc-group position-relative">
													<input class="form-control h_40" type="email" placeholder="" value="" id="stripe_email" name="stripe_email" required>
													<span id="stripe-connect-btn" url="{{ route('connect-stripe') }}" class="copy-link bg-primary text-white">Connect</span>
												</div>
												@endif 
											</div>
										</div> -->
										<div class="about-step">
											<h5>Find us on</h5>
											<div class="social-links">
												@if(!empty($user->organisation_facebook))
													<a href="{{ $user->organisation_facebook }}" class="social-link" target="_blank" data-bs-toggle="tooltip" title="Facebook"><i class="fab fa-facebook-square"></i></a>
												@endif
												@if(!empty($user->organisation_instagram))
													<a href="{{ $user->organisation_instagram }}" class="social-link" target="_blank" data-bs-toggle="tooltip" title="Instagram"><i class="fab fa-instagram"></i></a>
												@endif
												@if(!empty($user->organisation_twitter))
													<a href="{{ $user->organisation_twitter }}" class="social-link" target="_blank" data-bs-toggle="tooltip" title="𝕏"><i class="fab"> 𝕏</i></a>
												@endif
												@if(!empty($user->organisation_linkedin))
													<a href="{{ $user->organisation_linkedin }}" class="social-link" target="_blank" data-bs-toggle="tooltip" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
												@endif
												@if(!empty($user->organisation_website))
													<a href="{{ $user->organisation_website ? : '#' }}" class="social-link" target="_blank" data-bs-toggle="tooltip" title="Website"><i class="fa-solid fa-globe"></i></a>
												@endif 
											</div>
										</div>
										<!-- <div class="about-step">
											<a href="{{ $organisation_detail_page_url }}" target="_blank" class="view-profile-link a-link">
												View Public Profile <i class="fa-solid fa-arrow-up-right-from-square ms-2"></i>
											</a>
										</div> -->
									</div>
								</div>
							</div>
						</div>
					@else
						<div class="col-lg-6 col-md-12 organisation-details-div">
							<div class="conversion-setup h-100">
								<div class="main-card mt-5 h-100 d-flex align-items-center justify-content-center" >
									<div class="text-center w-100">
										<h4 class="mb-3">You have not organized any event yet</h4>
										<p class="mb-4">Make one to declare yourself as an organizer.</p>
										
										<a href="{{ route('front-post-a-event') }}" class="btn border border-rounded-lg border-primary  ">
										 	Organize Your First Event
										</a>
									</div>
								</div>
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
	<x-reusables.frontend.profile-popup :cities="$cities" :user="$user"/>
@endsection
@push('script')
    <script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('assets/js/validation.js')}}"></script>
@endpush