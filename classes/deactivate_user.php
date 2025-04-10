<?php
session_start();
require_once __DIR__ . '/admin.class.php';
require_once __DIR__ . '/db.class.php';

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminObj = new Admin();
    $response = ['success' => false, 'message' => ''];
    
    // Check if all required fields are present
    if (isset($_POST['deactivate_user_id']) && isset($_POST['deactivation_duration']) && isset($_POST['deactivation_reason'])) {
        $user_id = $_POST['deactivate_user_id'];
        $deactivation_reason = htmlspecialchars(trim($_POST['deactivation_reason']));
        $duration = $_POST['deactivation_duration'];
        
        $today = new DateTime();
        $deactivated_until = clone $today;
        
        switch ($duration) {
            case '3days':
                $deactivated_until->add(new DateInterval('P3D'));
                break;
            case '7days':
                $deactivated_until->add(new DateInterval('P7D'));
                break;
            case '1month':
                $deactivated_until->add(new DateInterval('P1M'));
                break;
            case 'forever':
                $deactivated_until = new DateTime('9999-12-31'); // Far future date for "forever"
                break;
            default:
                $deactivated_until->add(new DateInterval('P3D')); // Default to 3 days
        }
        
        $deactivated_until_str = $deactivated_until->format('Y-m-d');

        $deactivate = $adminObj->deactivateUser($user_id, $deactivated_until_str, $deactivation_reason);
        
        if ($deactivate) {
            $response['success'] = true;
        } else {
            $response['message'] = 'Database operation failed';
        }
    } else {
        $response['message'] = 'Missing required fields';
    }
    
    // Return JSON response
    echo json_encode($response);
    exit;
} else {
    // If not a POST request, redirect to the main page
    header('Location: (admin)manageaccount.php');
    exit;
}
?>