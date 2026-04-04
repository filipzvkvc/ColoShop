<?php
    session_start();
    require_once "../config/config.php";
    require_once "../config/connection.php";

    $data = json_decode(file_get_contents('php://input'), true);
    $paypalOrderId = $data['paypalOrderId'];

    $clientId = PAYPAL_CLIENT_ID;
    $secret = PAYPAL_SECRET;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/$paypalOrderId/capture");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$secret");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode === 201 && $result['status'] === 'COMPLETED') {

        $transactionId = $result['purchase_units'][0]['payments']['captures'][0]['id'];

        echo json_encode([
            'success' => true,
            'captureId' => $result['id'],
            'transactionId' => $transactionId
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Payment capture failed'
        ]);
    }
?>