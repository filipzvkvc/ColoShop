<?php
	$productId = get('id');
    $productBasicDetails = getProductBasicDetails($productId);

    $productImages = getProductImages($productId);
    $productColors = getProductColors($productId);
    $productSizes = getProductSizes($productId);
	$productPrice = getProductPrice($productId);
    $productDiscount = getProductDiscount($productId);
    $productCategory = getProductCategoryName($productId);

    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];
        
        $userProducts = selectWishlistedProducts($user->id_user);
    }
?>
<div id="loading-overlay">
    <div class="spinner"></div>
</div>
<div class="container single_product_container">
    <div class="row">
        <div class="col">
            <!-- Breadcrumbs -->
           <div class="breadcrumbs d-flex flex-row align-items-center">
                <ul class="breadcrumbs_menu">
                    <li><a href="index.php?page=home">Home</a></li>
                    <li class="nav-item">
                        <i class="fa fa-angle-right breadcrumb-arrow" aria-hidden="true"></i>
                        <a class="nav_link" href="index.php?page=single&id=<?= get('id') ?>">
                            <span class="breadcrumb-text">Single Product</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7">
            <div class="single_product_pics">
                <div class="row">
                    <div class="col-lg-3 thumbnails_col order-lg-1 order-2">
                        <div class="single_product_thumbnails">
                            <ul>
                                    <li><img src="<?= $productBasicDetails['cover'] ?>" alt="" data-image="<?= $productBasicDetails['cover'] ?>"></li>
                                <?php foreach ($productImages as $image): ?>
                                    <li><img src="<?= $image ?>" alt="" data-image="<?= $image ?>"></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-9 image_col order-lg-2 order-1">
                        <div class="single_product_image">
                            <div class="single_product_image_background" style="background-image:url(<?= $productBasicDetails['cover'] ?>)"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="product_details">
                <div class="product_details_title">
                    <h2><?= $productBasicDetails['name'] ?></h2>
                    <p><?= $productBasicDetails['description'] ?></p>
                </div>
                <!-- Other product details -->
                <!-- <div class="free_delivery d-flex flex-row align-items-center justify-content-center">
                    <span class="ti-truck"></span><span>free delivery</span>
                </div><br> -->
				<?php if ($productDiscount > 0): ?>
        			<div class="original_price">$<?= number_format($productPrice['oldPrice'], 2) ?></div>
   				<?php endif; ?>
				<div class="product_price">$<?= number_format($productPrice['newPrice'], 2) ?></div>
                <ul class="star_rating" id="product_main_rating">


                </ul>
                <div class="product_size">
                    <span>Size:</span>
                    <select id='product_size' class='input_ph'>
                        <option value='' disabled hidden selected>Select size:</option>
                        <?php foreach ($productSizes as $size): ?>
                            <option value='<?=$size['name']?>'><?=$size['name']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <span id="error_size" class="error-message"></span>

                <div class="custom-color-dropdown">
                    <span>Color:</span>
                    <div class="selected-color input_ph">
                        <span class="color-box" id="selected-box"></span>
                        <span class="selected-name">Select color:</span>
                    </div>
                    <ul class="color-options">
                        <?php foreach ($productColors as $color): ?>
                            <li data-color="<?= $color['name'] ?>">
                                <span class="color-box" style="background-color: <?= htmlspecialchars(strtolower($color['name'])) ?>;"></span>
                                <?= htmlspecialchars($color['name']) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <input type="hidden" name="product_color" id="product_color">
                <span id="error_color" class="error-message"></span>


                <div class="quantity d-flex flex-column flex-sm-row align-items-sm-center">
                    <!-- <span>Quantity:</span>
                    <div class="quantity_selector">
                        <span class="minus"><i class="fa fa-minus" aria-hidden="true"></i></span>
                        <span id="quantity_value">1</span>
                        <span class="plus"><i class="fa fa-plus" aria-hidden="true"></i></span>
                    </div> -->
                    <div class="red_button add_to_cart_button">
                        <a href="#" id='cart-button' class="add-cart-btn" data-id="<?=$productBasicDetails['id']?>" data-name="<?=$productBasicDetails['name']?>" data-original-price="<?=$productPrice['oldPrice']?>" data-discounted-price="<?=$productPrice['newPrice']?>" data-discount="<?=$productDiscount?>" data-image="<?=$productBasicDetails['cover']?>" data-category="<?=$productCategory?>">add to cart</a>
                    </div>

                    <?php if(isset($user)):?>
                        <a href="#" class="add-to-wishlist singleProductLink" data-product-id="<?= $productId ?>" data-wishlisted="<?= in_array($productId, $userProducts) ? 'true' : 'false' ?>">
                            <div class="product_favorite wishlist-icon d-flex flex-column align-items-center justify-content-center <?= in_array($productId, $userProducts) ? 'active' : '' ?>"></div>
                        </a>
                    <?php else:?>
                        <a class='singleProductLink' href="index.php?page=login"><div class="product_favorite d-flex flex-column align-items-center justify-content-center"></div></a>
                    <?php endif?>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Tabs -->
