<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    session_start();

    header('Content-Type: application/json; charset=utf-8');

    if(isset($_SESSION['user'])){
		$user = $_SESSION['user'];
	}

    $errors = [];

    $firstName = htmlspecialchars(post('first_name'));
    $lastName = htmlspecialchars(post('last_name'));
    $email = htmlspecialchars(post('email'));
    $new_password = htmlspecialchars(post('new_password'));
    $confirm_password = htmlspecialchars(post('confirm_password'));
    $profilePicture = $user->profile_picture;


    if(!empty(post('remove_picture'))) {
        $profilePicture = 'assets/images/default_profile_picture.jpg';

        if($user->profile_picture && $user->profile_picture !== $profilePicture && file_exists('../'.$user->profile_picture)) {
            unlink('../'.$user->profile_picture);
        }
    }

    if(empty($firstName)){
        $firstName = $user->first_name;
    }
    else{
        if (!testFields('name', $firstName)) {
            $errors[] = [
                'inputSelector' => '#input_first_name',
                'errorSelector' => '#error_first_name',
                'errorMessage' => messageText('firstName')
            ];
        }
    }

    if(empty($lastName)){
        $lastName = $user->last_name;
    }
    else{
        if (!testFields('name', $lastName)) {
            $errors[] = [
                'inputSelector' => '#input_last_name',
                'errorSelector' => '#error_last_name',
                'errorMessage' => messageText('lastName')
            ];
        }
    }

    if(empty($email)){
        $email = $user->email;
    }
    else{
        if (!testFields('email', $email)) {
            $errors[] = [
                'inputSelector' => '#input_email',
                'errorSelector' => '#error_email',
                'errorMessage' => messageText('email')
            ];
        }
    }


    if (empty($new_password) && empty($confirm_password)) {
        $password = $user->password;
    } 
    elseif (strtolower($new_password) != strtolower($confirm_password)) {
        $errors[] = [
            'inputSelector' => '.input_password',
            'errorSelector' => '#error_password',
            'errorMessage' => messageText('passwordsNotMatching')
        ];
    } 
    elseif (!testFields('password', $new_password)) {
        $errors[] = [
            'inputSelector' => '#new_password, #confirm_password',
            'errorSelector' => '#error_password',
            'errorMessage' => messageText('password')
        ];
    } 
    elseif (password_verify($new_password, $user->password)) {
        $errors[] = [
            'inputSelector' => '.input_password',
            'errorSelector' => '#error_password',
            'errorMessage' => messageText('newPasswordIsOldPassword')
        ];
    } 
    else {
        $password = hashPassword($new_password);
    }
    

    if(empty($errors) && isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_image'];
        
        $allowedTypes = ['image/jpeg', 'image/png'];
        $maxSize = 2 * 1024 * 1024;
        
        if(!in_array($file['type'], $allowedTypes)) {
            $errors[] = [
                'inputSelector' => '#profile_image',
                'errorSelector' => '#error_profile_image',
                'errorMessage' => 'Only JPG and PNG file extensions are allowed!'
            ];
        } 
        elseif($file['size'] > $maxSize) 
        {
            $errors[] = [
                'inputSelector' => '#profile_image',
                'errorSelector' => '#error_profile_image',
                'errorMessage' => 'Image size must be less than 2MB.'
            ];
        } 
        else {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $token_value = bin2hex(random_bytes(5));
            $newFilename = $token_value . '.' . $extension;
            $uploadPath = '../uploads/profile_pictures/' . $newFilename;
            
            if(move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $profilePicture = 'uploads/profile_pictures/' . $newFilename;
            } 
            else 
            {
                $errors[] = [
                    'inputSelector' => '#profile_image',
                    'errorSelector' => '#error_profile_image',
                    'errorMessage' => 'Failed to upload image.'
                ];
            }
        }
    }



    if (empty($errors)) 
    {
        updateUserInformation($user->id_user, $firstName, $lastName, $email, $password, $profilePicture);
    
        $_SESSION['user']->first_name = $firstName;

        $_SESSION['user']->last_name = $lastName;

        $_SESSION['user']->email = $email;


        $_SESSION['user']->profile_picture = $profilePicture;


        echo json_encode([
            "success" => true,
            "first_name" =>$firstName,
            "last_name" =>$lastName,
            "email" =>$email,
            "profile_picture" =>$profilePicture,
            "message" => messageText('userInformationUpdateSuccess')
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