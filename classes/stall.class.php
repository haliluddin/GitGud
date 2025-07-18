<?php
require_once 'db.php';

class Stall {
    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    public function getStallId($userId, $parkId){
        $sql = "
          SELECT id 
            FROM stalls 
           WHERE user_id = :user_id
             AND park_id = :park_id
           LIMIT 1
        ";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([
          ':user_id' => $userId,
          ':park_id' => $parkId
        ]);
        $row = $stmt->fetch();
        return $row ? $row['id'] : false;
    }
    

    public function getStall($stallId){
        $sql = "SELECT * FROM stalls WHERE id = :id;";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':id' => $stallId));
        $result = $query->fetch();

        if ($result === false) {
            return false;
        }
        return $result;
    }

    public function getProducts($stallId) {
        $sql = "SELECT p.*, c.name AS category_name, 
                    COALESCE(SUM(s.quantity), 0) AS stock 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                LEFT JOIN stocks s ON p.id = s.product_id 
                WHERE p.stall_id = :stall_id 
                GROUP BY p.id;";
        
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':stall_id' => $stallId));
        $result = $query->fetchAll();
     
        return $result ?: [];
    }
    
    public function getProductVariations($productId) {
        $sql = "SELECT * FROM product_variations WHERE product_id = ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getVariationOptions($variationId) {
        $sql = "SELECT * FROM variation_options WHERE variation_id = ?";
        $stmt =$this->db->connect()->prepare($sql);
        $stmt->execute([$variationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getStock($productId, $variationOptionId) {
        $sql = "SELECT quantity FROM stocks WHERE product_id = ? AND variation_option_id = ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$productId, $variationOptionId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['quantity'] : 0;
    }
    
    public function getProductById($productId) {
        $sql = "SELECT p.*, c.name AS category_name, 
            COALESCE(SUM(s.quantity), 0) AS stock 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            LEFT JOIN stocks s ON p.id = s.product_id 
            WHERE p.id = ?
            GROUP BY p.id;";
            
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addInventory($product_id, $variation_option_id, $type, $quantity, $reason) {
        $conn = $this->db->connect();
        
        $conn->beginTransaction();
        
        try {
            $sql = "INSERT INTO inventory (product_id, variation_option_id, type, quantity, reason) 
                    VALUES (:product_id, :variation_option_id, :type, :quantity, :reason)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindValue(':type', $type, PDO::PARAM_STR);
            $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindValue(':reason', $reason, PDO::PARAM_STR);
            if ($variation_option_id === null) {
                $stmt->bindValue(':variation_option_id', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':variation_option_id', $variation_option_id, PDO::PARAM_INT);
            }
            $stmt->execute();
    
            $sqlCheck = "SELECT quantity FROM stocks WHERE product_id = :product_id AND variation_option_id " . 
                        ($variation_option_id === null ? "IS NULL" : "= :variation_option_id");
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            if ($variation_option_id !== null) {
                $stmtCheck->bindValue(':variation_option_id', $variation_option_id, PDO::PARAM_INT);
            }
            $stmtCheck->execute();
            $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                if ($type === 'Stock In') {
                    $newQuantity = $row['quantity'] + $quantity;
                } else {
                    $newQuantity = $row['quantity'] - $quantity;
                    if ($newQuantity < 0) {
                        $newQuantity = 0;
                    }
                }
                $sqlUpdate = "UPDATE stocks SET quantity = :quantity WHERE product_id = :product_id AND variation_option_id " .
                             ($variation_option_id === null ? "IS NULL" : "= :variation_option_id");
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bindValue(':quantity', $newQuantity, PDO::PARAM_INT);
                $stmtUpdate->bindValue(':product_id', $product_id, PDO::PARAM_INT);
                if ($variation_option_id !== null) {
                    $stmtUpdate->bindValue(':variation_option_id', $variation_option_id, PDO::PARAM_INT);
                }
                $stmtUpdate->execute();
            } else {
                $newQuantity = ($type === 'Stock In') ? $quantity : 0;
                $sqlInsert = "INSERT INTO stocks (product_id, variation_option_id, quantity)
                              VALUES (:product_id, :variation_option_id, :quantity)";
                $stmtInsert = $conn->prepare($sqlInsert);
                $stmtInsert->bindValue(':product_id', $product_id, PDO::PARAM_INT);
                if ($variation_option_id === null) {
                    $stmtInsert->bindValue(':variation_option_id', null, PDO::PARAM_NULL);
                } else {
                    $stmtInsert->bindValue(':variation_option_id', $variation_option_id, PDO::PARAM_INT);
                }
                $stmtInsert->bindValue(':quantity', $newQuantity, PDO::PARAM_INT);
                $stmtInsert->execute();
            }
            
            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            return false;
        }
    }
    
    public function getInventory($product_id, $type, $variation_option_id = null) {
        if ($variation_option_id !== null) {
            $sql = "SELECT * FROM inventory 
                    WHERE product_id = :product_id 
                      AND type = :type 
                      AND variation_option_id = :variation_option_id 
                    ORDER BY created_at DESC";
            $stmt = $this->db->connect()->prepare($sql);
            $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindValue(':type', $type, PDO::PARAM_STR);
            $stmt->bindValue(':variation_option_id', $variation_option_id, PDO::PARAM_INT);
        } else {
            $sql = "SELECT * FROM inventory 
                    WHERE product_id = :product_id 
                      AND type = :type 
                    ORDER BY created_at DESC";
            $stmt = $this->db->connect()->prepare($sql);
            $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindValue(':type', $type, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getConnection() {
        return $this->db->connect();
    }
    
    public function getTotalProducts($stallId) {
        $sql = "SELECT COUNT(*) AS total_products FROM products WHERE stall_id = :stall_id;";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':stall_id' => $stallId));
        $result = $query->fetch();

        if ($result === false) {
            return false;
        }
        return $result['total_products'];
    }

    /**
     * Update an inventory record and adjust the stock accordingly
     * 
     * @param int $inventory_id The ID of the inventory record to update
     * @param int $product_id The product ID
     * @param int|null $variation_option_id The variation option ID (nullable)
     * @param string $type The inventory type (Stock In or Stock Out)
     * @param int $quantity The new quantity
     * @param string $reason The reason for the update
     * @return bool True if successful, false otherwise
     */
    public function updateInventory($inventory_id, $product_id, $variation_option_id, $type, $quantity, $reason) {
        $conn = $this->db->connect();
        
        $conn->beginTransaction();
        
        try {
            // First, get the original inventory record to calculate the difference
            $sqlOriginal = "SELECT quantity, type FROM inventory WHERE id = :id";
            $stmtOriginal = $conn->prepare($sqlOriginal);
            $stmtOriginal->bindValue(':id', $inventory_id, PDO::PARAM_INT);
            $stmtOriginal->execute();
            $originalRecord = $stmtOriginal->fetch(PDO::FETCH_ASSOC);
            
            if (!$originalRecord) {
                throw new Exception("Original inventory record not found");
            }
            
            // Update the inventory record
            $sql = "UPDATE inventory SET quantity = :quantity, reason = :reason 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindValue(':reason', $reason, PDO::PARAM_STR);
            $stmt->bindValue(':id', $inventory_id, PDO::PARAM_INT);
            $stmt->execute();
            
            // Adjust the stock based on the difference
            $originalQuantity = $originalRecord['quantity'];
            $originalType = $originalRecord['type'];
            
            // Get current stock
            $sqlCheck = "SELECT quantity FROM stocks WHERE product_id = :product_id AND variation_option_id " . 
                       ($variation_option_id === null ? "IS NULL" : "= :variation_option_id");
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            if ($variation_option_id !== null) {
                $stmtCheck->bindValue(':variation_option_id', $variation_option_id, PDO::PARAM_INT);
            }
            $stmtCheck->execute();
            $currentStock = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if (!$currentStock) {
                throw new Exception("Stock record not found");
            }
            
            // Calculate what the new stock should be
            $stockAdjustment = 0;
            
            // If the type hasn't changed, just adjust by the difference
            if ($type == $originalType) {
                if ($type == 'Stock In') {
                    $stockAdjustment = $quantity - $originalQuantity;
                } else { // Stock Out
                    $stockAdjustment = $originalQuantity - $quantity;
                }
            } else {
                // Type has changed (e.g., from Stock In to Stock Out or vice versa)
                if ($originalType == 'Stock In') {
                    // Was Stock In, now Stock Out
                    $stockAdjustment = -$originalQuantity - $quantity;
                } else {
                    // Was Stock Out, now Stock In
                    $stockAdjustment = $originalQuantity + $quantity;
                }
            }
            
            // Apply the adjustment to the current stock
            $newStock = $currentStock['quantity'] + $stockAdjustment;
            if ($newStock < 0) {
                $newStock = 0;
            }
            
            // Update the stock
            $sqlUpdate = "UPDATE stocks SET quantity = :quantity 
                         WHERE product_id = :product_id AND variation_option_id " .
                         ($variation_option_id === null ? "IS NULL" : "= :variation_option_id");
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bindValue(':quantity', $newStock, PDO::PARAM_INT);
            $stmtUpdate->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            if ($variation_option_id !== null) {
                $stmtUpdate->bindValue(':variation_option_id', $variation_option_id, PDO::PARAM_INT);
            }
            $stmtUpdate->execute();
            
            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            return false;
        }
    }

    /**
     * Delete an inventory record and adjust the stock accordingly
     * 
     * @param int $inventory_id The ID of the inventory record to delete
     * @param int $product_id The product ID
     * @param int|null $variation_option_id The variation option ID (nullable)
     * @return bool True if successful, false otherwise
     */
    public function deleteInventory($inventory_id, $product_id, $variation_option_id) {
        $conn = $this->db->connect();
        
        $conn->beginTransaction();
        
        try {
            // First, get the inventory record to know how to adjust the stock
            $sql = "SELECT quantity, type FROM inventory WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $inventory_id, PDO::PARAM_INT);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$record) {
                throw new Exception("Inventory record not found");
            }
            
            // Delete the record
            $sqlDelete = "DELETE FROM inventory WHERE id = :id";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->bindValue(':id', $inventory_id, PDO::PARAM_INT);
            $stmtDelete->execute();
            
            // Get current stock
            $sqlCheck = "SELECT quantity FROM stocks WHERE product_id = :product_id AND variation_option_id " . 
                       ($variation_option_id === null ? "IS NULL" : "= :variation_option_id");
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            if ($variation_option_id !== null) {
                $stmtCheck->bindValue(':variation_option_id', $variation_option_id, PDO::PARAM_INT);
            }
            $stmtCheck->execute();
            $currentStock = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            if (!$currentStock) {
                throw new Exception("Stock record not found");
            }
            
            // Adjust stock based on the deleted record
            $newStock = $currentStock['quantity'];
            if ($record['type'] == 'Stock In') {
                $newStock -= $record['quantity'];
            } else { // Stock Out
                $newStock += $record['quantity'];
            }
            
            if ($newStock < 0) {
                $newStock = 0;
            }
            
            // Update the stock
            $sqlUpdate = "UPDATE stocks SET quantity = :quantity 
                         WHERE product_id = :product_id AND variation_option_id " .
                         ($variation_option_id === null ? "IS NULL" : "= :variation_option_id");
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bindValue(':quantity', $newStock, PDO::PARAM_INT);
            $stmtUpdate->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            if ($variation_option_id !== null) {
                $stmtUpdate->bindValue(':variation_option_id', $variation_option_id, PDO::PARAM_INT);
            }
            $stmtUpdate->execute();
            
            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            return false;
        }
    }

    public function getUserOrders($user_id, $park_id) {
        $sql = "SELECT 
                    o.id AS order_id,
                    o.created_at AS order_date,
                    o.payment_method,
                    o.order_type,
                    os.id AS order_stall_id,
                    os.stall_id,
                    os.status AS order_status,
                    os.subtotal AS stall_subtotal,
                    os.queue_number,
                    s.name AS stall_name,
                    s.park_id,
                    p.name AS product_name,
                    p.image AS product_image,
                    oi.product_id,
                    oi.variations,
                    oi.request,
                    oi.quantity, 
                    oi.price,
                    oi.subtotal AS item_subtotal
                FROM orders o
                JOIN order_stalls os ON o.id = os.order_id
                JOIN stalls s ON os.stall_id = s.id
                JOIN order_items oi ON os.id = oi.order_stall_id
                JOIN products p ON oi.product_id = p.id
                WHERE o.user_id = ? 
                  AND s.park_id = ?
                ORDER BY o.created_at DESC, os.status";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$user_id, $park_id]);
        return $stmt->fetchAll();
    }
    
    public function getStallOrders($stall_id) {
        $sql = "SELECT 
            o.id AS order_id,
            o.payment_method,
            o.order_type,
            o.user_id,
            o.created_at AS order_date,
            os.id AS order_stall_id,
            os.status AS order_status,
            os.subtotal AS stall_subtotal,
            os.queue_number,
            os.cancellation_reason,  -- Added field here
            p.name AS product_name,
            p.image AS product_image,
            oi.product_id,
            oi.variations,
            oi.request,
            oi.quantity,
            oi.price,
            oi.subtotal AS item_subtotal
        FROM orders o
        JOIN order_stalls os ON o.id = os.order_id
        JOIN order_items oi ON os.id = oi.order_stall_id
        JOIN products p ON oi.product_id = p.id
        WHERE os.stall_id = ?
        ORDER BY o.created_at ASC, os.status";

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$stall_id]);
        return $stmt->fetchAll();
    }

    public function getNotifications($user_id, $park_id){
        $stmt = $this->db->connect()->prepare("
            SELECT n.*, s.logo, s.name 
            FROM notifications n
            JOIN stalls s ON n.stall_id = s.id
            WHERE n.user_id = ? 
              AND s.park_id = ?
            ORDER BY n.created_at DESC
        ");
        $stmt->execute([$user_id, $park_id]);
        return $stmt->fetchAll();
    }
    
    public function createNotification($user_id, $order_id, $stall_id, $message) {
        $sql = "INSERT INTO notifications (user_id, order_id, stall_id, message) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->connect()->prepare($sql);
        return $stmt->execute([$user_id, $order_id, $stall_id, $message]);
    }

    public function getOrderDetails($order_id) {
        $db = $this->db->connect();
        $sql = "
            SELECT 
                b.business_name,
                b.barangay,
                b.street_building_house,
                s.name AS stall_name,
                p.name AS product_name,
                o.id AS order_id,
                o.created_at AS order_created_at,
                o.total_price,
                os.subtotal AS stall_subtotal,
                oi.quantity,
                oi.price,
                oi.subtotal AS item_subtotal
            FROM orders o
            JOIN order_stalls os ON o.id = os.order_id
            JOIN stalls s ON os.stall_id = s.id
            JOIN business b ON s.park_id = b.id
            JOIN order_items oi ON os.id = oi.order_stall_id
            JOIN products p ON oi.product_id = p.id
            WHERE o.id = :order_id
            ORDER BY s.id, oi.created_at
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute(['order_id' => $order_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$rows) {
            return false;
        }
        
        $orderDetails = [];
        
        $orderDetails['order'] = [
             'order_id'            => $rows[0]['order_id'],
             'order_created_at'    => $rows[0]['order_created_at'],
             'total_price'         => $rows[0]['total_price'],
             'business_name'       => $rows[0]['business_name'],
             'barangay'            => $rows[0]['barangay'],
             'street_building_house'=> $rows[0]['street_building_house']
        ];
        
        $orderDetails['stalls'] = [];
        foreach ($rows as $row) {
             $stallName = $row['stall_name'];
             if (!isset($orderDetails['stalls'][$stallName])) {
                  $orderDetails['stalls'][$stallName] = [
                         'stall_name'       => $stallName,
                         'stall_subtotal'   => $row['stall_subtotal'],
                         'order_items'      => []
                  ];
             }
             $orderDetails['stalls'][$stallName]['order_items'][] = [
                   'product_name'  => $row['product_name'],
                   'quantity'      => $row['quantity'],
                   'price'         => $row['price'],
                   'item_subtotal' => $row['item_subtotal']
             ];
        }
        
        return $orderDetails;
    }
    
    


    /*public function getStallCreationDate($stall_id){ 
        $stmt = $this->db->connect()->prepare("SELECT created_at FROM stalls WHERE id = ?");
        $stmt->execute([$stall_id]);
        return $stmt->fetchColumn();
    }
    
    private function getSalesReport($stall_id, $start, $end){
        $stmt = $this->db->connect()->prepare("
            SELECT COUNT(*) as totalOrders, COALESCE(SUM(subtotal),0) as totalSales 
            FROM order_items 
            WHERE order_stall_id IN (
                SELECT id FROM order_stalls WHERE stall_id = ? AND status IN ('Preparing', 'Ready', 'Completed')
            ) 
            AND created_at BETWEEN ? AND ?
        ");
        $stmt->execute([$stall_id, $start, $end]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getSalesToday($stall_id, $start = null, $end = null){
        if(!$start || !$end) {
            $today = date("Y-m-d");
            $start = $today." 00:00:00";
            $end   = $today." 23:59:59";
        }
        return $this->getSalesReport($stall_id, $start, $end);
    }
    
    public function getSalesYesterday($stall_id, $start = null, $end = null){
        if(!$start || !$end) {
            $yesterday = date("Y-m-d", strtotime("-1 day"));
            $start = $yesterday." 00:00:00";
            $end   = $yesterday." 23:59:59";
        }
        return $this->getSalesReport($stall_id, $start, $end);
    }
    
    public function getSales7Days($stall_id, $start = null, $end = null){
        if(!$start || !$end) {
            $created = $this->getStallCreationDate($stall_id);
            $start = date("Y-m-d 00:00:00", strtotime($created));
            $end   = date("Y-m-d 23:59:59", strtotime($created." +6 days"));
        }
        return $this->getSalesReport($stall_id, $start, $end);
    }
    
    public function getSales30Days($stall_id, $start = null, $end = null){
        if(!$start || !$end) {
            $created = $this->getStallCreationDate($stall_id);
            $start = date("Y-m-d 00:00:00", strtotime($created));
            $end   = date("Y-m-d 23:59:59", strtotime($created." +29 days"));
        }
        return $this->getSalesReport($stall_id, $start, $end);
    }
    
    public function getSales1Year($stall_id, $start = null, $end = null){
        if(!$start || !$end) {
            $created = $this->getStallCreationDate($stall_id);
            $start = date("Y-m-d 00:00:00", strtotime($created));
            $end   = date("Y-m-d 23:59:59", strtotime($created." +364 days"));
        }
        return $this->getSalesReport($stall_id, $start, $end);
    }
    
    public function getProductsReport($stall_id, $start, $end){
        $stmt = $this->db->connect()->prepare("
            SELECT p.name, COALESCE(SUM(oi.quantity),0) as order_count, COALESCE(SUM(oi.subtotal),0) as sales 
            FROM products p 
            LEFT JOIN order_items oi ON p.id = oi.product_id 
                AND oi.created_at BETWEEN ? AND ?
                AND oi.order_stall_id IN (
                    SELECT id FROM order_stalls WHERE status IN ('Preparing', 'Ready', 'Completed')
                )
            WHERE p.stall_id = ?
            GROUP BY p.id
        ");
        $stmt->execute([$start, $end, $stall_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLiveOpsMonitor($stall_id, $start, $end){
        $data = [];
        
        $stmt = $this->db->connect()->prepare("
            SELECT COUNT(*) as canceled_orders 
            FROM order_stalls 
            WHERE stall_id = ? AND status = 'Canceled' AND created_at BETWEEN ? AND ?
        ");
        $stmt->execute([$stall_id, $start, $end]);
        $data['canceled_orders'] = $stmt->fetchColumn();
        
        $stmt = $this->db->connect()->prepare("
            SELECT o.user_id, COUNT(*) as orders 
            FROM orders o 
            JOIN order_stalls os ON o.id = os.order_id 
            WHERE os.stall_id = ? AND o.created_at BETWEEN ? AND ? AND os.status IN ('Preparing', 'Ready', 'Completed')
            GROUP BY o.user_id
        ");
        $stmt->execute([$stall_id, $start, $end]);
        $new = 0;
        $repeated = 0;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            if($row['orders'] == 1){
                $new++;
            } else {
                $repeated++;
            }
        }
        $data['new_customers'] = $new;
        $data['repeated_customers'] = $repeated;
        
        $stmt = $this->db->connect()->prepare("
            SELECT COUNT(*) 
            FROM products p 
            WHERE p.stall_id = ? 
              AND p.id NOT IN (
                  SELECT DISTINCT product_id 
                  FROM order_items oi 
                  JOIN order_stalls os ON oi.order_stall_id = os.id 
                  WHERE os.stall_id = ? 
                    AND oi.created_at BETWEEN ? AND ? 
                    AND os.status IN ('Preparing', 'Ready', 'Completed')
              )
        ");
        $stmt->execute([$stall_id, $stall_id, $start, $end]);
        $data['no_sales'] = $stmt->fetchColumn();
        
        return $data;
    }
    
    public function getOperationsHealth($stall_id, $start, $end){
        $data = [];
        $stmt = $this->db->connect()->prepare("
            SELECT o.payment_method, SUM(o.total_price) as sales 
            FROM orders o 
            JOIN order_stalls os ON o.id = os.order_id 
            WHERE os.stall_id = ? AND o.created_at BETWEEN ? AND ? AND os.status IN ('Preparing', 'Ready', 'Completed')
            GROUP BY o.payment_method
        ");
        $stmt->execute([$stall_id, $start, $end]);
        $online = 0;
        $cash = 0;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            if(strtolower($row['payment_method']) == 'cash'){
                $cash += $row['sales'];
            } else {
                $online += $row['sales'];
            }
        }
        $data['GCash'] = $online;
        $data['Cash'] = $cash;
        
        $stmt = $this->db->connect()->prepare("
            SELECT o.order_type, SUM(o.total_price) as sales 
            FROM orders o 
            JOIN order_stalls os ON o.id = os.order_id 
            WHERE os.stall_id = ? AND o.created_at BETWEEN ? AND ? AND os.status IN ('Preparing', 'Ready', 'Completed')
            GROUP BY o.order_type
        ");
        $stmt->execute([$stall_id, $start, $end]);
        $dineIn = 0;
        $takeOut = 0;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            if(strtolower($row['order_type']) == 'dine in'){
                $dineIn += $row['sales'];
            } else if(strtolower($row['order_type']) == 'take out'){
                $takeOut += $row['sales'];
            }
        }
        $data['Dine In'] = $dineIn;
        $data['Take Out'] = $takeOut;
        
        $stmt = $this->db->connect()->prepare("
            SELECT COALESCE(SUM(subtotal),0) as lost_sales 
            FROM order_stalls 
            WHERE stall_id = ? AND status = 'Canceled' AND created_at BETWEEN ? AND ?
        ");
        $stmt->execute([$stall_id, $start, $end]);
        $data['lost_sales'] = $stmt->fetchColumn();
        
        $stmt = $this->db->connect()->prepare("
            SELECT AVG(TIMESTAMPDIFF(MINUTE, os.created_at, os.updated_at)) as avg_prep_time 
            FROM order_stalls os 
            WHERE os.stall_id = ? 
            AND os.status = 'Ready'
            AND os.created_at BETWEEN ? AND ?
        ");
        $stmt->execute([$stall_id, $start, $end]);
        $avg = $stmt->fetchColumn();
        $data['avg_prep_time'] = $avg ? round($avg) : 0;
    
        return $data;
    }
    
    public function getHighestSellingProducts($stall_id, $start, $end){
        $stmt = $this->db->connect()->prepare("
            SELECT p.name, SUM(oi.quantity) as order_count, SUM(oi.subtotal) as sales 
            FROM order_items oi 
            JOIN order_stalls os ON oi.order_stall_id = os.id 
            JOIN products p ON oi.product_id = p.id 
            WHERE os.stall_id = ? AND oi.created_at BETWEEN ? AND ? AND os.status IN ('Preparing', 'Ready', 'Completed')
            GROUP BY p.id 
            ORDER BY sales DESC 
            LIMIT 5
        ");
        $stmt->execute([$stall_id, $start, $end]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } */

    public function deleteallread($user_id){
        $stmt = $this->db->connect()->prepare("
            DELETE FROM notifications
            WHERE user_id = ?
            AND status = 'Read'
        ");
        $stmt->execute([$user_id]);
    } 

    public function markallread($user_id){
        $stmt = $this->db->connect()->prepare("UPDATE notifications SET status = 'Read' WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }
    
    public function saveRatings(int $user_id, int $order_stall_id, array $ratings): bool {
        $pdo = $this->db->connect();
        $sql = "INSERT INTO ratings
                 (user_id, order_stall_id, product_id, variations, rating_value, comment)
                VALUES
                 (:user_id, :order_stall_id, :product_id, :variations, :rating_value, :comment)";
        $stmt = $pdo->prepare($sql);
    
        foreach ($ratings as $r) {
            $stmt->execute([
                'user_id'         => $user_id,
                'order_stall_id'  => $order_stall_id,
                'product_id'      => $r['product_id'],
                'variations'      => $r['variations'] ?? null,
                'rating_value'    => $r['rating_value'],
                'comment'         => $r['comment'] ?? null,
            ]);
        }
    
        return true;
    }

    public function getRatingDetails(int $user_id, int $order_stall_id, int $product_id, ?string $variations): ?array {
        $pdo = $this->db->connect();
    
        $sql = "
          SELECT 
            r.rating_value,
            r.comment,
            r.variations,
            r.created_at,
            r.seller_response,
            r.response_at,
            COUNT(rh.id) AS helpful_count
          FROM ratings r
          LEFT JOIN rating_helpful rh ON rh.rating_id = r.id
          WHERE 
            r.user_id = :user_id
            AND r.order_stall_id = :osid
            AND r.product_id = :pid
            AND (
              (r.variations = :vars) 
              OR (r.variations IS NULL AND :vars IS NULL)
            )
          GROUP BY r.id
          LIMIT 1
        ";
    
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          'user_id' => $user_id,
          'osid'    => $order_stall_id,
          'pid'     => $product_id,
          'vars'    => $variations
        ]);
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function isOwner(int $stall_id, int $user_id): bool {
        $sql = "SELECT COUNT(*) FROM stalls WHERE id=:sid AND user_id=:uid";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute(['sid'=>$stall_id,'uid'=>$user_id]);
        return (bool)$stmt->fetchColumn();
    }
    
    public function saveSellerResponse(int $review_id, string $response): void {
        $sql = "UPDATE ratings SET seller_response=:resp, response_at=NOW() WHERE id=:rid";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute(['resp'=>$response,'rid'=>$review_id]);
    }
    

    public function getSalesByDay($stall_id, $start, $end) {
        $sql = "
          SELECT DATE(o.created_at) AS date,
                 SUM(oi.subtotal)    AS total_sales
          FROM order_items oi
          JOIN order_stalls os ON oi.order_stall_id = os.id
          JOIN orders       o  ON os.order_id        = o.id
          WHERE os.stall_id = ?
            AND DATE(o.created_at) BETWEEN ? AND ?
            AND os.status IN ('Preparing','Ready','Completed')   -- ← include these only
          GROUP BY DATE(o.created_at)
          ORDER BY DATE(o.created_at)
        ";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$stall_id, $start, $end]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getOrdersByDay($stall_id, $start, $end) {
        $sql = "
          SELECT DATE(created_at) AS date,
                 COUNT(*)         AS total_orders
          FROM order_stalls
          WHERE stall_id = ?
            AND DATE(created_at) BETWEEN ? AND ?
            AND status IN ('Preparing','Ready','Completed')   -- ← include these only
          GROUP BY DATE(created_at)
          ORDER BY DATE(created_at)
        ";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$stall_id, $start, $end]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getSalesByMenuItem($stall_id, $start, $end, $limit, $offset) {
        $limit  = (int)$limit;
        $offset = (int)$offset;
        $sql = "
          SELECT 
            p.id            AS product_id,
            p.name          AS product_name,
            SUM(oi.subtotal) AS total_sales,
            COUNT(*)        AS order_count
          FROM order_items oi
          JOIN order_stalls os ON oi.order_stall_id = os.id
          JOIN orders       o  ON os.order_id        = o.id
          JOIN products     p  ON oi.product_id      = p.id
          WHERE os.stall_id = ?
            AND DATE(o.created_at) BETWEEN ? AND ?
            AND os.status IN ('Preparing','Ready','Completed')   -- ← include these only
          GROUP BY p.id, p.name
          ORDER BY total_sales DESC
          LIMIT {$limit} OFFSET {$offset}
        ";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$stall_id, $start, $end]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getLiveOpsMonitor($stall_id, $start, $end) {
        $conn = $this->db->connect();
    
        // 1) Canceled orders
        $stmt = $conn->prepare("
          SELECT COUNT(*) 
            FROM order_stalls 
           WHERE stall_id = ?
             AND DATE(created_at) BETWEEN ? AND ?
             AND status = 'Canceled'
        ");
        $stmt->execute([$stall_id, $start, $end]);
        $canceled = (int)$stmt->fetchColumn();
    
        // 2) New vs. repeated customers within the period
        $stmt = $conn->prepare("
          SELECT
            SUM(CASE WHEN cnt = 1 THEN 1 ELSE 0 END) AS new_customers,
            SUM(CASE WHEN cnt > 1 THEN 1 ELSE 0 END) AS repeated_customers
          FROM (
            SELECT o.user_id,
                   COUNT(*) AS cnt
              FROM orders o
              JOIN order_stalls os 
                ON o.id = os.order_id
             WHERE os.stall_id = ?
               AND DATE(o.created_at) BETWEEN ? AND ?
               AND os.status IN ('Preparing','Ready','Completed')
             GROUP BY o.user_id
          ) AS per_user
        ");
        $stmt->execute([$stall_id, $start, $end]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // 3) **Products with zero sales in period**
        $stmt = $conn->prepare("
          SELECT COUNT(*) 
            FROM products p
           WHERE p.stall_id = ?
             AND NOT EXISTS (
               SELECT 1
                 FROM order_items oi
                 JOIN order_stalls os 
                   ON oi.order_stall_id = os.id
                WHERE os.stall_id = p.stall_id
                  AND oi.product_id = p.id
                  AND DATE(os.created_at) BETWEEN ? AND ?
                  AND os.status IN ('Preparing','Ready','Completed')
             )
        ");
        $stmt->execute([$stall_id, $start, $end]);
        $zeroSalesProducts = (int)$stmt->fetchColumn();
    
        return [
          'canceled_orders'    => $canceled,
          'new_customers'      => (int)$row['new_customers'],
          'repeated_customers' => (int)$row['repeated_customers'],
          'no_product_sales'   => $zeroSalesProducts,    // ← now products with zero sales
        ];
    }

    public function getPaymentMethodBreakdown($stall_id, $start, $end) {
        $sql = "
          SELECT o.payment_method,
                 SUM(os.subtotal) AS total_amount
          FROM order_stalls os
          JOIN orders o ON os.order_id = o.id
          WHERE os.stall_id = ?
            AND DATE(os.created_at) BETWEEN ? AND ?
            AND os.status IN ('Preparing','Ready','Completed')   -- ← include these only
          GROUP BY o.payment_method
        ";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$stall_id, $start, $end]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    

    public function getOrderTypeBreakdown($stall_id, $start, $end) {
        $sql = "
          SELECT o.order_type,
                 SUM(os.subtotal) AS total_amount
          FROM order_stalls os
          JOIN orders o ON os.order_id = o.id
          WHERE os.stall_id = ?
            AND DATE(os.created_at) BETWEEN ? AND ?
            AND os.status IN ('Preparing','Ready','Completed')   -- ← include these only
          GROUP BY o.order_type
        ";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$stall_id, $start, $end]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    

    public function getAvgPreparationTime($stall_id, $start, $end) {
        $sql = "
          SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) AS avg_min
          FROM order_stalls
          WHERE stall_id=? AND DATE(created_at) BETWEEN ? AND ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$stall_id,$start,$end]);
        return (float)$stmt->fetchColumn();
    }

    public function getLostSalesDueToCancel($stall_id, $start, $end) {
        $sql = "
          SELECT SUM(subtotal) FROM order_stalls
          WHERE stall_id=? AND DATE(created_at) BETWEEN ? AND ? AND status='Canceled'";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$stall_id,$start,$end]);
        return (float)$stmt->fetchColumn();
    }

    public function getMenuViewCount($stall_id, $start, $end) {
        $sql = "
          SELECT COUNT(*) FROM menu_views
          WHERE stall_id=? AND DATE(viewed_at) BETWEEN ? AND ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$stall_id,$start,$end]);
        return (int)$stmt->fetchColumn();
    }

    public function getOrderPlacementCount($stall_id, $start, $end) {
        $sql = "
          SELECT COUNT(DISTINCT o.user_id)
          FROM orders o
          JOIN order_stalls os ON o.id=os.order_id
          WHERE os.stall_id=? AND DATE(o.created_at) BETWEEN ? AND ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$stall_id,$start,$end]);
        return (int)$stmt->fetchColumn();
    }

    public function getCustomerConversion($stall_id, $start, $end) {
        $views  = $this->getMenuViewCount($stall_id,$start,$end);
        $orders = $this->getOrderPlacementCount($stall_id,$start,$end);
        return ['viewed'=>$views,'ordered'=>$orders];
    }
    
    public function logMenuView(int $stall_id, ?int $user_id): bool {
        $sql = "INSERT INTO menu_views (stall_id, user_id) VALUES (?, ?)";
        $stmt = $this->db->connect()->prepare($sql);
        return $stmt->execute([$stall_id, $user_id]);
    }
    
}
