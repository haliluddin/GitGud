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

if ( ! empty($groupedOrders['Completed']) ) {
foreach ($groupedOrders['Completed'] as $osid => &$orderGroup) {
    foreach ($orderGroup['items'] as &$item) {
        $rating = $stallObj->getRatingDetails(
            $user_id,
            $osid,
            intval($item['product_id']),
            $item['variations'] ?? null
        );
        if ($rating) {
            $item['rated']          = true;
            $item['rating_value']   = (int)$rating['rating_value'];
            $item['comment']        = $rating['comment'];            
            $item['created_at']     = $rating['created_at'];
            $item['seller_response']= $rating['seller_response'];     
            $item['response_at']    = $rating['response_at'];         
            $item['helpful_count']  = (int)$rating['helpful_count'];
        } else {
            $item['rated'] = false;
        }
    }
    unset($item);
}
unset($orderGroup);
}


$statusMapping = [
    'Pending'   => 'topay',      
    'Preparing' => 'preparing',
    'Ready'     => 'toreceive',
    'Completed' => 'completed',
    'Canceled'  => 'canceled'
];
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    .nav-main { padding: 20px 120px; }

    .ratemodal .modal-header, .ratemodal .modal-footer{
        position: sticky;
        z-index: 1020;
        background-color: white;
    }
    .ratemodal .modal-header{
        top: 0;
    }
    .ratemodal .modal-footer{
        bottom: 0;
    }
    .ratemodal {
        max-height: 80vh; 
        overflow-y: auto;
    }
    #productrate .modal-dialog {
        max-width: 50vw;
    }
