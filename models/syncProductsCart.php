<?php
    session_start();
    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }

    $id_user = $data['id_user'];
    $cartItems = $data['cart'];

    $id_cart = getOrCreateCartId($id_user);

    foreach ($cartItems as $item) {
        $success = insertOrUpdateCartItem(
            $id_cart,
            $item['id'],
            $item['quantity'],
            $item['size'] ?? null,
            $item['color'] ?? null
        );
    }

    echo json_encode(['success' => true]);
?>