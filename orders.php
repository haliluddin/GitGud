<?php 
include_once 'header.php'; 
include_once 'links.php'; 
include_once 'nav.php';
require_once __DIR__ . '/classes/stall.class.php';

$stallObj = new Stall();

if ($user['role'] === 'Admin' && isset($_GET['stall_id'])) {
    $stall_id = intval($_GET['stall_id']);
} else {
    $stall_id = $stallObj->getStallId(
        $_SESSION['user']['id'],
        $_SESSION['current_park_id']
    );
}

$ordersData = $stallObj->getStallOrders($stall_id);

$groupedOrders = [];
foreach ($ordersData as $order) {
    $status = $order['order_status'];
    if (!isset($groupedOrders[$status])) {
        $groupedOrders[$status] = [];
    }
    $osid = $order['order_stall_id'];
    if (!isset($groupedOrders[$status][$osid])) {
        $groupedOrders[$status][$osid] = [
            'order_id'             => $order['order_id'],
            'order_date'           => $order['order_date'],
            'stall_subtotal'       => $order['stall_subtotal'],
            'queue_number'         => $order['queue_number'],
            'cancellation_reason'  => $order['cancellation_reason'] ?? '',
            'items'                => []
        ];
        
    }
    $groupedOrders[$status][$osid]['items'][] = $order;
}

$statusMapping = [
    'Pending'   => 'pendingpayment',
    'Preparing' => 'preparing',
    'Ready'     => 'readyforpickup',
    'Completed' => 'completed',
    'Canceled'  => 'canceled'
];

?>
<style>
    .nav-main{
        padding: 20px 120px;
    }
    .orderbtns button{
        background-color: #CD5C08;
        color: white;
        border: none;
        padding: 6px 0;
        width: 200px;
    }
    .orderbtns button:hover {
        transform: scale(1.05); 
        filter: brightness(1.1); 
    }
    .prequeue{
        border-radius: 50%;
        width: 35px;
        height: 35px;
        background-color: gray;
    }
