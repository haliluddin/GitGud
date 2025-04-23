<?php
session_start();
require_once __DIR__ . '/classes/stall.class.php';
require_once __DIR__ . '/classes/db.class.php';

$stallObj  = new Stall();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['id'] ?? null;
    $product_id = $_POST['product_id'] ?? null;
    $variation_options = $_POST['variation_options'] ?? [];
    $quantity = $_POST['quantity'] ?? 1;
    $base_price = $_POST['base_price'] ?? 0; 
    $request = $_POST['request'] ?? '';

    if (!$user_id) {
        echo "User not logged in.";
        exit;
    }

    $conn = $stallObj->getConnection();

    if (!empty($variation_options)) {
        foreach ($variation_options as $variation_option_id) {
            $stmt = $conn->prepare("SELECT add_price, subtract_price FROM variation_options WHERE id = ?");
            $stmt->execute([$variation_option_id]);
            $variation = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($variation) {
                $add_price = floatval($variation['add_price']);
                $subtract_price = floatval($variation['subtract_price']);
                $final_price = $base_price;
                if ($add_price > 0) {
                    $final_price += $add_price;
                }
                if ($subtract_price > 0) {
                    $final_price -= $subtract_price;
                }

                $stmt = $conn->prepare("INSERT INTO cart 
                    (user_id, product_id, variation_option_id, request, quantity, price) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $user_id,
                    $product_id,
                    $variation_option_id,
                    $request,
                    $quantity,
                    $final_price
                ]);
            }
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO cart 
            (user_id, product_id, variation_option_id, request, quantity, price) 
            VALUES (?, ?, NULL, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $product_id,
            $request,
            $quantity,
            $base_price
        ]);
    }

    echo "Added to cart successfully!";
}
