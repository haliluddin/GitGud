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
                        'variation_names'   => []  
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
                        'variation_option_ids' => !empty($row['variation_option_id']) ? [$row['variation_option_id']] : []
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
                        $finalCart[$stallName][] = $data['non_var'];
                    }
                    if (!empty($data['var'])) {
                        if ($request !== '') {
                            foreach ($data['var'] as $batch) {
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
    
    public function createOrder($user_id, $total, $payment_method, $order_type, $order_class, $scheduled_time) { 
        $conn = $this->db->connect();
        $sql = "INSERT INTO orders (user_id, total_price, payment_method, order_type, order_class, scheduled_time)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $total, $payment_method, $order_type, $order_class, $scheduled_time]);
        return $conn->lastInsertId();
    }
    
    public function createOrderStall($order_id, $stall_id, $subtotal) {
        $conn = $this->db->connect();
        $sql = "INSERT INTO order_stalls (order_id, stall_id, subtotal)
                VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$order_id, $stall_id, $subtotal]);
        return $conn->lastInsertId();
    }
    
    public function createOrderItem($order_stall_id, $product_id, $variations, $request, $quantity, $price, $subtotal) {
        $conn = $this->db->connect();
        $sql = "INSERT INTO order_items (order_stall_id, product_id, variations, request, quantity, price, subtotal)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$order_stall_id, $product_id, $variations, $request, $quantity, $price, $subtotal]);
    }
    
    public function placeOrder($user_id, $payment_method, $order_type, $order_class, $scheduled_time, $cartGrouped) {
        $conn = $this->db->connect();
        $total_order = 0;
        $stallSubtotals = [];  
    
        foreach ($cartGrouped as $stallName => $items) {
            $stall_total = 0;
            foreach ($items as $item) {
                $stall_total += $item['quantity'] * $item['unit_price'];
            }
            $stall_id = $items[0]['stall_id'];
            $stallSubtotals[$stall_id] = $stall_total;
            $total_order += $stall_total;
        }
        if ($total_order <= 0) {
            throw new Exception("No items in your cart to order.");
        }
        
        $conn->beginTransaction();
        try {
            $order_id = $this->createOrder($user_id, $total_order, $payment_method, $order_type, $order_class, $scheduled_time);
        
            foreach ($cartGrouped as $stallName => $items) {
                $stall_id = $items[0]['stall_id'];
                $subtotal = $stallSubtotals[$stall_id];
                $order_stall_id = $this->createOrderStall($order_id, $stall_id, $subtotal);
        
                foreach ($items as $item) {
                    $item_subtotal = $item['quantity'] * $item['unit_price'];
                    $variations = (!empty($item['variation_names'])) ? implode(", ", $item['variation_names']) : null;
                    $this->createOrderItem($order_stall_id, $item['product_id'], $variations, $item['request'], $item['quantity'], $item['unit_price'], $item_subtotal);
                    
                    if (!empty($item['variation_option_ids'])) {
                        foreach ($item['variation_option_ids'] as $varOptId) {
                            $sqlStock = "UPDATE stocks 
                                         SET quantity = quantity - ? 
                                         WHERE product_id = ? 
                                         AND variation_option_id = ?";
                            $stmtStock = $conn->prepare($sqlStock);
                            $stmtStock->execute([
                                $item['quantity'], 
                                $item['product_id'], 
                                $varOptId
                            ]);
                        }
                    } else {
                        $sqlStock = "UPDATE stocks 
                                     SET quantity = quantity - ? 
                                     WHERE product_id = ? 
                                     AND variation_option_id IS NULL";
                        $stmtStock = $conn->prepare($sqlStock);
                        $stmtStock->execute([
                            $item['quantity'], 
                            $item['product_id']
                        ]);
                    }
                }
            }
            $conn->commit();
            return $order_id;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
    
    
    
}


