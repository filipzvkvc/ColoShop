<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $orderId = post('orderId');

    $orderItems = getOrderItems($orderId);

    $sizes = getAllFromTable('size');
    $colors = getAllFromTable('color');


    foreach($orderItems as $item){
        foreach($sizes as $s){
            if($s->id_size == $item->id_size){
                $item->size = $s->name;
            }
        }

        foreach($colors as $c){
            if($c->id_color == $item->id_color){
                $item->color = $c->name;
            }
        }

        $item->cover_photo = getProductInformation($item->id_product)->cover_photo;
        $item->name = getProductInformation($item->id_product)->name;
        $item->category = getProductInformation($item->id_product)->categoryName;
        $item->originalPrice = getProductPrice($item->id_product)['oldPrice'];
        $item->discountedPrice = getProductPrice($item->id_product)['newPrice'];
        $item->discount = getProductInformation($item->id_product)->discountValue;
    }

    echo json_encode([
        'orderItems' => $orderItems
    ]);

    exit;
?>