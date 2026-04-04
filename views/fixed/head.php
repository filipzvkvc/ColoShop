<!DOCTYPE html>
<html lang="en">
<head>
    <title>Colo Shop</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Colo Shop Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/plugins/themify-icons/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="assets/styles/bootstrap4/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/plugins/OwlCarousel2-2.2.1/owl.carousel.css">
    <link rel="stylesheet" type="text/css" href="assets/plugins/OwlCarousel2-2.2.1/owl.theme.default.css">
    <link rel="stylesheet" type="text/css" href="assets/plugins/OwlCarousel2-2.2.1/animate.css">
    <link rel="stylesheet" type="text/css" href="assets/styles/styles.css">
    <link rel="stylesheet" type="text/css" href="assets/styles/styles_responsive.css">
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">

<?php
    $current_page = isset($_GET['page']) ? $_GET['page'] : 'home';

    if ($current_page === 'home') {
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/main_styles.css">';
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/responsive.css">';
    } elseif ($current_page === 'contact') {
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/contact_styles.css">';
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/contact_responsive.css">';
    } elseif ($current_page === 'shop') {
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/categories_styles.css">';
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/categories_responsive.css">';
    } elseif ($current_page === 'single') {
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/single_styles.css">';
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/single_responsive.css">';
    }
    elseif ($current_page === 'register') {
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/register_styles.css">';
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/register_responsive.css">';
    }
    elseif ($current_page === 'login') {
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/login_styles.css">';
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/login_responsive.css">';
    }
    elseif($current_page === 'wishlist'){
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/wishlist_styles.css">';
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/wishlist_responsive.css">';
    }
    elseif($current_page === 'user'){
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/user_styles.css">';
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/user_responsive.css">';
    }
    elseif($current_page === 'cart'){
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/cart_styles.css">';
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/cart_responsive.css">';
    }
    elseif($current_page === 'checkout'){
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/checkout.css">';
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/checkout_responsive.css">';
    }
    elseif($current_page === 'orders'){
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/orders_styles.css">';
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/orders_responsive.css">';
    }
    elseif($current_page === 'orderInfo'){
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/order_info_styles.css">';
        echo '<link rel="stylesheet" type="text/css" href="assets/styles/order_info_responsive.css">';
    }
?>

</head>
<body>
<div class="super_container">