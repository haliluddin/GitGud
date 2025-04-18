<?php
require_once __DIR__ . '/db.php';

class Cart {

    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    function getCart($userId){
        $sql = "SELECT * FROM cart WHERE user_id = :user_id;";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':user_id' => $userId));
        return $query->fetchAll();
    }

    /*function addToCart($userId, $productId, $quantity, $stall_id) {
        $sql = "INSERT INTO cart (user_id, product_id, quantity, stall_id) 
                VALUES (:user_id, :product_id, :quantity, :stall_id)";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':user_id', $userId);
        $query->bindParam(':product_id', $productId);
        $query->bindParam(':quantity', $quantity);
        $query->bindParam(':stall_id', $stall_id);
        $query->execute();
    }*/

    function removeFromCart($userId, $productId) {
        $sql = "DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id";
        $query = $this->db->connect()->prepare($sql);
        $query->execute([':user_id' => $userId, ':product_id' => $productId]);
    }

    public function getCartGroupedItems($user_id, $park_id) {
        $sql = "SELECT 
                    c.*,
                    p.name AS product_name,
                    p.image AS product_image,
                    p.stall_id,
                    s.name AS stall_name,
                    s.park_id,
                    (SELECT GROUP_CONCAT(method) 
                     FROM stall_payment_methods 
                     WHERE stall_id = s.id) AS supported_methods,
                    vo.name AS variation_name,
                    c.variation_option_id 
                FROM cart c
                JOIN products p ON c.product_id = p.id
                JOIN stalls s ON p.stall_id = s.id
                LEFT JOIN variation_options vo ON c.variation_option_id = vo.id
                WHERE c.user_id = ? 
                  AND s.park_id = ?
                ORDER BY s.name, p.name, c.id";
        
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$user_id, $park_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $tempCart = [];
        
        foreach ($rows as $row) {
            $stallName = $row['stall_name'];
            $productId = $row['product_id'];
            $request = isset($row['request']) ? $row['request'] : '';
            
            if (!isset($tempCart[$stallName])) {
                $tempCart[$stallName] = [];
            }
            if (!isset($tempCart[$stallName][$productId])) {
                $tempCart[$stallName][$productId] = [];
            }
            if (!isset($tempCart[$stallName][$productId][$request])) {
                $tempCart[$stallName][$productId][$request] = [
                    'non_var' => null,
                    'var' => [] 
                ];
            }
            
            if (empty($row['variation_option_id'])) {
                if ($tempCart[$stallName][$productId][$request]['non_var'] === null) {
                    $tempCart[$stallName][$productId][$request]['non_var'] = [
                        'product_id'        => $productId,
                        'product_name'      => $row['product_name'],
                        'product_image'     => $row['product_image'],
                        'quantity'          => (int)$row['quantity'],
                        'unit_price'        => floatval($row['price']),
                        'request'           => $request,
                        'stall_id'          => $row['stall_id'],
                        'supported_methods' => $row['supported_methods'],
                        'variation_names'   => [],
                        // For non-variation items, if you already join stocks (or update later), you can set stock here.
                        // Otherwise, you may retrieve stock elsewhere.
                        'stock'             => 0 
                    ];
                } else {
                    $tempCart[$stallName][$productId][$request]['non_var']['quantity'] += (int)$row['quantity'];
                }
            } else {
                $batchKey = $row['created_at'];
                if (!isset($tempCart[$stallName][$productId][$request]['var'][$batchKey])) {
                    $tempCart[$stallName][$productId][$request]['var'][$batchKey] = [
                        'product_id'        => $productId,
                        'product_name'      => $row['product_name'],
                        'product_image'     => $row['product_image'],
                        'quantity'          => (int)$row['quantity'],
                        'unit_price'        => floatval($row['price']),
                        'request'           => $request,
                        'stall_id'          => $row['stall_id'],
                        'supported_methods' => $row['supported_methods'],
                        'variation_names'   => !empty($row['variation_name']) ? [$row['variation_name']] : [],
                        'variation_option_ids' => !empty($row['variation_option_id']) ? [$row['variation_option_id']] : [],
                        'stock'             => 0 // We'll update stock below.
                    ];
                } else {
                    $tempCart[$stallName][$productId][$request]['var'][$batchKey]['unit_price'] += floatval($row['price']);
                    if (!empty($row['variation_name']) &&
                        !in_array($row['variation_name'], $tempCart[$stallName][$productId][$request]['var'][$batchKey]['variation_names'])) {
                        $tempCart[$stallName][$productId][$request]['var'][$batchKey]['variation_names'][] = $row['variation_name'];
                    }
                    if (!in_array($row['variation_option_id'], $tempCart[$stallName][$productId][$request]['var'][$batchKey]['variation_option_ids'])) {
                        $tempCart[$stallName][$productId][$request]['var'][$batchKey]['variation_option_ids'][] = $row['variation_option_id'];
                    }
                }
            }
        }
        
        $finalCart = [];
        foreach ($tempCart as $stallName => $products) {
            if (!isset($finalCart[$stallName])) {
                $finalCart[$stallName] = [];
            }
            foreach ($products as $productId => $requests) {
                foreach ($requests as $request => $data) {
                    if ($data['non_var'] !== null) {
                        // For non-variation items, get their stock via a query
                        $sqlStock = "SELECT COALESCE(SUM(quantity), 0) AS stock 
                                     FROM stocks 
                                     WHERE product_id = ? 
                                     AND variation_option_id IS NULL";
                        $stmtStock = $this->db->connect()->prepare($sqlStock);
                        $stmtStock->execute([$productId]);
                        $stockData = $stmtStock->fetch(PDO::FETCH_ASSOC);
                        $data['non_var']['stock'] = $stockData ? (int)$stockData['stock'] : 0;
                        
                        $finalCart[$stallName][] = $data['non_var'];
                    }
                    if (!empty($data['var'])) {
                        if ($request !== '') {
                            foreach ($data['var'] as $batch) {
                                // Compute minimum stock for the variation options in this batch
                                $minStock = PHP_INT_MAX;
                                foreach ($batch['variation_option_ids'] as $varOptId) {
                                    $sql = "SELECT quantity FROM stocks 
                                            WHERE product_id = ? AND variation_option_id = ?";
                                    $stmt = $this->db->connect()->prepare($sql);
                                    $stmt->execute([$productId, $varOptId]);
                                    $rowStock = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $quantity = $rowStock ? (int)$rowStock['quantity'] : 0;
                                    if ($quantity < $minStock) {
                                        $minStock = $quantity;
                                    }
                                }
                                $batch['stock'] = ($minStock === PHP_INT_MAX ? 0 : $minStock);
                                $finalCart[$stallName][] = $batch;
                            }
                        } else {
                            $mergedBatches = [];
                            foreach ($data['var'] as $batch) {
                                $signature = '';
                                if (!empty($batch['variation_option_ids'])) {
                                    $sorted = $batch['variation_option_ids'];
                                    sort($sorted, SORT_NUMERIC);
                                    $signature = implode(',', $sorted);
                                }
                                if (!isset($mergedBatches[$signature])) {
                                    $mergedBatches[$signature] = $batch;
                                } else {
                                    $mergedBatches[$signature]['quantity'] += $batch['quantity'];
                                }
                            }
                            foreach ($mergedBatches as $batch) {
                                // For merged batches, also determine the minimum stock across the variation options
                                $minStock = PHP_INT_MAX;
                                foreach ($batch['variation_option_ids'] as $varOptId) {
                                    $sql = "SELECT quantity FROM stocks 
                                            WHERE product_id = ? AND variation_option_id = ?";
                                    $stmt = $this->db->connect()->prepare($sql);
                                    $stmt->execute([$productId, $varOptId]);
                                    $rowStock = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $quantity = $rowStock ? (int)$rowStock['quantity'] : 0;
                                    if ($quantity < $minStock) {
                                        $minStock = $quantity;
                                    }
                                }
                                $batch['stock'] = ($minStock === PHP_INT_MAX ? 0 : $minStock);
                                $finalCart[$stallName][] = $batch;
                            }
                        }
                    }
                }
            }
        }
        
        foreach ($finalCart as $stallName => $items) {
            $finalCart[$stallName] = array_values($items);
        }
        
        return $finalCart;
    }
    
    public function createOrder($user_id, $total, $payment_method, $order_type) { 
        $conn = $this->db->connect();
        $sql = "INSERT INTO orders (user_id, total_price, payment_method, order_type)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $total, $payment_method, $order_type]);
        return $conn->lastInsertId();
    }
    
    public function createOrderStall($order_id, $stall_id, $subtotal, $status = 'Pending', $queue_number = null) {
        $conn = $this->db->connect();
        $sql = "INSERT INTO order_stalls (order_id, stall_id, subtotal, status, queue_number)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$order_id, $stall_id, $subtotal, $status, $queue_number]);
        return $conn->lastInsertId();
    }    
    
    public function createOrderItem($order_stall_id, $product_id, $variations, $request, $quantity, $price, $subtotal) {
        $conn = $this->db->connect();
        $sql = "INSERT INTO order_items (order_stall_id, product_id, variations, request, quantity, price, subtotal)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$order_stall_id, $product_id, $variations, $request, $quantity, $price, $subtotal]);
    }
    
    public function placeOrder($user_id, $payment_method, $order_type, $cartGrouped) {
        $conn = $this->db->connect();
    
        $total_order    = 0;
        $stallSubtotals = [];
        $stallIds       = []; 
    
        foreach ($cartGrouped as $stallName => $items) {
            $stall_id   = $items[0]['stall_id'];
            $stallIds[] = $stall_id;
    
            $stall_total = 0;
            foreach ($items as $item) {
                $stall_total += $item['quantity'] * $item['unit_price'];
            }
    
            $stallSubtotals[$stall_id] = $stall_total;
            $total_order             += $stall_total;
        }
    
        if ($total_order <= 0) {
            throw new Exception("No items in your cart to order.");
        }
    
        $conn->beginTransaction();
        try {
            $order_id = $this->createOrder($user_id, $total_order, $payment_method, $order_type);
    
            $isGcash      = (strtolower($payment_method) === 'gcash');
            $stall_status = $isGcash ? 'Preparing' : 'Pending';
    
            foreach ($stallIds as $stall_id) {
                $subtotal = $stallSubtotals[$stall_id];
    
                $queue_number = null;
                if ($stall_status === 'Preparing') {
                    $stmtMax = $conn->prepare("
                      SELECT MAX(queue_number) AS max_queue 
                      FROM order_stalls 
                      WHERE DATE(created_at) = CURDATE() 
                        AND queue_number IS NOT NULL
                    ");
                    $stmtMax->execute();
                    $resMax       = $stmtMax->fetch();
                    $queue_number = $resMax['max_queue']
                                  ? intval($resMax['max_queue']) + 1
                                  : 1;
                }
    
                $order_stall_id = $this->createOrderStall(
                    $order_id,
                    $stall_id,
                    $subtotal,
                    $stall_status,
                    $queue_number
                );
    
                if ($isGcash) {
                    $msg = "Order ID " . str_pad($order_id, 4, '0', STR_PAD_LEFT)
                         . ": Preparing Order";
                    $stmtNoti = $conn->prepare("
                      INSERT INTO notifications
                        (user_id, order_id, stall_id, message)
                      VALUES (?, ?, ?, ?)
                    ");
                    $stmtNoti->execute([
                        $user_id,
                        $order_id,
                        $stall_id,
                        $msg
                    ]);
                }
    
                foreach ($cartGrouped as $name => $items) {
                    if ($items[0]['stall_id'] !== $stall_id) continue;
                    foreach ($items as $item) {
                        $item_subtotal = $item['quantity'] * $item['unit_price'];
                        $variations    = !empty($item['variation_names'])
                                      ? implode(", ", $item['variation_names'])
                                      : null;
    
                        $this->createOrderItem(
                            $order_stall_id,
                            $item['product_id'],
                            $variations,
                            $item['request'],
                            $item['quantity'],
                            $item['unit_price'],
                            $item_subtotal
                        );
    
                        if (!empty($item['variation_option_ids'])) {
                            foreach ($item['variation_option_ids'] as $varOptId) {
                                $stmtStock = $conn->prepare("
                                  UPDATE stocks 
                                  SET quantity = quantity - ? 
                                  WHERE product_id = ? 
                                    AND variation_option_id = ?
                                ");
                                $stmtStock->execute([
                                    $item['quantity'],
                                    $item['product_id'],
                                    $varOptId
                                ]);
                            }
                        } else {
                            $stmtStock = $conn->prepare("
                              UPDATE stocks 
                              SET quantity = quantity - ? 
                              WHERE product_id = ? 
                                AND variation_option_id IS NULL
                            ");
                            $stmtStock->execute([
                                $item['quantity'],
                                $item['product_id']
                            ]);
                        }
                    }
                }
            }
    
            if ($isGcash) {
                $firstStall = $stallIds[0];
                $formatted  = str_pad($order_id, 4, '0', STR_PAD_LEFT);
                $msg        = "Order ID {$formatted}: Payment Confirmed!";
                $stmtNoti   = $conn->prepare("
                  INSERT INTO notifications
                    (user_id, order_id, stall_id, message)
                  VALUES (?, ?, ?, ?)
                ");
                $stmtNoti->execute([
                    $user_id,
                    $order_id,
                    $firstStall,
                    $msg
                ]);
            }
    
            $conn->commit();
            return $order_id;
    
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
    
    
    public function deleteAllItems($user_id, $park_id) {
        $sql = "DELETE c FROM cart c 
                JOIN products p ON c.product_id = p.id 
                JOIN stalls s ON p.stall_id = s.id 
                WHERE c.user_id = ? AND s.park_id = ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$user_id, $park_id]);
    }

    public function deleteCartItem($user_id, $park_id, $product_id, $request = '') {
        $sql = "DELETE c 
                FROM cart c
                JOIN products p ON c.product_id = p.id
                JOIN stalls s ON p.stall_id = s.id
                WHERE c.user_id = ? 
                  AND p.id = ?
                  AND s.park_id = ? 
                  AND (c.request = ? OR (c.request IS NULL AND ? = ''))";
        $stmt = $this->db->connect()->prepare($sql);
        return $stmt->execute([$user_id, $product_id, $park_id, $request, $request]);
    }

    public function removeCartItems($user_id, $park_id, $availableCart) {
        $conn = $this->db->connect();
    
        foreach ($availableCart as $stallName => $items) {
            foreach ($items as $item) {
                // If the product has variation_option_ids, remove all rows with matching product and request.
                if (!empty($item['variation_option_ids']) && is_array($item['variation_option_ids'])) {
                    // Build a placeholder list for the IN clause
                    $placeholders = implode(',', array_fill(0, count($item['variation_option_ids']), '?'));
                    $sql = "DELETE FROM cart 
                            WHERE user_id = ? 
                              AND product_id = ? 
                              AND request = ? 
                              AND variation_option_id IN ($placeholders)";
                    
                    $params = array_merge([$user_id, $item['product_id'], $item['request']], $item['variation_option_ids']);
                    $stmt = $conn->prepare($sql);
                    $stmt->execute($params);
                } else {
                    // For non-variation items, or if variation data isn't available, delete using variation_option_id IS NULL.
                    $sql = "DELETE FROM cart 
                            WHERE user_id = ? 
                              AND product_id = ? 
                              AND variation_option_id IS NULL 
                              AND request = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$user_id, $item['product_id'], $item['request']]);
                }
            }
        }
    }

    
    
}


