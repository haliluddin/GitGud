<?php
header('Content-Type: application/json');

require_once __DIR__ . '/classes/paymongo.class.php';

if (!isset($_GET['payment_id'])) {
    echo json_encode(['error' => 'Payment ID not provided']);
    exit;
}

$paymentId = $_GET['payment_id'];
$payMongo = new PayMongoHandler();
$result = $payMongo->checkPaymentStatus($paymentId);

echo json_encode($result['success'] ? ['status' => $result['status']] : ['error' => $result['error']]);
?>
