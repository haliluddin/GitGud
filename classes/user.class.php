<?php
require_once __DIR__ . '/db.php';

class UserClass {

    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    private function generateUniqueUserSession($email) {
        do {
            $user_session = bin2hex(random_bytes(86));
    
            $stmt = $this->db->connect()->prepare("SELECT COUNT(*) FROM users WHERE user_session = ?");
            $stmt->execute([$user_session]);
            $exists = $stmt->fetchColumn() > 0;
    
        } while ($exists);
    
        $sql = "UPDATE users SET user_session = :user_session WHERE email = :email;";
        $query = $this->db->connect()->prepare($sql);

        return $query->execute(array(
            ':user_session' => $user_session,
            ':email' => $email
        ));
    }

    private function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = :id;";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':id' => $id));
        $user = $query->fetch();
        
        if (!$user) {
            return false;
        }
        
        return $user;
    }

    public function changePassword($id, $currentPassword, $newPassword, $logoutOtherDevices) {
        $user = $this->getUserById($id);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }
    
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect.'];
        } else if ($currentPassword == $newPassword) {
            return ['success' => false, 'message' => 'New password must be different from the current password.'];
        }
    
        $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $query = $this->db->connect()->prepare($sql);
        $query->execute([$hashedNewPassword, $id]);
    
        if ($query->rowCount() > 0) {
            // Only generate a new user session if the logoutOtherDevices option is ticked
            if ($logoutOtherDevices) {
                $new_session = $this->generateUniqueUserSession($user->email);
                return ['success' => true, 'user_session' => $new_session]; // Return the new session
            }
            
            // If not logging out other devices, return the current session
            return ['success' => true, 'user_session' => $user['user_session']];
        }
    
        return ['success' => false, 'message' => 'Failed to update password.'];
    }

    public function getUserStatus($user_id) {
        $sql = "SELECT deactivated_until FROM deactivation WHERE user_id = :id;";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':id' => $user_id));
        $deactivatedUser = $query->fetch();
    
        if ($deactivatedUser) {
            $currentDate = date('Y-m-d');
            if ($deactivatedUser['deactivated_until'] >= $currentDate) {
                return array(
                    'status' => 'deactivated',
                    'deactivated_until' => $deactivatedUser['deactivated_until']
                );
            }
        }
    
        $sql = "SELECT * FROM users WHERE id = :id;";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':id' => $user_id));
        $user = $query->fetch();
    
        if (!$user) {
            return false;
        }

        return array('status' => 'active');
    }    
}