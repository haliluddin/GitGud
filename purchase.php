<?php  
include_once 'header.php'; 
include_once 'links.php'; 
//include_once 'modals.php'; 
include_once 'nav.php';
require_once __DIR__ . '/classes/stall.class.php';
require_once __DIR__ . '/classes/encdec.class.php';

$stallObj = new Stall();
$ordersData = $stallObj->getUserOrders($user_id, $park_id);

$groupedOrders = [];
foreach ($ordersData as $order) {
    $status = $order['order_status']; 
    if (!isset($groupedOrders[$status])) {
        $groupedOrders[$status] = [];
    }
    $osid = $order['order_stall_id'];
    if (!isset($groupedOrders[$status][$osid])) {
        $groupedOrders[$status][$osid] = [
            'order_id'        => $order['order_id'],
            'order_date'      => $order['order_date'],
            'stall_id'        => $order['stall_id'],
            'stall_name'      => $order['stall_name'],
            'stall_subtotal'  => $order['stall_subtotal'],
            'items'           => []
        ];
    }
    $groupedOrders[$status][$osid]['items'][] = $order;
}

$statusMapping = [
    'Pending'   => 'topay',      
    'Preparing' => 'preparing',
    'Ready'     => 'toreceive',
    'Completed' => 'completed',
    'Canceled'  => 'canceled'
];
?>
<style>
    main { padding: 20px 120px; }
