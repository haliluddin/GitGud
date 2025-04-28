<?php
// Set content type to JSON
header('Content-Type: application/json');

// Start session to access user data
session_start();
require_once __DIR__ . '/classes/stall.class.php';
require_once __DIR__ . '/classes/db.class.php';

// Get user data from session
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

$userObj = new User();

$user = $userObj->getUser($_SESSION['user']['id']);

$stallObj = new Stall();

if ($user['role'] === 'Admin' && isset($_GET['stall_id'])) {
    $stall_id = intval(decrypt(urldecode($_GET['stall_id'])));
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

// Return the data as JSON
try {
    $response = [
        'groupedOrders' => $groupedOrders,
        'statusMapping' => $statusMapping,
        'timestamp' => date('Y-m-d H:i:s') // Add timestamp for debugging
    ];
    echo json_encode($response, JSON_THROW_ON_ERROR);
} catch (Exception $e) {
    // Log error and return error response
    error_log('JSON encoding error in get_stall_orders.php: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to encode response: ' . $e->getMessage()]);
}
