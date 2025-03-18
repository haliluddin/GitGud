<?php
include_once 'links.php'; 
require_once __DIR__ . '/classes/stall.class.php';

if (!isset($_GET['order_id'])) {
    echo "No order specified.";
    exit;
}

$order_id = $_GET['order_id'];
$stallObj = new Stall();

$orderDetails = $stallObj->getOrderDetails($order_id);

if (!$orderDetails) {
    echo "Order not found.";
    exit;
}

$formattedOrderId = str_pad($order_id, 4, '0', STR_PAD_LEFT);
?>
<br>
<div class="justify-content-center d-flex">
    <div class="bg-white border rounded-2 p-4 w-50 border-dark">
        <div class="d-flex mb-4">
            <div class="w-50">
                <h5 class="fw-bold"><?php echo htmlspecialchars($orderDetails['order']['business_name']); ?></h5>
                <p class="small m-0"><?php echo htmlspecialchars($orderDetails['order']['street_building_house']); ?>, <?php echo htmlspecialchars($orderDetails['order']['barangay']); ?></p>
                <p class="small m-0">Zamboanga del Sur, Zamboanga City, 7000</p>
                <p class="small m-0">Philippines</p>
            </div>
            <div class="w-50">
                <h5 class="text-end">RECEIPT</h5>
                <div class="d-flex justify-content-end gap-5">
                    <div>
                        <p class="small m-0 text-end">Order ID</p>
                        <p class="small m-0 text-end">Order Date</p>
                    </div>
                    <div>
                        <p class="small m-0 text-end"><?php echo $formattedOrderId; ?></p>
                        <p class="small m-0 text-end"><?php echo date("m/d/Y H:i", strtotime($orderDetails['order']['order_created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php foreach ($orderDetails['stalls'] as $stallName => $stall): ?>
            <div class="border-bottom pb-2 w-100"><?php echo htmlspecialchars($stall['stall_name']); ?></div>
            <table class="w-100">
                <tr>
                    <th class="py-2" style="width:10%">Qty</th>
                    <th class="py-2" style="width:50%">Item</th>
                    <th class="py-2 text-end" style="width:20%">Price</th>
                    <th class="py-2 text-end" style="width:20%">Total</th>
                </tr>
                <?php foreach ($stall['order_items'] as $item): ?>
                    <tr>
                        <td class="py-3"><?php echo $item['quantity']; ?></td>
                        <td class="py-3"><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td class="py-3 text-end">₱<?php echo number_format($item['price'], 2); ?></td>
                        <td class="py-3 text-end">₱<?php echo number_format($item['item_subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <div class="border-top pt-3 w-100 text-end fw-bold">₱<?php echo number_format($stall['stall_subtotal'], 2); ?></div>
        <?php endforeach; ?>
        <hr>
        <div class="w-100 d-flex align-items-center justify-content-end gap-4 mb-4">
            <span>Total</span>
            <span class="fw-bold fs-3">₱<?php echo number_format($orderDetails['order']['total_price'], 2); ?></span> 
        </div>
        <br>
        <div class="text-center">
            <a href="download_receipt.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary download-btn">Download Receipt</a>
        </div>
    </div>
</div>
<br>

