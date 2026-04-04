<?php

    $token = get('token');
    $result = updateNewsletterStatus($token);
    
    if ($result) {
        include 'views/pages/newsletterUnsubscribeSuccess.php';
    } else {
        include 'views/pages/newsletterUnsubscribeError.php';
    }
?>
