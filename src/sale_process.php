<?php
session_start(); if(!isset($_SESSION['user'])){ header("Location: login.php"); exit; }
require 'koneksi.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $pid = intval($_POST['product_id']); $qty = intval($_POST['qty']);
  // lock/check
  $row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id,price,stock FROM products WHERE id=$pid FOR UPDATE"));
  if(!$row){ $_SESSION['error']="Produk tidak ditemukan"; header("Location: sales.php"); exit; }
  if($row['stock'] < $qty){ $_SESSION['error']="Stok tidak cukup"; header("Location: sales.php"); exit; }
  $subtotal = $qty * $row['price'];
  // create sale
  mysqli_begin_transaction($conn);
  $invoice = 'INV'.date('YmdHis').rand(100,999);
  $ins = mysqli_prepare($conn,"INSERT INTO sales (invoice_no,user_id,total_amount,total_items,payment_method,payment_status) VALUES (?,?,?,?,?,?)");
  $uid = $_SESSION['user']['id']; $pm='cash'; $ps='success';
  mysqli_stmt_bind_param($ins,"siidds",$invoice,$uid,$subtotal,$qty,$pm,$ps); mysqli_stmt_execute($ins);
  $sale_id = mysqli_insert_id($conn);
  // insert detail
  $ins2 = mysqli_prepare($conn,"INSERT INTO sale_items (sale_id,product_id,qty,price,subtotal) VALUES (?,?,?,?,?)");
  mysqli_stmt_bind_param($ins2,"iiidd",$sale_id,$pid,$qty,$row['price'],$subtotal); mysqli_stmt_execute($ins2);
  // update stock & mutation
  mysqli_query($conn,"UPDATE products SET stock = stock - $qty WHERE id=$pid");
  $stmtm = mysqli_prepare($conn,"INSERT INTO stock_mutations (product_id,user_id,change_qty,type,reason) VALUES (?,?,?,?,?)");
  $chg = -$qty; $type='sale'; $reason = "Sale #$invoice";
  mysqli_stmt_bind_param($stmtm,"iiiss",$pid,$uid,$chg,$type,$reason); mysqli_stmt_execute($stmtm);
  mysqli_commit($conn);
  header("Location: receipt.php?id=".$sale_id);
  exit;
}
header("Location: sales.php");
