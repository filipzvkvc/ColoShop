<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    session_start();

    header('Content-Type: application/json; charset=utf-8');

    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];
    }

    $currentPage = isset($_POST['page']) ? (int)$_POST['page'] : 1;

    $itemsPerPage = 8;
    $offset = ($currentPage - 1) * $itemsPerPage;

    $orders = getUserOrders($user->id_user, $itemsPerPage, $offset);
    $totalOrders = getUserOrdersCount($user->id_user);
    $totalPages = ceil($totalOrders / $itemsPerPage);

    foreach($orders as $order){
        $order->totalPrice = getOrderTotalPrice($order->orderId);
        $order->statusClass = getOrderStatusClass($order->orderStatusName);
    }

    echo json_encode([
        'orders' => $orders,
        "totalPages" => $totalPages,
        "totalOrders" => $totalOrders,
        "currentPage" => $currentPage
    ]);

    exit;
?>