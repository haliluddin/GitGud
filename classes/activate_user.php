<?php

require_once __DIR__ . '/./admin.class.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    require_once "admin.class.php"; // Ensure class is included
    $admin = new Admin();

    if ($_POST["action"] === "activateUser" && isset($_POST["user_id"])) {
        $response = $admin->activateUser($_POST["user_id"]);
        echo json_encode($response);
        exit;
    }
}
