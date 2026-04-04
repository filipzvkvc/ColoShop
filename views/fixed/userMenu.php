<link rel="stylesheet" href="assets/styles/userMenu_styles.css">
<link rel="stylesheet" href="assets/styles/userMenu_responsive.css">

<div class="container product_section_container">
		<div class="row">
			<div class="col product_section clearfix">

				<!-- Breadcrumbs -->

				<div class="breadcrumbs d-flex flex-row align-items-center">
					<ul class="breadcrumbs_menu">
						<li><a href="index.php?page=home">Home</a></li>
						<li class="nav-item">
							<i class="fa fa-angle-right breadcrumb-arrow" aria-hidden="true"></i>
							<a class="nav_link" href="index.php?page=<?=get('page')?>&id=<?=get('id')?>">
								<span class="breadcrumb-text"><?= getBreadcrumbTitle(get('page')) ?></span>
							</a>
						</li>
					</ul>
				</div>

				<!-- Sidebar -->

				<div class="sidebar">
					<div class="sidebar_section">
						<div class="sidebar_title">
							<h5>Menu</h5>
						</div>

						<div class="nav flex-column">
							<a href="index.php?page=user&id=<?=get('id')?>" class="sidebar-link text-decoration-none p-3" id='user'>
								<i class="fa fa-user"></i>
								<span>Profile</span>
							</a>
							<a href="index.php?page=wishlist&id=<?=get('id')?>" class="sidebar-link text-decoration-none p-3" id='wishlist'>
								<i class="fa fa-heart"></i>
								<span>Wishlist</span>
							</a>
							<a href="index.php?page=cart&id=<?=get('id')?>" class="sidebar-link text-decoration-none p-3" id='cart'>
								<i class="fa fa-shopping-cart"></i>
								<span>Cart</span>
							</a>
							<a href="index.php?page=orders&id=<?=get('id')?>" class="sidebar-link text-decoration-none p-3" id='orders'>
								<i class="fa fa-box"></i>
								<span>Orders</span>
							</a>
							<a href="index.php?page=logout" class="sidebar-link text-decoration-none p-3 logoutButton" id='logout'>
								<i class="fa fa-sign-out"></i>
								<span>Logout</span>
							</a>
						</div>
					</div>
				</div>