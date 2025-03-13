<?php
require_once __DIR__ . '/classes/db.php';
require_once __DIR__ . '/classes/cart.class.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$product_id = intval($data['product_id']);
$user_id = strval($data['user_id']); 
session_start();

try {
    $cart = new Cart();
    $cart->removeFromCart($user_id, $product_id);

    echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>