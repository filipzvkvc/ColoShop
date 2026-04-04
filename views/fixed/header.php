<?php
    $navigation = getAllFromTable('navigation');

    if(isset($_SESSION['user'])){
		$user = $_SESSION['user'];
	}
?>
<header class="header trans_300">

    <!-- Top Navigation -->

    <div class="top_nav">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="top_nav_left"></div>
                </div>
                <div class="col-md-6 text-right">
                    <div class="top_nav_right">
                        <ul class="top_nav_menu">
                        <li class="account">
                            <?php if (isset($user)) : ?>
                                <a href="index.php?page=user&id=<?= $user->id_user ?>">
                                    <span class="user-greeting">Hi <?= htmlspecialchars($user->first_name) ?>!</span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="account_selection">
                                    <li><a href="index.php?page=user&id=<?=$user->id_user?>"><i class="fa fa-user" aria-hidden="true"></i> Profile</a></li>
                                    <li><a href="index.php?page=wishlist&id=<?=$user->id_user?>"><i class="fa fa-heart" aria-hidden="true"></i> Wishlist</a></li>
                                    <li><a href="index.php?page=logout"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
                                </ul>
                            <?php else: ?>
                                <a href="#">
                                    My Account
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="account_selection">
                                    <li><a href="index.php?page=login"><i class="fa fa-sign-in" aria-hidden="true"></i> Sign In</a></li>
                                    <li><a href="index.php?page=register"><i class="fa fa-user-plus" aria-hidden="true"></i> Register</a></li>
                                </ul>
                            <?php endif; ?>
                        </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->

    <div class="main_nav_container">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-right">
                    <div class="logo_container">
                        <a href="index.php?page=home">colo<span>shop</span></a>
                    </div>
                    <nav class="navbar">
                        <ul class="navbar_menu">
                            <?php foreach($navigation as $jedan):?>
                                <li class="nav-item">
                                    <a class="nav_link" id="<?= $jedan->id_navigation?>" href='index.php?page=<?= $jedan->href?>'><?= $jedan->text?></a>
                                </li>
                            <?php endforeach?>
                        </ul>
                        <ul class="navbar_user">
                            <?php if (isset($user)) : ?>
                                <li><a href="index.php?page=user&id=<?=$user->id_user?>"><img id="profile_picture" class="profile-pic" src="<?= $user->profile_picture?>"/></a></li>
                            <?php else: ?>
                                <li><a href="index.php?page=login"><i class="fa fa-user" aria-hidden="true"></i></a></li>
                            <?php endif; ?>
                            <li class="checkout">
                                <a href="index.php?page=cart">
                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                    <span id="checkout_items" class="checkout_items" hidden></span>
                                </a>
                            </li>
                        </ul>
                        <div class="hamburger_container">
                            <i class="fa fa-bars" aria-hidden="true"></i>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="fs_menu_overlay"></div>
		<div class="hamburger_menu">
			<div class="hamburger_close"><i class="fa fa-times" aria-hidden="true"></i></div>
			<div class="hamburger_menu_content text-right">
				<ul class="menu_top_nav">
					<li class="menu_item has-children">
                        <?php if (isset($user)) : ?>
                            <a href="#">
                                <span class="user-greeting">Hi <?= htmlspecialchars($user->first_name) ?>!</span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="menu_selection">
                                <li><a href="index.php?page=user&id=<?=$user->id_user?>"><i class="fa fa-user" aria-hidden="true"></i> Profile</a></li>
                                <li><a href="index.php?page=wishlist&id=<?=$user->id_user?>"><i class="fa fa-heart" aria-hidden="true"></i> Wishlist</a></li>
                                <li><a href="index.php?page=logout"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
                            </ul>
                        <?php else: ?>
                            <a href="#">
                                My Account
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="menu_selection">
                                <li><a href="index.php?page=login"><i class="fa fa-sign-in" aria-hidden="true"></i> Sign In</a></li>
                                <li><a href="index.php?page=register"><i class="fa fa-user-plus" aria-hidden="true"></i> Register</a></li>
                            </ul>
                        <?php endif; ?>
					</li>
                    <?php foreach($navigation as $jedan):?>
                            <li class="menu_item">
                                <a href='index.php?page=<?= $jedan->href?>'><?= $jedan->text?></a>
                            </li>
                    <?php endforeach?>
				</ul>
			</div>
		</div>
</header>