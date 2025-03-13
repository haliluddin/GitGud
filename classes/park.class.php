<?php
require_once 'db.php';

class Park {

    protected $db;

    function __construct(){
        $this->db = new Database();
    }
 
    function getParks() {
        $sql = "
            SELECT business.*, GROUP_CONCAT(DISTINCT CONCAT(operating_hours.days, '<br>', operating_hours.open_time, ' - ', operating_hours.close_time) SEPARATOR '; ') AS operating_hours
            FROM business
            JOIN operating_hours ON operating_hours.business_id = business.id
            GROUP BY business.id
        ";
        $query = $this->db->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function getPark($park_id) {
        $sql = "SELECT * FROM business WHERE id = ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$park_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function getPopularStalls($parkId) {
        $db = $this->db->connect();
        
        $avgSql = "
            SELECT AVG(order_count) AS avg_orders FROM (
                SELECT COUNT(*) AS order_count
                FROM order_stalls 
                WHERE stall_id IN (SELECT id FROM stalls WHERE park_id = :park_id)
                GROUP BY stall_id
            ) AS counts
        ";
        $avgQuery = $db->prepare($avgSql);
        $avgQuery->execute([':park_id' => $parkId]);
        $avgResult = $avgQuery->fetch(PDO::FETCH_ASSOC);
        $avgOrders = $avgResult ? $avgResult['avg_orders'] : 0;
        
        $sql = "
            SELECT s.*, 
                   GROUP_CONCAT(DISTINCT sc.name SEPARATOR ', ') AS stall_categories,
                   os.order_count
            FROM stalls s
            LEFT JOIN stall_categories sc ON s.id = sc.stall_id
            JOIN (
                SELECT stall_id, COUNT(*) AS order_count
                FROM order_stalls
                GROUP BY stall_id
            ) os ON s.id = os.stall_id
            WHERE s.park_id = :park_id AND os.order_count > :avg_orders
            GROUP BY s.id
        ";
        $query = $db->prepare($sql);
        $query->execute([
            ':park_id' => $parkId,
            ':avg_orders' => $avgOrders
        ]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPromoStalls($parkId) {
        $sql = "
            SELECT DISTINCT s.*, 
                   GROUP_CONCAT(DISTINCT sc.name SEPARATOR ', ') AS stall_categories
            FROM stalls s
            LEFT JOIN stall_categories sc ON s.id = sc.stall_id
            JOIN products p ON s.id = p.stall_id
            WHERE s.park_id = :park_id AND p.discount > 0
            GROUP BY s.id
        ";
        $query = $this->db->connect()->prepare($sql);
        $query->execute([':park_id' => $parkId]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNewProductStalls($parkId) {
        $sql = "
            SELECT DISTINCT s.*, 
                   GROUP_CONCAT(DISTINCT sc.name SEPARATOR ', ') AS stall_categories
            FROM stalls s
            LEFT JOIN stall_categories sc ON s.id = sc.stall_id
            JOIN products p ON s.id = p.stall_id
            WHERE s.park_id = :park_id AND p.created_at >= (NOW() - INTERVAL 30 DAY)
            GROUP BY s.id
        ";
        $query = $this->db->connect()->prepare($sql);
        $query->execute([':park_id' => $parkId]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function getStalls($parkId) {
        $sql = "
            SELECT 
                stalls.*, 
                CONCAT(users.first_name, ' ', users.last_name) AS owner_name,
                users.email,
                users.profile_img,
                GROUP_CONCAT(DISTINCT CONCAT(stall_operating_hours.days, '<br>', stall_operating_hours.open_time, ' - ', stall_operating_hours.close_time) SEPARATOR '; ') AS stall_operating_hours,
                GROUP_CONCAT(DISTINCT stall_categories.name SEPARATOR ', ') AS stall_categories,
                GROUP_CONCAT(DISTINCT stall_payment_methods.method SEPARATOR ', ') AS stall_payment_methods
            FROM stalls
            JOIN users ON stalls.user_id = users.id
            LEFT JOIN stall_operating_hours ON stalls.id = stall_operating_hours.stall_id
            LEFT JOIN stall_categories ON stalls.id = stall_categories.stall_id
            LEFT JOIN stall_payment_methods ON stalls.id = stall_payment_methods.stall_id
            WHERE stalls.park_id = :park_id
            GROUP BY stalls.id
        ";
        
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':park_id' => $parkId));
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if the user is the owner of the food park
    function isOwner($user_id, $park_id) {
        $query = "SELECT COUNT(*) FROM business WHERE user_id = ? AND id = ?";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([$user_id, $park_id]);
        return $stmt->fetchColumn() > 0;
    }

    // Check if the user is a stall owner inside the current park
    function isStallOwner($user_id, $park_id) {
        $query = "SELECT COUNT(*) FROM stalls WHERE user_id = ? AND park_id = ?";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([$user_id, $park_id]);
        return $stmt->fetchColumn() > 0;
    }

    function addStall($user_id, $park_id, $businessname, $description, $businessemail, $businessphonenumber, $website, $stalllogo, $operatingHours, $categories, $payment_methods) { 
        $conn = $this->db->connect(); 
    
        $sql = "INSERT INTO stalls (user_id, park_id, name, description, email, phone, website, logo) 
                VALUES (:user_id, :park_id, :businessname, :description, :businessemail, :businessphonenumber, :website, :stalllogo)";
        
        $query = $conn->prepare($sql);
    
        if ($query->execute([
            ':user_id' => $user_id,
            ':park_id' => $park_id,
            ':businessname' => $businessname,
            ':description' => $description,
            ':businessemail' => $businessemail,
            ':businessphonenumber' => $businessphonenumber,
            ':website' => $website,
            ':stalllogo' => $stalllogo
        ])) {
            $stall_id = $conn->lastInsertId();
    
            // Insert operating hours
            $stmt = $conn->prepare("INSERT INTO stall_operating_hours (stall_id, days, open_time, close_time) VALUES (:stall_id, :days, :open_time, :close_time)");
    
            foreach ($operatingHours as $schedule) {
                $days = implode(', ', $schedule['days']);
                $openTime = $schedule['openTime'];
                $closeTime = $schedule['closeTime'];
        
                $stmt->execute([
                    ':stall_id' => $stall_id,
                    ':days' => $days,
                    ':open_time' => $openTime,
                    ':close_time' => $closeTime
                ]);
            }
    
            // Insert stall categories
            if (!empty($categories)) {
                $stmt = $conn->prepare("INSERT INTO stall_categories (stall_id, name) VALUES (:stall_id, :name)");
                foreach ($categories as $category) {
                    $stmt->execute([
                        ':stall_id' => $stall_id,
                        ':name' => $category
                    ]);
                }
            }
    
            // Insert payment methods
            if (!empty($payment_methods)) {
                $stmt = $conn->prepare("INSERT INTO stall_payment_methods (stall_id, method) VALUES (:stall_id, :method)");
                foreach ($payment_methods as $method) {
                    $stmt->execute([
                        ':stall_id' => $stall_id,
                        ':method' => $method
                    ]);
                }
            }

            $sql = "UPDATE users SET role = 'Stall Owner' WHERE id = :user_id;";
            $query = $conn->prepare($sql);
            return $query->execute(array(':user_id' => $user_id));
        }
    }

    function getParkOwner($park_id) {
        $sql = "SELECT CONCAT(users.first_name, ' ', users.last_name) AS owner_name, users.email, users.profile_img 
                FROM users 
                JOIN business ON users.id = business.user_id 
                WHERE business.id = :business_id";
    
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute(['business_id' => $park_id]); 
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }
    

    function getStallOwners($park_id) {
        $sql = "SELECT CONCAT(users.first_name, ' ', users.last_name) AS owner_name, users.email, users.profile_img
                FROM users 
                JOIN stalls ON users.id = stalls.user_id 
                WHERE stalls.park_id = :park_id";
    
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute(['park_id' => $park_id]); // Corrected parameter binding
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function fetchRecord($recordID) {
        $sql = "
            SELECT 
                stalls.*, 
                GROUP_CONCAT(DISTINCT stall_categories.name SEPARATOR ', ') AS categories,
                GROUP_CONCAT(DISTINCT stall_payment_methods.method SEPARATOR ', ') AS payment_methods,
                GROUP_CONCAT(DISTINCT CONCAT(stall_operating_hours.days, ' ', stall_operating_hours.open_time, ' - ', stall_operating_hours.close_time) SEPARATOR '; ') AS operating_hours
            FROM stalls
            LEFT JOIN stall_categories ON stalls.id = stall_categories.stall_id
            LEFT JOIN stall_payment_methods ON stalls.id = stall_payment_methods.stall_id
            LEFT JOIN stall_operating_hours ON stalls.id = stall_operating_hours.stall_id
            WHERE stalls.id = :recordID
            GROUP BY stalls.id;
        ";
    
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':recordID', $recordID);
    
        if ($query->execute()) {
            return $query->fetch(PDO::FETCH_ASSOC);
        }
        return [];
    }
    
    function editStall($stall_id, $businessname, $description, $businessemail, $businessphonenumber, $website, $stalllogo, $operatingHours, $categories, $payment_methods) { 
        $conn = $this->db->connect();
    
        // Update stall details
        $sql = "UPDATE stalls 
                SET
                    name = :businessname, 
                    description = :description, 
                    email = :businessemail, 
                    phone = :businessphonenumber, 
                    website = :website, 
                    logo = :stalllogo
                WHERE id = :stall_id";
        
        $query = $conn->prepare($sql);
        
        if ($query->execute([
            ':stall_id' => $stall_id,
            ':businessname' => $businessname,
            ':description' => $description,
            ':businessemail' => $businessemail,
            ':businessphonenumber' => $businessphonenumber,
            ':website' => $website,
            ':stalllogo' => $stalllogo
        ])) {
            
    
            // Remove old categories
            $conn->prepare("DELETE FROM stall_categories WHERE stall_id = :stall_id")
                ->execute([':stall_id' => $stall_id]);
    
            // Insert updated categories
            if (!empty($categories)) {
                $stmt = $conn->prepare("INSERT INTO stall_categories (stall_id, name) VALUES (:stall_id, :name)");
                foreach ($categories as $category) {
                    $stmt->execute([
                        ':stall_id' => $stall_id,
                        ':name' => $category
                    ]);
                }
            }
    
            // Remove old payment methods
            $conn->prepare("DELETE FROM stall_payment_methods WHERE stall_id = :stall_id")
                ->execute([':stall_id' => $stall_id]);
    
            // Insert updated payment methods
            if (!empty($payment_methods)) {
                $stmt = $conn->prepare("INSERT INTO stall_payment_methods (stall_id, method) VALUES (:stall_id, :method)");
                foreach ($payment_methods as $method) {
                    $stmt->execute([
                        ':stall_id' => $stall_id,
                        ':method' => $method
                    ]);
                }
            }
    
            return true;
        }
    
        return false;
    }
    
    function searchParks($query) {
        $stmt = $this->db->connect()->prepare("SELECT id, business_name, business_logo, street_building_house, barangay FROM business WHERE business_name LIKE ? AND business_status = 'Approved'");
        $stmt->execute(["%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function getStall($stallId) {
        $sql = "
            SELECT 
                stalls.*, 
                GROUP_CONCAT(DISTINCT CONCAT(stall_operating_hours.days, '<br>', stall_operating_hours.open_time, ' - ', stall_operating_hours.close_time) SEPARATOR '; ') AS stall_operating_hours,
                GROUP_CONCAT(DISTINCT stall_categories.name ORDER BY stall_categories.name SEPARATOR ', ') AS stall_categories,
                GROUP_CONCAT(DISTINCT stall_payment_methods.method ORDER BY stall_payment_methods.method SEPARATOR ', ') AS stall_payment_methods
            FROM stalls
            LEFT JOIN stall_operating_hours ON stalls.id = stall_operating_hours.stall_id
            LEFT JOIN stall_categories ON stalls.id = stall_categories.stall_id
            LEFT JOIN stall_payment_methods ON stalls.id = stall_payment_methods.stall_id
            WHERE stalls.id = :stall_id
            GROUP BY stalls.id
        ";
    
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':stall_id' => $stallId));
        
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    
    
    
    
/*
    function addPark($name, $description, $location, $image, $ownerName, $contactNumber, $email, $openingTime, $closingTime, $priceRange, $status) {
        $uniqueUrl = uniqid();

        $sql = "INSERT INTO parks (name, description, location, image, owner_name, contact_number, email, opening_time, closing_time, price_range, status, url)
                VALUES (:name, :description, :location, :image, :owner_name, :contact_number, :email, :opening_time, :closing_time, :price_range, :status, :url)";

        $stmt = $this->db->connect()->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':owner_name', $ownerName);
        $stmt->bindParam(':contact_number', $contactNumber);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':opening_time', $openingTime);
        $stmt->bindParam(':closing_time', $closingTime);
        $stmt->bindParam(':price_range', $priceRange);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':url', $uniqueUrl);

        $stmt->execute();

        // echo "Park inserted successfully with unique URL: " . $uniqueUrl;
    }

    function addStall($parkId, $name, $description, $image, $ownerName, $contactNumber, $email, $openingTime, $closingTime, $priceRange, $status) {

        $sql = "INSERT INTO stalls (park_id, name, description, img, owner_name, contact_number, email, opening_time, closing_time, price_range, status)
                VALUES (:park_id, :name, :description, :img, :owner_name, :contact_number, :email, :opening_time, :closing_time, :price_range, :status)";

        $stmt = $this->db->connect()->prepare($sql);

        $stmt->bindParam(':park_id', $parkId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':img', $image);
        $stmt->bindParam(':owner_name', $ownerName);
        $stmt->bindParam(':contact_number', $contactNumber);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':opening_time', $openingTime);
        $stmt->bindParam(':closing_time', $closingTime);
        $stmt->bindParam(':price_range', $priceRange);
        $stmt->bindParam(':status', $status);

        $stmt->execute();

        // echo "Stall inserted successfully";
    }*/
}

// $parkObj = new Park();
// $parkObj->addPark();
// $parkObj->addStall();