<?php
include_once 'classes/db.class.php';
include_once 'classes/cart.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['park_id']) && isset($_POST['product_id'])) {
    $user_id = intval($_POST['user_id']);
    $park_id = intval($_POST['park_id']);
    $product_id = intval($_POST['product_id']);
    $request = isset($_POST['request']) ? urldecode($_POST['request']) : '';

    $cartObj = new Cart();
    $result = $cartObj->deleteCartItem($user_id, $park_id, $product_id, $request);
    
    if ($result) {
        echo "Item deleted";
    } else {
        http_response_code(500);
        echo "Deletion failed";
    }
    exit;
}
?>
