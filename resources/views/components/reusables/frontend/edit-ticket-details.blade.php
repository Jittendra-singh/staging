<div class="modal fade detailed-modal-popup" id="editMyRegistration" tabindex="-1" aria-labelledby="editMyRegistrationLabel" aria-hidden="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editMyRegistrationLabel">Ticket Details</h5>
					<button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i class="uil uil-multiply"></i></button>
				</div>
				<div class="modal-body">
					<div class="model-content main-form">
						<h6 class="mt-3">Registration Details</h6>
						<div class="row">
							<div class="col-lg-12 col-md-12">
								<div class="form-group mt-4">
									<label class="form-label" for="attendee_name">Attendee Name*</label>
									<input 
										class="form-control h_40 attendee-name-input" 
										type="text" 
										id="attendee_name" 
										name="attendee_name" 
										placeholder="John Doe" 
										value="John Doe" 
										required
									>																								
								</div>
							</div>
							<div class="col-lg-12 col-md-12">
								<div class="form-group mt-4">
									<label class="form-label" for="attendee_email">Attendee Email*</label>
									<input disabled
										class="form-control h_40 attendee-email-input" 
										type="email" 
										id="attendee_email" 
										name="attendee_email" 
										placeholder="test@gmail.com" 
										value="test@gmail.com"
									>																								
								</div>
							</div>
							<div class="col-lg-12 col-md-12">
								<div class="form-group mt-4">
									<label class="form-label" for="attendee_phone">Attendee Phone (Optional)</label>
									<input type="tel"  
										class="form-control h_40 attendee-phone-input" 
										id="attendee_phone" 
										name="attendee_phone" 
										placeholder="1245124541" 
										value="1245124541"
									>																								
								</div>
							</div>
							<div class="col-lg-12 col-md-12">
								<div class="form-group mt-4">
									<label class="community-hours d-flex mt-4" for="community_hours">
										<input type="checkbox" 
											class="community-hours-checkbox" 
											id="community_hours" 
											name="community_hours" 
											value="forever"
										>
										<span class="ms-2">Needs community service hours (optional)</span>
									</label>																							
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="primary-btn w-fit" data-bs-target="#aboutModal" data-bs-toggle="modal" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="primary-btn w-fit">Update</button>
				</div>
			</div>
		</div>
	</div>