<div class="tabs_section_container">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="tabs_container">
                    <ul class="tabs d-flex flex-sm-row flex-column align-items-left align-items-md-center justify-content-center">
                        <li class="tab active" data-active-tab="tab_1"><span>Description</span></li>
                        <li class="tab" data-active-tab="tab_2"><span>Additional Information</span></li>
                        <li class="tab" data-active-tab="tab_3">
                        <span>Reviews
                        </span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <!-- Tab Description -->
                <div id="tab_1" class="tab_container active">
                    <div class="row">
                        <div class="col additional_info_col">
                            <div class="tab_title additional_info_title">
                                <h4>Description</h4>
                            </div>
                            <div>
                                <h2><?= $productBasicDetails['name'] ?></h2>
                                <p><?= $productBasicDetails['description'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tab Additional Info -->
                <div id="tab_2" class="tab_container">
                    <div class="row">
                        <div class="col additional_info_col">
                            <div class="tab_title additional_info_title">
                                <h4>Additional Information</h4>
                            </div>
                            <p>COLOR:<span><?= implode(', ', array_map(fn($c) => $c['name'], $productColors)) ?></span></p>
                            <p>SIZE:<span><?= implode(', ', array_map(fn($s) => $s['name'], $productSizes)) ?></span></p>
                        </div>
                    </div>
                </div>
                <!-- Tab Reviews -->
                <div id="tab_3" class="tab_container">
                    <div class="row">

                        <div class="col-lg-6 reviews_col">

                            <div class="tab_title reviews_title">
                                <h4>Reviews</h4>
                            </div>
                            <!-- User Reviews -->

                            <div id="noReviews">

                            </div>
                        </div>

                        <div class="col-lg-6 add_review_col">
                            <div class="add_review">
                                <form id="review_form" method="post">
                                    <div>
                                        <h1>Add Review</h1>

                                        <input id="input_name" class="form_input input_name input_ph" type="text" name="name" placeholder="Name" required="required">
            							<span id="error_name" class="error-message"></span>

                                        <input id="input_email" class="form_input input_email input_ph" type="email" name="email" placeholder="Email" required="required">
							            <span id="error_email" class="error-message"></span>
                                    </div>
                                    <div>
                                        <h1>Your Rating:</h1>
                                        <ul id='ratingList' class="user_star_rating">
                                            <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                            <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                            <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                            <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                            <li><i class="fa fa-star-o" aria-hidden="true"></i></li>
                                        </ul>
                                        <textarea id="input_review" class="input_review input_ph" name="review"  placeholder="Your Review" rows="4"></textarea>
                                        <span id="error_review" class="error-message"></span>

                                        <span id="error_check_review" class="error-message"></span>
                                    </div>
                                    <div class="text-left text-sm-right">
                                        <button id="single_review_submit" type="submit" class="red_button review_submit_btn trans_300" value="Submit">submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
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


<div id="confirmModal" class="custom-modal">
    <div class="modal-content">
        <p id="confirmMessage"></p>
        <div class="modal-buttons">
            <button id="confirmYes" class="modal-btn yes-btn">Yes</button>
            <button id="confirmNo" class="modal-btn no-btn">No</button>
        </div>
    </div>
</div>