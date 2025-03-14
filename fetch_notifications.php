<?php
session_start();

require_once __DIR__ . '/classes/stall.class.php';

$user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
$park_id = isset($_SESSION['current_park_id']) ? $_SESSION['current_park_id'] : null;

if (!$user_id || !$park_id) {
    echo '<div class="alert alert-info">No notifications available.</div>';
    exit;
}

$stallClassObj = new Stall();
$notifications = $stallClassObj->getNotifications($user_id, $park_id);

if ($notifications) {
    foreach ($notifications as $noti) {
        if (strpos($noti['message'], 'Payment Confirmed') !== false) {
            // Payment Confirmed Notification
            ?>
            <div class="d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white mb-3">
                <div class="d-flex gap-3 align-items-center">
                    <img src="assets/images/gitgud.png" width="85" height="85" alt="Notification">
                    <div>
                        <h5 class="fw-bold m-0"><?php echo htmlspecialchars($noti['order_id'] . ': ' . $noti['title']); ?></h5>
                        <p class="my-1"><?php echo htmlspecialchars($noti['message']); ?></p>
                        <span class="text-muted"><?php echo date("m/d/Y H:i", strtotime($noti['created_at'])); ?></span>
                    </div>
                </div>
                <button class="p-1 border bg-white small" onclick="window.location.href='receipt.php?order_id=<?php echo $noti['order_id']; ?>';">View Receipt</button>
            </div>
            <?php
        } elseif (strpos($noti['message'], 'Pending Payment') !== false) {
            // Remind Payment Notification
            ?>
            <div class="d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white border-bottom">
                <div class="d-flex gap-3 align-items-center">
                    <img src="<?php echo !empty($noti['logo']) ? htmlspecialchars($noti['logo']) : 'assets/images/stall1.jpg'; ?>" width="85" height="85" alt="Notification">
                    <div>
                        <h5 class="fw-bold m-0"><?php echo htmlspecialchars($noti['order_id'] . ': ' . $noti['title']); ?></h5>
                        <p class="my-1"><?php echo htmlspecialchars($noti['message']); ?></p>
                        <span class="text-muted"><?php echo date("m/d/Y H:i", strtotime($noti['created_at'])); ?></span>
                    </div>
                </div>
                <button class="p-1 border bg-white small" onclick="window.location.href='purchase.php#toreceive';">View Details</button>
            </div>
            <?php
        } elseif (strpos($noti['message'], 'Ready to pickup') !== false || strpos($noti['message'], 'Preparing Order') !== false) {
            // Ready to Pickup / Preparing Order Notification
            ?>
            <div class="d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white border-bottom">
                <div class="d-flex gap-3 align-items-center">
                    <img src="<?php echo !empty($noti['logo']) ? htmlspecialchars($noti['logo']) : 'assets/images/stall1.jpg'; ?>" width="85" height="85" alt="Notification">
                    <div>
                        <h5 class="fw-bold m-0"><?php echo htmlspecialchars($noti['order_id'] . ': ' . $noti['title']); ?></h5>
                        <p class="my-1"><?php echo htmlspecialchars($noti['message']); ?></p>
                        <span class="text-muted"><?php echo date("m/d/Y H:i", strtotime($noti['created_at'])); ?></span>
                    </div>
                </div>
                <?php if (strpos($noti['message'], 'Ready to pickup') !== false): ?>
                    <button class="p-1 border bg-white small" onclick="window.location.href='purchase.php#toreceive';">View Details</button>
                <?php else: ?>
                    <button class="p-1 border bg-white small" onclick="window.location.href='purchase.php#preparing';">View Details</button>
                <?php endif; ?>
            </div>
            <?php
        } else {
            // Regular Notification
            ?>
            <div class="d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white border-bottom">
                <div class="d-flex gap-3 align-items-center">
                    <img src="assets/images/stall1.jpg" width="85" height="85" alt="Notification">
                    <div>
                        <h5 class="fw-bold m-0"><?php echo htmlspecialchars($noti['order_id'] . ': ' . $noti['title']); ?></h5>
                        <p class="my-1"><?php echo htmlspecialchars($noti['message']); ?></p>
                        <span class="text-muted"><?php echo date("m/d/Y H:i", strtotime($noti['created_at'])); ?></span>
                    </div>
                </div>
                <button class="p-1 border bg-white small view-details-btn" onclick="window.location.href='purchase.php#';">View Details</button>
            </div>
            <?php
        }
    }
} else {
    echo '<p>No notifications found.</p>';
}
?>
