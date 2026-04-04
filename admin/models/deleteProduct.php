<?php


    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $productId = get('id');

        $deleteSuccess = deleteProduct($productId);
        if ($deleteSuccess) {
            setFlash('success', messageText('productDeletedSuccess'));
        } else {
            setFlash('error', messageText('productDeleteError'));
        }

        header("Location: index.php?page=products");
        exit;
    }
    
?>