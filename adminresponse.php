<?php
require_once __DIR__ . '/classes/db.php';
require_once __DIR__ . '/classes/admin.class.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['business_id']) || !isset($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$business_id = intval($data['business_id']);
$action = $data['action'];

try {
    $admin = new Admin();

    if ($action === 'approve') {
        $admin->updateBusinessStatus($business_id, 'Approved');
        echo json_encode(['success' => true, 'message' => 'Business approved']);
    } elseif ($action === 'deny') {
        $admin->updateBusinessStatus($business_id, 'Rejected');
        echo json_encode(['success' => true, 'message' => 'Business denied']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>