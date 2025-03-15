<?php
require_once 'db.php';

class Product {
    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    function addProduct($stall_id, $productName, $productCode, $category, $description, $basePrice, $discount, $startDate, $endDate, $imagePath) {
        $db = $this->db->connect();  
        $sql = "INSERT INTO products (stall_id, name, code, category_id, description, base_price, discount, start_date, end_date, image) 
                VALUES (:stall_id, :name, :code, :category_id, :description, :base_price, :discount, :start_date, :end_date, :image)";
        
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':stall_id', $stall_id);
        $stmt->bindValue(':name', $productName);
        $stmt->bindValue(':code', $productCode);
        $stmt->bindValue(':category_id', $category);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':base_price', $basePrice);
        $stmt->bindValue(':discount', $discount);
        $stmt->bindValue(':start_date', $startDate);
        $stmt->bindValue(':end_date', $endDate);
        $stmt->bindValue(':image', $imagePath);
        
        if ($stmt->execute()) {
            return $db->lastInsertId();  // Get last inserted ID using the same connection
        }
        
        return false;
    }
    
    
    function addVariations($productId, $variationName) {
        $db = $this->db->connect();  
        $sql = "INSERT INTO product_variations (product_id, name) VALUES (:product_id, :name)";
        
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':product_id', $productId);
        $stmt->bindValue(':name', $variationName);
        
        if ($stmt->execute()) {
            return $db->lastInsertId();
        }
        
        return false;
    }

    function addVariationOptions($variationId, $optionName, $addPrice, $subtractPrice, $variationImagePath) {
        $db = $this->db->connect();  
        $sql = "INSERT INTO variation_options (variation_id, name, add_price, subtract_price, image) 
                VALUES (:variation_id, :name, :add_price, :subtract_price, :image)";
    
        $stmt = $db->prepare($sql);
    
        $stmt->bindValue(':variation_id', $variationId);
        $stmt->bindValue(':name', $optionName);
        $stmt->bindValue(':add_price', $addPrice);
        $stmt->bindValue(':subtract_price', $subtractPrice);
        $stmt->bindValue(':image', $variationImagePath);
    
        if ($stmt->execute()) {
            return $db->lastInsertId();
        }
        
        return false;
    }

    function addStock($productId, $variationOptionId, $quantity = 0) {
        $db = $this->db->connect();  
        $sql = "INSERT INTO stocks (product_id, variation_option_id, quantity) 
                VALUES (:product_id, :variation_option_id, :quantity)";
    
        $stmt = $db->prepare($sql);
    
        $stmt->bindValue(':product_id', $productId);
        $stmt->bindValue(':variation_option_id', $variationOptionId);
        $stmt->bindValue(':quantity', $quantity);
    
        return $stmt->execute(); // Returns true if successful, false otherwise
    }

    public function addCategory($stall_id, $name) {
        $sql = "INSERT INTO categories (stall_id, name) VALUES (:stall_id, :name)";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':stall_id', $stall_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        return $stmt->execute();
    }
    
    public function getCategories($stall_id) {
        $sql = "SELECT * FROM categories WHERE stall_id = :stall_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':stall_id', $stall_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function getCategory($category_id) {
        $sql = "SELECT * FROM categories WHERE id = :id;";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':id' => $category_id));
        $result = $query->fetch();
    
        if ($result === false) {
            return false;
        }
    
        return $result;
    }

    function isProductCodeExists($code) {
        $sql = "SELECT COUNT(*) FROM products WHERE code = :code;";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':code' => $code));
    
        return $query->fetchColumn() > 0;
    }




    public function getStallLikes($stall_id){
        $sql = "SELECT COUNT(*) AS count FROM stall_likes WHERE stall_id = :stall_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([':stall_id' => $stall_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['count'] : 0;
   }

   public function isStallLiked($stall_id, $user_id){
        $sql = "SELECT COUNT(*) AS count FROM stall_likes WHERE stall_id = :stall_id AND user_id = :user_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([':stall_id' => $stall_id, ':user_id' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['count'] > 0;
   }

    public function toggleStallLike($user_id, $stall_id){
        if ($this->isStallLiked($stall_id, $user_id)) {
            $sql = "DELETE FROM stall_likes WHERE stall_id = :stall_id AND user_id = :user_id";
            $stmt = $this->db->connect()->prepare($sql);
            $stmt->execute([':stall_id' => $stall_id, ':user_id' => $user_id]);
            $liked = false;
        } else {
            $sql = "INSERT INTO stall_likes (user_id, stall_id) VALUES (:user_id, :stall_id)";
            $stmt = $this->db->connect()->prepare($sql);
            $stmt->execute([':user_id' => $user_id, ':stall_id' => $stall_id]);
            $liked = true;
        }
        $likeCount = $this->getStallLikes($stall_id);
        return ['liked' => $liked, 'likeCount' => $likeCount];
    }

    public function getPopularProducts($stall_id) {
        $db = $this->db->connect();
        $avgSql = "
            SELECT AVG(order_count) AS avg_orders FROM (
                SELECT COUNT(oi.id) AS order_count
                FROM order_items oi
                WHERE oi.product_id IN (SELECT id FROM products WHERE stall_id = :stall_id)
                GROUP BY oi.product_id
            ) AS sub
        ";
        $avgStmt = $db->prepare($avgSql);
        $avgStmt->execute([':stall_id' => $stall_id]);
        $avgResult = $avgStmt->fetch(PDO::FETCH_ASSOC);
        $avgOrders = $avgResult ? $avgResult['avg_orders'] : 0;
    
        $sql = "
            SELECT p.*, 
                   c.name AS category_name, 
                   COUNT(oi.id) AS order_count,
                   (SELECT COALESCE(SUM(s.quantity), 0) FROM stocks s WHERE s.product_id = p.id) AS stock
            FROM products p
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN order_items oi ON p.id = oi.product_id
            WHERE p.stall_id = :stall_id
            GROUP BY p.id
            HAVING order_count > :avg_orders
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':stall_id' => $stall_id,
            ':avg_orders' => $avgOrders
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    

    public function getPromoProducts($stall_id) {
        $sql = "
            SELECT p.*, 
                   c.name AS category_name,
                   (SELECT COALESCE(SUM(s.quantity), 0) FROM stocks s WHERE s.product_id = p.id) AS stock
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.stall_id = :stall_id AND p.discount > 0
            GROUP BY p.id
        ";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([':stall_id' => $stall_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getNewProducts($stall_id) {
        $sql = "
            SELECT p.*, 
                   c.name AS category_name,
                   (SELECT COALESCE(SUM(s.quantity), 0) FROM stocks s WHERE s.product_id = p.id) AS stock
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.stall_id = :stall_id 
              AND p.created_at >= (NOW() - INTERVAL 30 DAY)
            GROUP BY p.id
        ";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([':stall_id' => $stall_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchProducts($stall_id, $searchTerm) {
        $sql = "
            SELECT p.*,
                   (SELECT COALESCE(SUM(s.quantity), 0) FROM stocks s WHERE s.product_id = p.id) AS stock
            FROM products p 
            WHERE p.stall_id = :stall_id AND p.name LIKE :search
        ";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([':stall_id' => $stall_id, ':search' => "%$searchTerm%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    


    /*function getProducts($stallId) {
        $sql = "SELECT * FROM products WHERE stall_id = :stall_id;";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':stall_id' => $stallId));
        $result = $query->fetchAll();

        if (empty($result)) {
            return [];
        }
    
        foreach ($result as $key => $product) {
            $category = $this->getCategory($product['category_id']);
            if ($category) {
                $result[$key]['category'] = $category['name'];
            }
        }
    
        return $result;
    }

    function getProductById($productId) {
        $sql = "SELECT * FROM products WHERE id = :id;";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':id' => $productId));
        $result = $query->fetch();
    
        if ($result === false) {
            return false;
        }
    
        $category = $this->getCategory($result['category_id']);
        if ($category) {
            $result['category'] = $category['name'];
        }
    
        return $result;
    }

    function getProduct($productId) {
        $sql = "SELECT p.*, c.name AS category_name, 
                    COALESCE(SUM(s.quantity), 0) AS stock 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                LEFT JOIN stocks s ON p.id = s.product_id 
                WHERE p.id = :product_id 
                GROUP BY p.id;";

        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':product_id' => $productId));
        $result = $query->fetch(); 

        return $result ?: null;
    }*/

}