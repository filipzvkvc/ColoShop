<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $commentId = post('commentId');

    if(deleteProductComment($commentId)){
        echo json_encode(['success' => true, 'message' => 'Deleted comment successfully!']);
        //exit;
    }

    exit;
?>