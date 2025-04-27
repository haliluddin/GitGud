<?php

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/classes/db.class.php';

$body = file_get_contents('php://input');
$data = json_decode($body, true);
$ratingId = isset($data['request_id']) ? (int)$data['request_id'] : 0;

if (!$ratingId) {
    echo json_encode([
      'success' => false,
      'message' => 'Missing request_id'
    ]);
    exit;
}

$userObj = new User();         
$deleted = $userObj->deleteRating($ratingId);

if (!$deleted) {
    echo json_encode([
      'success' => false,
      'message' => 'Failed to delete rating'
    ]);
    exit;
}


echo json_encode(['success' => true]);
exit;
