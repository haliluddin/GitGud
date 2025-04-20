<?php
$dbPath = __DIR__ . '/../classes/db.php';
if (!file_exists($dbPath)) {
    die("Error: The file $dbPath does not exist.");
}
require_once($dbPath);

require_once(__DIR__ . '/../vendor/autoload.php');
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    die('PHPMailer class is not loaded. Please check the autoloader and ensure PHPMailer is installed.');
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . '/../classes/encdec.class.php');

class StallInvitation {
    protected $db;

    function __construct() {
        $this->db = new Database();
    }

    function sendInvitationEmail($owner_email, $owner_id, $park_id, $user_id, $email, $first_name, $token) {
        // Make sure all parameters are strings before encrypting
        $token = urlencode(encrypt((string)$token));
        $owner_email = urlencode(encrypt((string)$owner_email));
        $owner_id = urlencode(encrypt((string)$owner_id));
        $park_id = urlencode(encrypt((string)$park_id));
        $user_id = urlencode(encrypt((string)$user_id));

        
        // Redirect to stallregistration.php with necessary parameters
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $invitationLink = "{$protocol}{$host}{$uri}/stallregistration.php?oe={$owner_email}&oi={$owner_id}&pi={$park_id}&token={$token}&id={$user_id}";
        
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            // IMPORTANT CREDENTIALS!!!!!!!!!!!!!!!!!!!!!!!! MUST USE .env, BUT NOT FOR NOW
            $mail->Username = 'vince280124@gmail.com';
            $mail->Password = 'frfqgqqgmkfxywtf';
            // IMPORTANT CREDENTIALS!!!!!!!!!!!!!!!!!!!!!!!!

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('pms-do-not-reply@gitgud.com', 'Stall Invitation');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Food Stall Invitation';
            $mail->Body = "
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Food Stall Invitation</title>
                    <style>
                        body {
                            font-family: 'Arial', sans-serif;
                            background-color: #f4f4f4;
                            margin: 0;
                            padding: 0;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            min-height: 100vh;
                        }
                        .container {
                            background-color: #ffffff;
                            padding: 40px;
                            border-radius: 10px;
                            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                            max-width: 500px;
                            width: 100%;
                            text-align: center;
                        }
                        h1 {
                            color: #333;
                            font-size: 24px;
                            margin-bottom: 20px;
                        }
                        p {
                            color: #555;
                            font-size: 16px;
                            line-height: 1.6;
                            margin-bottom: 20px;
                        }
                        .invitation-button {
                            display: inline-block;
                            background-color: #CD5C08;
                            color: #ffffff;
                            padding: 12px 24px;
                            border-radius: 5px;
                            text-decoration: none;
                            font-size: 16px;
                            transition: background-color 0.3s ease;
                        }
                        .invitation-button:hover {
                            background-color: #A64A06;
                        }
                        .footer {
                            margin-top: 20px;
                            color: #777;
                            font-size: 14px;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h1>Hello, {$first_name}!</h1>
                        <p>You have been invited to register a food stall in our food park management system.</p>
                        <p>Click the button below to set up your food stall and start your journey with us:</p>
                        <a href='{$invitationLink}' class='invitation-button'>Register Your Stall</a>
                        <p class='footer'>If you didn't expect this invitation, please ignore this email.</p>
                    </div>
                </body>
                </html>
                        ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function createStallInvitation($owner_id, $park_id, $user_id, $email, $first_name) {
        $sql = "SELECT * FROM stall_invitations WHERE user_id = :user_id AND park_id = :park_id";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':user_id' => $user_id, ':park_id' => $park_id));
        $token = uniqid();
        $invitation = $query->fetch();

        // Get owner email for invitation link
        $sql = "SELECT email FROM users WHERE id = :owner_id";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':owner_id' => $owner_id));
        $owner = $query->fetch();
        $owner_email = $owner['email'];

        if ($invitation) {
            $current_time = time();
            $last_sent = $invitation['last_sent'];
            $difference = $current_time - $last_sent;
            $cd = 300; // 5 minutes cooldown

            if ($difference < $cd) {
                return ['cd' => $cd - $difference, 'message' => 'cooldown'];
            } else if ($invitation['is_used'] == 0) {
                $expiration = date('Y-m-d H:i:s', strtotime('+7 days')); // Longer expiration for stall invitations

                $sql = "UPDATE stall_invitations SET 
                        invitation_token = :token, 
                        token_expiration = :token_expiration,
                        last_sent = :last_sent 
                        WHERE user_id = :user_id AND park_id = :park_id";

                $query = $this->db->connect()->prepare($sql);
                $result = $query->execute(array(
                    ':user_id' => $user_id,
                    ':token' => $token,
                    ':token_expiration' => $expiration,
                    ':last_sent' => time(),
                    ':park_id' => $park_id
                ));

                if (!$result) {
                    // Debug output
                    echo "Error updating invitation: ";
                    print_r($query->errorInfo());
                }

                return $this->sendInvitationEmail($owner_email, $owner_id, $park_id, $user_id, $email, $first_name, $token);
            } else {
                return ['message' => 'already_registered'];
            }
        } else {
            $expiration = date('Y-m-d H:i:s', strtotime('+7 days'));

            $sql = "INSERT INTO stall_invitations (user_id, park_id, invitation_token, token_expiration, last_sent) 
                    VALUES (:user_id, :park_id, :token, :token_expiration, :last_sent)";

            $query = $this->db->connect()->prepare($sql);
            $result = $query->execute(array(
                ':user_id' => $user_id,
                ':park_id' => $park_id,
                ':token' => $token,
                ':token_expiration' => $expiration,
                ':last_sent' => time()
            ));

            if (!$result) {
                // Debug output
                echo "Error creating invitation: ";
                print_r($query->errorInfo());
            }

            return $this->sendInvitationEmail($owner_email, $owner_id, $park_id, $user_id, $email, $first_name, $token);
        }
    }

    function verifyInvitation($token, $user_id, $park_id) {
        $sql = "SELECT * FROM stall_invitations WHERE user_id = :user_id AND park_id = :park_id";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':user_id' => $user_id, ':park_id' => $park_id));
        $invitation = $query->fetch();
        
        if ($invitation && $invitation['is_used'] == 0) {
            $sql = "SELECT * FROM stall_invitations WHERE invitation_token = :token";
            $query = $this->db->connect()->prepare($sql);
            $query->execute(array(':token' => $token));
            $invitation = $query->fetch();

            if ($invitation) {
                if (strtotime($invitation['token_expiration']) > time()) {
                    // Valid invitation token that hasn't expired
                    return true;
                } else {
                    // Token expired
                    return false;
                }
            } else {
                // Token not found
                return false;
            }
        } else if ($invitation && $invitation['is_used'] == 1) {
            // Invitation already used
            return ['message' => 'already_registered'];
        } else {
            // No invitation found
            return false;
        }
    }
    
    function markInvitationAsUsed($user_id, $park_id) {
        $sql = "UPDATE stall_invitations SET is_used = 1 WHERE user_id = :user_id AND park_id = :park_id";
        $query = $this->db->connect()->prepare($sql);
        $query->execute(array(':user_id' => $user_id, ':park_id' => $park_id));
        
        return $query->rowCount() > 0;
    }
}
?>