<?php
session_start();
require_once(__DIR__ . '/send_email_stall.php');
require_once(__DIR__ . '/../classes/db.class.php');

// Display form for testing
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Stall Invitation Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Test Stall Invitation Email</h1>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="owner_id" class="form-label">Owner ID (Food Park Owner):</label>
                        <input type="number" class="form-control" id="owner_id" name="owner_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="park_id" class="form-label">Park ID:</label>
                        <input type="number" class="form-control" id="park_id" name="park_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="user_id" class="form-label">User ID (Stall Owner to invite):</label>
                        <input type="number" class="form-control" id="user_id" name="user_id" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email (Stall Owner Email):</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name (Stall Owner):</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Invitation Email</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
    exit;
}

// Process form submission
$owner_id = $_POST['owner_id'];
$park_id = $_POST['park_id'];
$user_id = $_POST['user_id'];
$email = $_POST['email'];
$first_name = $_POST['first_name'];

// Get owner email
$db = new Database();
$sql = "SELECT email FROM users WHERE id = :owner_id";
$query = $db->connect()->prepare($sql);
$query->execute(array(':owner_id' => $owner_id));
$owner = $query->fetch();
$owner_email = $owner['email'];

// Send invitation email
$invitation = new StallInvitation();
$result = $invitation->createStallInvitation($owner_id, $park_id, $user_id, $email, $first_name);

if ($result === true) {
    echo '<div style="padding: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px;">';
    echo '<h4>Success!</h4>';
    echo '<p>Invitation email has been sent successfully to ' . htmlspecialchars($email) . '.</p>';
    echo '<p>The invitation link will direct the user to the stall registration page.</p>';
    echo '<p><a href="../index.php">Go back to homepage</a></p>';
    echo '</div>';
} else if (is_array($result) && isset($result['message']) && $result['message'] === 'cooldown') {
    echo '<div style="padding: 20px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 5px; margin: 20px;">';
    echo '<h4>Cooldown Period</h4>';
    echo '<p>An invitation email was recently sent. Please wait ' . htmlspecialchars($result['cd']) . ' seconds before sending another.</p>';
    echo '<p><a href="test_send_stall_invitation.php">Try again</a></p>';
    echo '</div>';
} else if (is_array($result) && isset($result['message']) && $result['message'] === 'already_registered') {
    echo '<div style="padding: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;">';
    echo '<h4>Already Registered</h4>';
    echo '<p>This user has already registered a stall in this food park.</p>';
    echo '<p><a href="test_send_stall_invitation.php">Try again with different details</a></p>';
    echo '</div>';
} else {
    echo '<div style="padding: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;">';
    echo '<h4>Error</h4>';
    echo '<p>Failed to send invitation email. Please check the server logs for details.</p>';
    echo '<p><a href="test_send_stall_invitation.php">Try again</a></p>';
    echo '</div>';
}
?>
