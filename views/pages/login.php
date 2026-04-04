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
							<a class="nav_link" href="index.php?page=login">
								<span class="breadcrumb-text">Login</span>
							</a>
						</li>
					</ul>
				</div>

			</div>
		</div>

	<!-- Contact Us -->

	<div id='verifyMessage'>
		<?php

			if (isset($_GET['message'])) {

				$decoded = decodeMessage(get('message'));

				if (isset($decoded->error)) 
				{
					echo '<div class="alert alert-danger">' . htmlspecialchars($decoded->error) . '</div>';
				} 
				elseif (isset($decoded->success))
				{
					echo '<div class="alert alert-success">' . htmlspecialchars($decoded->success) . '</div>';
				}
			}
		?>
	</div>

	<div class="row">
		<div class="col-lg-6 offset-lg-3 get_in_touch_col2">
			<div class="get_in_touch_contents2">

				<h1>Welcome back</h1>
				<!-- <p id='formtext'>It's quick and easy.</p> -->
				<form method="post">
					<div>
						<input id="input_email" class="form_input input_email input_ph field_mail" type="email" name="email" placeholder="Email" required="required">
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
						<button id="login_submit" type="submit" class="red_button message_submit_btn trans_300">Login</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>