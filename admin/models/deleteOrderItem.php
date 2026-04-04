<?php

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $orderItemId = get('orderItemId');
        $orderId = get('orderId');

        $deleteSuccess = deleteOrderItem($orderItemId);

        $orderItemCount = getOrderItemCount($orderId);

        if ($deleteSuccess) {

            if($orderItemCount == 0){
                setFlash('success', messageText('orderDeleteSuccess'));
                deleteOrder($orderId);
                header("Location: index.php?page=orders");
                exit;
            }
            setFlash('success2', messageText('orderItemDeleteSuccess'));
        } else {
            setFlash('error2', messageText('orderItemDeleteError'));
        }

        header("Location: index.php?page=editOrderForm&id=$orderId");
        exit;
    }
    
?>