<?php
require_once 'db.php';

class Stall {
    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    public function getStallId($userId){
        $sql = "SELECT id FROM stalls WHERE user_id = :user_id;";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':user_id' => $userId));
        $result = $query->fetch();

        if ($result === false) {
            return false;
        }
        return $result['id'];
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
        ORDER BY o.created_at DESC, os.status";

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$stall_id]);
        return $stmt->fetchAll();
    }

    public function createNotification($user_id, $order_id, $stall_id, $title, $message) {
        $sql = "INSERT INTO notifications (user_id, order_id, stall_id, title, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->connect()->prepare($sql);
        return $stmt->execute([$user_id, $order_id, $stall_id, $title, $message]);
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



    public function getStallCreationDate($stall_id){ 
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
    }
    
    
}
