<?php
// check_stock.php
include_once 'classes/db.class.php';
include_once 'classes/cart.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_items'])) {
    $cartItemsJson = $_POST['cart_items'];
    $cartGrouped = json_decode($cartItemsJson, true);
    
    if (!$cartGrouped) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid cart data.']);
        exit;
    }
    
    $cartObj = new Cart();
    $result = $cartObj->checkStock($cartGrouped);  // This function is defined in the Cart class.
    
    if ($result === true) {
        echo json_encode(['status' => 'ok']);
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $result]);
    }
    exit;
}
?>