</style>
<script> const userId = <?php echo $user['user_session']; ?>; </script>
<main class="nav-main">
    <!-- Navigation -->
    <div class="nav-container d-flex gap-3 my-2 flex-wrap">
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
        if (empty($groupedOrders)) {
            echo '<div class="d-flex justify-content-center align-items-center border rounded-2 bg-white h-25 mb-3">
                    No orders in this section.
                </div>';
        } else {
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
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 fw-tm">
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
                            <div class="d-flex justify-content-between border-bottom py-2 fw-tm">
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
                        <div class="d-flex justify-content-between pt-2 fw-tm">
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
                                    <button class="cancelorder rounded-2 rate-order-btn" data-bs-toggle="modal" data-bs-target="#productrate" data-order-stall-id="<?= $osid ?>" data-items='<?= htmlspecialchars(json_encode($orderGroup['items']), ENT_QUOTES) ?>'>Rate</button>
                                    <span class="dot text-muted"></span>
                                <?php endif; ?>
                                <?php if($status == 'Canceled'): ?>
                                    <button class="preparing rounded-2">Canceled</button>                               
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
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 fw-tm">
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
                        <div class="d-flex justify-content-between border-bottom py-2 fw-tm">
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
                    <div class="d-flex justify-content-between pt-2 fw-tm">
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
                                <button class="cancelorder rounded-2 rate-order-btn" data-bs-toggle="modal" data-bs-target="#productrate" data-order-stall-id="<?= $osid ?>" data-items='<?= htmlspecialchars(json_encode($orderGroup['items']), ENT_QUOTES) ?>'>Rate</button>
                                <span class="dot text-muted"></span>
                            <?php endif; ?>
                            <?php if($status == 'Canceled'): ?>
                                <button class="preparing rounded-2">Canceled</button>                               
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
            echo '<div class="d-flex justify-content-center align-items-center border rounded-2 bg-white h-25 mb-3">
                No orders in this section.
            </div>';
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

    <!-- Product Rate Modal -->
    <div class="modal fade" id="productrate" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content ratemodal">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Rate Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #F4F4F4;">
                    <div class="items-container"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <br><br><br><br>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cancelorder-btn').forEach(btn => {
        btn.addEventListener('click', () => {
        const yesBtn = document.getElementById('cancelOrderYesBtn');
        yesBtn.dataset.orderId   = btn.dataset.orderStallId;
        yesBtn.dataset.newStatus = 'Canceled';
        });
    });

    document.getElementById('cancelOrderYesBtn').addEventListener('click', function() {
        const orderStallId = this.dataset.orderId;
        const newStatus    = this.dataset.newStatus;
        const selected     = document.querySelector('input[name="cancelReason"]:checked');
        const cancelReason = selected ? selected.value : '';
        if (!orderStallId || !newStatus) {
        return Swal.fire({
            icon: 'warning',
            title: 'Missing Info',
            text: 'Please complete the order details.',
            confirmButtonColor: '#CD5C08'
        });
        }
        if (newStatus === 'Canceled' && !cancelReason) {
        return Swal.fire({
            icon: 'info',
            title: 'Select Reason',
            text: 'Please select a cancellation reason before proceeding.',
            confirmButtonColor: '#CD5C08'
        });
        }
        fetch('update_order_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            order_stall_id: orderStallId,
            new_status:     newStatus,
            cancel_reason:  cancelReason
        }).toString()
        })
        .then(r => r.json())
        .then(data => {
        if (data.status === 'success') location.reload();
        else Swal.fire({
            icon: 'error',
            title: 'Order Error',
            text: data.message || 'Something went wrong.',
            confirmButtonColor: '#CD5C08'
        });
        })
        .catch(err => Swal.fire({
        icon: 'error',
        title: 'Network Error',
        text: 'Request failed: ' + err,
        confirmButtonColor: '#CD5C08'
        }));
    });

    document.querySelectorAll('.order-received-btn').forEach(btn => {
        btn.addEventListener('click', () => {
        const yesBtn = document.getElementById('orderReceivedYesBtn');
        yesBtn.dataset.orderId   = btn.dataset.orderStallId;
        yesBtn.dataset.newStatus = btn.dataset.newStatus;
        });
    });

    document.getElementById('orderReceivedYesBtn').addEventListener('click', function() {
        const orderStallId = this.dataset.orderId;
        const newStatus    = this.dataset.newStatus;
        if (!orderStallId || !newStatus) {
        return Swal.fire({
            icon: 'warning',
            title: 'Missing Info',
            text: 'Please complete the order details.',
            confirmButtonColor: '#CD5C08'
        });
        }
        fetch('update_order_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            order_stall_id: orderStallId,
            new_status:     newStatus
        }).toString()
        })
        .then(r => r.json())
        .then(data => {
        if (data.status === 'success') location.reload();
        else Swal.fire({
            icon: 'error',
            title: 'Order Error',
            text: data.message || 'Something went wrong.',
            confirmButtonColor: '#CD5C08'
        });
        })
        .catch(err => Swal.fire({
        icon: 'error',
        title: 'Network Error',
        text: 'Request failed: ' + err,
        confirmButtonColor: '#CD5C08'
        }));
    });
    });

    const productRateModalEl = document.getElementById('productrate');
    productRateModalEl.addEventListener('show.bs.modal', function(event) {
        const triggerButton = event.relatedTarget;
        const osid          = triggerButton.dataset.orderStallId;
        let   items         = JSON.parse(triggerButton.dataset.items);
        const container     = productRateModalEl.querySelector('.items-container');
        const footer        = productRateModalEl.querySelector('.modal-footer');

        productRateModalEl.querySelectorAll('[data-dismiss]').forEach(el => {
            el.setAttribute('data-bs-dismiss','modal');
            el.removeAttribute('data-dismiss');
        });

        container.innerHTML = '';
        const ratingValues = {};

        function ratedHTML(item) {
            return `
            <div>
                <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div
                    data-coreui-read-only="true"
                    data-coreui-toggle="rating"
                    data-coreui-value="${item.rating_value}"
                    ></div>
                    <span class="text-muted small">${item.created_at}</span>
                </div>
                <span style="color: #CD5C08">Product Rated</span>
                </div>
                ${item.comment ? `<p class="my-3">${item.comment}</p>` : ''}
                ${item.seller_response
                ? `<p class="p-2 rounded-2 my-3" style="background-color: #f4f4f4">
                    Stall Response: ${item.seller_response}
                    </p>`
                : ''}
            </div>
            <div class="d-flex gap-3 align-items-center border rounded-2 mt-3">
                <img src="${item.product_image}" width="55" height="55" class="border rounded-start">
                <div>
                <span>${item.product_name}</span><br>
                ${item.variations
                    ? `<span class="small text-muted">Variation: ${item.variations}</span>`
                    : ''}
                </div>
            </div>
            <div class="small text-end mt-2">
                <i class="fa-regular fa-thumbs-up"></i>
                <span>Helpful <span>${item.helpful_count || 0}</span></span>
            </div>
            `;
        }

        items.forEach((item, idx) => {
            const block = document.createElement('div');
            block.className   = 'p-3 border rounded-2 bg-white mb-3';
            block.dataset.idx = idx;

            if (item.rated) {
            block.innerHTML = ratedHTML(item);
            block.querySelectorAll('[data-coreui-toggle="rating"]')
                .forEach(el => coreui.Rating.getOrCreateInstance(el));
            } else {
            block.innerHTML = `
                <div class="d-flex gap-3 align-items-center border-bottom pb-3 mb-3">
                <img src="${item.product_image}" width="50" height="50" class="border rounded-2">
                <div>
                    <span>${item.product_name}</span><br>
                    ${item.variations
                    ? `<span class="small text-muted">Variation: ${item.variations}</span><br>`
                    : ''}
                </div>
                </div>
                <div>
                <div class="d-flex align-items-center gap-3">
                    <div
                    id="rating-${idx}"
                    data-coreui-toggle="rating"
                    data-coreui-value="0"
                    data-coreui-size="lg"
                    data-coreui-allow-clear="true"
                    ></div>
                    <button class="rounded-1 small px-2 bg-dark text-white border-0"
                            type="button"
                            id="reset-${idx}">
                    reset
                    </button>
                </div>
                <div class="form-floating mt-3 d-none" id="comment-container-${idx}">
                    <textarea class="form-control"
                            placeholder="Leave a comment here"
                            id="comment-${idx}"></textarea>
                    <label for="comment-${idx}">Comments</label>
                </div>
                </div>
            `;
            const ratingEl   = block.querySelector(`#rating-${idx}`);
            const ratingInst = coreui.Rating.getOrCreateInstance(ratingEl);
            const commentCt  = block.querySelector(`#comment-container-${idx}`);
            ratingEl.addEventListener('change.coreui.rating', e => {
                if (e.value) {
                ratingValues[idx] = e.value;
                commentCt.classList.remove('d-none');
                } else {
                delete ratingValues[idx];
                commentCt.classList.add('d-none');
                block.querySelector(`#comment-${idx}`).value = '';
                }
            });
            block.querySelector(`#reset-${idx}`).addEventListener('click', () => {
                ratingInst.reset();
                delete ratingValues[idx];
                commentCt.classList.add('d-none');
            });
            }

            container.appendChild(block);
        });

        const oldBtn = footer.querySelector('.btn-primary');
        const newBtn = oldBtn.cloneNode(true);
        oldBtn.replaceWith(newBtn);

        function toggleSubmit() {
            newBtn.classList.toggle('d-none', items.every(i => i.rated));
        }
        toggleSubmit();

        newBtn.addEventListener('click', () => {
            const toSave = Object.keys(ratingValues).map(i => {
            const idx       = +i;
            const commentEl = document.getElementById(`comment-${idx}`);
            return {
                idx,
                product_id:   items[idx].product_id,
                variations:   items[idx].variations || null,
                rating_value: ratingValues[idx],
                comment:      commentEl ? commentEl.value.trim() : null
            };
            });

            if (!toSave.length) {
            return Swal.fire({
                icon: 'info',
                title: 'No Ratings',
                text: 'You have not provided any ratings to submit.',
                confirmButtonColor: '#CD5C08'
            });
            }

            fetch('rate_products.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                order_stall_id: osid,
                ratings:        JSON.stringify(toSave)
            }).toString()
            })
            .then(r => r.json())
            .then(json => {
            if (json.status === 'success') {
                // success message
                Swal.fire({
                icon: 'success',
                title: 'Thank You!',
                text: 'Your ratings have been saved.',
                confirmButtonColor: '#6A9C89'
                });

                toSave.forEach(r => {
                const itm = items[r.idx];
                itm.rated        = true;
                itm.rating_value = r.rating_value;
                itm.comment      = r.comment;
                itm.created_at   = new Date().toISOString().slice(0,19).replace('T',' ');
                itm.helpful_count = itm.helpful_count || 0;

                const blk = container.querySelector(`[data-idx="${r.idx}"]`);
                blk.innerHTML = ratedHTML(itm);
                blk.querySelectorAll('[data-coreui-toggle="rating"]')
                    .forEach(el => coreui.Rating.getOrCreateInstance(el));

                delete ratingValues[r.idx];
                });

                triggerButton.dataset.items = JSON.stringify(items);
                triggerButton.setAttribute('data-items', triggerButton.dataset.items);

                toggleSubmit();
            } else {
                Swal.fire({
                icon: 'error',
                title: 'Save Failed',
                text: json.message || 'Unable to save your ratings.',
                confirmButtonColor: '#CD5C08'
                });
            }
            })
            .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Failed to save ratings: ' + err,
                confirmButtonColor: '#CD5C08'
            });
            });
        });
    });
</script>

<script src="./assets/js/navigation.js?v=<?php echo time(); ?>"></script>
<?php include_once './footer.php'; ?>
