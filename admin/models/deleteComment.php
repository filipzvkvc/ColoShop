<?php

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $commentId = get('id');

        $deleteSuccess = deleteComment($commentId);

        if ($deleteSuccess) {
            setFlash('success2', 'Comment successfully deleted.');
        } else {
            setFlash('error2', 'Error deleting comment.');
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;

    }
    
?>