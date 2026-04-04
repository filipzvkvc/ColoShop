<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $products = getBestSellers();

    echo json_encode([
        'products' => $products
    ]);

    exit;
?>
