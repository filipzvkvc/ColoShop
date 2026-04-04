<?php
    session_start();
    require_once '../../config/connection.php';
    require_once 'adminFunctions.php';
    require_once '../../models/functions.php';

    if(!file_exists(TMP_UPLOAD_DIR)){
            mkdir(TMP_UPLOAD_DIR, 0777, true);
        }

    if (!isset($_SESSION['edit_user_form_data'])) {
        $_SESSION['edit_user_form_data'] = new stdClass();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idUser = post('id_user');
        $userInformation = getUserInformation($idUser);
        
        $firstName = post('first_name');
        $lastName = post('last_name');
        $email = post('email');
        $verified = (int)post('radio');
        $id_role = post('id_role') ? post('id_role') : $userInformation->id_role;

        $defaultProfile = DEFAULT_PROFILE_PICTURE;

        $profilePhoto = $userInformation->profile_picture;

        $tempProfile = $_SESSION['edit_user_form_data']->profile_photo_temp ?? null;

        if(empty($firstName)){
            $firstName = $userInformation->first_name;
        }
        else
        {
            if (!testFields('name', $firstName)) {
                setFlash('first_name', messageText('firstName'));
            }

            $_SESSION['edit_user_form_data']->first_name = $firstName;
        }


        if(empty($lastName)){
            $lastName = $userInformation->last_name;
        }
        else{
            if (!testFields('name', $lastName)) {
                setFlash('last_name', messageText('lastName'));
            }

            $_SESSION['edit_user_form_data']->last_name = $lastName;
        }


        if(empty($email)){
            $email = $userInformation->email;
        }
        else{
            if (!testFields('email', $email)) {
                setFlash('email', messageText('email'));
            }

            $_SESSION['edit_user_form_data']->email = $email;
        }

        $allowedTypes = ['image/jpeg', 'image/png'];
        $maxSize = 2 * 1024 * 1024;

        if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_photo'];

            if(!in_array($file['type'], $allowedTypes)) {
                setFlash('profile_photo', 'Only JPG and PNG allowed!');
            } elseif($file['size'] > $maxSize) {
                setFlash('profile_photo', 'Max size is 2MB!');
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = bin2hex(random_bytes(8)) . "." . $ext;

                $fsTmp = "../../tmp_uploads/$filename";
                $webTmp = "tmp_uploads/$filename";

                if(move_uploaded_file($file['tmp_name'], $fsTmp)) {

                    if ($tempProfile && file_exists("../../".$tempProfile)) {
                        unlink("../../".$tempProfile);
                    }

                    $_SESSION['edit_user_form_data']->profile_photo_temp = $webTmp;
                    $tempProfile = $webTmp;
                }
            }
        }



        if (hasFlash('first_name') || hasFlash('last_name') || hasFlash('email') || hasFlash('profile_photo')) {

            setFlash('error', messageText('userInformationUpdateError'));

            header("Location: ../index.php?page=editUserForm&id=$idUser");
            exit;
        }

        if ($tempProfile) {
            $tmpFS = TMP_UPLOAD_DIR . basename($tempProfile);
            $finalFS = PROFILE_PICTURES . basename($tempProfile);

            rename($tmpFS, $finalFS);

            $profilePhoto = "uploads/profile_pictures/" . basename($tempProfile);

            unset($_SESSION['edit_user_form_data']->profile_photo_temp);
        }

        $success = updateUserInformationDashboard($idUser, $id_role, $firstName, $lastName, $email, $profilePhoto, $verified);

        if($success){
            setFlash('success', messageText('userInformationUpdateSuccess'));
        }
        else{
            setFlash('error', messageText('userInformationUpdateError'));
        }

        header("Location: ../index.php?page=editUserForm&id=$idUser");
        exit;

    }
?>