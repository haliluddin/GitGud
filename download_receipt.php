<?php
require_once __DIR__ . '/classes/stall.class.php';
require_once 'vendor/autoload.php'; 

use Dompdf\Dompdf;
use Dompdf\Options;

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

ob_start();
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>Receipt <?php echo $formattedOrderId; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <style>
            .rec{
                display: flex !important;
            }
        </style>
    </head>
    <body>
        <div class="bg-white border rounded-2 p-4 border-dark">
            <table class="mb-4 w-100">
                <tr>
                    <td>
                        <h5 class="fw-bold"><?php echo htmlspecialchars($orderDetails['order']['business_name']); ?></h5>
                        <p class="small m-0"><?php echo htmlspecialchars($orderDetails['order']['street_building_house']); ?>, <?php echo htmlspecialchars($orderDetails['order']['barangay']); ?></p>
                        <p class="small m-0">Zamboanga del Sur, Zamboanga City, 7000</p>
                        <p class="small m-0">Philippines</p>
                    </td>
                    <td style="vertical-align: top;" class="text-end">
                        <h5 class="fw-normal">RECEIPT</h5>
                        <table class="w-100 text-end small p-0 m-0">
                            <tr>
                                <td class="p-0">Order ID</td>
                                <td class="p-0"><?php echo $formattedOrderId; ?></td>
                            </tr>
                            <tr>
                                <td class="p-0">Order Date</td>
                                <td class="p-0"><?php echo date("m/d/Y H:i", strtotime($orderDetails['order']['order_created_at'])); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
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
                            <td class="py-3 text-end"><?php echo number_format($item['price'], 2); ?></td>
                            <td class="py-3 text-end"><?php echo number_format($item['item_subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <div class="border-top pt-3 w-100 text-end fw-bold"><?php echo number_format($stall['stall_subtotal'], 2); ?></div>
            <?php endforeach; ?>
            <hr>
            <table class="text-end w-100">
                <tr>
                    <td class="small" style="width: 85%;">Total</td>
                    <td style="width: 15%"><h2 class="fw-bold"><?php echo number_format($orderDetails['order']['total_price'], 2); ?></h2></td>
                </tr>
            </table>
        </div>
    </body>
</html>

<?php
$html = ob_get_clean();
if (ob_get_length()) {
    ob_end_clean();
}
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream("receipt_{$formattedOrderId}.pdf", array("Attachment" => 1));
?>
