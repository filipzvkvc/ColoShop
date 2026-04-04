<?php
	session_start();

	require_once 'config/connection.php';
	require_once 'models/functions.php';

	$plainPages = ['newsletterUnsubscribeConfirm'];

	$viewPages = ['home', 'shop', 'contact', 'user', 'cart', 'checkout', 'login', 'register', 'author', 'single', 'wishlist', 'orders', 'orderInfo'];

	$modelPages = ['logout', 'verify', 'verifySubscription', 'unsubscribeNewsletter', 'unsubscribeNewsletterCheck'];

	$page = get('page') ?? 'home';

	// $page = strtolower($page);

	$logData = '[PAGE_VISIT]' . "\t" . 'PAGE: ' . $page . "\t" . 'IP: ' . $_SERVER['REMOTE_ADDR'] . "\t" . 'TIME: ' . date("Y-m-d H:i:s") . PHP_EOL;

	logFile($logData);

	if (in_array($page, $modelPages)) 
	{
		require_once "models/$page.php";
	}
	elseif (in_array($page, $plainPages)) {
		require_once "views/pages/$page.php";
	}
	elseif (in_array($page, $viewPages)) 
	{
		if (isset($_SESSION['user']) && ($page === 'login' || $page === 'register')) {
			header("Location: index.php?page=home");
			exit;
		}

		if ($page == 'single') {
			$productId = get('id');
			$productBasicDetails = getProductBasicDetails($productId);
			if (!$productBasicDetails || $productBasicDetails['is_active'] == 0) {
				header("Location: index.php?page=404");
				exit;
			}
		}

		if ($page == 'checkout') {
			if (isset($_SESSION['user'])) {

				$id_cart = getOrCreateCartId($_SESSION['user']->id_user);
				$products = getCartProductsByCartId($id_cart);

				if (!$products || count($products) === 0) {
					header("Location: index.php?page=home");
					exit;
				}
			}
			else {
				if (empty($_SESSION['guest_cart'])) {
					header("Location: index.php?page=home");
					exit;
				}
			}
		}

		require_once "views/fixed/head.php";
		require_once "views/fixed/header.php";

		if($page == 'user' || $page == 'wishlist' || $page == 'orders'){
			require_once "views/fixed/userMenu.php";
		}
		
		require_once "views/pages/$page.php";
		require_once 'views/fixed/footer.php';
	} 
	else 
	{
		require_once "views/pages/404.php";
	}
?>