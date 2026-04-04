<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $name = htmlspecialchars(post('name'));
    $email = htmlspecialchars(post('email'));
    $subject = htmlspecialchars(post('subject'));
    $message = htmlspecialchars(post('message'));

    $errors = [];

    if (!testFields('name', $name)) {
        $errors[] = [
            'inputSelector' => '#input_name',
            'errorSelector' => '#error_name',
            'errorMessage' => messageText('name')
        ];
    }

    if (!testFields('email', $email)) {
        $errors[] = [
            'inputSelector' => '#input_email',
            'errorSelector' => '#error_email',
            'errorMessage' => messageText('email')
        ];
    }

    if (!testFields('subject', $subject)) {
        $errors[] = [
            'inputSelector' => '#input_subject',
            'errorSelector' => '#error_subject',
            'errorMessage' => messageText('subject')
        ];
    }

    if (!testFields('message', $message)) {
        $errors[] = [
            'inputSelector' => '#input_message',
            'errorSelector' => '#error_message',
            'errorMessage' => messageText('message')
        ];
    }

    if (empty($errors)) 
    {
        sendContactConfirmationEmail($email, $name);

        sendContactToAdmin($name, $email, $subject, $message);

        echo json_encode([
            "success" => true, 
            "message" => messageText('contactSuccess')
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