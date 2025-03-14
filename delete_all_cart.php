<?php
include_once 'classes/db.class.php';
include_once 'classes/cart.class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['park_id'])) {
    $user_id = intval($_POST['user_id']);
    $park_id = intval($_POST['park_id']);
    
    $cartObj = new Cart();
    $cartObj->deleteAllItems($user_id, $park_id);
    
    echo "Cart cleared";
    exit;
}
?>
