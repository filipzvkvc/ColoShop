<?php
    $token = get('token');

    if (newsletterSubscriptionCheck($token)) {
        include 'views/pages/newsletterUnsubscribeConfirm.php';
    } else {
        include 'views/pages/newsletterUnsubscribeError.php';
    }
?>