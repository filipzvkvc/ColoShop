<div id="loading-overlay">
    <div class="spinner"></div>
</div>
<!-- Main Content -->

				<div class="main_content">

					<!-- Products -->

					<div class="products_iso">
						<div class="row">
							<div class="col">
									<h3>Profile</h3>
									<form id='profileForm' method="post" class="form-container" enctype="multipart/form-data">
										

										<div class="form-group">
											<div>
												<input type="text" class="form_input input_name input_ph" id="input_first_name" name="first_name" placeholder="First name" required="required">
												<span id="error_first_name" class="error-message"></span>
											</div>

											<div>
												<input type="text" class="form_input input_last_name input_ph" id="input_last_name" name="last_name" placeholder="Last name" required="required">
												<span id="error_last_name" class="error-message"></span>
											</div>
										</div>

										<div class="form-group">
											<div>
												<input type="email" class="form_input input_email input_ph" id="input_email" name="email" placeholder="Email" required="required">
												<span id="error_email" class="error-message"></span>
											</div>
										</div>

										<div class="password-wrapper">
											<div class="form-group">
												<div>
													<input type="password" class="form_input input_password input_ph" id="new_password" name="new_password" placeholder="New password" data-error-target="error_password" required="required">
													<div class="password-eye-wrapper">
														<button type="button" class="toggle-password-btn" id="toggle_password">
															<i class="fa fa-eye-slash"></i>
														</button>
													</div>
												</div>

												<div>
													<input type="password" class="form_input input_password input_ph" id="confirm_password" name="confirm_password" placeholder="Confirm password" data-error-target="error_password" required="required">
													<div class="password-eye-wrapper">
														<button type="button" class="toggle-password-btn" id="toggle_password">
															<i class="fa fa-eye-slash"></i>
														</button>
													</div>
												</div>
											</div>
											<span id="error_password" class="error-message"></span>

										</div>



										<label>Profile picture</label>
										<div class="form-group profile-row">
											<input type="file" class="form_input profile_image input_ph" id="profile_image" name="profile_image" accept="image/*">

											<button type="button" id="remove_picture_btn" class="remove-picture-btn">
												Remove current picture
											</button>
										</div>
										<span id="error_profile_image" class="error-message"></span>


										<button id="edit_profile" type="submit" class="submit-btn">Save Changes</button>
									</form>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="confirmModal" class="custom-modal">
		<div class="modal-content">
			<p id="confirmMessage"></p>
			<div class="modal-buttons">
				<button id="confirmYes" class="modal-btn yes-btn">Yes</button>
				<button id="confirmNo" class="modal-btn no-btn">No</button>
			</div>
		</div>
	</div>
