<?php
include_once 'classes/db.class.php';
include_once 'classes/cart.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['user_id']) &&
    isset($_POST['park_id']) &&
    isset($_POST['product_id']) &&
    isset($_POST['quantity'])
) {
    $user_id    = intval($_POST['user_id']);
    $park_id    = intval($_POST['park_id']);
    $product_id = intval($_POST['product_id']);
    $request    = isset($_POST['request']) ? urldecode($_POST['request']) : '';
    $quantity   = intval($_POST['quantity']);

    $cartObj = new Cart();
    $result = $cartObj->updateCartQuantity($user_id, $park_id, $product_id, $request, $quantity);
    
    if ($result) {
        echo "Quantity updated";
    } else {
        http_response_code(500);
        echo "Update failed";
    }
    exit;
}
?>
