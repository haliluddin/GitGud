<?php
require_once __DIR__ . '/classes/db.class.php';
require_once __DIR__ . '/classes/stall.class.php';

header('Content-Type: application/json');
session_start();

$stallObj = new Stall();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user']['id'])) {
    echo json_encode(['status'=>'error','message'=>'Invalid request']);
    exit;
}
$user_id = $_SESSION['user']['id'];

$stallObj->deleteallread($user_id);

echo json_encode(['status'=>'success']);
