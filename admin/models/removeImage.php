<?php
    session_start();
    require_once '../../config/connection.php';
    require_once '../../models/functions.php';
    require_once 'adminFunctions.php';

    header('Content-Type: application/json');

    $input = json_decode(file_get_contents("php://input"), true);
    $type = $input['type'] ?? null;
    $filePath = $input['filePath'] ?? null;

    if (!$type) {
        echo json_encode(["success" => false, "message" => "Missing type"]);
        exit;
    }

    switch ($type) {

        case "profile_photo_temp":
            if (!empty($_SESSION['edit_user_form_data']->profile_photo_temp)) {
                $fileName = basename($_SESSION['edit_user_form_data']->profile_photo_temp);
                $path = TMP_UPLOAD_DIR . $fileName;
                if (file_exists($path)) unlink($path);
                unset($_SESSION['edit_user_form_data']->profile_photo_temp);
                echo json_encode(["success" => true, "newSrc" => "../" . DEFAULT_PROFILE_PICTURE]);
                exit;
            }
            echo json_encode(["success" => false, "message" => "No temp profile photo"]);
            exit;


        case "profile_photo":
            $userId = $_SESSION['edit_user_id'] ?? null;

            if (!$userId || !$filePath) {
                echo json_encode([
                    "success" => false,
                    "message" => "Missing user or file"
                ]);
                exit;
            }

            $success = deleteUserProfilePicture($filePath, $userId);

            if ($success) {
                echo json_encode([
                    "success" => true,
                    "message" => "Profile image removed!",
                    "newSrc" => "../" . DEFAULT_PROFILE_PICTURE
                ]);
                exit;
            }

            echo json_encode([
                "success" => false,
                "message" => "Error removing profile image!"
            ]);
            exit;


        case "insert_cover_temp":
            if (!empty($_SESSION['insert_product_form_data']->cover_photo)) {
                $fileName = basename($_SESSION['insert_product_form_data']->cover_photo);
                $path = TMP_UPLOAD_DIR . $fileName;
                if (file_exists($path)) unlink($path);
                unset($_SESSION['insert_product_form_data']->cover_photo);
                echo json_encode(["success" => true]);
                exit;
            }
            echo json_encode(["success" => false, "message" => "No cover photo"]);
            exit;


        case "insert_additional_temp":
            if (!$filePath) {
                echo json_encode(["success" => false, "message" => "Missing file path"]);
                exit;
            }

            if (!empty($_SESSION['insert_product_form_data']->additional_pictures)) {

                $_SESSION['insert_product_form_data']->additional_pictures =
                    array_values(array_filter(
                        $_SESSION['insert_product_form_data']->additional_pictures,
                        fn($p) => $p !== $filePath
                    ));

                $fileName = basename($filePath);
                $path = TMP_UPLOAD_DIR . $fileName;
                if (file_exists($path)) unlink($path);

                echo json_encode(["success" => true]);
                exit;
            }

            echo json_encode(["success" => false, "message" => "No additional picture"]);
            exit;

        case "edit_cover_temp":
        if (!empty($_SESSION['edit_product_form_data']->cover_photo)) {
            $fileName = basename($_SESSION['edit_product_form_data']->cover_photo);
            $path = TMP_UPLOAD_DIR . $fileName;
            if (file_exists($path)) unlink($path);
            unset($_SESSION['edit_product_form_data']->cover_photo);

            $productId = $_SESSION['edit_product_id'] ?? null;
            $product = getProductInformation($productId);
            $cover = $product->cover_photo ?? DEFAULT_PRODUCT_PICTURE;

            echo json_encode(["success" => true, "newSrc" => "../" . $cover]);
            exit;
        }
        echo json_encode(["success" => false, "message" => "No cover photo"]);
        exit;


         case "edit_additional_temp":
            if (!$filePath) {
                echo json_encode(["success" => false, "message" => "Missing file path"]);
                exit;
            }

            if (!empty($_SESSION['edit_product_form_data']->additional_pictures)) {

                $_SESSION['edit_product_form_data']->additional_pictures =
                    array_values(array_filter(
                        $_SESSION['edit_product_form_data']->additional_pictures,
                        fn($p) => $p !== $filePath
                    ));

                $fileName = basename($filePath);
                $path = TMP_UPLOAD_DIR . $fileName;
                if (file_exists($path)) unlink($path);

                echo json_encode(["success" => true]);
                exit;
            }

            echo json_encode(["success" => false, "message" => "No additional picture"]);
            exit;


        case "edit_cover_real":
            $productId = $_SESSION['edit_product_id'] ?? null;

            if (!$productId || !$filePath) {
                echo json_encode([
                    "success" => false,
                    "message" => "Missing product or file"
                ]);
                exit;
            }

            $success = deleteProductCoverPicture($filePath, $productId);

            if ($success) {
                echo json_encode([
                    "success" => true,
                    "message" => "Product cover removed!",
                    "newSrc" => "../" . DEFAULT_PRODUCT_PICTURE
                ]);
                exit;
            }

            echo json_encode([
                "success" => false,
                "message" => "Error removing product cover!"
            ]);
            exit;


        case "edit_additional_real":
            $productId = $_SESSION['edit_product_id'] ?? null;
            $filePath = $input['filePath'] ?? null;

            if (!$productId || !$filePath) {
                echo json_encode([
                    "success" => false,
                    "message" => "Missing product ID or file path"
                ]);
                exit;
            }

            $success = deleteSingleProductAdditionalPicture($productId, $filePath);

            if ($success) {
                echo json_encode([
                    "success" => true,
                    "message" => "Additional picture removed!"
                ]);
                exit;
            }

            echo json_encode([
                "success" => false,
                "message" => "Error removing additional picture!"
            ]);
            exit;


        default:
            echo json_encode(["success" => false, "message" => "Unknown type"]);
            exit;
    }
