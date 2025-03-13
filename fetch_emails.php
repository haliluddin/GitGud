<?php 
$servername = "localhost";
$username = "root"; // Change this to your DB username
$password = ""; // Change this to your DB password
$database = "gitgud"; // Your database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT id, email, profile_img FROM users WHERE email LIKE ? LIMIT 10"; // Fetch user ID as well
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$emails = [];
while ($row = $result->fetch_assoc()) {
    $emails[] = [
        'id' => $row['id'], // Include user ID
        'email' => $row['email'],
        'text' => $row['email'],
        'profile_img' => $row['profile_img'] // Ensure this contains a valid image URL
    ];
}

echo json_encode($emails);
?>
