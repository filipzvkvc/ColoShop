<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    session_start();

    header('Content-Type: application/json; charset=utf-8');

    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];
    }

    $currentPage = isset($_POST['page']) ? (int)$_POST['page'] : 1;

    $itemsPerPage = 8;
    $offset = ($currentPage - 1) * $itemsPerPage;

    $products = getWishlistProducts($user->id_user, $itemsPerPage, $offset);
    $totalProducts = getWishlistProductsCount($user->id_user);
    $totalPages = ceil($totalProducts / $itemsPerPage);

    echo json_encode([
        'products' => $products,
        "totalPages" => $totalPages,
        "totalProducts" => $totalProducts,
        "currentPage" => $currentPage
    ]);

    exit;
?>