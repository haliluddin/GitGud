<?php
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/product.class.php';

if (isset($_GET['stall_id']) && isset($_GET['search'])) {
    $stall_id = intval($_GET['stall_id']);
    $searchTerm = trim($_GET['search']);
    
    $productObj = new Product();
    $results = $productObj->searchProducts($stall_id, $searchTerm);
    
    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}
?>
