<?php
    session_start();
    require_once "../config/connection.php";
    require_once "../config/config.php";
    require_once "functions.php";

    $data = json_decode(file_get_contents('php://input'), true);

    $checkout_data = $_SESSION['checkout_data'];

    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];

        $id_cart = getOrCreateCartId($user->id_user);

        $products = getCartProductsByCartId($id_cart);
    }
    else{
        $user = null;
    }

    $paypalOrderId = $data['paypalOrderId'];
    $transactionId = $data['transactionId'];
    $cart = $data['cart'];

    $clientId = PAYPAL_CLIENT_ID;
    $secret = PAYPAL_SECRET;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/$paypalOrderId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$secret");

    $response = curl_exec($ch);
    curl_close($ch);

    $paypalOrder = json_decode($response, true);

    if ($paypalOrder['status'] !== 'COMPLETED') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Payment not completed']);
        exit;
    }

    insertOrder($checkout_data['user'], $checkout_data['email'], $checkout_data['firstName'], $checkout_data['lastName'], $checkout_data['streetName'], $checkout_data['streetNumber'], $checkout_data['city'], $checkout_data['phoneNumber'], $checkout_data['selectedShipping'], $paypalOrderId, $transactionId);

    $lastOrderId = getLastOrder();

    if($user){
        foreach($products as $p){
            insertOrderItems($lastOrderId->id_order, $p['id'], $p['quantity'], $p['size'], $p['color']);
        }
        clearUserCart($id_cart);
    }
    else{
        foreach($cart as $c){
            insertOrderItems($lastOrderId->id_order, $c['id'], $c['quantity'], $c['size'], $c['color']);
        }
    }

    echo json_encode([
        "success" => true,
        "message" => messageText('orderSubmitSuccess')
    ]);

    unset($_SESSION['checkout_data']);
    unset($_SESSION['guest_cart']);
?>