<?php
session_start();
require_once __DIR__ . '/db.class.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $userObj = new User();
    $result = $userObj->deleteUser($user_id);
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
    }
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}
?>
