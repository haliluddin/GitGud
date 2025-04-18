<?php
session_start();

require_once __DIR__ . '/classes/stall.class.php';

$user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;
$park_id = isset($_SESSION['current_park_id']) ? $_SESSION['current_park_id'] : null;

if (!$user_id || !$park_id) {
    echo json_encode(['status' => 'error', 'message' => 'No user or park ID found']);
    exit;
}

$stallObj = new Stall();
$notifications = $stallObj->getNotifications($user_id, $park_id);

ob_start();

if (count($notifications) > 0) {
    foreach ($notifications as $noti) {
        $readClass = ($noti['status'] === 'Read') ? ' read' : '';
        if (strpos($noti['message'], 'Payment Confirmed') !== false) {
            // Payment Confirmed Notification
            ?>
            <div class="notification d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white border-bottom<?php echo $readClass; ?>">
                <div class="d-flex gap-3 align-items-center">
                    <img src="assets/images/gitgud.png" width="85" height="85" alt="Notification">
                    <div>
                        <h5 class="fw-bold m-0"><?php echo htmlspecialchars($noti['message']); ?></h5>
                        <p class="my-1">All your payments have been successfully confirmed! Here's your receipt—click to view and download</p>
                        <span class="text-muted"><?php echo date("m/d/Y H:i", strtotime($noti['created_at'])); ?></span>
                    </div>
                </div>
                <button class="p-1 border bg-white small" onclick="window.open('receipt.php?order_id=<?php echo $noti['order_id']; ?>', '_blank');">View Receipt</button>
            </div>
            <?php
        } elseif (strpos($noti['message'], 'Pending Payment') !== false) {
            // Remind Payment Notification
            ?>
            <div class="notification d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white border-bottom<?php echo $readClass; ?>">
                <div class="d-flex gap-3 align-items-center">
                    <img src="<?php echo htmlspecialchars($noti['logo']); ?>" width="85" height="85" alt="Notification">
                    <div>
                        <h5 class="fw-bold m-0"><?php echo htmlspecialchars($noti['message']); ?></h5>
                        <p class="my-1">Your order at <?php echo htmlspecialchars($noti['name']); ?> is awaiting payment. Please go to the stall to pay.</p>
                        <span class="text-muted"><?php echo date("m/d/Y H:i", strtotime($noti['created_at'])); ?></span>
                    </div>
                </div>
                <button class="p-1 border bg-white small" onclick="window.location.href='purchase.php#toreceive';">View Details</button>
            </div>
            <?php
        } elseif (strpos($noti['message'], 'Ready to pickup') !== false || strpos($noti['message'], 'Preparing Order') !== false) {
            // Ready to Pickup / Preparing Order Notification
            ?>
            <div class="notification d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white border-bottom<?php echo $readClass; ?>">
                <div class="d-flex gap-3 align-items-center">
                    <img src="<?php echo htmlspecialchars($noti['logo']); ?>" width="85" height="85" alt="Notification">
                    <div>
                        <h5 class="fw-bold m-0"><?php echo htmlspecialchars($noti['message']); ?></h5>
                        <?php if (strpos($noti['message'], 'Ready to pickup') !== false): ?>
                            <p class="my-1">Your order at <?php echo htmlspecialchars($noti['name']); ?> is ready for pickup. Please proceed to the counter with your receipt to collect your order</p>
                        <?php else: ?>
                            <p class="my-1">Your order at <?php echo htmlspecialchars($noti['name']); ?> is now in preparation queue</p>
                        <?php endif; ?>
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
            <div class="notification d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white border-bottom<?php echo $readClass; ?>">
                <div class="d-flex gap-3 align-items-center">
                    <img src="assets/images/stall1.jpg" width="85" height="85" alt="Notification">
                    <div>
                        <h5 class="fw-bold m-0"><?php echo htmlspecialchars($noti['message']); ?></h5>
                        <span class="text-muted"><?php echo date("m/d/Y H:i", strtotime($noti['created_at'])); ?></span>
                    </div>
                </div>
                <button class="p-1 border bg-white small view-details-btn" onclick="window.location.href='purchase.php#';">View Details</button>
            </div>
            <?php
        }
    }
} else {
    ?>
    <div class="d-flex justify-content-center align-items-center border rounded-2 bg-white h-25 mb-3">
        No notification found.
    </div>
    <?php
}

$html = ob_get_clean();

echo json_encode([
    'status' => 'success',
    'html' => $html,
    'count' => count($notifications)
]);
?>
