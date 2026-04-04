<?php

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $userId = get('id');

        $deleteSuccess = deleteUser($userId);
        if ($deleteSuccess) {
            setFlash('success', messageText('userDeleteSuccess'));
        } else {
            setFlash('error', messageText('userDeleteError'));
        }

        header("Location: index.php?page=users");
        exit;
    }
    
?>