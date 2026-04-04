<?php

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $orderId = get('id');

        $deleteSuccess = deleteOrder($orderId);
        if ($deleteSuccess) {
            setFlash('success', messageText('orderDeleteSuccess'));
        } else {
            setFlash('error', messageText('orderDeleteError'));
        }

        header("Location: index.php?page=orders");
        exit;
    }
    
?>