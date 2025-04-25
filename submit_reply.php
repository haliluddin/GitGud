<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/classes/product.class.php';

if (!isset($_SESSION['user'])) {
  echo json_encode(['success'=>false,'message'=>'Login required']);
  exit;
}

$rid      = (int)($_POST['review_id'] ?? 0);
$response = trim($_POST['seller_response'] ?? '');

if (!$rid || $response === '') {
  echo json_encode(['success'=>false,'message'=>'Invalid input']);
  exit;
}

$productObj = new Product();
// (Optionally verify owner here…)

if ($productObj->saveSellerResponse($rid, $response)) {
  echo json_encode([
    'success' => true,
    'html'    => htmlspecialchars($response)
  ]);
} else {
  echo json_encode(['success'=>false,'message'=>'DB error']);
}
