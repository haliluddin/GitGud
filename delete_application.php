<?php
session_start();
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';

$adminObj = new Admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $application_id = $_POST['application_id'];
    
    // Delete application from database
    $result = $adminObj->deleteApplication($application_id);
    
    if ($result) {
        // Success
        echo "<script>
            alert('Application deleted successfully!');
            window.location.href = '(admin)manageaccount.php#applications';
        </script>";
    } else {
        // Error
        echo "<script>
            alert('Failed to delete application. Please try again.');
            window.location.href = '(admin)manageaccount.php#applications';
        </script>";
    }
} else {
    // Invalid request
    header('Location: (admin)manageaccount.php#applications');
    exit();
}
?>
