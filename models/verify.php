<?php
    $token = get('token');

    $tokenInformation = getTokenInformation($token);

    $currentTime = time();
    $tokenExpiringTime = strtotime($tokenInformation->expires_at);

    $userStatus = userStatus($tokenInformation->id_token);

    $message = [];

    if($userStatus->id_token == $tokenInformation->id_token)
    {
        if($userStatus->verified == 1)
        {
            $message = ['error' => 'User already verified, please log in!'];
        }
        else
        {
            if($currentTime > $tokenExpiringTime)
            {
                $token = generateToken();
        
                $lastToken = getLastToken();
        
                updateUserToken($lastToken->id_token, $userStatus->email);
        
                sendVerificationEmail($userStatus->email, $lastToken->token_value);

                $message = ['error' => messageText('tokenExpired')];
            }
            else
            {
                verifyUser($tokenInformation->id_token);

                $message = ['success' => 'Account verifed, please log in!'];

            }
        }
    }
    else
    {
        $message = ['error' => 'Verification link is not valid.'];
    }

    $encodedMessage = encodeMessage($message);

    header('Location: https://coloshop.infinityfreeapp.com/index.php?page=login&message=' . $encodedMessage);
    
    exit;
?>