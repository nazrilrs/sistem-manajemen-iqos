<?php
session_start();
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$product_id = intval($_POST['product_id'] ?? 0);
$qty = intval($_POST['qty'] ?? 1);

if($product_id && $qty > 0){
    if(isset($_SESSION['cart'][$product_id])){
        $_SESSION['cart'][$product_id] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }
}

header("Location: sales.php");
exit;
