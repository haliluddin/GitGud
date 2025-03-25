<?php
require_once 'classes/admin.class.php';
require_once 'classes/db.class.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $adminObj = new Admin();

    $userData = $adminObj->getUserName($user_id);
    $profile_img = isset($userData['profile_img']) && !empty($userData['profile_img']) 
                   ? $userData['profile_img'] 
                   : 'assets/images/profile.jpg';
    
    $activities = $adminObj->getUserActivities($user_id);
    
    if (!empty($activities)) {
        $grouped = [];
        foreach ($activities as $activity) {
            $date = date("F j, Y", strtotime($activity['created_at']));
            $grouped[$date][] = $activity;
        }
        foreach ($grouped as $date => $acts) {
            echo '<div class="p-3 rounded-2 border mb-3">';
            echo '<h6 class="mb-2">' . htmlspecialchars($date) . '</h6>';
            foreach ($acts as $act) {
                echo '<div class="d-flex justify-content-between align-items-center actlog mb-2">';
                echo '<div class="d-flex gap-3">';
                echo '<img src="' . htmlspecialchars($profile_img) . '" width="65" height="65" style="border-radius: 50%">';
                echo '<div>';
                echo '<p class="m-0">' . htmlspecialchars($act['message']) . '</p>';
                echo '<p class="small text-muted m-0">' . htmlspecialchars($act['detail']) . '</p>';
                echo '<p class="small text-muted m-0">' . date("g:i A", strtotime($act['created_at'])) . '</p>';
                echo '</div>';
                echo '</div>';
                echo '<i class="fa-solid fa-check-double text-success"></i>';
                echo '</div>';
            }
            echo '</div>';
        }
    } else {
        echo '<p class="text-center py-5">No activity found</p>';
    }
} else {
    echo '<p class="text-center py-5">User not specified.</p>';
}
?>
