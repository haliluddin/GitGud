<?php

require_once(__DIR__ . '/../classes/db.php');
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../classes/encdec.class.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PasswordReset {
    protected $db;

    function __construct() {
        $this->db = new Database();
    }

    function sendResetEmail($user_id, $email, $first_name, $token) {
        $encrypted_token = urlencode(encrypt($token));
        $encrypted_user_id = urlencode(encrypt($user_id));
        
        $resetLink = "http://localhost/GitGud/changepassword.php?token={$encrypted_token}&id={$encrypted_user_id}";

        // Store the reset token in database using actual user_id
        $sql = "INSERT INTO password_resets (user_id, token) VALUES (:user_id, :token)
                ON DUPLICATE KEY UPDATE token = :token, created_at = NOW(), expires_at = DATE_ADD(NOW(), INTERVAL 24 HOUR)";
        $query = $this->db->connect()->prepare($sql);
        $query->execute([':user_id' => $user_id, ':token' => $token]);
        
        $mail = new PHPMailer(true);
        try {
            // SMTP configuration (same as verification)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'vince280124@gmail.com';
            $mail->Password = 'frfqgqqgmkfxywtf';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('pms-do-not-reply@gitgud.com', 'Password Reset');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = $this->getResetEmailTemplate($first_name, $resetLink);

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function getResetEmailTemplate($name, $link) {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Password Reset</title>
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
                .reset-button {
                    display: inline-block;
                    background-color: #CD5C08;
                    color: #ffffff;
                    padding: 12px 24px;
                    border-radius: 5px;
                    text-decoration: none;
                    font-size: 16px;
                    transition: background-color 0.3s ease;
                }
                .reset-button:hover {
                    background-color: #0056b3;
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
                <h1>Password Reset</h1>
                <p>Hi {$name},</p>
                <p>We received a request to reset your password. Click the button below to proceed:</p>
                <a href='{$link}' class='reset-button'>Reset Password</a>
                <p>If you didn't request this, you can safely ignore this email.</p>
                <p class='footer'>This link will expire in 24 hours.</p>
                <p class='footer'>If you're having trouble with the button, copy and paste this link into your browser:</p>
                <p class='footer'>{$link}</p>
            </div>
        </body>
        </html>
        ";
    }
}
