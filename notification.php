<?php 

include_once 'links.php'; 
include_once 'header.php';
include_once 'modals.php';
require_once __DIR__ . '/classes/stall.class.php';

$stallObj = new Stall();
$notifications = $stallObj->getNotifications($user_id, $park_id);
?>

<style>
    main {
        padding: 20px 120px;
    }
</style>

<main>
    <div class="d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white mb-3 carttop">
        <h4 class="fw-bold mb-0">Notifications</h4>
        <button id="markAllReadBtn" class="btn btn-outline-primary btn-sm">Mark all as read</button>
    </div>
    
    <div id="notifications-container">
        <?php if(count($notifications) > 0): ?>
            <?php foreach($notifications as $noti): ?>
                <?php if(strpos($noti['message'], 'Payment Confirmed') !== false): ?>
                    <!-- Payment Confirmed Notification -->
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
                <?php elseif(strpos($noti['message'], 'Pending Payment') !== false): ?>
                    <!-- Remind Payment Notification -->
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
                <?php elseif(strpos($noti['message'], 'Ready to pickup') !== false || strpos($noti['message'], 'Preparing Order') !== false): ?>
                    <!-- Ready to Pickup / Preparing Order Notification -->
                    <div class="d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white border-bottom">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="<?php echo !empty($noti['logo']) ? htmlspecialchars($noti['logo']) : 'assets/images/stall1.jpg'; ?>" width="85" height="85" alt="Notification">
                            <div>
                                <h5 class="fw-bold m-0"><?php echo htmlspecialchars($noti['order_id'] . ': ' . $noti['title']); ?></h5>
                                <p class="my-1"><?php echo htmlspecialchars($noti['message']); ?></p>
                                <span class="text-muted"><?php echo date("m/d/Y H:i", strtotime($noti['created_at'])); ?></span>
                            </div>
                        </div>
                        <?php if(strpos($noti['message'], 'Ready to pickup') !== false): ?>
                            <button class="p-1 border bg-white small" onclick="window.location.href='purchase.php#toreceive';">View Details</button>
                        <?php else: ?>
                            <button class="p-1 border bg-white small" onclick="window.location.href='purchase.php#preparing';">View Details</button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <!-- Regular Notification -->
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
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No notifications found.</p>
        <?php endif; ?>
    </div>
</main>

<script>
    // Function to fetch notifications via AJAX
    function fetchNotifications() {
        fetch('fetch_notifications.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('notifications-container').innerHTML = data;
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }
    
    // Set up polling every 5 seconds
    setInterval(fetchNotifications, 5000);

    // Mark all as read functionality
    document.getElementById('markAllReadBtn').addEventListener('click', function(){
        fetch('mark_all_read.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'user_id=' + encodeURIComponent(<?php echo json_encode($user_id); ?>)
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success'){
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => alert("Request failed: " + error));
    });
</script>

<?php 
include_once 'footer.php'; 
?>
