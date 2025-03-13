<?php
include_once 'header.php';
require_once __DIR__ . '/classes/stall.class.php';

if (!isset($_GET['order_id'])) {
    echo "No order specified.";
    exit;
}

$order_id = $_GET['order_id'];
$stallObj = new Stall();

// Assuming you have or create a method to fetch complete order details
$orderDetails = $stallObj->getOrderDetails($order_id);

if (!$orderDetails) {
    echo "Order not found.";
    exit;
}

$formattedOrderId = str_pad($order_id, 4, '0', STR_PAD_LEFT);
?>

<main>
    <div class="receipt-container">
        <h2>Receipt for Order ID <?php echo $formattedOrderId; ?></h2>
        <!-- Display order details as needed -->
        <div class="order-info">
            <!-- Example: list items, subtotal, payment method, etc. -->
            <pre><?php print_r($orderDetails); ?></pre>
        </div>
        <!-- Download Receipt button -->
        <a href="download_receipt.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary">Download Receipt</a>
    </div>
</main>

<?php include_once 'footer.php'; ?>
