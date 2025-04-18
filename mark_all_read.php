<?php
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/stall.class.php';

header('Content-Type: application/json');

$stallObj = new Stall();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$user_id = $_POST['user_id'];

$stallObj->markallread($user_id);

echo json_encode(['status' => 'success', 'message' => 'Notifications marked as read']);
