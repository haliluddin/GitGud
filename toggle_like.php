<?php
session_start();

require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/product.class.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
$user_id = $_SESSION['user']['id'];

if (!isset($_POST['stall_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing stall id']);
    exit;
}
$stall_id = intval($_POST['stall_id']);

$productObj = new Product();
$result = $productObj->toggleStallLike($user_id, $stall_id);

echo json_encode($result);
?>
