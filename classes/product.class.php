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

    public function updateCategory($categoryId, $name) {
        $sql = "UPDATE categories SET name = :name WHERE id = :categoryId";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteProduct($productId) {
        try {
            $db = $this->db->connect();
            $db->beginTransaction();
            
            $sqlCart = "DELETE FROM cart WHERE product_id = :productId";
            $stmtCart = $db->prepare($sqlCart);
            $stmtCart->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmtCart->execute();
            
            $sqlProduct = "DELETE FROM products WHERE id = :id";
            $stmtProduct = $db->prepare($sqlProduct);
            $stmtProduct->bindParam(':id', $productId, PDO::PARAM_INT);
            $stmtProduct->execute();
            
            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Delete Product Error: " . $e->getMessage());
            return false;
        }
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

    function getProducts($stallId) {
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

    public function getProduct($productId) {
        $db = $this->db->connect();
        $sql = "SELECT p.*, COALESCE(s.quantity, 0) AS initial_stock
                FROM products p
                LEFT JOIN stocks s 
                  ON p.id = s.product_id 
                 AND s.variation_option_id IS NULL
                WHERE p.id = :product_id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }    

    public function getProductVariations($productId) {
        $db = $this->db->connect();
        
        $sql = "SELECT id as variation_id, name as title 
                FROM product_variations 
                WHERE product_id = :product_id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($variations as &$variation) {
            $sqlOptions = "SELECT vo.id as option_id, vo.name as optionName, vo.add_price as addPrice, 
                                  vo.subtract_price as subtractPrice, vo.image as imageBase64 
                           FROM variation_options vo
                           WHERE vo.variation_id = :variation_id";
            $stmtOptions = $db->prepare($sqlOptions);
            $stmtOptions->bindValue(':variation_id', $variation['variation_id'], PDO::PARAM_INT);
            $stmtOptions->execute();
            $options = $stmtOptions->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($options as &$option) {
                $sqlStock = "SELECT quantity FROM stocks 
                             WHERE product_id = :product_id AND variation_option_id = :option_id";
                $stmtStock = $db->prepare($sqlStock);
                $stmtStock->bindValue(':product_id', $productId, PDO::PARAM_INT);
                $stmtStock->bindValue(':option_id', $option['option_id'], PDO::PARAM_INT);
                $stmtStock->execute();
                $stockRow = $stmtStock->fetch(PDO::FETCH_ASSOC);
                $option['initialStock'] = $stockRow ? $stockRow['quantity'] : 0;
            }
            $variation['rows'] = $options;
        }
        
        return $variations;
    }
    public function updateProduct($productId, $stall_id, $productName, $category, $description, $basePrice, $discount, $startDate, $endDate, $imagePath) {
        $db = $this->db->connect();
        $sql = "UPDATE products 
                SET stall_id = :stall_id, 
                    name = :name, 
                    category_id = :category_id, 
                    description = :description, 
                    base_price = :base_price, 
                    discount = :discount, 
                    start_date = :start_date, 
                    end_date = :end_date, 
                    image = :image 
                WHERE id = :product_id";
        
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':stall_id', $stall_id);
        $stmt->bindValue(':name', $productName);
        $stmt->bindValue(':category_id', $category);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':base_price', $basePrice);
        $stmt->bindValue(':discount', $discount);
        $stmt->bindValue(':start_date', $startDate);
        $stmt->bindValue(':end_date', $endDate);
        $stmt->bindValue(':image', $imagePath);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    public function updateProductVariations($productId, $variationsData, $initialStock = null) {
        $db = $this->db->connect();
        try {
            $db->beginTransaction();
            
            $stmt1 = $db->prepare("DELETE FROM variation_options WHERE variation_id IN (SELECT id FROM product_variations WHERE product_id = :product_id)");
            $stmt1->execute([':product_id' => $productId]);
            
            $stmt2 = $db->prepare("DELETE FROM product_variations WHERE product_id = :product_id");
            $stmt2->execute([':product_id' => $productId]);
            
            $stmt3 = $db->prepare("DELETE FROM stocks WHERE product_id = :product_id");
            $stmt3->execute([':product_id' => $productId]);
            
            if (!empty($variationsData)) {
                foreach ($variationsData as $varData) {
                    $stmtVar = $db->prepare("INSERT INTO product_variations (product_id, name) VALUES (:product_id, :name)");
                    $stmtVar->execute([
                        ':product_id' => $productId,
                        ':name'       => $varData['title']
                    ]);
                    $variationId = $db->lastInsertId();
                    
                    if (!empty($varData['rows'])) {
                        foreach ($varData['rows'] as $option) {
                            $stmtOpt = $db->prepare("INSERT INTO variation_options (variation_id, name, add_price, subtract_price, image) 
                                                     VALUES (:variation_id, :name, :add_price, :subtract_price, :image)");
                            $stmtOpt->execute([
                                ':variation_id'  => $variationId,
                                ':name'          => $option['optionName'],
                                ':add_price'     => $option['addPrice'],
                                ':subtract_price'=> $option['subtractPrice'],
                                ':image'         => $option['imageBase64']
                            ]);
                            $variationOptionId = $db->lastInsertId();
                            
                            $stmtStock = $db->prepare("INSERT INTO stocks (product_id, variation_option_id, quantity) 
                                                       VALUES (:product_id, :variation_option_id, :quantity)");
                            $stmtStock->execute([
                                ':product_id'         => $productId,
                                ':variation_option_id'=> $variationOptionId,
                                ':quantity'           => $option['initialStock']
                            ]);
                        }
                    }
                }
            } else {
                $stmtStock = $db->prepare("INSERT INTO stocks (product_id, variation_option_id, quantity) 
                                           VALUES (:product_id, NULL, :quantity)");
                $stmtStock->execute([
                    ':product_id' => $productId,
                    ':quantity'   => $initialStock
                ]);
            }
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }
        
}