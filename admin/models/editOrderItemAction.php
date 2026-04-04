<?php
    session_start();
    require_once '../../config/connection.php';
    require_once 'adminFunctions.php';
    require_once '../../models/functions.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $orderId = post('id_order');

        $orderItemId = post('id_order_item');
        $orderItemInformation = getOrderItemInformation($orderItemId);

        $quantity = post('quantity');

        $id_size = (int)post('id_size');
        $id_color = (int)post('id_color');

        if (!isset($_SESSION['edit_order_form_data'])) {
            $_SESSION['edit_order_form_data'] = new stdClass();
        }

        if ($quantity === '') {
            deleteOrderItem($orderItemId);

            unset($_SESSION['edit_order_form_data']->{$orderItemId});

            $orderItemCount = getOrderItemCount($orderId);

            if($orderItemCount == 0){
                setFlash('success', messageText('orderDeleteSuccess'));
                deleteOrder($orderId);
                header("Location: ../index.php?page=orders");
                exit;
            }
        } 
        elseif (!is_numeric($quantity)) {
            setFlash('quantity', 'Quantity must be a whole number (no decimals)');

            $_SESSION['edit_order_form_data']->{$orderItemId} = $quantity;
        } 
        elseif (!testFields('quantity', $quantity)) {
            setFlash('quantity', 'Quantity must be a whole number (no decimals)');
            $_SESSION['edit_order_form_data']->{$orderItemId} = $quantity;
        }
        elseif ($quantity < 0) {
            setFlash('quantity', 'Quantity cannot be negative');
            $_SESSION['edit_order_form_data']->{$orderItemId} = $quantity;
        }
        elseif ($quantity == 0) {
            deleteOrderItem($orderItemId);

            unset($_SESSION['edit_order_form_data']->{$orderItemId});

            $orderItemCount = getOrderItemCount($orderId);

            if($orderItemCount == 0){
                setFlash('success', messageText('orderDeleteSuccess'));
                deleteOrder($orderId);
                header("Location: ../index.php?page=orders");
                exit;
            }
        }

        if (hasFlash('quantity')) {

            setFlash('error2', messageText('orderItemUpdateError'));

            header("Location: ../index.php?page=editOrderForm&id=$orderId");
            exit;
        }

        $success = updateOrderItem($orderItemId, $quantity, $id_size, $id_color);

        if($success){
            setFlash('success2', messageText('orderItemUpdateSuccess'));

            unset($_SESSION['edit_order_form_data']->{$orderItemId});
        }
        else{
            setFlash('error2', messageText('orderItemUpdateError'));
        }

        header("Location: ../index.php?page=editOrderForm&id=$orderId");
        exit;

    }
?>