</style>
<script> const userId = <?php echo $user['user_session']; ?>; </script>
<main>
    <!-- Navigation -->
    <div class="nav-container d-flex gap-3 my-2">
        <a href="#all" class="nav-link" data-target="all">All</a>
        <a href="#topay" class="nav-link" data-target="topay">To Pay</a>
        <a href="#preparing" class="nav-link" data-target="preparing">Preparing</a>
        <a href="#toreceive" class="nav-link" data-target="toreceive">To Receive</a>
        <a href="#completed" class="nav-link" data-target="completed">Completed</a>
        <a href="#canceled" class="nav-link" data-target="canceled">Canceled</a>
    </div>

    <!-- All Orders Section -->
    <div id="all" class="section-content">
        <?php 
        foreach ($groupedOrders as $status => $ordersGroup) {
            foreach ($ordersGroup as $osid => $orderGroup) {
                $formattedOrderId = str_pad($orderGroup['order_id'], 4, '0', STR_PAD_LEFT);
                $formattedDate = date("m/d/Y H:i", strtotime($orderGroup['order_date']));
                $displayStatus = ($status == 'Pending') ? "TO PAY" :
                                 (($status == 'Preparing') ? "PREPARING" :
                                 (($status == 'Ready') ? "TO RECEIVE" :
                                 (($status == 'Completed') ? "COMPLETED" : "CANCELED")));
                ?>
                <div class="border py-3 px-4 rounded-2 bg-white mb-3">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <div class="d-flex gap-3 align-items-center">
                            <span class="fw-bold">ORDER ID: <?php echo $formattedOrderId; ?></span>
                            <span class="dot text-muted"></span>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="fw-bold"><?php echo htmlspecialchars($orderGroup['stall_name']); ?></span> 
                                <button class="viewstall border bg-white small px-2" onclick="window.location.href='stall.php?id=<?= encrypt($orderGroup['stall_id']); ?>';">View Stall</button>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <span style="color: #6A9C89" class="small"><?php echo $formattedDate; ?></span>
                            <span class="dot text-muted"></span>
                            <span class="fw-bold" style="color: #CD5C08"><?php echo $displayStatus; ?></span>
                        </div>
                    </div>
                    <?php 
                    foreach ($orderGroup['items'] as $item) { ?>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div class="d-flex gap-3 align-items-center">
                                <img src="<?php echo htmlspecialchars($item['product_image']); ?>" width="85px" height="85px" class="border rounded-2">
                                <div>
                                    <span class="fs-5"><?php echo htmlspecialchars($item['product_name']); ?></span><br>
                                    <?php if (!empty($item['variations'])): ?>
                                        <span class="small text-muted">Variation: <?php echo htmlspecialchars($item['variations']); ?></span><br>
                                    <?php endif; ?>
                                    <?php if (!empty($item['request'])): ?>
                                        <span class="small text-muted">"<?php echo htmlspecialchars($item['request']); ?>"</span><br>
                                    <?php endif; ?>
                                    <span>x<?php echo $item['quantity']; ?></span>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-end">
                                <span class="fw-bold">₱<?php echo number_format($item['item_subtotal'], 2); ?></span>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="d-flex justify-content-between pt-2">
                        <div class="d-flex gap-3 align-items-center text-muted small">
                            <span>Payment Method: <?php echo $item['payment_method']; ?></span>
                            <span class="dot text-muted"></span>
                            <span>Order Type: <?php echo $item['order_type']; ?></span>
                        </div>
                        <div class="d-flex gap-4 align-items-center">
                            <?php if($status == 'Pending'): ?>
                                <button class="cancelorder-btn cancelorder rounded-2" data-order-stall-id="<?php echo $osid; ?>" data-bs-toggle="modal" data-bs-target="#cancelorder">Cancel Order</button>
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                            <?php if($status == 'Preparing'): ?>
                                <button class="preparing rounded-2">Preparing</button>                                
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                            <?php if($status == 'Ready'): ?>
                                <button class="order-received-btn cancelorder rounded-2" data-order-stall-id="<?php echo $osid; ?>" data-new-status="Completed" data-bs-toggle="modal" data-bs-target="#orderreceived">Order Received</button>
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                            <?php if($status == 'Completed'): ?>
                                <button class="likeorder rounded-2"><i class="fa-regular fa-heart me-2"></i>Like</button>                             
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                            <?php if($status == 'Canceled'): ?>
                                <button class="likeorder rounded-2"><i class="fa-regular fa-heart me-2"></i>Like</button>                               
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                            <div class="d-flex gap-3 align-items-center">
                                <span class="text-muted">Sub Total:</span>
                                <span class="fw-bold fs-4">₱<?php echo number_format($orderGroup['stall_subtotal'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
            }
        }
        ?>
    </div>

    <!-- Individual Status Sections -->
    <?php 
    foreach ($statusMapping as $status => $sectionId) {
        echo '<div id="'.$sectionId.'" class="section-content '. (empty($groupedOrders[$status]) ? 'd-none' : '') .'">';
        if (!empty($groupedOrders[$status])) {
            foreach ($groupedOrders[$status] as $osid => $orderGroup) { 
                $formattedOrderId = str_pad($orderGroup['order_id'], 4, '0', STR_PAD_LEFT);
                $formattedDate = date("m/d/Y H:i", strtotime($orderGroup['order_date']));
                $displayStatus = ($status == 'Pending') ? "TO PAY" :
                                 (($status == 'Preparing') ? "PREPARING" :
                                 (($status == 'Ready') ? "TO RECEIVE" :
                                 (($status == 'Completed') ? "COMPLETED" : "CANCELED")));
                ?>
                <div class="border py-3 px-4 rounded-2 bg-white mb-3">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <div class="d-flex gap-3 align-items-center">
                            <span class="fw-bold">ORDER ID: <?php echo $formattedOrderId; ?></span>
                            <span class="dot text-muted"></span>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="fw-bold"><?php echo htmlspecialchars($orderGroup['stall_name']); ?></span>
                                <button class="viewstall border bg-white small px-2" onclick="window.location.href='stall.php?stall_id=<?php echo $orderGroup['stall_id']; ?>';">View Stall</button>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <span style="color: #6A9C89" class="small"><?php echo $formattedDate; ?></span>
                            <span class="dot text-muted"></span>
                            <span class="fw-bold" style="color: #CD5C08"><?php echo $displayStatus; ?></span>
                        </div>
                    </div>
                    <?php 
                    foreach ($orderGroup['items'] as $item) { ?>
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div class="d-flex gap-3 align-items-center">
                                <img src="<?php echo htmlspecialchars($item['product_image']); ?>" width="85px" height="85px" class="border rounded-2">
                                <div>
                                    <span class="fs-5"><?php echo htmlspecialchars($item['product_name']); ?></span><br>
                                    <?php if (!empty($item['variations'])): ?>
                                        <span class="small text-muted">Variation: <?php echo htmlspecialchars($item['variations']); ?></span><br>
                                    <?php endif; ?>
                                    <?php if (!empty($item['request'])): ?>
                                        <span class="small text-muted">"<?php echo htmlspecialchars($item['request']); ?>"</span><br>
                                    <?php endif; ?>
                                    <span>x<?php echo $item['quantity']; ?></span>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-end">
                                <span class="fw-bold">₱<?php echo number_format($item['item_subtotal'], 2); ?></span>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="d-flex justify-content-between pt-2">
                        <div class="d-flex gap-3 align-items-center text-muted small">
                            <span>Payment Method: <?php echo $item['payment_method']; ?></span>
                            <span class="dot text-muted"></span>
                            <span>Order Type: <?php echo $item['order_type']; ?></span>
                        </div>

                        <div class="d-flex gap-4 align-items-center">
                            <?php if($status == 'Pending'): ?>
                                <button class="cancelorder-btn cancelorder rounded-2" data-order-stall-id="<?php echo $osid; ?>" data-bs-toggle="modal" data-bs-target="#cancelorder">Cancel Order</button>
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                            <?php if($status == 'Preparing'): ?>
                                <button class="preparing rounded-2">Preparing</button>                                
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                            <?php if($status == 'Ready'): ?>
                                <button class="order-received-btn cancelorder rounded-2" data-order-stall-id="<?php echo $osid; ?>" data-new-status="Completed" data-bs-toggle="modal" data-bs-target="#orderreceived">Order Received</button>
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                            <?php if($status == 'Completed'): ?>
                                <button class="likeorder rounded-2"><i class="fa-regular fa-heart me-2"></i>Like</button>                             
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                            <?php if($status == 'Canceled'): ?>
                                <button class="likeorder rounded-2"><i class="fa-regular fa-heart me-2"></i>Like</button>                               
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                            <div class="d-flex gap-3 align-items-center">
                                <span class="text-muted">Sub Total:</span>
                                <span class="fw-bold fs-4">₱<?php echo number_format($orderGroup['stall_subtotal'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
            }
        } else {
            echo "<p>No orders in this section.</p>";
        }
        echo '</div>';
    }
    ?>

    <!-- Order Received Modal (existing) -->
    <div class="modal fade" id="orderreceived" tabindex="-1" aria-labelledby="orderreceivedLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="text-center">
                        <h4 class="fw-bold mb-4"><i class="fa-solid fa-circle-check"></i> Received Order</h4>
                        <span>Mark this order as received?</span>
                        <div class="mt-5 mb-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary" id="orderReceivedYesBtn" data-order-id="" data-new-status="">Yes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelorder" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"> 
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="modal-title m-0 fw-bold">Select Cancellation Reason</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="reminder p-3 my-3">
                        <i class="fa-solid fa-circle-exclamation me-1"></i> Please take note that this will cancel all items in the order and the action cannot be undone.
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancelReason" id="reason1" value="Need to modify order">
                        <label class="form-check-label" for="reason1">Need to modify order</label>
                    </div><br>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancelReason" id="reason2" value="Payment procedure too troublesome">
                        <label class="form-check-label" for="reason2">Payment procedure too troublesome</label>
                    </div><br>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancelReason" id="reason3" value="Found cheaper elsewhere">
                        <label class="form-check-label" for="reason3">Found cheaper elsewhere</label>
                    </div><br>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancelReason" id="reason4" value="Don't want to buy anymore">
                        <label class="form-check-label" for="reason4">Don't want to buy anymore</label>
                    </div><br>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancelReason" id="reason5" value="Others">
                        <label class="form-check-label" for="reason5">Others</label>
                    </div>

                    <div class="text-center mt-4">
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Close</button>
                        <button type="button" class="btn btn-primary" id="cancelOrderYesBtn">Cancel Order</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bind cancel order button(s) on purchase.php
    document.querySelectorAll('.cancelorder-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var orderStallId = this.getAttribute('data-order-stall-id');
            var modalYesBtn = document.getElementById('cancelOrderYesBtn');
            modalYesBtn.setAttribute('data-order-id', orderStallId);
            modalYesBtn.setAttribute('data-new-status', 'Canceled');
        });
    });

    document.getElementById('cancelOrderYesBtn').addEventListener('click', function() {
        var orderStallId = this.getAttribute('data-order-id');
        var newStatus = this.getAttribute('data-new-status');
        
        var selectedRadio = document.querySelector('input[name="cancelReason"]:checked');
        var cancelReason = selectedRadio ? selectedRadio.value : '';
        
        if (!orderStallId || !newStatus) {
            alert("Missing order information.");
            return;
        }
        
        if (newStatus === 'Canceled' && cancelReason === '') {
            alert("Please select a cancellation reason.");
            return;
        }
        
        var postBody = 'order_stall_id=' + encodeURIComponent(orderStallId) +
                    '&new_status=' + encodeURIComponent(newStatus) +
                    (cancelReason ? '&cancel_reason=' + encodeURIComponent(cancelReason) : '');
        
        fetch('update_order_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: postBody
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => alert("Request failed: " + error));
    });

});
</script>
<script src="./assets/js/navigation.js?v=<?php echo time(); ?>"></script>
<?php include_once './footer.php'; ?>
