<?php
session_start();
require_once __DIR__ . '/send_email_stall.php';
require_once __DIR__ . '/../classes/db.class.php';
require_once __DIR__ . '/../classes/park.class.php';

// Set content type to JSON
header('Content-Type: application/json');

// Debug mode - uncomment to see detailed session info
// echo json_encode(['debug' => ['session' => $_SESSION]]); exit;

// Check if request is POST and has emails data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emails']) && is_array($_POST['emails'])) {
    // Get current park ID from session
    if (!isset($_SESSION['current_park_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'No park selected']);
        exit;
    }
    
    $park_id = $_SESSION['current_park_id'];
    
    // Get owner ID (current logged in user)
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in or invalid user data']);
        exit;
    }
    
    // Extract the user ID from the session user array
    $owner_id = $_SESSION['user']['id'];
    
    // Initialize response array
    $response = [
        'status' => 'success',
        'results' => []
    ];
    
    $db = new Database();
    $invitation = new StallInvitation();
    
    // Process each email
    foreach ($_POST['emails'] as $email_id) {
        // Get user details from the database
        $sql = "SELECT id, email, first_name FROM users WHERE id = :id";
        $query = $db->connect()->prepare($sql);
        $query->execute([':id' => $email_id]);
        $user = $query->fetch();
        
        if ($user) {
            $user_id = $user['id'];
            $email = $user['email'];
            $first_name = $user['first_name'];
            
            // Send invitation
            $result = $invitation->createStallInvitation($owner_id, $park_id, $user_id, $email, $first_name);
            
            if ($result === true) {
                $response['results'][] = [
                    'email' => $email,
                    'status' => 'success',
                    'message' => 'Invitation sent successfully'
                ];
            } else if (is_array($result) && isset($result['message'])) {
                if ($result['message'] === 'cooldown') {
                    $response['results'][] = [
                        'email' => $email,
                        'status' => 'warning',
                        'message' => 'Cooldown period, please wait ' . floor($result['cd'] / 60) . ' minutes'
                    ];
                } else if ($result['message'] === 'already_registered') {
                    $response['results'][] = [
                        'email' => $email,
                        'status' => 'warning',
                        'message' => 'User already has a stall in this food park'
                    ];
                }
            } else {
                $response['results'][] = [
                    'email' => $email,
                    'status' => 'error',
                    'message' => 'Failed to send invitation'
                ];
            }
        } else {
            $response['results'][] = [
                'email_id' => $email_id,
                'status' => 'error',
                'message' => 'User not found'
            ];
        }
    }
    
    // If any errors occurred, change overall status
    foreach ($response['results'] as $result) {
        if ($result['status'] === 'error') {
            $response['status'] = 'error';
            break;
        } else if ($result['status'] === 'warning' && $response['status'] !== 'error') {
            $response['status'] = 'warning';
        }
    }
    
    // Return JSON response
    echo json_encode($response);
} else {
    // Invalid request
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
