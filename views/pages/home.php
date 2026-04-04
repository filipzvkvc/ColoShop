<?php
	$genders = getAllFromTable('gender');
	$discount = getTopDiscount();

	if(isset($_SESSION['user'])){
		$user = $_SESSION['user'];
	}
?>
<div id="loading-overlay">
    <div class="spinner"></div>
</div>
		<!-- Slider -->
		<div class="main_slider" style="background-image:url(assets/images/slider_1.jpg)">
			<div class="container fill_height">
				<div class="row align-items-center fill_height">
					<div class="col">
						<div class="main_slider_content">
							<h6>Spring / Summer Collection 2017</h6>
							<h1>Get up to 30% Off New Arrivals</h1>
							<!-- <div class="red_button shop_now_button"><a href="index.php?page=shop">shop now</a></div> -->
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Banner -->

		<div class="banner">
			<div class="container">
				<!-- <div class="row"> -->
				<div class="center1">

					<?php foreach($genders as $jedan): ?>
						<div class="col-md-4">
							<div class="banner_item align-items-center" style="background-image:url(<?= $jedan->cover_photo ?>)">
								<div class="banner_category">
									<a href="index.php?page=shop&gender=<?= $jedan->id_gender?>"><?= $jedan->name ?></a>
								</div>
							</div>
						</div>
					<?php endforeach ?>
					
				</div>
			</div>
		</div>

		<!-- New Arrivals -->

		<div class="new_arrivals">
			<div class="container">
				<div class="row">
					<div class="col text-center">
						<div class="section_title new_arrivals_title">
							<h2>New Arrivals</h2>
						</div>
					</div>
				</div>
				<div class="row align-items-center">
					<div class="col text-center">
						<div class="new_arrivals_sorting">
							<ul class="arrivals_grid_sorting clearfix button-group filters-button-group">

								<li class="grid_sorting_button button d-flex flex-column justify-content-center align-items-center">all</li>
								<?php foreach($genders as $jedan):?>
									<li class="grid_sorting_button button d-flex flex-column justify-content-center align-items-center" id="<?=$jedan->id_gender?>"><?=$jedan->name?></li>
								<?php endforeach?>
							</ul>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<!-- <div class="product-grid"> -->
							
						<div class="product-grid" data-isotope='{ "itemSelector": ".product-item", "layoutMode": "fitRows" }'>

						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Deal of the week -->

		<div class="deal_ofthe_week">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-lg-6">
						<div class="deal_ofthe_week_img">
							<img src="assets/images/deal_ofthe_week.png" alt="">
						</div>
					</div>
					<div class="col-lg-6 text-right deal_ofthe_week_col">
						<div class="deal_ofthe_week_content d-flex flex-column align-items-center float-right">
							<div class="section_title">
								<h2>Deal Of The Week: <?=$discount->value?>% off</h2>
							</div>
							<div id='tajmer'>
								<ul class="timer">
									<li class="d-inline-flex flex-column justify-content-center align-items-center">
										<div id="day" class="timer_num">0</div>
										<div class="timer_unit">Day</div>
									</li>
									<li class="d-inline-flex flex-column justify-content-center align-items-center">
										<div id="hour" class="timer_num">0</div>
										<div class="timer_unit">Hours</div>
									</li>
									<li class="d-inline-flex flex-column justify-content-center align-items-center">
										<div id="minute" class="timer_num">0</div>
										<div class="timer_unit">Mins</div>
									</li>
									<li class="d-inline-flex flex-column justify-content-center align-items-center">
										<div id="second" class="timer_num">0</div>
										<div class="timer_unit">Sec</div>
									</li>
								</ul>
							</div>

							<!-- <div class="red_button deal_ofthe_week_button"><a href="index.php?page=shop">shop now</a></div> -->
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Best Sellers -->

		<div class="best_sellers">
			<div class="container">
				<div class="row">
					<div class="col text-center">
						<div class="section_title new_arrivals_title">
							<h2>Best Sellers</h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<div class="product_slider_container">
							<div class="owl-carousel owl-theme product_slider">
								<!-- Products will be dynamically inserted here -->
							</div>

							<!-- Slider Navigation -->
							<div class="product_slider_nav">
								<div class="product_slider_prev"><i class="fas fa-chevron-left"></i></div>
								<div class="product_slider_next"><i class="fas fa-chevron-right"></i></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


		<!-- Benefit -->

		<div class="benefit">
			<div class="container">
				<div class="row benefit_row">
					<div class="col-lg-3 benefit_col">
						<div class="benefit_item d-flex flex-row align-items-center">
							<div class="benefit_icon"><i class="fa fa-truck" aria-hidden="true"></i></div>
							<div class="benefit_content">
								<h6>free shipping</h6>
								<p>Suffered Alteration in Some Form</p>
							</div>
						</div>
					</div>
					<div class="col-lg-3 benefit_col">
						<div class="benefit_item d-flex flex-row align-items-center">
							<div class="benefit_icon"><i class="fa fa-money" aria-hidden="true"></i></div>
							<div class="benefit_content">
								<h6>cach on delivery</h6>
								<p>The Internet Tend To Repeat</p>
							</div>
						</div>
					</div>
					<div class="col-lg-3 benefit_col">
						<div class="benefit_item d-flex flex-row align-items-center">
							<div class="benefit_icon"><i class="fa fa-undo" aria-hidden="true"></i></div>
							<div class="benefit_content">
								<h6>45 days return</h6>
								<p>Making it Look Like Readable</p>
							</div>
						</div>
					</div>
					<div class="col-lg-3 benefit_col">
						<div class="benefit_item d-flex flex-row align-items-center">
							<div class="benefit_icon"><i class="fa fa-clock-o" aria-hidden="true"></i></div>
							<div class="benefit_content">
								<h6>opening all week</h6>
								<p>8AM - 09PM</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>