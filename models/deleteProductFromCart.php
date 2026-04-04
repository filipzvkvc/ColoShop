<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    session_start();
    header('Content-Type: application/json; charset=utf-8');

    if(!isset($_SESSION['user'])){
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }

    $user = $_SESSION['user'];
    $id_cart = getOrCreateCartId($user->id_user);

    $id_product = post('id_product');
    $size = post('size');
    $color = post('color');

    if(deleteProductFromCart($id_cart, $id_product, $size, $color)){
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete item']);
    }

    exit;
?>