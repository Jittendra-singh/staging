

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
	#vol_avatar_preview, #organisation_logo_preview{
		border-radius: 50%;
	}
	.org_design_button{
		padding: unset;
	}
</style>
@endpush
<!-- Organisation Profile Update Model Start-->
	<div class="modal fade" id="org-profile-update-pop" tabindex="-1" aria-labelledby="orgProfileUpdatepopLabel" aria-hidden="false">
		<div class="modal-dialog modal-medium-2 modal-dialog-centered modal-sm-height modal-lg">
			<div class="modal-content modal-dialog-scrollable">
				<div class="modal-header">
					<h5 class="modal-title" id="orgProfileUpdatepopLabel">Volunteer details</h5>
					<button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
				</div>
				<form class="validateform" action="{{route('save.front-profile')}}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="modal-body">
						<div class="model-content main-form">
							<div class="row">
								<div class="col-lg-12 col-md-12 text-center">
									<!-- <div class="form-group text-center mt-5">
										<span class="org_design_button btn-file">
											<img id="vol_avatar_preview" height="120px" width="120px" src="{{$user->formattedImage('profile_picture')}}" alt="{{$user->name}}">
											<input type="file" id="vol_avatar" accept="image/*" name="profile_picture">
										</span>																								
									</div> -->
									{{-- Hidden input to store the cropped base64 image --}}
									<input type="hidden" name="cropped_image" id="cropped_image">

									{{-- Current profile image preview --}}
									<div class="img-preview-container">
										<img src="{{$user->formattedImage('profile_picture')}}"
											id="current-preview" alt="Profile" style="width:100%; height:100%; object-fit:cover;">
									</div>

									{{-- Hidden file input --}}
									<input type="file" id="image-input" accept="image/*" style="display: none;">

									{{-- Visible button --}}
									<button type="button" class="btn btn-outline-primary" id="upload-btn">
										Update Profile Picture
									</button>
									<br/>
									<small>Recommended size: 200 × 200 px</small>
								</div>
								<div class="col-lg-6 col-md-6">
									<div class="form-group mt-4">
										<label class="form-label">First Name*</label>
										<input class="form-control h_40" required type="text" name="first_name" placeholder="" value="{{ old('first_name', $user->first_name) }}">																								
									</div>
								</div>
								<div class="col-lg-6 col-md-6">
									<div class="form-group mt-4">
										<label class="form-label">Last Name*</label>
										<input class="form-control h_40" required type="text" name="last_name" placeholder="" value="{{ old('last_name', $user->last_name) }}">																								
									</div>
								</div>
								<div class="col-lg-6 col-md-12">
									<div class="form-group mt-4">
										<label class="form-label">Phone <span class="fs-7 text-info"> Enter US number must start with +1 </span></label>
										<input class="form-control h_40 usphonemask" type="text" name="phone" placeholder="" value="{{ old('phone', $user->phone) }}">																								
									</div>
								</div>
								<div class="col-lg-12 col-md-12">
									<h4 class="address-title">Address</h4>
								</div>
								<div class="col-lg-6 col-md-12">
									<div class="form-group mt-4">
										<label class="form-label">Address Line 1</label>
										<input class="form-control h_40" type="text" name="address_line_1" placeholder="" value="{{ old('address_line_1', $user->address_line_1) }}">																								
									</div>
								</div>
								<div class="col-lg-6 col-md-12">
									<div class="form-group mt-4">
										<label class="form-label">Address Line 2</label>
										<input class="form-control h_40" type="text" name="address_line_2" placeholder="" value="{{ old('address_line_2', $user->address_line_2) }}">																								
									</div>
								</div>
								<!-- <div class="col-lg-6 col-md-12">
									<div class="form-group mt-4">
										<label class="form-label">City</label>
										<select class="form-control h_40" name="city_id">
											@foreach($cities as $c)
												<option value="{{ $c->city_id }}" @selected(old('city', $user->city_id) == $c->city_id)>
													{{ $c->city_name }} - {{$c->state->state_name}} 
												</option>
											@endforeach
										</select>
									</div>
								</div> -->
								
								<div class="col-lg-6 col-md-12">
									<div class="form-group mt-4">
										<label class="form-label">Zip Code (City, State)</label>
										<!-- <input class="form-control h_40" type="text" name="pincode" placeholder="" value="{{ old('pincode', $user->pincode) }}">		 -->																				
										<select datatype="pincode" class="form-control form-select select2ajax" id="city_id" name="city_id">
											@if($user && $user->city_state_zip && $user->city_state_zip->id)
											<option selected value="{{ $user->city_state_zip->id }}">{{ $user->city_state_zip->zipcode.' ('.$user->city_state_zip->city.', '.$user->city_state_zip->state.')' }}</option>
											@endif
										</select>		
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="primary-btn w-fit" data-bs-toggle="modal" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="primary-btn w-fit">Update</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	@if($user->is_organiser == 1)
		<!-- Organisation Profile Update Model End-->
		<!-- Organisation Privacy Settings Model Start-->
		<div class="modal fade" id="orgPrivacySettings" tabindex="-1" aria-labelledby="orgPrivacySettingsLabel" aria-hidden="false">
			<div class="modal-dialog modal-medium-2 modal-dialog-centered modal-sm-height modal-lg overflow-hidden">
				<div class="modal-content modal-dialog-scrollable">
					<div class="modal-header">
						<h5 class="modal-title" id="orgPrivacySettingsLabel">Manage Organization Details</h5>
						<button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
					</div>
					<form class="validateform" action="{{route('save.front.organisation.details')}}" method="POST" enctype="multipart/form-data" >
						@csrf
						<div class="modal-body">
							<div class="model-content main-form pt-2 p-4">
								<div class="row">
									<div class="col-lg-12 col-md-12 text-center">
										<div class="form-group text-center mt-5">
											<span class="org_design_button btn-file">
												@if(!empty($user->organisation_logo))
													<img id="organisation_logo_preview" height="120px" width="120px" src="{{ $user->formattedImage('organisation_logo') }}" alt="{{ $user->organisation_name }}">
												@else
													<img id="organisation_logo_preview" height="120px" width="120px" src="{{ asset('assets_front/images/profile-imgs/img-13.jpg') }} " alt="{{ $user->organisation_name }}">
												@endif
												<input type="file" id="organisation_logo" accept="image/*" name="organisation_logo">
											</span>																								
										</div>
										<small>Recommended size: 200 × 200 px</small>
									</div>
									<div class="col-lg-12 col-md-12">
										<div class="form-group">
											<label class="form-label" for="organisation_name">Organization Name*</label>
											<input required type="text" class="form-control h_40" id="organisation_name" name="organisation_name" placeholder="" value="{{ old('organisation_name', $user->organisation_name ?? '') }}">
										</div>
									</div>
									<div class="col-lg-6 col-md-12">
										<div class="form-group mt-4">
											<label class="form-label" for="organisation_website">Website</label>
											<input class="form-control h_40 urlmask" type="text" id="organisation_website" name="organisation_website" placeholder="" value="{{ old('organisation_website', $user->organisation_website ?? '') }}">																								
										</div>
									</div>
									<div class="col-lg-6 col-md-12">
										<div class="form-group mt-4">
											<label class="form-label" for="organisation_landing_slug">Landing Page Slug*</label>
											<input required class="form-control h_40" type="text" id="organisation_landing_slug" name="organisation_landing_slug" placeholder="" value="{{ old('organisation_landing_slug', $user->organisation_landing_slug ?? '') }}">																								
										</div>
									</div>
									<div class="col-lg-12 col-md-12">
										<div class="form-group mt-4">
											<label class="form-label" for="organisation_short_description">Short Description</label>
											<textarea name="organisation_short_description" class="form-control ckeditor" id="organisation_short_description" cols="30" rows="10">{{ old('organisation_short_description', $user->organisation_short_description ?? '') }}</textarea>
										</div>
									</div>
									
									<div class="col-lg-6 col-md-12">
										<div class="form-group mt-4">
											<label class="form-label" for="organisation_facebook">Facebook (URL)</label>
											<input class="form-control h_40 urlmask" type="text" id="organisation_facebook" name="organisation_facebook" placeholder="" value="{{ old('organisation_facebook', $user->organisation_facebook ?? '') }}">																								
										</div>
									</div>
									<div class="col-lg-6 col-md-12">
										<div class="form-group mt-4">
											<label class="form-label" for="organisation_instagram">Instagram (URL)</label>
											<input class="form-control h_40 urlmask" type="text" id="organisation_instagram" name="organisation_instagram" placeholder="" value="{{ old('organisation_instagram', $user->organisation_instagram ?? '') }}">																								
										</div>
									</div>
									<div class="col-lg-6 col-md-12">
										<div class="form-group mt-4">
											<label class="form-label" for="organisation_twitter">X (URL)</label>
											<input class="form-control h_40 urlmask" type="text" id="organisation_twitter" name="organisation_twitter" placeholder="" value="{{ old('organisation_twitter', $user->organisation_twitter ?? '') }}">																								
										</div>
									</div>
									<div class="col-lg-6 col-md-12">
										<div class="form-group mt-4">
											<label class="form-label" for="organisation_linkedin">LinkedIn (URL)</label>
											<input class="form-control h_40 urlmask" type="text" id="organisation_linkedin" name="organisation_linkedin" placeholder="" value="{{ old('organisation_linkedin', $user->organisation_linkedin ?? '') }}">																								
										</div>
									</div>
									
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="primary-btn w-fit" data-bs-toggle="modal" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="primary-btn w-fit">Update</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	@endif
	<div class="modal fade" id="cropper-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop Your Profile Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="crop-image-container">
                    <img id="image-to-crop" src="" alt="Image to crop">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="crop-btn">✂ Crop & Use This</button>
            </div>
        </div>
    </div>
</div>
@push('script')
<script src="{{ asset('assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
<script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/libs/imask/imask.min.js') }}"></script>
@endpush