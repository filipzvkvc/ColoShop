<?php
    $navigation = getAllFromTable('navigation');
	$socialNetwork = getAllFromTable('social_network');
?>
<footer class="footer">
		<div class="newsletter">
			<div class="container">
				<div class="row">
					<div class="col-lg-6">
						<div class="newsletter_text d-flex flex-column justify-content-center align-items-lg-start align-items-md-center text-center">
							<h4>Newsletter</h4>
							<p>Subscribe to our newsletter and stay updated on our latest offers.</p>
						</div>
					</div>
					<div class="col-lg-6">
						<form action="post">
							<div class="newsletter_form d-flex flex-md-row flex-column flex-xs-column align-items-center justify-content-lg-end justify-content-center">
								<input id="newsletter_email" class="form_input input_email input_ph" type="email" placeholder="Your email" required="required">
								<button id="newsletter_submit" type="submit" class="newsletter_submit_btn trans_300" value="Submit">subscribe</button>
							</div>
							<span id="error_newsletter_email" class="error-message"></span>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-lg-6">
					<div class="footer_nav_container d-flex flex-sm-row flex-column align-items-center justify-content-lg-start justify-content-center text-center">
					<ul class="footer_nav">
                            <?php foreach($navigation as $jedan):?>
                                <li class="nav-item">
                                    <a class="nav_link" href='index.php?page=<?= $jedan->href?>'><?= $jedan->text?></a>
                                </li>
                            <?php endforeach?>
                        </ul>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="footer_social d-flex flex-row align-items-center justify-content-lg-end justify-content-center">
						<ul>
							<?php foreach($socialNetwork as $jedan):?>
								<li>
									<a href="<?= $jedan->href?>">
										<i class="<?=$jedan->icon?>" aria-hidden="true"></i>
									</a>
								</li>
							<?php endforeach?>
						</ul>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="footer_nav_container">
						<div class="cr"><span id="currentYear"></span> All Rights Reserverd. Made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="#">Colorlib</a> &amp; distributed by <a href="https://themewagon.com">ThemeWagon</a></div>
					</div>
				</div>
			</div>
		</div>
	</footer>
</div>
        <script src="assets/js/jquery-3.2.1.min.js"></script>
        <script src="assets/styles/bootstrap4/popper.js"></script>
        <script src="assets/styles/bootstrap4/bootstrap.min.js"></script>
        <script src="assets/plugins/Isotope/isotope.pkgd.min.js"></script>
        <script src="assets/plugins/OwlCarousel2-2.2.1/owl.carousel.js"></script>
        <script src="assets/plugins/easing/easing.js"></script>
		<script src="assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.js"></script>
        
        <script src="assets/js/main.js"></script>


	<?php
		$current_page = isset($_GET['page']) ? $_GET['page'] : 'home';

		if ($current_page === 'home') {
			echo '<script src="assets/js/custom.js"></script>';
		} elseif ($current_page === 'contact') {
			echo '<script src="assets/js/contact_custom.js"></script>';
		} elseif ($current_page === 'shop') {
			echo '<script src="assets/js/categories_custom.js"></script>';
		} elseif ($current_page === 'single') {
			echo '<script src="assets/js/single_custom.js"></script>';
		} elseif ($current_page === 'register') {
			echo '<script src="assets/js/register_custom.js"></script>';
		} elseif ($current_page === 'login') {
			echo '<script src="assets/js/login_custom.js"></script>';
		} elseif ($current_page === 'wishlist') {
			echo '<script src="assets/js/wishlist_custom.js"></script>';
		} elseif ($current_page === 'user') {
			echo '<script src="assets/js/user_custom.js"></script>';
		} elseif ($current_page === 'orders') {
			echo '<script src="assets/js/orders_custom.js"></script>';
		} elseif ($current_page === 'orderInfo') {
			echo '<script src="assets/js/order_info_custom.js"></script>';
		}
		
	?>

</body>
</html>