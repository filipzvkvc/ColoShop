<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    session_start();

    header('Content-Type: application/json; charset=utf-8');

    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];
    }

    $id_cart = getOrCreateCartId($user->id_user);

    $products = getCartProductsByCartId($id_cart);

    echo json_encode([
        'products' => $products
    ]);

    exit;
?>