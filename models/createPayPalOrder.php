<?php
    session_start();
    require_once "../config/config.php";
    require_once "../config/connection.php";
    require_once "functions.php";

    $data = json_decode(file_get_contents('php://input'), true);

    $checkout_data = $_SESSION['checkout_data'];

    $cart = $data['cart'] ?? null;

    $cartTotal = calculateOrderTotal($checkout_data['selectedShipping'], $cart);

    if ($cartTotal <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid cart total']);
        exit;
    }

    $clientId = PAYPAL_CLIENT_ID;
    $secret = PAYPAL_SECRET;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$secret");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'amount' => [
                'currency_code' => 'USD',
                'value' => number_format($cartTotal, 2, '.', '')
            ]
        ]]
    ]));

    $response = curl_exec($ch);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode === 201 && isset($result['id'])) {
        echo json_encode(['id' => $result['id']]);
    } else {
        http_response_code(400);
        echo json_encode([
            'error' => 'Failed to create PayPal order',
            'details' => $result
        ]);
    }
?>