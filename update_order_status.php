<?php
header('Content-Type: application/json');
require_once __DIR__ . '/classes/stall.class.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['order_stall_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing order_stall_id']);
    exit;
}

$order_stall_id = $_POST['order_stall_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';

$stallObj = new Stall();

$dsn = "mysql:host=localhost;dbname=gitgud;charset=utf8";
$pdo = new PDO($dsn, 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$pdo->beginTransaction();

try {
    $stmtGet = $pdo->prepare("SELECT order_id, stall_id FROM order_stalls WHERE id = :order_stall_id");
    $stmtGet->execute(['order_stall_id' => $order_stall_id]);
    $orderDetails = $stmtGet->fetch();
    if (!$orderDetails) {
        throw new Exception("Order not found.");
    }
    $order_id = $orderDetails['order_id'];
    $stall_id = $orderDetails['stall_id'];
    
    $stmtUser = $pdo->prepare("SELECT user_id FROM orders WHERE id = :order_id");
    $stmtUser->execute(['order_id' => $order_id]);
    $orderUser = $stmtUser->fetch();
    if (!$orderUser) {
        throw new Exception("Order user not found.");
    }
    $user_id = $orderUser['user_id'];
    
    if ($action === 'remind_payment' || $action === 'notify_customer') {
        if ($action === 'remind_payment') {
            $message = "Order ID " . str_pad($order_id, 4, '0', STR_PAD_LEFT) . ": Pending Payment";
        } else if ($action === 'notify_customer') {
            $message = "Order ID " . str_pad($order_id, 4, '0', STR_PAD_LEFT) . ": Ready to pickup!";
        }
        $stmtNoti = $pdo->prepare("INSERT INTO notifications (user_id, order_id, stall_id, message) VALUES (?, ?, ?, ?)");
        $stmtNoti->execute([$user_id, $order_id, $stall_id, $message]);
        
        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Notification sent']);
        exit;
    }
    
    
    if (!isset($_POST['new_status'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing new_status']);
        exit;
    }
    $new_status = $_POST['new_status'];
    $allowed_status = ['Pending', 'Preparing', 'Ready', 'Completed', 'Canceled'];
    if (!in_array($new_status, $allowed_status)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
        exit;
    }
    
    if ($new_status == 'Preparing') {
        $stmtCheck = $pdo->prepare("SELECT queue_number, created_at FROM order_stalls WHERE id = :order_stall_id");
        $stmtCheck->execute(['order_stall_id' => $order_stall_id]);
        $row = $stmtCheck->fetch();
        
        if (!$row['queue_number'] || (date("Y-m-d", strtotime($row['created_at'])) != date("Y-m-d"))) {
            $stmtMax = $pdo->prepare("SELECT MAX(queue_number) AS max_queue FROM order_stalls WHERE DATE(created_at) = CURDATE() AND queue_number IS NOT NULL");
            $stmtMax->execute();
            $resMax = $stmtMax->fetch();
            $nextQueue = $resMax['max_queue'] ? intval($resMax['max_queue']) + 1 : 1;
            
            $stmtUpdateQueue = $pdo->prepare("UPDATE order_stalls SET queue_number = :queue_number WHERE id = :order_stall_id");
            $stmtUpdateQueue->execute(['queue_number' => $nextQueue, 'order_stall_id' => $order_stall_id]);
        }
    }
    
    if ($new_status === 'Canceled') {
        $cancel_reason = isset($_POST['cancel_reason']) ? $_POST['cancel_reason'] : 'Canceled';
        $stmt = $pdo->prepare("UPDATE order_stalls SET status = :new_status, cancellation_reason = :cancel_reason WHERE id = :order_stall_id");
        $stmt->execute([
            'new_status'      => $new_status,
            'cancel_reason'   => $cancel_reason,
            'order_stall_id'  => $order_stall_id
        ]);
    } else {
        $stmt = $pdo->prepare("UPDATE order_stalls SET status = :new_status WHERE id = :order_stall_id");
        $stmt->execute(['new_status' => $new_status, 'order_stall_id' => $order_stall_id]);
    }
    
    
    if ($new_status === 'Preparing' || $new_status === 'Ready') {
        if ($new_status === 'Preparing') {
            $message = "Order ID " . str_pad($order_id, 4, '0', STR_PAD_LEFT) . ": Preparing Order";
        } else {
            $message = "Order ID " . str_pad($order_id, 4, '0', STR_PAD_LEFT) . ": Ready to pickup!";
        }
        $stmtNoti = $pdo->prepare("INSERT INTO notifications (user_id, order_id, stall_id, message) VALUES (?, ?, ?, ?)");
        $stmtNoti->execute([$user_id, $order_id, $stall_id, $message]);
    }

    $stmtAll = $pdo->prepare("
        SELECT COUNT(*) AS not_confirmed 
        FROM order_stalls 
        WHERE order_id = :order_id 
          AND status NOT IN ('Preparing', 'Ready', 'Completed')
    ");
    $stmtAll->execute(['order_id' => $order_id]);
    $notConfirmed = $stmtAll->fetchColumn();

    if ($notConfirmed == 0) {
        $stmtNotiCheck = $pdo->prepare("
            SELECT COUNT(*) 
            FROM notifications 
            WHERE order_id = :order_id 
              AND message LIKE :msg
        ");
        $searchMsg = '%Payment Confirmed%';
        $stmtNotiCheck->execute(['order_id' => $order_id, 'msg' => $searchMsg]);
        $exists = $stmtNotiCheck->fetchColumn();

        if (!$exists) {
            $formattedOrderId = str_pad($order_id, 4, '0', STR_PAD_LEFT);
            $message = "Order ID {$formattedOrderId}: Payment Confirmed!";
            $stmtNoti = $pdo->prepare("INSERT INTO notifications (user_id, order_id, stall_id, message) VALUES (?, ?, ?, ?)");
            $stmtNoti->execute([$user_id, $order_id, $stall_id, $message]);
        }
    }
    
    $pdo->commit();
    echo json_encode(['status' => 'success', 'message' => 'Order status updated']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
