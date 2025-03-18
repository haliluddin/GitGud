<?php
// Include required files
$dbPath = __DIR__ . '/../classes/db.php';
if (!file_exists($dbPath)) {
    die("Error: The file $dbPath does not exist.");
}
require_once($dbPath);

require_once(__DIR__ . '/../vendor/autoload.php');
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    die('PHPMailer class is not loaded. Please check the autoloader and ensure PHPMailer is installed.');
}

require_once(__DIR__ . '/../classes/encdec.class.php');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has permissions
if (!isset($_SESSION['user']['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'You need to be logged in to perform this action'
    ]);
    exit;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Check if required data is present
if (!isset($_POST['users']) || !isset($_POST['park_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

// Get owner ID from session
$owner_id = $_SESSION['user']['id'];
$park_id = $_POST['park_id'];

// Process users data
$users = json_decode(stripslashes(is_string($_POST['users']) ? $_POST['users'] : json_encode($_POST['users'])), true);

if (!$users || !is_array($users)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid user data format'
    ]);
    exit;
}

// Database connection for user information
$db = new Database();

// Prepare response
$response = [
    'success' => true,
    'message' => 'URLs generated successfully',
    'urls' => []
];

// Process each user
foreach ($users as $user) {
    if (!isset($user['id']) || !isset($user['email'])) {
        $response['urls'][] = [
            'email' => $user['email'] ?? 'Unknown',
            'message' => 'Missing user ID or email',
            'success' => false
        ];
        continue;
    }

    $user_id = $user['id'];
    $email = $user['email'];

    // Get user's first name
    $sql = "SELECT first_name FROM users WHERE id = :user_id";
    $query = $db->connect()->prepare($sql);
    $query->execute([':user_id' => $user_id]);
    $userData = $query->fetch();

    if (!$userData) {
        $response['urls'][] = [
            'email' => $email,
            'message' => 'User not found in database',
            'success' => false
        ];
        continue;
    }

    // Generate token for the invitation
    $token = uniqid();
    
    // Check if there's an existing invitation on cooldown
    $sql = "SELECT * FROM stall_invitations WHERE user_id = :user_id AND park_id = :park_id";
    $query = $db->connect()->prepare($sql);
    $query->execute([':user_id' => $user_id, ':park_id' => $park_id]);
    $existing = $query->fetch();
    
    if ($existing) {
        // Check if it's already used
        if ($existing['is_used'] == 1) {
            $response['urls'][] = [
                'email' => $email,
                'message' => 'User already has a stall registered in this park',
                'success' => false
            ];
            continue;
        }
        
        // Check for cooldown
        $current_time = time();
        $last_sent = $existing['last_sent'];
        $difference = $current_time - $last_sent;
        $cd = 300; // 5 minutes cooldown
        
        if ($difference < $cd) {
            $response['urls'][] = [
                'email' => $email,
                'message' => 'Invitation on cooldown. Try again in ' . floor(($cd - $difference) / 60) . ' minutes',
                'success' => false
            ];
            continue;
        }
    }
    
    // Make sure all parameters are strings before encrypting
    $encrypted_token = urlencode(encrypt((string)$token));
    $encrypted_owner_email = urlencode(encrypt((string)$email));
    $encrypted_owner_id = urlencode(encrypt((string)$owner_id));
    $encrypted_park_id = urlencode(encrypt((string)$park_id));
    $encrypted_user_id = urlencode(encrypt((string)$user_id));
    
    // Generate the URL for redirection
    $redirectUrl = "stallregistration.php?oe={$encrypted_owner_email}&oi={$encrypted_owner_id}&pi={$encrypted_park_id}&token={$encrypted_token}&id={$encrypted_user_id}";
    
    // Store token in database for validation
    $expiration = date('Y-m-d H:i:s', strtotime('+7 days'));
    
    if ($existing) {
        // Update existing invitation
        $sql = "UPDATE stall_invitations SET 
                invitation_token = :token, 
                token_expiration = :expiration,
                last_sent = :last_sent 
                WHERE user_id = :user_id AND park_id = :park_id";
                
        $query = $db->connect()->prepare($sql);
        $result = $query->execute([
            ':token' => $token,
            ':expiration' => $expiration,
            ':last_sent' => time(),
            ':user_id' => $user_id,
            ':park_id' => $park_id
        ]);
    } else {
        // Create new invitation
        $sql = "INSERT INTO stall_invitations (user_id, park_id, invitation_token, token_expiration, last_sent) 
                VALUES (:user_id, :park_id, :token, :expiration, :last_sent)";
                
        $query = $db->connect()->prepare($sql);
        $result = $query->execute([
            ':user_id' => $user_id,
            ':park_id' => $park_id,
            ':token' => $token,
            ':expiration' => $expiration,
            ':last_sent' => time()
        ]);
    }
    
    if (!$result) {
        $response['urls'][] = [
            'email' => $email,
            'message' => 'Database error: ' . json_encode($query->errorInfo()),
            'success' => false
        ];
        continue;
    }
    
    // Add URL to response
    $response['urls'][] = [
        'email' => $email,
        'message' => 'URL generated successfully',
        'url' => $redirectUrl,
        'success' => true
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;