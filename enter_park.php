<?php
session_start();
require_once 'classes/db.class.php';
require_once 'classes/park.class.php';
require_once __DIR__ . '/classes/encdec.class.php';

$parkObj = new Park();

if (isset($_GET['id'])) {
    $park_id = decrypt($_GET['id']);
    $park = $parkObj->getPark($park_id);

    if ($park) {
        $_SESSION['current_park_id'] = $park_id;
        $_SESSION['current_park_name'] = $park['business_name'];
        header("Location: park.php");
        exit();
    } else {
        echo "Invalid park.";
    }
} else {
    header("Location: index.php");
    exit();
}
?>
