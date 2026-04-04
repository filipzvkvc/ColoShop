<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    session_start();

    header('Content-Type: application/json; charset=utf-8');

    $userData = [
        "id_user" => null,
        "first_name" => null,
        "last_name" => null,
        "email" => null,
        "profile_picture" => ""
    ];

    $products = null;

    if (isset($_SESSION['user']) && $_SESSION['user'] !== null) {
        $user = $_SESSION['user'];

        $userData = [
            "id_user" => $user->id_user ?? null,
            "first_name" => $user->first_name ?? null,
            "last_name" => $user->last_name ?? null,
            "email" => $user->email ?? null,
            "profile_picture" => $user->profile_picture ?? ''
        ];

        $products = selectWishlistedProducts($user->id_user);
    }

    echo json_encode([
        "user" => $userData,
        "products" => $products
    ]);

    exit;
?>
