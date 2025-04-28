<?php
require_once 'db.php';

class Park {

    protected $db;

    function __construct(){
        $this->db = new Database();
    }
 
    public function getParks() {
        $currentDate = date('Y-m-d');
    
        $sql = "
            SELECT business.*, 
                   GROUP_CONCAT(DISTINCT CONCAT(operating_hours.days, '<br>', operating_hours.open_time, ' - ', operating_hours.close_time) SEPARATOR '; ') AS operating_hours
            FROM business
            JOIN operating_hours ON operating_hours.business_id = business.id
            LEFT JOIN deactivation ON deactivation.user_id = business.user_id
            WHERE (deactivation.user_id IS NULL OR deactivation.deactivated_until < :currentDate)
            GROUP BY business.id
        ";
    
        $query = $this->db->connect()->prepare($sql);
        $query->execute([':currentDate' => $currentDate]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPark($park_id) {
        $sql = "
            SELECT 
                business.*, 
                GROUP_CONCAT(
                    DISTINCT CONCAT(operating_hours.days, '<br>', operating_hours.open_time, ' - ', operating_hours.close_time)
                    SEPARATOR '; '
                ) AS operating_hours
            FROM business
            LEFT JOIN operating_hours ON operating_hours.business_id = business.id
            WHERE business.id = ?
            GROUP BY business.id
        ";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$park_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }    

    public function getPopularStalls($parkId) {
        $db = $this->db->connect();
    
        $avgSql = "
          SELECT AVG(pop_score) AS avg_score
          FROM (
            SELECT 
              os.stall_id,
              os.order_count,
              COALESCE(AVG(r.rating_value),0)    AS avg_rating,
              os.order_count * COALESCE(AVG(r.rating_value),0) AS pop_score
            FROM (
              SELECT stall_id, COUNT(*) AS order_count
              FROM order_stalls
              GROUP BY stall_id
            ) os
            LEFT JOIN order_stalls ost ON os.stall_id = ost.stall_id
            LEFT JOIN ratings r        ON ost.id = r.order_stall_id
            WHERE os.stall_id IN (SELECT id FROM stalls WHERE park_id = :park_id)
            GROUP BY os.stall_id
          ) sub;
        ";
        $avgQ = $db->prepare($avgSql);
        $avgQ->execute([':park_id' => $parkId]);
        $avgScore = (float) $avgQ->fetchColumn() ?: 0;
    
        $sql = "
          SELECT 
            s.*,
            GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS stall_categories,
            os.order_count,
            COALESCE(AVG(r.rating_value),0) AS avg_rating,
            os.order_count * COALESCE(AVG(r.rating_value),0) AS popularity_score
          FROM stalls s
          LEFT JOIN stall_categories sc ON s.id = sc.stall_id
          LEFT JOIN stored_categories c ON sc.category_id = c.id
    
          JOIN (
            SELECT stall_id, COUNT(*) AS order_count
            FROM order_stalls
            GROUP BY stall_id
          ) os ON s.id = os.stall_id
    
          LEFT JOIN order_stalls ost ON s.id = ost.stall_id
          LEFT JOIN ratings r        ON ost.id = r.order_stall_id
    
          WHERE s.park_id = :park_id
          GROUP BY s.id
          HAVING popularity_score > :avg_score
          ORDER BY popularity_score DESC
        ";
        $q = $db->prepare($sql);
        $q->execute([
          ':park_id'   => $parkId,
          ':avg_score' => $avgScore
        ]);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPromoStalls($parkId) {
        $sql = "
            SELECT s.*,
                   GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS stall_categories
            FROM stalls s
            LEFT JOIN stall_categories sc ON s.id = sc.stall_id
            LEFT JOIN stored_categories c ON sc.category_id = c.id
            JOIN products p ON s.id = p.stall_id
            WHERE s.park_id = :park_id
              AND p.discount > 0
            GROUP BY s.id
        ";
        $q = $this->db->connect()->prepare($sql);
        $q->execute([':park_id' => $parkId]);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNewProductStalls($parkId) {
        $sql = "
            SELECT s.*,
                   GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS stall_categories
            FROM stalls s
            LEFT JOIN stall_categories sc ON s.id = sc.stall_id
            LEFT JOIN stored_categories c ON sc.category_id = c.id
            JOIN products p ON s.id = p.stall_id
            WHERE s.park_id = :park_id
              AND p.created_at >= (NOW() - INTERVAL 30 DAY)
            GROUP BY s.id
        ";
        $q = $this->db->connect()->prepare($sql);
        $q->execute([':park_id' => $parkId]);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStalls($parkId) {
        $sql = "
            SELECT
                stalls.*,
                CONCAT(users.first_name, ' ', users.last_name) AS owner_name,
                users.email,
                users.profile_img,
                GROUP_CONCAT(DISTINCT CONCAT(stall_operating_hours.days, '<br>',
                                           stall_operating_hours.open_time, ' - ',
                                           stall_operating_hours.close_time)
                             SEPARATOR '; ') AS stall_operating_hours,
                GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS stall_categories,
                GROUP_CONCAT(DISTINCT stall_payment_methods.method SEPARATOR ', ') AS stall_payment_methods
            FROM stalls
            JOIN users ON stalls.user_id = users.id
            LEFT JOIN stall_operating_hours ON stalls.id = stall_operating_hours.stall_id
            LEFT JOIN stall_categories sc ON stalls.id = sc.stall_id
            LEFT JOIN stored_categories c ON sc.category_id = c.id
            LEFT JOIN stall_payment_methods ON stalls.id = stall_payment_methods.stall_id
            WHERE stalls.park_id = :park_id
            GROUP BY stalls.id
        ";
        $q = $this->db->connect()->prepare($sql);
        $q->execute([':park_id' => $parkId]);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    function isOwner($user_id, $park_id) {
        $query = "SELECT COUNT(*) FROM business WHERE user_id = ? AND id = ?";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([$user_id, $park_id]);
        return $stmt->fetchColumn() > 0;
    }

    function isStallOwner($user_id, $park_id) {
        $query = "SELECT COUNT(*) FROM stalls WHERE user_id = ? AND park_id = ?";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([$user_id, $park_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function getUserStall($user_id, $park_id) {
        $sql  = "SELECT * 
                 FROM stalls 
                 WHERE user_id = :user_id 
                   AND park_id = :park_id
                 LIMIT 1";
        $conn = $this->db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':park_id' => $park_id
        ]);
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
                $stmt = $conn->prepare(
                    "INSERT INTO stall_categories (stall_id, category_id) VALUES (:stall_id, :category_id)"
                );
                foreach ($categories as $categoryId) {
                    $stmt->execute([
                        ':stall_id'     => $stall_id,
                        ':category_id'  => $categoryId
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

    public function getCategories() {
        $sql = "SELECT id, name, image_url FROM stored_categories ORDER BY name";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    
    public function fetchRecord($recordID) {
        $sql = "
            SELECT
                stalls.*,
                /* add this line to pull back the raw IDs */
                GROUP_CONCAT(DISTINCT sc.category_id SEPARATOR ',') AS category_ids,
                /* keep the human‑readable names */
                GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS categories,
                GROUP_CONCAT(DISTINCT stall_payment_methods.method SEPARATOR ', ') AS payment_methods,
                GROUP_CONCAT(DISTINCT CONCAT(
                    stall_operating_hours.days, ' ',
                    stall_operating_hours.open_time, ' - ',
                    stall_operating_hours.close_time
                ) SEPARATOR '; ') AS operating_hours
            FROM stalls
            LEFT JOIN stall_categories       sc  ON stalls.id = sc.stall_id
            LEFT JOIN stored_categories      c   ON sc.category_id = c.id
            LEFT JOIN stall_payment_methods      ON stalls.id = stall_payment_methods.stall_id
            LEFT JOIN stall_operating_hours      ON stalls.id = stall_operating_hours.stall_id
            WHERE stalls.id = :recordID
            GROUP BY stalls.id
        ";
        $q = $this->db->connect()->prepare($sql);
        $q->bindParam(':recordID', $recordID);
        return $q->execute() ? $q->fetch(PDO::FETCH_ASSOC) : [];
    }    

    public function editStall(
        $stall_id,
        $businessname,
        $description,
        $businessemail,
        $businessphonenumber,
        $website,
        $stalllogo,
        $operatingHours,
        array $categoryIds,
        $payment_methods
    ) {
        $conn = $this->db->connect();

        $sql = "UPDATE stalls
                SET name = :businessname,
                    description = :description,
                    email = :businessemail,
                    phone = :businessphonenumber,
                    website = :website,
                    logo = :stalllogo
                WHERE id = :stall_id";
        $u = $conn->prepare($sql);
        if (!$u->execute([
            ':stall_id'           => $stall_id,
            ':businessname'       => $businessname,
            ':description'        => $description,
            ':businessemail'      => $businessemail,
            ':businessphonenumber'=> $businessphonenumber,
            ':website'            => $website,
            ':stalllogo'          => $stalllogo
        ])) {
            return false;
        }

        $conn->prepare("DELETE FROM stall_categories WHERE stall_id = :stall_id")
             ->execute([':stall_id' => $stall_id]);
        if (!empty($categoryIds)) {
            $ins = $conn->prepare(
                "INSERT INTO stall_categories (stall_id, category_id)
                 VALUES (:stall_id, :category_id)"
            );
            foreach ($categoryIds as $catId) {
                $ins->execute([
                    ':stall_id'    => $stall_id,
                    ':category_id' => $catId
                ]);
            }
        }

        $conn->prepare("DELETE FROM stall_payment_methods WHERE stall_id = :stall_id")
             ->execute([':stall_id'=>$stall_id]);
        if (!empty($payment_methods)) {
            $stmt = $conn->prepare("
                INSERT INTO stall_payment_methods (stall_id, method)
                VALUES (:stall_id, :method)
            ");
            foreach ($payment_methods as $method) {
                $stmt->execute([
                    ':stall_id'=> $stall_id,
                    ':method'  => $method
                ]);
            }
        }

        $conn->prepare("DELETE FROM stall_operating_hours WHERE stall_id = :stall_id")
             ->execute([':stall_id'=>$stall_id]);
        if (!empty($operatingHours)) {
            $ohStmt = $conn->prepare("
                INSERT INTO stall_operating_hours (stall_id, days, open_time, close_time)
                VALUES (:stall_id, :days, :open_time, :close_time)
            ");
            foreach ($operatingHours as $sched) {
                $days = implode(', ',$sched['days']);
                $ohStmt->execute([
                    ':stall_id'  => $stall_id,
                    ':days'      => $days,
                    ':open_time' => $sched['openTime'],
                    ':close_time'=> $sched['closeTime']
                ]);
            }
        }

        return true;
    }

    public function fetchBusinessOperatingHours($businessId) {
        $sql = "SELECT * FROM operating_hours WHERE business_id = :business_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindParam(':business_id', $businessId);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return [];
    }

    public function updateBusinessOperatingHours($businessId, $operatingHours) {
        $conn = $this->db->connect();
        $conn->prepare("DELETE FROM operating_hours WHERE business_id = :business_id")
            ->execute([':business_id' => $businessId]);
            
        if (!empty($operatingHours)) {
            $stmt = $conn->prepare("INSERT INTO operating_hours (business_id, days, open_time, close_time) VALUES (:business_id, :days, :open_time, :close_time)");
            foreach ($operatingHours as $schedule) {
                $days = implode(', ', $schedule['days']);
                $openTime = $schedule['openTime'];
                $closeTime = $schedule['closeTime'];
                $stmt->execute([
                    ':business_id' => $businessId,
                    ':days' => $days,
                    ':open_time' => $openTime,
                    ':close_time' => $closeTime
                ]);
            }
        }
        return true;
    }

    public function updateBusinessLogo($parkId, $businessLogo) {
        $conn = $this->db->connect();
        $sql = "UPDATE business SET business_logo = :business_logo WHERE id = :park_id";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([
            ':business_logo' => $businessLogo,
            ':park_id' => $parkId
        ]);
        return $result;
    }
    
    function searchParks($query) {
        $stmt = $this->db->connect()->prepare("SELECT id, business_name, business_logo, street_building_house, barangay FROM business WHERE business_name LIKE ? AND business_status = 'Approved'");
        $stmt->execute(["%$query%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getStall($stallId) {
        $sql = "
            SELECT
                stalls.*,
                GROUP_CONCAT(DISTINCT CONCAT(stall_operating_hours.days, '<br>',
                                           stall_operating_hours.open_time, ' - ',
                                           stall_operating_hours.close_time)
                             SEPARATOR '; ') AS stall_operating_hours,
                GROUP_CONCAT(DISTINCT c.name ORDER BY c.name SEPARATOR ', ') AS stall_categories,
                GROUP_CONCAT(DISTINCT stall_payment_methods.method ORDER BY stall_payment_methods.method SEPARATOR ', ') AS stall_payment_methods
            FROM stalls
            LEFT JOIN stall_operating_hours ON stalls.id = stall_operating_hours.stall_id
            LEFT JOIN stall_categories sc ON stalls.id = sc.stall_id
            LEFT JOIN stored_categories c ON sc.category_id = c.id
            LEFT JOIN stall_payment_methods ON stalls.id = stall_payment_methods.stall_id
            WHERE stalls.id = :stall_id
            GROUP BY stalls.id
        ";
        $q = $this->db->connect()->prepare($sql);
        $q->execute([':stall_id' => $stallId]);
        return $q->fetch(PDO::FETCH_ASSOC);
    }

    public function searchStalls($parkId, $searchTerm) {
        $sql = "
            SELECT
                stalls.*,
                CONCAT(users.first_name, ' ', users.last_name) AS owner_name,
                users.email,
                users.profile_img,
                GROUP_CONCAT(DISTINCT CONCAT(stall_operating_hours.days, '<br>',
                                           stall_operating_hours.open_time, ' - ',
                                           stall_operating_hours.close_time)
                             SEPARATOR '; ') AS stall_operating_hours,
                GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS stall_categories,
                GROUP_CONCAT(DISTINCT stall_payment_methods.method SEPARATOR ', ') AS stall_payment_methods
            FROM stalls
            JOIN users ON stalls.user_id = users.id
            LEFT JOIN stall_operating_hours ON stalls.id = stall_operating_hours.stall_id
            LEFT JOIN stall_categories sc ON stalls.id = sc.stall_id
            LEFT JOIN stored_categories c ON sc.category_id = c.id
            LEFT JOIN stall_payment_methods ON stalls.id = stall_payment_methods.stall_id
            WHERE stalls.park_id = :park_id
              AND stalls.name LIKE :search
            GROUP BY stalls.id
        ";
        $q = $this->db->connect()->prepare($sql);
        $q->execute([
            ':park_id' => $parkId,
            ':search'  => "%{$searchTerm}%"
        ]);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function filterStallsByCategory($parkId, $categoryId) {
        $sql = "
            SELECT
                stalls.*,
                CONCAT(users.first_name, ' ', users.last_name) AS owner_name,
                users.email,
                users.profile_img,
                GROUP_CONCAT(DISTINCT CONCAT(stall_operating_hours.days, '<br>',
                                           stall_operating_hours.open_time, ' - ',
                                           stall_operating_hours.close_time)
                             SEPARATOR '; ') AS stall_operating_hours,
                GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS stall_categories,
                GROUP_CONCAT(DISTINCT stall_payment_methods.method SEPARATOR ', ') AS stall_payment_methods
            FROM stalls
            JOIN users ON stalls.user_id = users.id
            LEFT JOIN stall_operating_hours ON stalls.id = stall_operating_hours.stall_id
            LEFT JOIN stall_categories sc ON stalls.id = sc.stall_id
            LEFT JOIN stored_categories c ON sc.category_id = c.id
            LEFT JOIN stall_payment_methods ON stalls.id = stall_payment_methods.stall_id
            WHERE stalls.park_id = :park_id
                AND sc.category_id = :category_id
            GROUP BY stalls.id
        ";
        $q = $this->db->connect()->prepare($sql);
        $q->execute([
        ':park_id'     => $parkId,
        ':category_id'=> $categoryId
        ]);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById(int $categoryId): array{
        $sql = "SELECT id, name FROM stored_categories WHERE id = :id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    
    public function deleteStall($stallId) {
        $conn = $this->db->connect();
        $conn->beginTransaction();
        
        try {
            $stmt = $conn->prepare("DELETE FROM cart WHERE product_id IN (SELECT id FROM products WHERE stall_id = :stall_id)");
            $stmt->execute([':stall_id' => $stallId]);
            
            $stmt = $conn->prepare("DELETE FROM categories WHERE stall_id = :stall_id");
            $stmt->execute([':stall_id' => $stallId]);
            
            $stmt = $conn->prepare("DELETE FROM stall_operating_hours WHERE stall_id = :stall_id");
            $stmt->execute([':stall_id' => $stallId]);
            
            $stmt = $conn->prepare("DELETE FROM stall_categories WHERE stall_id = :stall_id");
            $stmt->execute([':stall_id' => $stallId]);
            
            $stmt = $conn->prepare("DELETE FROM stall_payment_methods WHERE stall_id = :stall_id");
            $stmt->execute([':stall_id' => $stallId]);
            
            $stmt = $conn->prepare("DELETE FROM products WHERE stall_id = :stall_id");
            $stmt->execute([':stall_id' => $stallId]);
            
            $stmt = $conn->prepare("DELETE FROM order_stalls WHERE stall_id = :stall_id");
            $stmt->execute([':stall_id' => $stallId]);
            
            $stmt = $conn->prepare("DELETE FROM stalls WHERE id = :stall_id");
            $stmt->execute([':stall_id' => $stallId]);
            
            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            return false;
        }
    }
    

    public function updateStallStatus($stallId, $status){
        if (!in_array($status, ['Available', 'Unavailable'])) {
            return false;
        }
        $sql = "UPDATE stalls SET status = :status WHERE id = :id;";
        $query = $this->db->connect()->prepare($sql);
        return $query->execute([':status' => $status, ':id' => $stallId]);
    }

    public function updateParkStatus($park_id, $status){
        if (!in_array($status, ['Available', 'Unavailable'])) {
            return false;
        }
        $sql = "UPDATE business SET status = :status WHERE id = :id;";
        $query = $this->db->connect()->prepare($sql);
        return $query->execute([':status' => $status, ':id' => $park_id]);
    }

    function getStallReports($park_id) {
        $sql = "SELECT sr.id, sr.reported_by, sr.reported_stall, sr.reason, sr.status, sr.created_at,
                       u.profile_img, u.first_name, u.last_name,
                       s.name AS stall_name
                FROM stall_reports sr
                JOIN users u ON sr.reported_by = u.id
                JOIN stalls s ON sr.reported_stall = s.id
                WHERE s.park_id = :park_id
                ORDER BY sr.created_at DESC";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':park_id', $park_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    function updateStallReportStatus($report_id, $newStatus) {
        $sql = "UPDATE stall_reports SET status = :status WHERE id = :report_id";
        $stmt = $this->db->connect()->prepare($sql);
        return $stmt->execute([
            ':status' => $newStatus,
            ':report_id' => $report_id
        ]);
    }

    public function deleteBusiness($parkId) {
        $conn = $this->db->connect();
        try {
            $conn->beginTransaction();
    
            $sql1 = "DELETE FROM stalls WHERE park_id = :park_id";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->execute([':park_id' => $parkId]);
    
            $sql2 = "DELETE FROM stall_invitations WHERE park_id = :park_id";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute([':park_id' => $parkId]);
    
            $sql3 = "DELETE FROM operating_hours WHERE business_id = :park_id";
            $stmt3 = $conn->prepare($sql3);
            $stmt3->execute([':park_id' => $parkId]);
    
            $sql4 = "DELETE FROM business WHERE id = :park_id";
            $stmt4 = $conn->prepare($sql4);
            $stmt4->execute([':park_id' => $parkId]);
    
            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            return false;
        }
    }
    
    public function isParkFirstTime($park_id) {
        $sql = "SELECT COUNT(*) FROM park_first_opening WHERE park_id = :park_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([':park_id' => $park_id]);
        return $stmt->fetchColumn() == 0;
    }

    public function deleteParkFirstOpening($park_id) {
        try {
            $stmt1 = $this->db->connect()->prepare("DELETE FROM park_first_opening WHERE park_id = ?");
            $stmt1->execute([$park_id]);
            
            $stmt2 = $this->db->connect()->prepare("UPDATE business SET status = 'Available' WHERE id = ?");
            $stmt2->execute([$park_id]);

            return true;
            
        } catch (PDOException $e) {
            return false;
        }
    }

    public function isStallOwnerOfPark($userId, $parkId) {
        $stmt = $this->db->connect()->prepare("SELECT COUNT(*) FROM stalls WHERE user_id = ? AND park_id = ?");
        $stmt->execute([$userId, $parkId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getStallProducts($stallId) {
        $sql = "SELECT * FROM products WHERE stall_id = :stall_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([':stall_id' => $stallId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParkStalls($parkId) {
        $sql = "SELECT * FROM stalls WHERE park_id = :park_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([':park_id' => $parkId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isParkOwnerOfPark($userId, $parkId) {
        $stmt = $this->db->connect()->prepare("SELECT COUNT(*) FROM business WHERE user_id = ? AND id = ?");
        $stmt->execute([$userId, $parkId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getCartItemCount(int $user_id): int {
        $sql = "
          SELECT COALESCE(SUM(quantity),0)
          FROM cart
          WHERE user_id = ?
        ";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$user_id]);
        return (int) $stmt->fetchColumn();
    }

    public function getNotificationCount(int $user_id): int {
        $sql = "
          SELECT COUNT(*) 
          FROM notifications
          WHERE user_id  = ?
            AND status   = 'Unread'
        ";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$user_id]);
        return (int) $stmt->fetchColumn();
    }
    

    public function isParkEmpty(int $park_id): int {
        $sql = "SELECT COUNT(*) FROM stalls WHERE park_id = ?";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([$park_id]);
        return (int) $stmt->fetchColumn() === 0;
    }
}