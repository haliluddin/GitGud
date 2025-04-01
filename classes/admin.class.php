<?php
require_once __DIR__ . '/db.php';

class Admin {

    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    function getUsers($search = null) {
        $sql = "SELECT * FROM users";
        
        if ($search) {
            $sql .= " WHERE first_name LIKE :search 
                       OR last_name LIKE :search 
                       OR email LIKE :search 
                       OR phone LIKE :search 
                       OR birth_date LIKE :search 
                       OR sex LIKE :search 
                       OR status LIKE :search 
                       OR role LIKE :search 
                       OR created_at LIKE :search";
        }
        
        $query = $this->db->connect()->prepare($sql);
        
        if ($search) {
            $query->bindValue(':search', "%" . $search . "%");
        }
        
        $query->execute();
        return $query->fetchAll();
    }
    

    function getBusinesses() {
        $sql = "
            SELECT 
                business.id, 
                business.business_name, 
                business.business_type, 
                business.region_province_city, 
                business.barangay, 
                business.street_building_house, 
                business.business_status, 
                business.business_email, 
                business.business_phone, 
                business.business_permit,
                business.business_logo, 
                business.created_at, 
                CONCAT(users.first_name, ' ', users.last_name) AS owner_name,
                GROUP_CONCAT(DISTINCT CONCAT(operating_hours.days, '<br>', operating_hours.open_time, ' - ', operating_hours.close_time) SEPARATOR '; ') AS operating_hours
            FROM business
            INNER JOIN users ON business.user_id = users.id
            LEFT JOIN operating_hours ON operating_hours.business_id = business.id
            GROUP BY business.id
        ";
        $query = $this->db->connect()->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function updateBusinessStatus($id, $status, $rejection_reason = null) {
        if ($status == 'Rejected') {
           $sql = "UPDATE business SET business_status = :status, rejection_reason = :rejection_reason WHERE id = :id";
        } else {
           $sql = "UPDATE business SET business_status = :status WHERE id = :id";
        }
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':status', $status);
        $query->bindParam(':id', $id);
        if ($status == 'Rejected') {
            $query->bindParam(':rejection_reason', $rejection_reason);
        }
        return $query->execute();
    }

