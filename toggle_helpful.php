<?php

session_start();
require_once __DIR__ . '/classes/product.class.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
  echo json_encode(['success'=>false,'message'=>'You must be logged in']);
  exit;
}

$userId   = $_SESSION['user']['id'];
$reviewId = (int)($_POST['review_id'] ?? 0);

if (!$reviewId) {
  echo json_encode(['success'=>false,'message'=>'Invalid review']);
  exit;
}

$productObj = new Product();

if ($productObj->hasUserMarkedHelpful($reviewId, $userId)) {
  $productObj->removeHelpfulMark($reviewId, $userId);
  $toggledOn = false;
} else {
  $productObj->addHelpfulMark($reviewId, $userId);
  $toggledOn = true;
}

$count = $productObj->getHelpfulCount($reviewId);

echo json_encode([
  'success'   => true,
  'toggledOn' => $toggledOn,
  'count'     => $count
]);
