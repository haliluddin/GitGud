<?php
session_start();
require_once __DIR__ . '/classes/db.class.php';
$data = json_decode(file_get_contents('php://input'), true);
$rid  = (int)$data['review_id'];
$userObj = new User();
$ok = $userObj->requestReviewDeletion($rid);
header('Content-Type: application/json');
echo json_encode(['success'=>$ok]);
