<?php
    session_start();
    
    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $email = htmlspecialchars(post('email'));
    $password = htmlspecialchars(post('password'));

    $user = getUserInformation($email);

    $errors = [];

    if (!userExists($email)) {
        $errors[] = [
            'inputSelector' => '#input_email',         
            'errorSelector' => '#error_email', 
            'errorMessage' => messageText('userDoesNotExists')
        ];

        $errors[] = [
            'inputSelector' => '#input_password',         
            'errorSelector' => '#error_password'
        ];
    }
    else{
        if(!password_verify($password, $user->password)){
            $errors[] = [
                'inputSelector' => '#input_password',         
                'errorSelector' => '#error_password', 
                'errorMessage' => messageText('incorrectPassword')
            ];
        }
        
        if($user->verified != 1 && password_verify($password, $user->password)){

            $userToken = getTokenByUserId($user->id_user);
            
            if($userToken) {
                $currentTime = date('Y-m-d H:i:s');
                
                if(strtotime($userToken->expires_at) < strtotime($currentTime)) {
                    $token = generateToken();
            
                    $lastToken = getLastToken();
            
                    updateUserToken($lastToken->id_token, $email);
            
                    sendVerificationEmail($email, $lastToken->token_value);
                    
                    $errors[] = [
                        'inputSelector' => '#input_email',         
                        'errorSelector' => '#error_email', 
                        'errorMessage' => messageText('tokenExpired')
                    ];
                } else {
                    $errors[] = [
                        'inputSelector' => '#input_email',         
                        'errorSelector' => '#error_email', 
                        'errorMessage' => messageText('unverifiedAccount')
                    ];
                }
            } else {
                $token = generateToken();
        
                $lastToken = getLastToken();
        
                updateUserToken($lastToken->id_token, $email);
        
                sendVerificationEmail($email, $lastToken->token_value);
                
                $errors[] = [
                    'inputSelector' => '#input_email',         
                    'errorSelector' => '#error_email', 
                    'errorMessage' => messageText('unverifiedAccountNewToken')
                ];
            }
        }
    }



    if (empty($errors)) 
    {
        $_SESSION['user'] = $user;

        if($_SESSION['user']->id_role === 1){
            $location = 'https://coloshop.infinityfreeapp.com/admin/index.php';
        }
        else
        {
            $location = 'https://coloshop.infinityfreeapp.com/index.php?page=home';
        }

        $logData = '[USER_LOGIN]' . "\t" . "ID: " .  $_SESSION['user']->id_user . "\t" . 'IP: ' . $_SERVER['REMOTE_ADDR'] . "\t" . "TIME: " . date("Y-m-d H:i:s") . PHP_EOL ;

        logFile($logData);
        
        echo json_encode([
            "success" => true,
            "location" => $location
        ]);
    } 
    else 
    {
        echo json_encode([
            "success" => false,
            "errors" => $errors,
            "message" => messageText('formSubmitError')
        ]);
    }

    exit;
?>