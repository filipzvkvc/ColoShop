<?php
	session_start();

	if(!isset($_SESSION['user'])){
		header('Location: https://coloshop.infinityfreeapp.com/index.php?page=login');
        exit;
	}
	else{
		if($_SESSION['user']->id_role != 1){
			header('Location: https://coloshop.infinityfreeapp.com/index.php?page=home');
            exit;
		}
	}


	require_once '../config/connection.php';
	require_once '../models/functions.php';
	require_once 'models/adminFunctions.php';

	$viewPages = [
		'dashboard' => 'views/pages/dashboard.php',
		'insertCategoryForm' => 'views/pages/insertCategoryForm.php',
		'insertProductForm' => 'views/pages/insertProductForm.php',
		'editProductForm' => 'views/pages/editProductForm.php',
		'updateProductForm' => 'views/pages/updateProductForm.php',
		'products' => 'views/pages/products.php',
		'users' => 'views/pages/users.php',
		'editUserForm' => 'views/pages/editUserForm.php',
		'orders' => 'views/pages/orders.php',
		'editOrderForm' => 'views/pages/editOrderForm.php',
		'newsletter' => 'views/pages/newsletter.php',
		'newsletterSubscribers' => 'views/pages/newsletterSubscribers.php',
		'editNewsletterForm' => 'views/pages/editNewsletterForm.php',
		'editCommentForm' => 'views/pages/editCommentForm.php'
	];

	$modelPages = [
		'logout' => '../models/logout.php',
		'deleteUser' => 'models/deleteUser.php',
		'deleteProduct' => 'models/deleteProduct.php',
		'deleteOrder' => 'models/deleteOrder.php',
		'deleteOrderItem' => 'models/deleteOrderItem.php',
		'deleteNewsletter' => 'models/deleteNewsletter.php',
        'deleteComment' => 'models/deleteComment.php'
	];

	$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

	if (array_key_exists($page, $modelPages)) {
		require_once $modelPages[$page];
	} 
	elseif (array_key_exists($page, $viewPages)) {
		require_once "views/fixed/head.php";
		require_once "views/fixed/header.php";
		require_once "views/fixed/adminSidebar.php";
		require_once $viewPages[$page];
		require_once 'views/fixed/footer.php';
	}
	else {
		require_once "views/pages/404.php";
	}
?>
