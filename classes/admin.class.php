<?php
require_once __DIR__ . '/db.php';

class Admin {

    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    function getUsers() {
        $sql = "SELECT * FROM users";
        $query = $this->db->connect()->prepare($sql);
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
    
    
    function updateBusinessStatus($id, $status) {
        $sql = "UPDATE business SET business_status = :status WHERE id = :id";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':status', $status);
        $query->bindParam(':id', $id);
        return $query->execute();
    }
}