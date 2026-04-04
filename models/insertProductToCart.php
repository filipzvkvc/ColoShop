<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    session_start();

    header('Content-Type: application/json; charset=utf-8');

    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];
    }

    $id_cart = getOrCreateCartId($user->id_user);

    $id_product = post('id_product');
    $quantity = post('quantity');
    $id_size = post('id_size');
    $id_color = post('id_color');

    if(insertOrUpdateCartItem($id_cart, $id_product, $quantity, $id_size, $id_color)){
        echo json_encode(['success' => true, 'message' => 'Inserted cart item succesfully']);
        //exit;
    }

    exit;
?>