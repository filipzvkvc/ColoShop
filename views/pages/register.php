<!-- Loading overlay -->
<div id="loading-overlay">
    <div class="spinner"></div>
</div>

	<div class="container contact_container">
		<div class="row">
			<div class="col">

				<!-- Breadcrumbs -->

				<div class="breadcrumbs d-flex flex-row align-items-center">
					<ul class="breadcrumbs_menu">
						<li><a href="index.php?page=home">Home</a></li>
						<li class="nav-item">
							<i class="fa fa-angle-right breadcrumb-arrow" aria-hidden="true"></i>
							<a class="nav_link" href="index.php?page=register">
								<span class="breadcrumb-text">Register</span>
							</a>
						</li>
					</ul>

				</div>

			</div>
		</div>

	<!-- Contact Us -->

	<div class="row">
		<div class="col-lg-6 offset-lg-3 get_in_touch_col2">
			<div class="get_in_touch_contents2">
				<h1>Create a new account</h1>
				<p id='formtext'>It's quick and easy.</p>
				<form method="post">
					<div>
						<input id="input_first_name" class="form_input input_name input_ph" type="text" name="first_name" placeholder="First name" required="required">
						<span id="error_first_name" class="error-message"></span>

						<input id="input_last_name" class="form_input input_last_name input_ph" type="text" name="last_name" placeholder="Last name" required="required">
						<span id="error_last_name" class="error-message"></span>

						<input id="input_email" class="form_input input_email input_ph" type="email" name="email" placeholder="Email" required="required">
						<span id="error_email" class="error-message"></span>

						<div class="password-wrapper">
							<input id="input_password" class="form_input input_password input_ph" type="password" name="password" placeholder="New password" required="required">
							
							<div class="password-eye-wrapper">
								<button type="button" class="toggle-password-btn" id="toggle_password">
									<i class="fa fa-eye-slash"></i>
								</button>
							</div>
                    	</div>
						<span id="error_password" class="error-message"></span>
					</div>
					<div>
						<button id="register_submit" type="submit" class="red_button message_submit_btn trans_300">Sign Up</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>