<?php
session_start();
include_once 'classes/park.class.php';
include_once 'classes/encdec.class.php';

// Check if user is logged in
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Check if stall ID is provided
if (!isset($_POST['stall_id']) || empty($_POST['stall_id'])) {
    echo json_encode(['success' => false, 'message' => 'Stall ID is required']);
    exit;
}

try {
    // Decrypt the stall ID
    $stall_id = $_POST['stall_id'];
    
    // Create park object
    $parkObj = new Park();
    
    // Delete the stall
    $result = $parkObj->deleteStall($stall_id);
    
    if ($result) {
        // Return success response
        echo json_encode(['success' => true]);
    } else {
        // Return error response
        echo json_encode(['success' => false, 'message' => 'Failed to delete stall']);
    }
} catch (Exception $e) {
    // Return error response with exception message
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
