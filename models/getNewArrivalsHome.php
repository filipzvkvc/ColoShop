<?php
    require_once "../config/connection.php";
    require_once "functions.php";
    
    header('Content-Type: application/json; charset=utf-8');

    $id_gender = post('gender');

    $products = getNewArrivals($id_gender);

    echo json_encode([
        'products' => $products
    ]);

    exit;
?>