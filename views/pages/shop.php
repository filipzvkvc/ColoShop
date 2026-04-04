<?php
	$categories = getAllFromTable('categories');
	$sizes = getAllFromTable('size');
	$colors = getAllFromTable('color');
	$gender = getAllFromTable('gender');
?>
<div id="loading-overlay">
    <div class="spinner"></div>
</div>
<div class="container product_section_container">
		<div class="row">
			<div class="col product_section clearfix">

				<!-- Breadcrumbs -->

				<div class="breadcrumbs d-flex flex-row align-items-center">
					<ul class="breadcrumbs_menu">
						<li><a href="index.php?page=home">Home</a></li>
						<li class="nav-item">
							<i class="fa fa-angle-right breadcrumb-arrow" aria-hidden="true"></i>
							<a class="nav_link" href="index.php?page=shop&gender=<?=get('gender')?>">
								<span class="breadcrumb-text">Shop</span>
							</a>
						</li>
					</ul>
				</div>

				<!-- Sidebar -->

				<div class="sidebar">


					<div class="sidebar_section">
						<div class="sidebar_title">
							<h5>Product Category</h5>
						</div>
						<ul class="checkboxes" id="categories">
							<?php foreach($categories as $jedna):?>
								<li><i class="fa fa-square-o" aria-hidden="true" id=<?=$jedna->id_categories?>></i><span><?=$jedna->name?></span></li>
							<?php endforeach?>
						</ul>
					</div>

					<!-- Price Range Filtering -->
					<div class="sidebar_section">
						<div class="sidebar_title">
							<h5>Filter by Price</h5>
						</div>
						<p>
							<input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;">
							<div style="margin-top:10px; display:flex; gap:10px;">
								<input type="number" id="priceMinInput" placeholder="Min" min="1" max="2000" class="price-input">
								<input type="number" id="priceMaxInput" placeholder="Max" min="1" max="2000" class="price-input">
							</div>

						</p>
						<div id="slider-range"></div>
						<div class="filter_button"><span>filter</span></div>
					</div>

					<!-- Sizes -->
					<div class="sidebar_section">
						<div class="sidebar_title">
							<h5>Sizes</h5>
						</div>
						<ul class="checkboxes" id="size">
							<?php foreach($sizes as $jedan):?>
								<li><i class="fa fa-square-o" aria-hidden="true" id=<?=$jedan->id_size?>></i><span><?=$jedan->name?></span></li>
							<?php endforeach?>
						</ul>
					</div>

					<!-- Color -->
					<div class="sidebar_section">
						<div class="sidebar_title">
							<h5>Color</h5>
						</div>
						<ul class="checkboxes" id="color">
							<?php foreach($colors as $jedan):?>
								<li><i class="fa fa-square-o" aria-hidden="true" id=<?=$jedan->id_color?>></i><span><?= $jedan->name?></span></li>
							<?php endforeach?>
						</ul>
					</div>

				</div>

				<!-- Main Content -->

				<div class="main_content">

					<!-- Products -->

					<div class="products_iso">
						<div class="row">
							<div class="col">

								<!-- Product Sorting -->

								<div class="product_sorting_container product_sorting_container_top">
									<ul class="product_sorting">
										<li>
											<span class="type_sorting_text">Default Sorting</span>
											<i class="fa fa-angle-down"></i>
											<ul class="sorting_type">
												<li class="type_sorting_btn" data-value="default"><span>Default Sorting</span></li>
												<li class="type_sorting_btn" data-value="price_asc"><span>Price ASC</span></li>
												<li class="type_sorting_btn" data-value="price_desc"><span>Price DESC</span></li>
												<li class="type_sorting_btn" data-value="name_asc"><span>Product Name ASC</span></li>
												<li class="type_sorting_btn" data-value="name_desc"><span>Product Name DESC</span></li>
												<!-- <li class="type_sorting_btn" data-value="rating"><span>Rating</span></li> -->
												<li class="type_sorting_btn" data-value="discount"><span>Discount</span></li>
									</ul>
										</li>
									</ul>

								</div>

								<!-- Product Grid -->

								<div class="product-grid" id="products-shop">
									
								</div>

								<!-- Product Sorting -->

								<div class="product_sorting_container product_sorting_container_bottom clearfix">
									<span class='showing_results' id="showing_results_shop"></span>

									<div class='pagination' id="pagination_shop">

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>