</style>
<main class="nav-main">
    <!-- Navigation -->
    <div class="nav-container d-flex gap-3 my-2 flex-wrap">
        <a href="#all" class="nav-link" data-target="all">All</a>
        <a href="#pendingpayment" class="nav-link" data-target="pendingpayment">Pending Payment</a>
        <a href="#preparing" class="nav-link" data-target="preparing">Preparing</a>
        <a href="#readyforpickup" class="nav-link" data-target="readyforpickup">Ready for Pickup</a>
        <a href="#completed" class="nav-link" data-target="completed">Completed</a>
        <a href="#canceled" class="nav-link" data-target="canceled">Canceled</a>
    </div>

    <div id="all" class="section-content">
        <?php 
        if (empty($groupedOrders)) {
            echo '<div class="d-flex justify-content-center align-items-center border rounded-2 bg-white h-25 mb-3">
                    No orders in this section.
                </div>';
        } else {
            foreach ($groupedOrders as $status => $ordersGroup) {
                foreach ($ordersGroup as $osid => $orderGroup) { 
                    $formattedOrderId = str_pad($orderGroup['order_id'], 4, '0', STR_PAD_LEFT);
                    $formattedDate = date("m/d/Y H:i", strtotime($orderGroup['order_date']));
                    $displayStatus = ($status == 'Pending') ? "PENDING PAYMENT" :
                                    (($status == 'Preparing') ? "PREPARING" :
                                    (($status == 'Ready') ? "READY FOR PICKUP" :
                                    (($status == 'Completed') ? "COMPLETED" : "CANCELED")));
                    ?>
                    <div class="border rounded-2 bg-white mb-3 d-flex fw-tm">
                        <div class="flex-grow-1 border-end">
                            <div class="d-flex justify-content-between align-items-center border-bottom py-3 px-5 fw-tm">
                                <div class="d-flex gap-3 align-items-center">
                                    <?php if (!empty($orderGroup['queue_number'])): ?>
                                        <span class="prequeue fw-bold text-white d-flex align-items-center justify-content-center">
                                            <?php echo str_pad($orderGroup['queue_number'], 2, '0', STR_PAD_LEFT); ?>
                                        </span>
                                        <span class="dot text-muted"></span>
                                    <?php endif; ?>
                                    <span class="fw-bold">ORDER ID: <?php echo $formattedOrderId; ?></span>
                                </div>
                                <div class="d-flex gap-3 align-items-center">
                                    <span style="color: #6A9C89" class="small"><?php echo $formattedDate; ?></span>
                                    <span class="dot text-muted"></span>
                                    <span class="fw-bold" style="color: #CD5C08"><?php echo $displayStatus; ?></span>
                                </div>
                            </div>
                            <?php 
                            foreach ($orderGroup['items'] as $item) { ?>
                                <div class="d-flex justify-content-between border-bottom py-2 px-5 fw-tm">
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
                            <div class="d-flex justify-content-between py-2 px-5 fw-tm">
                                <div class="d-flex gap-3 align-items-center text-muted small">
                                    <span>Payment Method: <?php echo $item['payment_method']; ?></span>
                                    <span class="dot text-muted"></span>
                                    <span>Order Type: <?php echo $item['order_type']; ?></span>
                                </div>
                                <div class="d-flex gap-3 align-items-center">
                                    <span class="text-muted">Total:</span>
                                    <span class="fw-bold fs-4">₱<?php echo number_format($orderGroup['stall_subtotal'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-4 justify-content-center align-items-center flex-shrink-0 w-25 orderbtns">
                            <?php if($status == 'Pending'): ?>
                                <button class="rounded-2 prepare-order-btn" data-order-stall-id="<?php echo $osid; ?>" data-new-status="Preparing" data-bs-toggle="modal" data-bs-target="#prepareorder">Prepare Order</button>
                                <button class="rounded-2 cancelorder-btn" style="background-color: #6A9C89;" data-order-stall-id="<?php echo $osid; ?>" data-bs-toggle="modal" data-bs-target="#cancelorder">Cancel Order</button>
                                <button class="rounded-2 remind-payment-btn" data-order-stall-id="<?php echo $osid; ?>" data-action="remind_payment" style="background-color: gray;">Remind Payment</button>
                            <?php elseif($status == 'Preparing'): ?>
                                <button class="rounded-2 order-ready-btn" data-order-stall-id="<?php echo $osid; ?>" data-new-status="Ready" data-bs-toggle="modal" data-bs-target="#orderready">Order Ready</button>
                            <?php elseif($status == 'Ready'): ?>
                                <button class="rounded-2 order-complete-btn" data-order-stall-id="<?php echo $osid; ?>" data-new-status="Completed" data-bs-toggle="modal" data-bs-target="#ordercomplete">Order Complete</button>
                                <button class="rounded-2 notify-customer-btn" data-order-stall-id="<?php echo $osid; ?>" data-action="notify_customer" style="background-color: #6A9C89;">Notify Customer</button>
                            <?php elseif($status == 'Completed'): ?>
                                <span class="text-muted">Completed</span>
                            <?php elseif($status == 'Canceled'): ?>
                                <span class="text-muted text-center">Reason<br>(<?php echo htmlspecialchars($orderGroup['cancellation_reason']); ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php 
                }
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
                $displayStatus = ($status == 'Pending') ? "PENDING PAYMENT" :
                                 (($status == 'Preparing') ? "PREPARING" :
                                 (($status == 'Ready') ? "READY FOR PICKUP" :
                                 (($status == 'Completed') ? "COMPLETED" : "CANCELED")));
                ?>
                <div class="border rounded-2 bg-white mb-3 d-flex fw-tm">
                    <div class="flex-grow-1 border-end">
                        <div class="d-flex justify-content-between align-items-center border-bottom py-3 px-5 fw-tm">
                            <div class="d-flex gap-3 align-items-center">
                                <?php if (!empty($orderGroup['queue_number'])): ?>
                                    <span class="prequeue fw-bold text-white d-flex align-items-center justify-content-center">
                                        <?php echo str_pad($orderGroup['queue_number'], 2, '0', STR_PAD_LEFT); ?>
                                    </span>
                                    <span class="dot text-muted"></span>
                                <?php endif; ?>
                                <span class="fw-bold">ORDER ID: <?php echo $formattedOrderId; ?></span>
                            </div>

                            <div class="d-flex gap-3 align-items-center">
                                <span style="color: #6A9C89" class="small"><?php echo $formattedDate; ?></span>
                                <span class="dot text-muted"></span>
                                <span class="fw-bold" style="color: #CD5C08"><?php echo $displayStatus; ?></span>
                            </div>
                        </div>
                        <?php 
                        foreach ($orderGroup['items'] as $item) { ?>
                            <div class="d-flex justify-content-between border-bottom py-2 px-5 fw-tm">
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
                        <div class="d-flex justify-content-between py-2 px-5 fw-tm">
                            <div class="d-flex gap-3 align-items-center text-muted small">
                                <span>Payment Method: <?php echo $item['payment_method']; ?></span>
                                <span class="dot text-muted"></span>
                                <span>Order Type: <?php echo $item['order_type']; ?></span>
                            </div>
                            <div class="d-flex gap-3 align-items-center">
                                <span class="text-muted">Total:</span>
                                <span class="fw-bold fs-4">₱<?php echo number_format($orderGroup['stall_subtotal'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-4 justify-content-center align-items-center flex-shrink-0 w-25 orderbtns">
                        <?php if($status == 'Pending'): ?>
                            <button class="rounded-2 prepare-order-btn" data-order-stall-id="<?php echo $osid; ?>" data-new-status="Preparing" data-bs-toggle="modal" data-bs-target="#prepareorder">Prepare Order</button>
                            <button class="rounded-2 cancelorder-btn" style="background-color: #6A9C89;" data-order-stall-id="<?php echo $osid; ?>" data-bs-toggle="modal" data-bs-target="#cancelorder">Cancel Order</button>
                            <button class="rounded-2 remind-payment-btn" data-order-stall-id="<?php echo $osid; ?>" data-action="remind_payment" style="background-color: gray;">Remind Payment</button>
                        <?php elseif($status == 'Preparing'): ?>
                            <button class="rounded-2 order-ready-btn" data-order-stall-id="<?php echo $osid; ?>" data-new-status="Ready" data-bs-toggle="modal" data-bs-target="#orderready">Order Ready</button>
                        <?php elseif($status == 'Ready'): ?>
                            <button class="rounded-2 order-complete-btn" data-order-stall-id="<?php echo $osid; ?>" data-new-status="Completed" data-bs-toggle="modal" data-bs-target="#ordercomplete">Order Complete</button>
                            <button class="rounded-2 notify-customer-btn" data-order-stall-id="<?php echo $osid; ?>" data-action="notify_customer" style="background-color: #6A9C89;">Notify Customer</button>
                        <?php elseif($status == 'Completed'): ?>
                            <span class="text-muted">Completed</span>
                            <?php elseif($status == 'Canceled'): ?>
                            <span class="text-muted text-center">Reason<br>(<?php echo htmlspecialchars($orderGroup['cancellation_reason']); ?>)</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php 
            }
        } else {
            echo '<div class="d-flex justify-content-center align-items-center border rounded-2 bg-white h-25 mb-3">
                No orders in this section.
            </div>';
        }
        echo '</div>';
    }
    ?>

    <!-- Prepare Order Modal -->
    <div class="modal fade" id="prepareorder" tabindex="-1" aria-labelledby="prepareorderLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="text-center">
                        <h4 class="fw-bold mb-4"><i class="fa-solid fa-utensils me-2"></i> Prepare Order</h4>
                        <p class="mb-2">Start preparing this order?</p>
                        <span class="text-muted small">Preparing this order means that their payment is confirmed.</span>
                        <div class="mt-5 mb-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary" id="prepareOrderYesBtn" data-order-id="" data-new-status="">Yes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Ready Modal -->
    <div class="modal fade" id="orderready" tabindex="-1" aria-labelledby="orderreadyLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="text-center">
                        <h4 class="fw-bold mb-4"><i class="fa-solid fa-circle-check me-2"></i> Order Ready</h4>
                        <p class="mb-2">Mark this order as ready for pickup?</p>
                        <span class="text-muted small">Marking this order will notify the customer about their order.</span>
                        <div class="mt-5 mb-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary" id="orderReadyYesBtn" data-order-id="" data-new-status="">Yes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Complete Modal -->
    <div class="modal fade" id="ordercomplete" tabindex="-1" aria-labelledby="ordercompleteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="text-center">
                        <h4 class="fw-bold mb-4"><i class="fa-solid fa-circle-check me-2"></i> Order Complete</h4>
                        <p class="mb-2">Mark this order as completed?</p>
                        <span class="text-muted small">Marking this order means that the order is done.</span>
                        <div class="mt-5 mb-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary" id="orderCompleteYesBtn" data-order-id="" data-new-status="">Yes</button>
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
    <br><br><br><br>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize live order fetching
    initLiveOrderFetching();
    
    // Function to initialize live order fetching
    function initLiveOrderFetching() {
        // Fetch orders immediately on page load
        fetchOrders();
        
        // Set interval to refresh orders every 3 seconds
        setInterval(fetchOrders, 3000);
        
        // Add a small indicator in the corner to show when updates happen
        const indicator = document.createElement('div');
        indicator.style.position = 'fixed';
        indicator.style.bottom = '10px';
        indicator.style.right = '10px';
        indicator.style.width = '10px';
        indicator.style.height = '10px';
        indicator.style.borderRadius = '50%';
        indicator.style.backgroundColor = '#CD5C08';
        indicator.style.opacity = '0';
        indicator.style.transition = 'opacity 0.3s';
        indicator.id = 'update-indicator';
        document.body.appendChild(indicator);
    }
    
    // Function to fetch orders via AJAX
    function fetchOrders() {
        // Get stall_id from URL if present (for Admin)
        const urlParams = new URLSearchParams(window.location.search);
        const stallId = urlParams.get('stall_id');
        
        // Prepare URL for fetching orders
        let url = 'get_stall_orders.php';
        if (stallId) {
            url += '?stall_id=' + encodeURIComponent(stallId);
        }
        
        console.log('Fetching orders from:', url);
        
        fetch(url)
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    console.error('Error fetching orders:', data.error);
                    return;
                }
                updateOrdersDisplay(data.groupedOrders, data.statusMapping);
            })
            .catch(error => {
                console.error('Error fetching orders:', error);
                // Try to get more details about the error
                console.error('Error details:', error.message, error.stack);
            });
    }
    
    // Function to update the orders display
    function updateOrdersDisplay(groupedOrders, statusMapping) {
        
        // Flash the update indicator
        const indicator = document.getElementById('update-indicator');
        if (indicator) {
            indicator.style.opacity = '1';
            setTimeout(() => {
                indicator.style.opacity = '0';
            }, 500);
        }
        
        // Update the "All" section
        updateAllSection(groupedOrders);
        
        // Update individual status sections
        for (const [status, sectionId] of Object.entries(statusMapping)) {
            updateStatusSection(status, sectionId, groupedOrders[status] || {});
        }
        
        // Rebind event listeners to new elements
        rebindEventListeners();
        
        // Update the last refresh time in the footer
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const refreshTimeElement = document.getElementById('last-refresh-time');
        if (!refreshTimeElement) {
            const footer = document.createElement('div');
            footer.style.position = 'fixed';
            footer.style.bottom = '10px';
            footer.style.left = '10px';
            footer.style.fontSize = '12px';
            footer.style.color = '#666';
            footer.innerHTML = `Last updated: <span id="last-refresh-time">${timeString}</span>`;
            document.body.appendChild(footer);
        } else {
            refreshTimeElement.textContent = timeString;
        }
    }
    
    // Function to update the "All" section
    function updateAllSection(groupedOrders) {
        const allSection = document.getElementById('all');
        if (!allSection) {
            console.error('All section element not found');
            return;
        }
        
        // If no orders, show empty message
        if (!groupedOrders || Object.keys(groupedOrders).length === 0) {
            allSection.innerHTML = '<div class="d-flex justify-content-center align-items-center border rounded-2 bg-white h-25 mb-3">No orders in this section.</div>';
            return;
        }
        
        let allHtml = '';
        
        // Loop through all statuses and their orders
        for (const [status, ordersGroup] of Object.entries(groupedOrders)) {
            if (!ordersGroup) continue;
            
            for (const [osid, orderGroup] of Object.entries(ordersGroup)) {
                if (!orderGroup) continue;
                
                try {
                    allHtml += generateOrderHtml(status, osid, orderGroup);
                } catch (error) {
                    console.error('Error generating HTML for order:', error, { status, osid, orderGroup });
                }
            }
        }
        
        // Only update if we have content
        if (allHtml) {
            allSection.innerHTML = allHtml;
        }
    }
    
    // Function to update a specific status section
    function updateStatusSection(status, sectionId, ordersGroup) {
        const section = document.getElementById(sectionId);
        if (!section) {
            console.error(`Section element not found for ID: ${sectionId}`);
            return;
        }
        
        // If no orders for this status, hide section
        if (!ordersGroup || Object.keys(ordersGroup).length === 0) {
            section.innerHTML = '<div class="d-flex justify-content-center align-items-center border rounded-2 bg-white h-25 mb-3">No orders in this section.</div>';
            return;
        }
        
        // Show section if it has orders
        section.classList.remove('d-none');
        
        let sectionHtml = '';
        
        // Loop through orders for this status
        for (const [osid, orderGroup] of Object.entries(ordersGroup)) {
            if (!orderGroup) continue;
            
            try {
                sectionHtml += generateOrderHtml(status, osid, orderGroup);
            } catch (error) {
                console.error('Error generating HTML for order in section:', error, { status, osid, orderGroup });
            }
        }
        
        // Only update if we have content
        if (sectionHtml) {
            section.innerHTML = sectionHtml;
        }
    }
    
    // Function to generate HTML for an order
    function generateOrderHtml(status, osid, orderGroup) {
        // Validate required data
        if (!orderGroup || !orderGroup.items || !Array.isArray(orderGroup.items) || orderGroup.items.length === 0) {
            console.error('Invalid order group data:', orderGroup);
            return '';
        }
        const formattedOrderId = String(orderGroup.order_id).padStart(4, '0');
        const orderDate = new Date(orderGroup.order_date);
        const formattedDate = orderDate.toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: 'numeric' }) + ' ' + 
                            orderDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
        
        let displayStatus = '';
        if (status === 'Pending') displayStatus = 'PENDING PAYMENT';
        else if (status === 'Preparing') displayStatus = 'PREPARING';
        else if (status === 'Ready') displayStatus = 'READY FOR PICKUP';
        else if (status === 'Completed') displayStatus = 'COMPLETED';
        else displayStatus = 'CANCELED';
        
        let html = `
        <div class="border rounded-2 bg-white mb-3 d-flex fw-tm">
            <div class="flex-grow-1 border-end">
                <div class="d-flex justify-content-between align-items-center border-bottom py-3 px-5 fw-tm">
                    <div class="d-flex gap-3 align-items-center">`;
        
        if (orderGroup.queue_number) {
            html += `
                        <span class="prequeue fw-bold text-white d-flex align-items-center justify-content-center">
                            ${String(orderGroup.queue_number).padStart(2, '0')}
                        </span>
                        <span class="dot text-muted"></span>`;
        }
        
        html += `
                        <span class="fw-bold">ORDER ID: ${formattedOrderId}</span>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <span style="color: #6A9C89" class="small">${formattedDate}</span>
                        <span class="dot text-muted"></span>
                        <span class="fw-bold" style="color: #CD5C08">${displayStatus}</span>
                    </div>
                </div>`;
        
        // Add items
        for (const item of orderGroup.items) {
            html += `
                <div class="d-flex justify-content-between border-bottom py-2 px-5 fw-tm">
                    <div class="d-flex gap-3 align-items-center">
                        <img src="${item.product_image ? item.product_image.replace(/"/g, '&quot;') : ''}" width="85px" height="85px" class="border rounded-2">
                        <div>
                            <span class="fs-5">${item.product_name ? item.product_name.replace(/</g, '&lt;').replace(/>/g, '&gt;') : ''}</span><br>`;
            
            if (item.variations) {
                html += `<span class="small text-muted">Variation: ${item.variations ? item.variations.replace(/</g, '&lt;').replace(/>/g, '&gt;') : ''}</span><br>`;
            }
            
            if (item.request) {
                html += `<span class="small text-muted">"${item.request ? item.request.replace(/</g, '&lt;').replace(/>/g, '&gt;') : ''}"</span><br>`;
            }
            
            html += `
                            <span>x${item.quantity}</span>
                        </div>
                    </div>
                    <div class="d-flex flex-column justify-content-end">
                        <span class="fw-bold">₱${parseFloat(item.item_subtotal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                    </div>
                </div>`;
        }
        
        // Add order footer
        html += `
                <div class="d-flex justify-content-between py-2 px-5 fw-tm">
                    <div class="d-flex gap-3 align-items-center text-muted small">
                        <span>Payment Method: ${orderGroup.items[0].payment_method}</span>
                        <span class="dot text-muted"></span>
                        <span>Order Type: ${orderGroup.items[0].order_type}</span>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <span class="text-muted">Total:</span>
                        <span class="fw-bold fs-4">₱${parseFloat(orderGroup.stall_subtotal).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column gap-4 justify-content-center align-items-center flex-shrink-0 w-25 orderbtns">`;
        
        // Add buttons based on status
        if (status === 'Pending') {
            html += `
                <button class="rounded-2 prepare-order-btn" data-order-stall-id="${osid}" data-new-status="Preparing" data-bs-toggle="modal" data-bs-target="#prepareorder">Prepare Order</button>
                <button class="rounded-2 cancelorder-btn" style="background-color: #6A9C89;" data-order-stall-id="${osid}" data-bs-toggle="modal" data-bs-target="#cancelorder">Cancel Order</button>
                <button class="rounded-2 remind-payment-btn" data-order-stall-id="${osid}" data-action="remind_payment" style="background-color: gray;">Remind Payment</button>`;
        } else if (status === 'Preparing') {
            html += `
                <button class="rounded-2 order-ready-btn" data-order-stall-id="${osid}" data-new-status="Ready" data-bs-toggle="modal" data-bs-target="#orderready">Order Ready</button>`;
        } else if (status === 'Ready') {
            html += `
                <button class="rounded-2 order-complete-btn" data-order-stall-id="${osid}" data-new-status="Completed" data-bs-toggle="modal" data-bs-target="#ordercomplete">Order Complete</button>
                <button class="rounded-2 notify-customer-btn" data-order-stall-id="${osid}" data-action="notify_customer" style="background-color: #6A9C89;">Notify Customer</button>`;
        } else if (status === 'Completed') {
            html += `
                <span class="text-muted">Completed</span>`;
        } else if (status === 'Canceled') {
            html += `
                <span class="text-muted text-center">Reason<br>(${orderGroup.cancellation_reason})</span>`;
        }
        
        html += `
            </div>
        </div>`;
        
        return html;
    }
    
    // Function to rebind event listeners to new elements
    function rebindEventListeners() {
        // Rebind cancel order buttons
        rebindCancelOrderButtons();
        
        // Rebind remind payment buttons
        rebindRemindPaymentButtons();
        
        // Rebind notify customer buttons
        rebindNotifyCustomerButtons();
        
        // Rebind action buttons (prepare, ready, complete)
        rebindActionButtons();
    }
    
    // Function to rebind cancel order buttons
    function rebindCancelOrderButtons() {
        document.querySelectorAll('.cancelorder-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var orderStallId = this.getAttribute('data-order-stall-id');
                var modalYesBtn = document.getElementById('cancelOrderYesBtn');
                modalYesBtn.setAttribute('data-order-id', orderStallId);
                modalYesBtn.setAttribute('data-new-status', 'Canceled');
            });
        });
    }
    
    // Function to rebind remind payment buttons
    function rebindRemindPaymentButtons() {
        document.querySelectorAll('.remind-payment-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var orderStallId = this.getAttribute('data-order-stall-id');
                var action = this.getAttribute('data-action');
                if (!orderStallId || !action) {
                    Swal.fire({icon: 'warning', title: 'Missing Info', text: 'Order information is incomplete. Please try again.', confirmButtonColor: '#CD5C08'});
                    return;
                }
                fetch('update_order_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'order_stall_id=' + encodeURIComponent(orderStallId) + '&action=' + encodeURIComponent(action)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({icon: 'success', title: 'Success', text: 'Payment reminder sent successfully.', confirmButtonColor: '#CD5C08'});
                    } else {
                        Swal.fire({icon: 'error', title: 'Order Error', text: 'Error: ' + data.message, confirmButtonColor: '#CD5C08'});
                    }
                })
                .catch(error => Swal.fire({icon: 'error', title: 'Request Failed', text: 'Request failed: ' + error, confirmButtonColor: '#CD5C08'}));
            });
        });
    }
    
    // Function to rebind notify customer buttons
    function rebindNotifyCustomerButtons() {
        document.querySelectorAll('.notify-customer-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var orderStallId = this.getAttribute('data-order-stall-id');
                var action = this.getAttribute('data-action');
                if (!orderStallId || !action) {
                    Swal.fire({icon: 'warning', title: 'Missing Info', text: 'Order information is incomplete. Please try again.', confirmButtonColor: '#CD5C08'});
                    return;
                }
                fetch('update_order_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'order_stall_id=' + encodeURIComponent(orderStallId) + '&action=' + encodeURIComponent(action)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({icon: 'success', title: 'Success', text: 'Customer notification sent successfully.', confirmButtonColor: '#CD5C08'});
                    } else {
                        Swal.fire({icon: 'error', title: 'Order Error', text: 'Error: ' + data.message, confirmButtonColor: '#CD5C08'});
                    }
                })
                .catch(error => Swal.fire({icon: 'error', title: 'Request Failed', text: 'Request failed: ' + error, confirmButtonColor: '#CD5C08'}));
            });
        });
    }
    
    // Function to rebind action buttons
    function rebindActionButtons() {
        bindAction('.prepare-order-btn', 'prepareOrderYesBtn');
        bindAction('.order-ready-btn', 'orderReadyYesBtn');
        bindAction('.order-complete-btn', 'orderCompleteYesBtn');
    }
    
    // Bind cancel order button(s) on orders.php
    document.querySelectorAll('.cancelorder-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var orderStallId = this.getAttribute('data-order-stall-id');
            var modalYesBtn = document.getElementById('cancelOrderYesBtn');
            modalYesBtn.setAttribute('data-order-id', orderStallId);
            modalYesBtn.setAttribute('data-new-status', 'Canceled');
        });
    });

    document.querySelectorAll('.remind-payment-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var orderStallId = this.getAttribute('data-order-stall-id');
            var action = this.getAttribute('data-action'); // should be "remind_payment"
            if (!orderStallId || !action) {
                Swal.fire({icon: 'warning', title: 'Missing Info', text: 'Order information is incomplete. Please try again.', confirmButtonColor: '#CD5C08'});
                return;
            }
            fetch('update_order_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'order_stall_id=' + encodeURIComponent(orderStallId) + '&action=' + encodeURIComponent(action)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    Swal.fire({icon: 'error', title: 'Order Error', text: 'Error: ' + data.message, confirmButtonColor: '#CD5C08'});
                }
            })
            .catch(error => Swal.fire({icon: 'error', title: 'Request Failed', text: 'Request failed: ' + error, confirmButtonColor: '#CD5C08'}));
        });
    });
    
    // Bind Notify Customer button
    document.querySelectorAll('.notify-customer-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var orderStallId = this.getAttribute('data-order-stall-id');
            var action = this.getAttribute('data-action'); // should be "notify_customer"
            if (!orderStallId || !action) {
                Swal.fire({icon: 'warning', title: 'Missing Info', text: 'Order information is incomplete. Please try again.', confirmButtonColor: '#CD5C08'});
                return;
            }
            fetch('update_order_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'order_stall_id=' + encodeURIComponent(orderStallId) + '&action=' + encodeURIComponent(action)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    Swal.fire({icon: 'error', title: 'Order Error', text: 'Error: ' + data.message, confirmButtonColor: '#CD5C08'});
                }
            })
            .catch(error => Swal.fire({icon: 'error', title: 'Request Failed', text: 'Request failed: ' + error, confirmButtonColor: '#CD5C08'}));
        });
    });

    document.getElementById('cancelOrderYesBtn').addEventListener('click', function() {
        var orderStallId = this.getAttribute('data-order-id');
        var newStatus = this.getAttribute('data-new-status');
        
        var selectedRadio = document.querySelector('input[name="cancelReason"]:checked');
        var cancelReason = selectedRadio ? selectedRadio.value : '';
        
        if (!orderStallId || !newStatus) {
            Swal.fire({icon: 'warning', title: 'Missing Info', text: 'Order information is incomplete. Please try again.', confirmButtonColor: '#CD5C08'});
            return;
        }
        
        if (newStatus === 'Canceled' && cancelReason === '') {
            Swal.fire({icon: 'error', title: 'Please Select a Reason', text: 'Please select a cancellation reason.', confirmButtonColor: '#CD5C08'});
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
                // Fetch orders instead of reloading the page
                fetchOrders();
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('cancelorder'));
                if (modal) modal.hide();
                // Show success message
                Swal.fire({icon: 'success', title: 'Order Canceled', text: 'Order has been canceled successfully.', confirmButtonColor: '#CD5C08'});
            } else {
                Swal.fire({icon: 'error', title: 'Order Error', text: 'Error: ' + data.message, confirmButtonColor: '#CD5C08'});
            }
        })
        .catch(error => Swal.fire({icon: 'error', title: 'Request Failed', text: 'Request failed: ' + error, confirmButtonColor: '#CD5C08'}));
    });

    function bindAction(buttonSelector, modalYesBtnId) {
        document.querySelectorAll(buttonSelector).forEach(function(btn) {
            btn.addEventListener('click', function() {
                var orderStallId = this.getAttribute('data-order-stall-id');
                var modalYesBtn = document.getElementById(modalYesBtnId);
                modalYesBtn.setAttribute('data-order-id', orderStallId);
                modalYesBtn.setAttribute('data-new-status', this.getAttribute('data-new-status'));
            });
        });
    }

    bindAction('.prepare-order-btn', 'prepareOrderYesBtn');
    bindAction('.order-ready-btn', 'orderReadyYesBtn');
    bindAction('.order-complete-btn', 'orderCompleteYesBtn');

    function bindYesButton(yesBtnId) {
        document.getElementById(yesBtnId).addEventListener('click', function() {
            var orderStallId = this.getAttribute('data-order-id');
            var newStatus = this.getAttribute('data-new-status');
            if (!orderStallId || !newStatus) {
                Swal.fire({icon: 'warning', title: 'Missing Info', text: 'Order information is incomplete. Please try again.', confirmButtonColor: '#CD5C08'});
                return;
            }
            fetch('update_order_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'order_stall_id=' + encodeURIComponent(orderStallId) + '&new_status=' + encodeURIComponent(newStatus)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Fetch orders instead of reloading the page
                    fetchOrders();
                    // Close the modal
                    const modalId = {
                        'prepareOrderYesBtn': 'prepareorder',
                        'orderReadyYesBtn': 'orderready',
                        'orderCompleteYesBtn': 'ordercomplete'
                    }[yesBtnId];
                    if (modalId) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                        if (modal) modal.hide();
                    }
                    // Show success message
                    const successMessages = {
                        'Preparing': 'Order is now being prepared.',
                        'Ready': 'Order is now ready for pickup.',
                        'Completed': 'Order has been completed successfully.'
                    };
                    Swal.fire({
                        icon: 'success', 
                        title: 'Status Updated', 
                        text: successMessages[newStatus] || 'Order status updated successfully.', 
                        confirmButtonColor: '#CD5C08'
                    });
                } else {
                    Swal.fire({icon: 'error', title: 'Order Error', text: 'Error: ' + data.message, confirmButtonColor: '#CD5C08'});
                }
            })
            .catch(error => Swal.fire({icon: 'error', title: 'Request Failed', text: 'Request failed: ' + error, confirmButtonColor: '#CD5C08'}));
        });
    }
    
    bindYesButton('prepareOrderYesBtn');
    bindYesButton('orderReadyYesBtn');
    bindYesButton('orderCompleteYesBtn');
});
</script>
<script src="./assets/js/navigation.js?v=<?php echo time(); ?>"></script>
<?php include_once './footer.php'; ?>
