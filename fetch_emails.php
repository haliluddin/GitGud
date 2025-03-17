<?php 
// Include the Database class
require_once 'classes/db.php';

// Create a new Database instance and connect
$database = new Database();
$conn = $database->connect();

if (!$conn) {
    die("Connection failed");
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT id, email, profile_img FROM users JOIN verification ON users.id = verification.user_id WHERE email LIKE ? AND role != 'Park Owner' AND role != 'Stall Owner' AND verification.is_verified = 1 LIMIT 10"; // Exclude park owners and stall owners
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->execute([$searchTerm]); // PDO uses array for parameters
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$emails = [];
foreach ($result as $row) {
    $emails[] = [
        'id' => $row['id'], // Include user ID
        'email' => $row['email'],
        'text' => $row['email'],
        'profile_img' => $row['profile_img'] // Ensure this contains a valid image URL
    ];
}

echo json_encode($emails);
?>
