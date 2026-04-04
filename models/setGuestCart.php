<?php
    session_start();

    $data = json_decode(file_get_contents("php://input"), true);
    $cart = $data['cart'] ?? [];

    if (empty($cart)) {
        unset($_SESSION['guest_cart']);
    } else {
        $_SESSION['guest_cart'] = $cart;
    }

    echo json_encode([
        "success" => true
    ]);
?>