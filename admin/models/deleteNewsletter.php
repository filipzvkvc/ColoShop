<?php

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $newsletterId = get('id');

        $deleteSuccess = deleteNewsletter($newsletterId);
        if ($deleteSuccess) {
            setFlash('success', 'Subscriber has been successfully deleted.');
        } else {
            setFlash('error', 'An error occurred while deleting the subscriber.');
        }

        header("Location: index.php?page=newsletterSubscribers");
        exit;
    }
    
?>