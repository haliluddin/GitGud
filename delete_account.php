<?php
session_start();
require_once 'classes/db.class.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to delete your account']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get confirmation text
    $confirmation = isset($_POST['confirmation']) ? $_POST['confirmation'] : '';
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    
    // Check if confirmation is correct
    if ($confirmation !== 'DELETE') {
        echo json_encode(['success' => false, 'message' => 'Please type "DELETE" to confirm account deletion']);
        exit();
    }
    
    // Verify password
    $userObj = new User();
    $userObj->id = $_SESSION['user']['id'];
    $user = $userObj->getUser($_SESSION['user']['id']);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    // Verify password
    $verify = $userObj->verifyPassword($_POST['current_password'], $_SESSION['user']['id']);
    
    // Delete user account
    $delete_result = $userObj->deleteUser($_SESSION['user']['id']);
    
    if ($delete_result) {
        // Clear session data
        session_unset();
        session_destroy();
        
        echo json_encode(['success' => true, 'message' => 'Your account has been deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete account. Please try again later.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
