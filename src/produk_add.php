<?php
session_start();
if(!isset($_SESSION['user'])){ header("Location: login.php"); exit; }
require 'koneksi.php';

if(isset($_POST['save'])){
    $sku   = $_POST['sku'];
    $name  = $_POST['name'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    mysqli_query($conn,"INSERT INTO products (sku,name,price,stock) VALUES(
        '".mysqli_real_escape_string($conn,$sku)."',
        '".mysqli_real_escape_string($conn,$name)."',
        $price,
        $stock
    )");

    header("Location: produk.php");
    exit;
}

include 'sidebar.php';
?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="style.css">

<style>
    /* Card Form Center */
    .form-container {
        width: 50%;
        margin: 30px auto;
    }

    @media (max-width: 768px) {
        .form-container {
            width: 90%;
        }
    }

    .form-container .card {
        padding: 25px;
        border-radius: 12px;
    }

    .form-container input {
        width: 100%;
        padding: 12px;
        margin-top: 8px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    .btn {
        padding: 10px 18px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 15px;
    }

    .btn:hover {
        background: #1e4ecb;
    }
</style>

</head>
<body>

<div class="header">
  <div class="title">Tambah Produk</div>
</div>

<div class="content">

  <div class="form-container">
    <div class="card">

      <form method="post">

        <label>SKU</label>
        <input type="text" name="sku" placeholder="SKU">

        <label>Nama Produk</label>
        <input type="text" name="name" placeholder="Nama Produk" required>

        <label>Harga</label>
        <input type="number" step="0.01" name="price" placeholder="Harga" required>

        <label>Stok</label>
        <input type="number" name="stock" placeholder="Stok" required>

        <button class="btn" name="save">Simpan</button>

      </form>

    </div>
  </div>

</div>

</body>
</html>
x`