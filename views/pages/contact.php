<?php
	$social_network = getAllFromTable('social_network');
?>
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
							<a class="nav_link" href="index.php?page=contact">
								<span class="breadcrumb-text">Contact</span>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<!-- Map Container -->

		<div class="row">
			<div class="col">
				<div id="google_map">
					<div class="map_container">
						<div id="map">
							<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d24846.31346651262!2d-77.01627859556417!3d38.88306488255116!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89b7b77f50ae6f01%3A0x676e7adc4032899b!2sNASA!5e0!3m2!1sen!2srs!4v1735580737694!5m2!1sen!2srs" width="1110" height="507" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Contact Us -->

		<div class="row">

			<div class="col-lg-6 contact_col">
				<div class="contact_contents">
					<h1>Contact Us</h1>
					<p>There are many ways to contact us. You may drop us a line, give us a call or send an email, choose what suits you the most.</p>
					<div>
						<p>123-456-789</p>
						<p>coloshop732@gmail.com</p>
					</div>
					<div>
						<p>Open hours:</p>
						<p>Mon-Fri: 8.00-18.00</p>
						<p>Sat: 8.00-14.00</p>
						<p>Sunday: Closed</p>
					</div>
				</div>

				<!-- Follow Us -->

				<div class="follow_us_contents">
					<h1>Follow Us</h1>
					<ul class="social d-flex flex-row">
						<?php foreach($social_network as $jedan):?>
							<li><a href="<?=$jedan->href?>" style="background-color: <?=$jedan->color?>"><i class="<?=$jedan->icon?>" aria-hidden="true"></i></a></li>
						<?php endforeach?>
					</ul>
				</div>

			</div>

			<div class="col-lg-6 get_in_touch_col">
				<div class="get_in_touch_contents">
					<h1>Get In Touch With Us!</h1>
					<p id='formtext'>Fill out the form below to recieve a free and confidential.</p>
					<form method="post">
						<div>
							<input id="input_name" class="form_input input_name input_ph" type="text" name="name" placeholder="Name" required="required">
							<span id="error_name" class="error-message"></span>

							<input id="input_email" class="form_input input_email input_ph" type="email" name="email" placeholder="Email" required="required">
							<span id="error_email" class="error-message"></span>

							<input id="input_subject" class="form_input input_subject input_ph" type="text" name="subject" placeholder="Subject" required="required">
							<span id="error_subject" class="error-message"></span>

							<textarea id="input_message" class="input_ph input_message" name="message" placeholder="Message" rows="3" required></textarea>
							<span id="error_message" class="error-message"></span>
						</div>
						<div>
							<button id="review_submit" type="submit" class="red_button message_submit_btn trans_300" value="Submit">Send Message</button>
						</div>
					</form>
				</div>
			</div>
			
		</div>
	</div>