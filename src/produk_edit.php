<?php
session_start(); 
if(!isset($_SESSION['user'])){ header("Location: login.php"); exit; }
require 'koneksi.php';

$id = intval($_GET['id'] ?? 0);

/* Delete */
if(isset($_GET['del'])){
    $d = intval($_GET['del']);

    // Hapus dulu item penjualan yang memakai produk ini
    mysqli_query($conn, "DELETE FROM sale_items WHERE product_id = $d");

    // Baru hapus produknya
    mysqli_query($conn, "DELETE FROM products WHERE id = $d");

    header("Location: produk.php");
    exit;
}

/* Ambil data */
if($id){
    $r = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM products WHERE id=$id"));

    /* Update */
    if(isset($_POST['save'])){
        $sku   = $_POST['sku'];
        $name  = $_POST['name'];
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);

        mysqli_query($conn,"UPDATE products SET 
            sku='".mysqli_real_escape_string($conn,$sku)."',
            name='".mysqli_real_escape_string($conn,$name)."',
            price=$price,
            stock=$stock 
        WHERE id=$id");

        header("Location: produk.php");
        exit;
    }
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
  <div class="title">Edit Produk</div>
</div>

<div class="content">

  <div class="form-container">
    <div class="card">

      <?php if($id): ?>

      <form method="post">

        <label>SKU</label>
        <input type="text" name="sku" value="<?=htmlspecialchars($r['sku'])?>" placeholder="SKU">

        <label>Nama Produk</label>
        <input type="text" name="name" value="<?=htmlspecialchars($r['name'])?>" placeholder="Nama Produk" required>

        <label>Harga</label>
        <input type="number" step="0.01" name="price" value="<?=$r['price']?>" placeholder="Harga" required>

        <label>Stok</label>
        <input type="number" name="stock" value="<?=$r['stock']?>" placeholder="Stok" required>

        <button class="btn" name="save">Update</button>

      </form>

      <?php else: ?>
        <p>Produk tidak ditemukan.</p>
      <?php endif; ?>

    </div>
  </div>

</div>

</body>
</html>
