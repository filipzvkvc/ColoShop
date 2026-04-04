<?php
    session_start();
    require_once '../../config/config.php';
    require_once '../../config/connection.php';
    require_once 'adminFunctions.php';
    require_once '../../models/functions.php';

    if(!file_exists(TMP_UPLOAD_DIR)){
        mkdir(TMP_UPLOAD_DIR, 0777, true);
    }

    if(!isset($_SESSION['insert_product_form_data'])) {
        $_SESSION['insert_product_form_data'] = new stdClass();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $errors = 0;

        $productName = post('product_name');
        $description = post('description');
        $categoryId = post('id_category');
        $genderId = post('radio');
        $colorIds = $_POST['id_color'] ?? [];
        $sizeIds  = $_POST['id_size'] ?? [];
        $price = post('price');
        $discount = post('id_discount');
        
        $_SESSION['insert_product_form_data']->colorIds = $colorIds;
        $_SESSION['insert_product_form_data']->sizeIds  = $sizeIds;

        $startDateObj = DateTime::createFromFormat('d/m/Y', $start_date);
        $endDateObj   = DateTime::createFromFormat('d/m/Y', $end_date);

        if(empty($productName)){
            setFlash('product_name', messageText('productName'));
            $errors++;
        } else {
            if(!testFields('productName', $productName)){
                setFlash('product_name', messageText('productNameCheck'));
                $errors++;
            }
            $_SESSION['insert_product_form_data']->product_name = $productName;
        }

        if(empty($description)){
            setFlash('description', messageText('productDescription'));
            $errors++;
        } else {
            if(!testFields('description', $description)){
                setFlash('description', messageText('description'));
                $errors++;
            }
            $_SESSION['insert_product_form_data']->description = $description;
        }

        if(empty($categoryId)){
            setFlash('category', messageText('categorySelect'));
            $errors++;
        } else {
            $_SESSION['insert_product_form_data']->categoryId = $categoryId;
        }

        if(empty($genderId)){
            setFlash('gender', messageText('genderSelect'));
            $errors++;
        } else {
            $_SESSION['insert_product_form_data']->genderId = $genderId;
        }

        if (empty($colorIds)) {
            setFlash('color', messageText('colorSelect'));
            $errors++;
        }

        if (empty($sizeIds)) {
            setFlash('size', messageText('sizeSelect'));
            $errors++;
        }

        if(empty($price)){
            setFlash('price', messageText('priceEnter'));
            $errors++;
        } else {
            if($price < 50 || $price > 1000){
                setFlash('price', "Price must be between 50 and 1000 dollars!");
                $errors++;
            }
            $_SESSION['insert_product_form_data']->price = $price;
        }

        if($discount === ''){
            setFlash('discount', messageText('discountSelect'));
            $errors++;
        } else {
            $_SESSION['insert_product_form_data']->discount = $discount;
        }

        $allowedTypes = ['image/jpeg','image/png'];
        $maxSize = 2 * 1024 * 1024;

        $coverPhoto = $_SESSION['insert_product_form_data']->cover_photo ?? null;

        if(isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] === UPLOAD_ERR_OK){
            $file = $_FILES['cover_photo'];

            if(!in_array($file['type'], $allowedTypes)){
                setFlash('cover_photo', 'Only JPG or PNG!');
                $errors++;
            } elseif($file['size'] > $maxSize){
                setFlash('cover_photo', 'Max 2MB!');
                $errors++;
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = bin2hex(random_bytes(8)) . "." . $ext;

                $fsTmpPath  = TMP_UPLOAD_DIR . $filename;
                $webTmpPath = "tmp_uploads/" . $filename;

                if(move_uploaded_file($file['tmp_name'], $fsTmpPath)){
                    if($coverPhoto){
                        $oldFsPath = '../../' . $coverPhoto;
                        if(file_exists($oldFsPath)) unlink($oldFsPath);
                    }
                    $_SESSION['insert_product_form_data']->cover_photo = $webTmpPath;
                    $coverPhoto = $webTmpPath;
                } else {
                    setFlash('cover_photo', 'Failed to upload cover image!');
                    $errors++;
                }
            }
        }

        if(!$coverPhoto){
            setFlash('cover_photo', 'Please upload cover image!');
            $errors++;
        }

        $additionalPictures = $_SESSION['insert_product_form_data']->additional_pictures ?? [];

        if(isset($_FILES['additional_pictures'])){
            foreach($_FILES['additional_pictures']['tmp_name'] as $i => $tmpName){
                if($_FILES['additional_pictures']['error'][$i] !== UPLOAD_ERR_OK) continue;
                if(!in_array($_FILES['additional_pictures']['type'][$i], $allowedTypes)){
                    setFlash('additional_pictures', "Only JPG or PNG allowed.");
                    continue;
                }
                if($_FILES['additional_pictures']['size'][$i] > $maxSize){
                    setFlash('additional_pictures', "Each image must be < 2MB.");
                    continue;
                }

                $ext = pathinfo($_FILES['additional_pictures']['name'][$i], PATHINFO_EXTENSION);
                $filename = bin2hex(random_bytes(8)) . "_extra." . $ext;

                $fsTmpPath  = TMP_UPLOAD_DIR . $filename;
                $webTmpPath = "tmp_uploads/" . $filename;

                if(move_uploaded_file($tmpName, $fsTmpPath)){
                    $additionalPictures[] = $webTmpPath;
                }
            }
            $_SESSION['insert_product_form_data']->additional_pictures = $additionalPictures;
        }

        if($errors > 0){
            setFlash('error', messageText('productInsertError'));
            header("Location: ../index.php?page=insertProductForm");
            exit;
        }

        $fsTmpCover = '../../' . $coverPhoto;
        $fsFinalCover = FINAL_UPLOAD_DIR . basename($coverPhoto);
        rename($fsTmpCover, $fsFinalCover);
        $finalCover = 'assets/images/' . basename($coverPhoto);

        $finalAdditional = [];
        foreach($additionalPictures as $tmpFile){
            $fsTmpPath = '../../' . $tmpFile;
            $fsNewPath = FINAL_UPLOAD_DIR . basename($tmpFile);
            rename($fsTmpPath, $fsNewPath);
            $finalAdditional[] = 'assets/images/' . basename($tmpFile);
        }

        $success = insertProduct(
            $productName,
            $description,
            $finalCover,
            $categoryId,
            $genderId,
            $colorIds,
            $sizeIds,
            $price,
            $discount,
            $finalAdditional
        );

        if($success){
            setFlash('success', messageText('productInsertSuccess'));
            unset($_SESSION['insert_product_form_data']);
        } else {
            setFlash('error', messageText('productInsertError'));
        }

        header("Location: ../index.php?page=insertProductForm");
        exit;
    }
?>
