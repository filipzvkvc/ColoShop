<?php
    session_start();

    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $name = htmlspecialchars(post('name'));
    $email = htmlspecialchars(post('email'));
    $rating = htmlspecialchars(post('rating'));
    $review = htmlspecialchars(post('review'));
    $id_product = htmlspecialchars(post('productId'));

    
    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];
    }

    $errors = [];

    $hasError = false;

    if($user->email !== $email){
        $errors[] = [
            'inputSelector' => '#input_email',         
            'errorSelector' => '#error_email', 
            'errorMessage' => messageText('emailDoesNotMatch')
        ];

        $hasError = true;
    }

    if($name !== $user->first_name){
        $errors[] = [
            'inputSelector' => '#input_name',         
            'errorSelector' => '#error_name', 
            'errorMessage' => messageText('nameDoesNotMatch')
        ];

        $hasError = true;
    }

    if (!testFields('review', $review)) {
        $errors[] = [
            'inputSelector' => '#input_review',
            'errorSelector' => '#error_review',
            'errorMessage' => messageText('review')
        ];

        $hasError = true;
    }
    
    if(!$hasError){
        if(checkReview($user->id_user, $id_product)){
            $errors[] = [
                'inputSelector' => '#input_name, #input_email, #input_review',       
                'errorSelector' => '#error_check_review', 
                'errorMessage' => messageText('reviewAlreadySubmitted')
            ];

            $hasError = true;
        }
    }

    if (!$hasError) 
    {
        insertProductReview($user->id_user, $id_product, $review, $rating);
        echo json_encode([
            "success" => true, 
            "message" => messageText('productReviewSuccess')
        ]);
    } 
    else
    {
        echo json_encode([
            "success" => false,
            "errors" => $errors,
            "message" => messageText('productReviewError')
        ]);
    }

    exit;
?>