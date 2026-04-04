<?php
    session_start();

    require_once "../config/connection.php";
    require_once "functions.php";

    $data = json_decode(file_get_contents('php://input'), true);

    $email = htmlspecialchars($data['email'] ?? '');
    $firstName = htmlspecialchars($data['firstName'] ?? '');
    $lastName = htmlspecialchars($data['lastName'] ?? '');
    $streetName = htmlspecialchars($data['streetName'] ?? '');
    $streetNumber = htmlspecialchars($data['streetNumber'] ?? '');
    $country = htmlspecialchars($data['country'] ?? '');
    $city = htmlspecialchars($data['city'] ?? '');
    $phoneNumber = htmlspecialchars($data['phoneNumber'] ?? '');
    $selectedShipping = htmlspecialchars($data['selectedShipping'] ?? '');
    $cart = $data['cart'] ?? [];

    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];

        $id_cart = getOrCreateCartId($user->id_user);

        $products = getCartProductsByCartId($id_cart);
    }
    else{
        $user = null;
    }

    $errors = [];


    if($user)
    {
        if($user->email !== $email){
            $errors[] = [
                'inputSelector' => '#email',         
                'errorSelector' => '#error_email', 
                'errorMessage' => messageText('emailDoesNotMatch')
            ];
        }

        if($user->first_name !== $firstName){
            $errors[] = [
                'inputSelector' => '#first_name',         
                'errorSelector' => '#error_first_name', 
                'errorMessage' => messageText('nameDoesNotMatch')
            ];
        }

        if($user->last_name !== $lastName){
            $errors[] = [
                'inputSelector' => '#last_name',         
                'errorSelector' => '#error_last_name', 
                'errorMessage' => messageText('lastNameDoesNotMatch')
            ];
        }
    }
    else
    {
        if(!testFields('email', $email)){
            $errors[] = [
                'inputSelector' => '#email',         
                'errorSelector' => '#error_email', 
                'errorMessage' => messageText('email')
            ];
        }

        if(!testFields('name', $firstName)){
            $errors[] = [
                'inputSelector' => '#first_name',         
                'errorSelector' => '#error_first_name', 
                'errorMessage' => messageText('firstName')
            ];
        }

        if(!testFields('name', $lastName)){
            $errors[] = [
                'inputSelector' => '#last_name',         
                'errorSelector' => '#error_last_name', 
                'errorMessage' => messageText('lastName')
            ];
        }
    }

    if(!testFields('streetName', $streetName)){
        $errors[] = [
            'inputSelector' => '#street_name',         
            'errorSelector' => '#error_street_name', 
            'errorMessage' => messageText('streetName')
        ];
    }

    if(!testFields('streetNumber', $streetNumber)){
        $errors[] = [
            'inputSelector' => '#street_number',         
            'errorSelector' => '#error_street_number', 
            'errorMessage' => messageText('streetNumber')
        ];
    }

    if(!testFields('phoneNumber', $phoneNumber)){
        $errors[] = [
            'inputSelector' => '#phone_number',         
            'errorSelector' => '#error_phone_number', 
            'errorMessage' => messageText('phoneNumber')
        ];
    }

    if(empty($country)){
        $errors[] = [
            'inputSelector' => '#country',         
            'errorSelector' => '#error_country', 
            'errorMessage' => messageText('country')
        ];
    }
    else
    {
        if(empty($city)){
            $errors[] = [
                'inputSelector' => '#city',         
                'errorSelector' => '#error_city', 
                'errorMessage' => messageText('city')
            ];
        }
    }

    if (empty($errors))
    {
        $_SESSION['checkout_data'] = [
            'user' => $user ? $user->id_user : null,
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'streetName' => $streetName,
            'streetNumber' => $streetNumber,
            'city' => $city,
            'phoneNumber' => $phoneNumber,
            'selectedShipping' => $selectedShipping
        ];

        echo json_encode([
            "success" => true
        ]);
    } 
    else 
    {
        echo json_encode([
            "success" => false,
            "errors" => $errors,
            "message" => messageText('orderSubmitError')
        ]);
    }

    exit;
?>