<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $id = get('country_id');

    echo json_encode(getCities($id));

    exit;
?>
