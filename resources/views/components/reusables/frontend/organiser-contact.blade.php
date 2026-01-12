<div class="modal fade" id="contactOrgraniser" tabindex="-1" aria-labelledby="contactOrgraniserLabel" aria-modal="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="contactOrgraniserLabel">Contact Organizer</h5>
				<button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
						<path d="M2.146 2.146a.5.5 0 0 1 .708 0L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854a.5.5 0 0 1 0-.708z" />
					</svg></button>
			</div>
			<form class="validateform" action="{{route('save-organizer-lead')}}" method="post">
				@csrf
				<div class="modal-body">
					<div class="model-content main-form">
						<div class="row">
							<div class="col-lg-12 col-md-12">
								<div class="form-group mt-4">
									<input type="hidden" name="organiser_id" value="{{$organiser}}">
									<label class="form-label">Name*</label>
									<input class="form-control h_40" type="text" name="name" placeholder="" value="" required>
								</div>
							</div>
							<div class="col-lg-12 col-md-12">
								<div class="form-group mt-4">
									<label class="form-label">Email*</label>
									<input class="form-control h_40" type="email" name="email" placeholder="" value="" required>
								</div>
							</div>
							<div class="col-lg-12 col-md-12">
								<div class="form-group mt-4">
									<label class="form-label">Phone*</label>
									<input class="form-control h_40 usphonemask" type="text" name="phone" placeholder="" value="" required>
								</div>
							</div>
							<div class="col-lg-12 col-md-12">
								<div class="form-group mt-4">
									<label class="form-label">Message*</label>
									<textarea class="form-control" cols="30" rows="7" name="message" required></textarea>
								</div>
							</div>

							<div class="col-lg-12 col-md-12 mt-4">
								<button type="submit" class="primary-btn w-100">Send Message</button>
							</div>
						</div>
					</div>
				</div>
				<!-- <div class="modal-footer">
						<button type="button" class="primary-btn w-fit" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="primary-btn w-fit">Add</button>
					</div> -->
			</form>
		</div>
	</div>
</div>
@push('script')
<script src="{{ asset('assets/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
<script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/libs/imask/imask.min.js') }}"></script>
@endpush