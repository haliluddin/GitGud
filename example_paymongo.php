<?php
require_once __DIR__ . '/classes/paymongo.class.php';

// Create a new instance of PayMongoHandler
$payMongo = new PayMongoHandler();

// Create a payment link (amount in cents, so 300.00 PHP = 30000)
$result = $payMongo->createPaymentLink(
    30000,
    'Parking Payment',
    ['order_id' => '123456']
);

if (isset($result['error'])) {
    echo json_encode(['error' => $result['error']]);
} else {
    // Output the payment link and status checking HTML
    echo "<a href='" . $result['checkout_url'] . "' target='_blank'>Click here to pay</a>";
    echo $result['status_check_html'];
}
?>
