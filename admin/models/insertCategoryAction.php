<?php
    session_start();
    require_once '../../config/connection.php';
    require_once 'adminFunctions.php';
    require_once '../../models/functions.php';

    $categories = getAllFromTable('categories');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim(post('categoryName'));

        foreach($categories as $cat){
            if($cat->name === $name){
                setFlash('error', messageText('categoryNameExists'));
                header("Location: ../index.php?page=insertCategoryForm");
                exit;
            }
        }

        if (empty($name) || !testFields('name', $name)) {
            setFlash('error', messageText('name'));
            header("Location: ../index.php?page=insertCategoryForm");
            exit;
        }

        $success = insertCategoryName($name);

        if($success){
            setFlash('success', messageText('categoryAddSuccess'));
        }
        else{
            setFlash('error', messageText('categoryAddError'));
        }

        header("Location: ../index.php?page=insertCategoryForm");
        exit;
    }
?>