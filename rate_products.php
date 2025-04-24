<?php
header('Content-Type: application/json');
require_once __DIR__ . '/classes/stall.class.php';
require_once __DIR__ . '/classes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Only POST allowed']);
    exit;
}

$user_id = $_SESSION['user']['id'];
$order_stall_id = intval($_POST['order_stall_id'] ?? 0);
$ratingsJson = $_POST['ratings'] ?? '[]';
$ratings = json_decode($ratingsJson, true);

if (!$user_id || !$order_stall_id || !is_array($ratings)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

try {
    $stallObj = new Stall();
    $stallObj->saveRatings($user_id, $order_stall_id, $ratings);
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
