<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $productId = htmlspecialchars(post('productId'));

    $currentPage = isset($_POST['page']) ? (int)$_POST['page'] : 1;

    $reviewCount = getProductReviewCount($productId);

    $itemsPerPage = 3;
    $offset = ($currentPage - 1) * $itemsPerPage;

    $productReviews = getProductReviews2($productId, $itemsPerPage, $offset);
    $totalReviews = getProductReviewCount($productId);
    $totalPages = ceil($totalReviews / $itemsPerPage);

    
    $allReviews = getProductReviewsInformation($productId);

    $productRatings = 0;
    foreach ($allReviews as $pr) {
        $productRatings += $pr['rating'];
    }

    $averageRating = count($allReviews) > 0 ? $productRatings / count($allReviews) : 0;


    echo json_encode([
        'productReviews' => $productReviews,
        'reviewCount' => $reviewCount,
        'averageRating' => $averageRating,
        "totalPages" => $totalPages,
        "totalReviews" => $totalReviews,
        "currentPage" => $currentPage
    ]);

    exit;
?>