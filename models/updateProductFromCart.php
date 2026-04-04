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
    $size = post('size') ?? null;
    $color = post('color') ?? null;
    $type = post('type');


    $currentQty = getProductCartQuantity($id_cart, $id_product, $size, $color);

    if($type == 'increase'){
        $newQty = $currentQty + 1;
    }

    if($type == 'decrease'){
        $newQty = max(1, $currentQty - 1);
    }

    if(updateProductFromCart($id_cart, $id_product, $newQty, $size, $color)){
        echo json_encode(['success' => true, 'message' => 'Cart successfully updated!']);
    }


    exit;
?>