@php
	$organisation_detail_page_url = route('organizer-detail', ['slug' => $user->organisation_landing_slug] ); 
@endphp
@extends('layouts.frontend')
@push('style')
<link rel="stylesheet" href="{{ asset('assets_front/vendor/cropperjs/cropper.min.css') }} ">
<style>
    .img-preview-container,.og_img-preview-container {
        width: 150px;
        height: 150px;
        overflow: hidden;
        border-radius: 50%;
        border: 2px solid #ddd;
        margin: 10px auto;
    }
    #cropper-modal .modal-body, #og_cropper-modal .modal-body {
        max-height: 70vh;
        overflow: hidden;
    }
    #crop-image-container img, #og_crop-image-container img {
        max-width: 100%;
        display: block;
    }
	#cropper-modal .modal-body, #og_cropper-modal .modal-body {
		max-height: 60vh;
		overflow: hidden;
		padding: 0;
	}
	#crop-image-container, #og_crop-image-container {
		max-height: 60vh;
		width: 100%;
		overflow: hidden;
	}
	#image-to-crop, #og_image-to-crop {
		display: block;
		max-width: 100%;
		max-height: 60vh;
	}
</style>
@endpush
@push('script')
<script src="{{ asset('assets_front/vendor/cropperjs/cropper.min.js') }}"></script>
<script src="{{ asset('assets_front/vendor/cropperjs/jquery-cropper.min.js') }}"></script>

<script>
$(document).ready(function () {

    let cropperInstance = null;
    // Step 1: When user picks a file, show the modal with cropper
    $('#image-input').on('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            // Set image src and open modal
            $('#image-to-crop').attr('src', event.target.result);
            $('#cropper-modal').modal('show');
        };
        reader.readAsDataURL(file);
    });
    // Step 2: When modal opens, initialize cropper
    $('#cropper-modal').on('shown.bs.modal', function () {
        // Destroy any existing cropper first
        if (cropperInstance) {
            $('#image-to-crop').cropper('destroy');
            cropperInstance = null;
        }

        // Init jQuery Cropper
        $('#image-to-crop').cropper({
            aspectRatio: 1 / 1,        // Square crop for profile pic
            viewMode: 1,               // Restrict crop box to image
            autoCropArea: 0.8,
            movable: true,
            zoomable: true,
            scalable: true,
            preview: '.img-preview-container', // Live preview in the circle above
        });
        cropperInstance = true;
    });
    // Step 3: Destroy cropper when modal closes
    $('#cropper-modal').on('hidden.bs.modal', function () {
        $('#image-to-crop').cropper('destroy');
        cropperInstance = null;
		if($('#cropped_image').val() != ''){
			$('#current-preview').attr('src', $('#cropped_image').val());
		}
		$('#image-input').val('');
    });
    // Step 4: On "Crop & Use This" click, get the canvas data
    $('#crop-btn').on('click', function () {
        const canvas = $('#image-to-crop').cropper('getCroppedCanvas', {
            width: 400,
            height: 400,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        // Convert to base64 and store in hidden input
        const base64Data = canvas.toDataURL('image/jpeg', 0.85);
        $('#cropped_image').val(base64Data);

        // Update the current preview image
        $('#current-preview').attr('src', base64Data);

        // Enable save button
        $('#save-btn').prop('disabled', false);

        // Close modal
        $('#cropper-modal').modal('hide');
    });
	$('#upload-btn').on('click', function () {
		$('#image-input').trigger('click');
	});



	// og image 
	
    let cropperInstanceOg = null;
    // Step 1: When user picks a file, show the modal with cropper
    $('#og_image-input').on('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            // Set image src and open modal
            $('#og_image-to-crop').attr('src', event.target.result);
            $('#og_cropper-modal').modal('show');
        };
        reader.readAsDataURL(file);
    });
    // Step 2: When modal opens, initialize cropper
    $('#og_cropper-modal').on('shown.bs.modal', function () {
        // Destroy any existing cropper first
        if (cropperInstanceOg) {
            $('#og_image-to-crop').cropper('destroy');
            cropperInstanceOg = null;
        }

        // Init jQuery Cropper
        $('#og_image-to-crop').cropper({
            aspectRatio: 1 / 1,        // Square crop for profile pic
            viewMode: 1,               // Restrict crop box to image
            autoCropArea: 0.8,
            movable: true,
            zoomable: true,
            scalable: true,
            preview: '.og_img-preview-container', // Live preview in the circle above
        });
        cropperInstanceOg = true;
    });
    // Step 3: Destroy cropper when modal closes
    $('#og_cropper-modal').on('hidden.bs.modal', function () {
        $('#og_image-to-crop').cropper('destroy');
        cropperInstanceOg = null;
		if($('#og_cropped_image').val() != ''){
			$('#og_current-preview').attr('src', $('#og_cropped_image').val());
		}
		$('#og_image-input').val('');
    });
    // Step 4: On "Crop & Use This" click, get the canvas data
    $('#og_crop-btn').on('click', function () {
        const canvas = $('#og_image-to-crop').cropper('getCroppedCanvas', {
            width: 400,
            height: 400,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        // Convert to base64 and store in hidden input
        const base64Data = canvas.toDataURL('image/jpeg', 0.85);
        $('#og_cropped_image').val(base64Data);

        // Update the current preview image
        $('#og_current-preview').attr('src', base64Data);

        // Enable save button
        $('#save-btn').prop('disabled', false);

        // Close modal
        $('#og_cropper-modal').modal('hide');
    });
	$('#og_upload-btn').on('click', function () {
		$('#og_image-input').trigger('click');
	});
});
</script>

@endpush
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
											<img id="pimage" src="{{$user->formattedImage('profile_picture')}}" alt="{{$user->name}}">
											<div class="demo"></div>

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