<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $firstName = htmlspecialchars(post('firstName'));
    $lastName = htmlspecialchars(post('lastName'));
    $email = htmlspecialchars(post('email'));
    $password = htmlspecialchars(post('password'));

    $errors = [];

    if (userExists($email)) {
        $errors[] = [
            'inputSelector' => '#input_email',         
            'errorSelector' => '#error_email', 
            'errorMessage' => messageText('userExists')
        ];
    }
    else
    {
        if (!testFields('name', $firstName)) {
            $errors[] = [
                'inputSelector' => '#input_first_name',
                'errorSelector' => '#error_first_name',
                'errorMessage' => messageText('firstName')
            ];
        }
    
        if (!testFields('name', $lastName)) {
            $errors[] = [
                'inputSelector' => '#input_last_name',
                'errorSelector' => '#error_last_name',
                'errorMessage' => messageText('lastName')
            ];
        }
    
        if (!testFields('email', $email)) {
            $errors[] = [
                'inputSelector' => '#input_email',
                'errorSelector' => '#error_email',
                'errorMessage' => messageText('email')
            ];
        }
    
        if (!testFields('password', $password)) {
            $errors[] = [
                'inputSelector' => '#input_password',
                'errorSelector' => '#error_password',
                'errorMessage' => messageText('password')
            ];
        }
    }

    

    if (empty($errors)) 
    {
        $token = generateToken();

        $tokenValue = getLastToken()->token_value;

        registration($firstName, $lastName, $email, $password);

        sendVerificationEmail($email, $tokenValue);

        echo json_encode([
            "success" => true,
            "message" => messageText('registerSuccess')
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