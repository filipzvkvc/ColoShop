<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    session_start();

    header('Content-Type: application/json; charset=utf-8');

    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];
    }

    $id_product = htmlspecialchars(post('id_product'));
    
    if(deleteProductFromWishlist($user->id_user, $id_product))
    {
        echo json_encode([
            "success" => true,
            "message" => messageText('productWishlistRemovedSuccess')
        ]);
    }
    else
    {
        echo json_encode([
            "success" => false,
            "message" => messageText('productWishlistRemovedError')
        ]);
    }

    exit;
?>