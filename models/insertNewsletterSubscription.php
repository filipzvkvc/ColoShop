<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $email = htmlspecialchars(post('email'));
    $errors = [];

    if (!testFields('email', $email)) {
        echo json_encode([
            "success" => false,
            "errors" => [[
                'inputSelector' => '#newsletter_email',
                'errorSelector' => '#error_newsletter_email',
                'errorMessage' => 'Please enter valid email!'
            ]],
            "message" => "Please check the data again."
        ]);
        exit;
    }

    $currentTime = time();

    $newsletterInformation = getNewsletterInformation($email);

    if ($newsletterInformation) {
        if ($newsletterInformation->subscribed == 0) {

            $tokenInformation = getTokenInformationById($newsletterInformation->id_token);

            $tokenExpiringTime = strtotime($tokenInformation->expires_at);

            if($currentTime > $tokenExpiringTime){
                $token = generateToken();

                $lastToken = getLastToken();


                sendVerificationEmailNewsletter($email, $lastToken->token_value);

                updateNewsletterToken($lastToken->id_token, $email);

                echo json_encode([
                    "success" => true,
                    "message" => messageText('resendConfirmation')
                ]);
            }
            else{
                echo json_encode([
                    "success" => true,
                    "message" => "Verification email already sent, please check your email."
                
                ]);
            }
            
        } else {
            
            echo json_encode([
                "success" => false,
                "errors" => [[
                    'inputSelector' => '#newsletter_email',
                    'errorSelector' => '#error_newsletter_email',
                    'errorMessage' => messageText('subscriptionExists')
                ]],
                "message" => "Error while trying to subscribe."
            ]);
        }
    } else {

        $token = generateToken();

        $tokenValue = getLastToken()->token_value;

        insertSubscription($email);

        sendVerificationEmailNewsletter($email, $tokenValue);

        echo json_encode([
            "success" => true,
            "message" => messageText('pendingSubscription')
        ]);
    }

    exit;
?>
