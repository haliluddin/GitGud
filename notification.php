<?php 

include_once 'links.php'; 
include_once 'header.php';
include_once 'modals.php';
require_once __DIR__ . '/classes/stall.class.php';

$stallObj = new Stall();
$notifications = $stallObj->getNotifications($user_id, $park_id);
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    .nav-main {
        padding: 20px 120px;
    }
</style>

<main class="nav-main">
    <div class="d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white mb-3 carttop">
        <h4 class="fw-bold mb-0">Notifications</h4>
        <div class="d-flex gap-4">

            <?php if(count($notifications) > 0): ?>
                <button id="deleteAllReadBtn">Delete all read</button>
            <?php endif; ?>

            <div id="markAllReadBtnContainer">
                <?php if(count($notifications) > 0): ?>
                    <button id="markAllReadBtn">Mark all as read</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div id="notifications-container">
        <?php if(count($notifications) > 0): ?>
            <?php foreach($notifications as $noti): 
                $readClass = ($noti['status'] === 'Read') ? ' read' : '';
            ?>
                <?php if(strpos($noti['message'], 'Payment Confirmed') !== false): ?>
                    <!-- Payment Confirmed Notification -->
                    <div class="notification d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white border-bottom<?php echo $readClass; ?> not-tm">
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
                <?php elseif(strpos($noti['message'], 'Pending Payment') !== false): ?>
                    <!-- Remind Payment Notification -->
                    <div class="notification d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white border-bottom<?php echo $readClass; ?> not-tm">
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
                <?php elseif(strpos($noti['message'], 'Ready to pickup') !== false || strpos($noti['message'], 'Preparing Order') !== false): ?>
                    <!-- Ready to Pickup / Preparing Order Notification -->
                    <div class="notification d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white border-bottom<?php echo $readClass; ?> not-tm">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="<?php echo htmlspecialchars($noti['logo']); ?>" width="85" height="85" alt="Notification">
                            <div>
                                <h5 class="fw-bold m-0"><?php echo htmlspecialchars($noti['message']); ?></h5>
                                <?php if(strpos($noti['message'], 'Ready to pickup') !== false): ?>
                                    <p class="my-1">Your order at <?php echo htmlspecialchars($noti['name']); ?> is ready for pickup. Please proceed to the counter with your receipt to collect your order</p>
                                <?php else: ?>
                                    <p class="my-1">Your order at <?php echo htmlspecialchars($noti['name']); ?> is now in preparation queue</p>
                                <?php endif; ?>
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
                    <div class="notification d-flex justify-content-between align-items-center border py-3 px-4 rounded-2 bg-white border-bottom<?php echo $readClass; ?> not-tm">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="assets/images/stall1.jpg" width="85" height="85" alt="Notification">
                            <div>
                                <h5 class="fw-bold m-0"><?php echo htmlspecialchars($noti['message']); ?></h5>
                                <span class="text-muted"><?php echo date("m/d/Y H:i", strtotime($noti['created_at'])); ?></span>
                            </div>
                        </div>
                        <button class="p-1 border bg-white small view-details-btn" onclick="window.location.href='purchase.php#';">View Details</button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="d-flex justify-content-center align-items-center border rounded-2 bg-white h-25 mb-3">
                No notification found.
            </div>
        <?php endif; ?>
    </div>
    <br><br><br><br><br>
</main>

<script>
    function fetchNotifications() {
        fetch('fetch_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('notifications-container').innerHTML = data.html;
                    
                    const markAllReadBtnContainer = document.getElementById('markAllReadBtnContainer');
                    if (data.count > 0) {
                        if (!document.getElementById('markAllReadBtn')) {
                            markAllReadBtnContainer.innerHTML = '<button id="markAllReadBtn">Mark all as read</button>';
                            attachMarkAllReadListener();
                        }
                    } else {
                        markAllReadBtnContainer.innerHTML = '';
                    }
                } else {
                    console.error('Error fetching notifications:', data.message);
                }
            })
            .catch(error => console.error('Request failed:', error));
    }

    function attachMarkAllReadListener() {
        const markAllReadBtn = document.getElementById('markAllReadBtn');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                fetch('mark_all_read.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'user_id=' + encodeURIComponent(<?php echo json_encode($user_id); ?>)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        fetchNotifications(); 
                    } else {
                        Swal.fire({icon: 'error', title: 'Notification Error', text: 'Error: ' + data.message, confirmButtonColor: '#CD5C08'});
                    }
                })
                .catch(error => Swal.fire({icon: 'error', title: 'Request Failed', text: 'Request failed: ' + error, confirmButtonColor: '#CD5C08'}));
            });
        }
    }

    attachMarkAllReadListener();

    setInterval(fetchNotifications, 3000);

    function attachDeleteAllReadListener() {
        const btn = document.getElementById('deleteAllReadBtn');
        if (!btn) return;
        btn.addEventListener('click', function() {
            fetch('delete_read.php', {
            method: 'POST',
            credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(data => {
            if (data.status === 'success') {
                fetchNotifications();  // reload the list so all Read items vanish
            } else {
                Swal.fire({icon: 'error', title: 'Notification Error', text: 'Error: ' + data.message, confirmButtonColor: '#CD5C08'});
            }
            })
            .catch(err => console.error('Delete failed:', err));
        });
        }

        // after attaching mark‐all listener, also:
        attachDeleteAllReadListener();

</script>

<?php 
include_once 'footer.php'; 
?>