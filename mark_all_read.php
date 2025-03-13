<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$user_id = $_POST['user_id'];

$dsn = "mysql:host=localhost;dbname=gitgud;charset=utf8";
$pdo = new PDO($dsn, 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$stmt = $pdo->prepare("UPDATE notifications SET status = 'Read' WHERE user_id = ?");
$stmt->execute([$user_id]);

echo json_encode(['status' => 'success', 'message' => 'Notifications marked as read']);
