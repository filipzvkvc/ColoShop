<?php
    $token = get('token');
    $tokenInformation = getTokenInformation($token);

    $message = [];

    if (!$tokenInformation) {
        $message = ['error' => 'Invalid or expired token. Please subscribe again.'];
    } else {
        $currentTime = time();
        $tokenExpiringTime = strtotime($tokenInformation->expires_at);

        $newsletterStatus = newsletterStatus($tokenInformation->id_token);

        if ($newsletterStatus && $newsletterStatus->id_token == $tokenInformation->id_token) {

            if ($newsletterStatus->subscribed == 1) {
                $message = ['error' => 'You are already subscribed to our newsletter.'];
            } else {

                if ($currentTime > $tokenExpiringTime) {

                    $newToken = generateToken();
                    $lastToken = getLastToken();

                    updateNewsletterToken($lastToken->id_token, $newsletterStatus->email);

                    sendVerificationEmailNewsletter($newsletterStatus->email, $lastToken->token_value);

                    $message = [
                        'error' =>
                        'Your confirmation link has expired. We’ve sent you a new verification email.'
                    ];

                } else {
                    verifySubscription($tokenInformation->id_token);

                    $message = [
                        'success' =>
                        'Subscription confirmed! Thank you for joining our newsletter.'
                    ];
                }
            }

        } else {
            $message = ['error' => 'Invalid or expired token. Please subscribe again.'];
        }
    }

    $encodedMessage = urlencode(json_encode($message));
    header('Location: https://coloshop.infinityfreeapp.com/index.php?page=home&message=' . $encodedMessage);
    exit;
?>