    public function getUserName($user_id) {
        $sql = "SELECT first_name, last_name, profile_img FROM users WHERE id = :user_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getUserBusinessActivity($user_id) {
        $sql = "SELECT business_name AS food_park_name, created_at FROM business WHERE user_id = :user_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserCartActivity($user_id) {
        $sql = "SELECT GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names, c.created_at 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = :user_id 
                GROUP BY c.created_at";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserNotifications($user_id) {
        $sql = "SELECT message, created_at FROM notifications WHERE user_id = :user_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserOrdersActivity($user_id) {
        $sql = "SELECT GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names, o.created_at 
                FROM orders o 
                JOIN order_stalls os ON o.id = os.order_id 
                JOIN order_items oi ON os.id = oi.order_stall_id 
                JOIN products p ON oi.product_id = p.id 
                WHERE o.user_id = :user_id 
                GROUP BY o.id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserReportsActivity($user_id) {
        $sql = "SELECT r.id, CONCAT('reported ', b.business_name) AS reported_entity, r.reason, r.created_at 
                FROM reports r
                JOIN business b ON r.reported_park = b.id
                WHERE r.reported_by = :user_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserStallsActivity($user_id) {
        $sql = "SELECT name AS food_stall_name, created_at FROM stalls WHERE user_id = :user_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserStallLikesActivity($user_id) {
        $sql = "SELECT s.name AS food_stall_name, sl.created_at 
                FROM stall_likes sl 
                JOIN stalls s ON sl.stall_id = s.id 
                WHERE sl.user_id = :user_id";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getUserActivities($user_id) {
        $activities = [];
        $userData = $this->getUserName($user_id);
        $userFullName = $userData ? trim($userData['first_name'] . ' ' . $userData['last_name']) : '';

        $business = $this->getUserBusinessActivity($user_id);
        if ($business) {
            foreach ($business as $b) {
                $activities[] = [
                    'message'    => $userFullName . ' registered their food park',
                    'detail'     => '"' . $b['food_park_name'] . '"',
                    'created_at' => $b['created_at']
                ];
            }
        }

        $cart = $this->getUserCartActivity($user_id);
        if ($cart) {
            foreach ($cart as $c) {
                $activities[] = [
                    'message'    => $userFullName . ' added to cart',
                    'detail'     => '"' . $c['product_names'] . '"',
                    'created_at' => $c['created_at']
                ];
            }
        }

        $notifications = $this->getUserNotifications($user_id);
        if ($notifications) {
            foreach ($notifications as $n) {
                $activities[] = [
                    'message'    => $userFullName . ' received notification',
                    'detail'     => '"' . $n['message'] . '"',
                    'created_at' => $n['created_at']
                ];
            }
        }

        $orders = $this->getUserOrdersActivity($user_id);
        if ($orders) {
            foreach ($orders as $o) {
                $activities[] = [
                    'message'    => $userFullName . ' ordered',
                    'detail'     => '"' . $o['product_names'] . '"',
                    'created_at' => $o['created_at']
                ];
            }
        }

        $reports = $this->getUserReportsActivity($user_id);
        if ($reports) {
            foreach ($reports as $r) {
                $activities[] = [
                    'message'    => $userFullName . ' ' . $r['reported_entity'],
                    'detail'     => '"' . $r['reason'] . '"',
                    'created_at' => $r['created_at']
                ];
            }
        }

        $stalls = $this->getUserStallsActivity($user_id);
        if ($stalls) {
            foreach ($stalls as $s) {
                $activities[] = [
                    'message'    => $userFullName . ' registered their food stall',
                    'detail'     => '"' . $s['food_stall_name'] . '"',
                    'created_at' => $s['created_at']
                ];
            }
        }

        $stallLikes = $this->getUserStallLikesActivity($user_id);
        if ($stallLikes) {
            foreach ($stallLikes as $l) {
                $activities[] = [
                    'message'    => $userFullName . ' liked',
                    'detail'     => '"' . $l['food_stall_name'] . '"',
                    'created_at' => $l['created_at']
                ];
            }
        }

        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $activities;
    }

    public function updateReportStatus($report_id, $newStatus) {
        $sql = "UPDATE reports SET status = :newStatus WHERE id = :report_id";
        $stmt = $this->db->connect()->prepare($sql);
        return $stmt->execute([':newStatus' => $newStatus, ':report_id' => $report_id]);
    }
    
    public function getReports() {
        $sql = "SELECT r.id, r.reported_by, r.reported_park, r.reason, r.status, r.created_at,
                       u1.first_name as reporter_first, u1.last_name as reporter_last,
                       b.business_name as reported_park_name
                FROM reports r
                JOIN users u1 ON r.reported_by = u1.id
                JOIN business b ON r.reported_park = b.id
                ORDER BY r.created_at DESC";
        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }    

    public function searchBusinesses($search = null) {
        $sql = "
            SELECT 
                business.id, 
                business.business_name, 
                business.business_type, 
                business.region_province_city, 
                business.barangay, 
                business.street_building_house, 
                business.business_status, 
                business.business_email, 
                business.business_phone, 
                business.business_permit,
                business.business_logo, 
                business.created_at, 
                CONCAT(users.first_name, ' ', users.last_name) AS owner_name,
                GROUP_CONCAT(DISTINCT CONCAT(operating_hours.days, '<br>', operating_hours.open_time, ' - ', operating_hours.close_time) SEPARATOR '; ') AS operating_hours
            FROM business
            INNER JOIN users ON business.user_id = users.id
            LEFT JOIN operating_hours ON operating_hours.business_id = business.id
        ";
        
        if ($search) {
            $sql .= " WHERE CONCAT(users.first_name, ' ', users.last_name) LIKE :search
                      OR business.business_name LIKE :search
                      OR business.region_province_city LIKE :search
                      OR business.barangay LIKE :search
                      OR business.street_building_house LIKE :search
                      OR business.created_at LIKE :search
                      OR business.business_status LIKE :search";
        }
        
        $sql .= " GROUP BY business.id";
        
        $query = $this->db->connect()->prepare($sql);
        if ($search) {
            $query->bindValue(':search', "%" . $search . "%");
        }
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function searchReports($search = null) {
        $sql = "SELECT r.id, r.reported_by, r.reported_user, r.reason, r.status, r.created_at,
                       u1.first_name as reporter_first, u1.last_name as reporter_last,
                       u2.first_name as reported_first, u2.last_name as reported_last
                FROM reports r
                JOIN users u1 ON r.reported_by = u1.id
                JOIN users u2 ON r.reported_user = u2.id";
        
        if ($search) {
            $sql .= " WHERE u1.first_name LIKE :search
                      OR u1.last_name LIKE :search
                      OR u2.first_name LIKE :search
                      OR u2.last_name LIKE :search
                      OR r.reason LIKE :search
                      OR r.created_at LIKE :search
                      OR r.status LIKE :search";
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        
        $stmt = $this->db->connect()->prepare($sql);
        if ($search) {
            $stmt->bindValue(':search', "%" . $search . "%");
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function deactivateUser($user_id, $deactivated_until, $reason) {
        try {
            $db = $this->db->connect();
            $db->beginTransaction();
    
            // Update user status
            $updateUserQuery = "UPDATE users SET status = 'Deactivated' WHERE id = :user_id";
            $stmt = $db->prepare($updateUserQuery);
            $stmt->execute([':user_id' => $user_id]);
    
            // Insert or update deactivation record (removing the non-existent 'status' column)
            $query = "INSERT INTO deactivation (user_id, deactivated_until, deactivation_reason) 
                      VALUES (:user_id, :deactivated_until, :reason)
                      ON DUPLICATE KEY UPDATE 
                          deactivated_until = :deactivated_until,
                          deactivation_reason = :reason";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':user_id' => $user_id,
                ':deactivated_until' => $deactivated_until,
                ':reason' => $reason
            ]);
            
            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Error deactivating user: " . $e->getMessage());
            return false;
        }
    }    
    
    public function getDeactivationRecords() {
        $query = "SELECT d.*, u.first_name, u.last_name FROM deactivation d JOIN users u ON d.user_id = u.id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function activateUser($user_id) {
        try {
            $db = $this->db->connect();
            $db->beginTransaction();
    
            $deleteDeactivationQuery = "DELETE FROM deactivation WHERE user_id = :user_id";
            $stmt = $db->prepare($deleteDeactivationQuery);
            $stmt->execute([':user_id' => $user_id]);
    
            $db->commit();
            return ["success" => true];
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Error activating user: " . $e->getMessage());
            return ["success" => false, "error" => $e->getMessage()];
        }
    }
    
}