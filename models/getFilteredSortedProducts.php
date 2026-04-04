<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $filters = [
        'categories' => post('categories') ?? [],
        'gender' => post('gender'),
        'sizes' => post('sizes') ?? [],
        'colors' => post('colors') ?? [],
        'priceMin' => post('priceMin') ?? null,
        'priceMax' => post('priceMax') ?? null
    ];
    
    $sort = post('sort') ?? 'default';

    $currentPage = isset($_POST['page']) ? (int)$_POST['page'] : 1;

    $itemsPerPage = 8;
    $offset = ($currentPage - 1) * $itemsPerPage;

    $products = getFilteredSortedProducts($filters, $sort, $itemsPerPage, $offset);
    $totalProducts = getFilteredSortedProductCount($filters, $sort);
    $totalPages = ceil($totalProducts / $itemsPerPage);

    echo json_encode([
        "products" => $products,
        "totalPages" => $totalPages,
        "totalProducts" => $totalProducts,
        "currentPage" => $currentPage
    ]);

    exit;
?>
