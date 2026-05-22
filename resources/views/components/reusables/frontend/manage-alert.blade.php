@push('style')
<link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }} ">
<style>
    .select2-container{
        width: 100% !important;
    }
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
<div class="modal fade" id="manageAlerts" tabindex="-1" aria-labelledby="manageAlertsLabel" aria-hidden="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<form action="{{route('front-save-email-alerts')}}" method="POST">
					@csrf
					<div class="modal-header">
						<h5 class="modal-title" id="manageAlertsLabel">Manage Weekly Alert</h5>
						<button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i class="uil uil-multiply"></i></button>
					</div>
					<div class="modal-body">
						<div class="model-content main-form">
							<div class="row">
								<div class="col-lg-12 col-md-12">
									<div class="form-group mt-4">
										<label class="form-label">Alert Name*</label>
										<input class="form-control h_40" type="text" placeholder="" name="alert_name"  required>																								
									</div>
								</div>
								<div class="col-lg-12 col-md-12">
									<div class="form-group mt-4">
										<label class="form-label">Zipcode</label>					
										<select datatype="pincode" class="form-control form-select select2ajax" id="city_id" name="city_id">
										</select>		
									</div>
								</div>
								<div class="col-lg-12 col-md-12">
									<div class="form-group mt-4">
										<label class="form-label">Email Frequency</label>
										<input class="form-control h_40" type="tel" placeholder="1245124541" value="Weekly" disabled>																								
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="primary-btn w-fit" data-bs-target="#aboutModal" data-bs-toggle="modal" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="primary-btn w-fit">Create</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@push('script')
<script src="{{ asset('assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
<script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/libs/imask/imask.min.js') }}"></script>
@endpush