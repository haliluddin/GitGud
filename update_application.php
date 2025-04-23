<?php
session_start();
require_once __DIR__ . '/classes/admin.class.php';
require_once __DIR__ . '/classes/db.class.php';

$adminObj = new Admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $application_id = $_POST['application_id'];
    $status = htmlspecialchars(trim($_POST['status']));
    $rejection_reason = null;
    
    if ($status === 'Rejected') {
        $reasons = [];
        
        // Process checkbox rejection reasons
        if (isset($_POST['rejection_reasons']) && is_array($_POST['rejection_reasons'])) {
            foreach ($_POST['rejection_reasons'] as $reason) {
                $reasons[] = htmlspecialchars(trim($reason));
            }
        }
        
        // Process additional rejection reason from textarea
        if (isset($_POST['rejection_reason']) && !empty(trim($_POST['rejection_reason']))) {
            $additional_reason = htmlspecialchars(trim($_POST['rejection_reason']));
            $reasons[] = "Additional details: " . $additional_reason;
        }
        
        // Combine all reasons if any exist
        if (!empty($reasons)) {
            $rejection_reason = implode(", ", $reasons);
        }
    }
    
    // Update application status in database
    $result = $adminObj->updateBusinessStatus($application_id, $status, $rejection_reason);
    
    if ($result) {
        // Success
        echo "<script>
            Swal.fire({icon: 'success', title: 'Success!', text: 'Application status updated!', confirmButtonColor: '#CD5C08'});
            window.location.href = '(admin)manageaccount.php#applications';
        </script>";
    } else {
        // Error
        echo "<script>
            Swal.fire({icon: 'error', title: 'Oops!', text: 'Couldn\'t update the application. Try again.', confirmButtonColor: '#CD5C08'});
            window.location.href = '(admin)manageaccount.php#applications';
        </script>";
    }
} else {
    // Invalid request
    header('Location: (admin)manageaccount.php#applications');
    exit();
}
?>
