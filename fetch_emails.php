<?php 
require_once 'classes/db.php';
require_once 'classes/encdec.class.php';

$database = new Database();
$conn = $database->connect();

if (!$conn) {
    die("Connection failed");
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$park_id = isset($_GET['park_id']) ? decrypt(urldecode($_GET['park_id'])) : 0;

$sql = "SELECT u.id, u.email, u.profile_img 
        FROM users u
        JOIN verification v ON u.id = v.user_id 
        WHERE u.email LIKE ? 
        AND u.role NOT IN ('Admin', 'Park Owner') 
        AND v.is_verified = 1 
        AND u.id NOT IN (
            SELECT DISTINCT s.user_id 
            FROM stalls s 
            WHERE s.park_id = ?
        )
        LIMIT 10";

$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->execute([$searchTerm, $park_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$emails = [];
foreach ($result as $row) {
    $emails[] = [
        'id' => $row['id'], 
        'email' => $row['email'],
        'text' => $row['email'],
        'profile_img' => $row['profile_img']
    ];
}

echo json_encode($emails);
?>
