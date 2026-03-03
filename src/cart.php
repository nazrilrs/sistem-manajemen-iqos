<?php
session_start();
if(!isset($_SESSION['user'])){ 
    header("Location: login.php"); 
    exit; 
}

require 'koneksi.php';
include 'sidebar.php';

$cart = $_SESSION['cart'] ?? [];

// Hapus item jika ada parameter remove
if(isset($_GET['remove'])){
    $remove_id = intval($_GET['remove']);
    if(isset($cart[$remove_id])){
        unset($cart[$remove_id]);
        $_SESSION['cart'] = $cart; // update session
    }
    header("Location: cart.php");
    exit;
}

// Jika cart kosong
if(empty($cart)){
    echo "<div class='content'><div class='card'><p>Keranjang kosong.</p><a class='btn' href='sales.php'>Kembali ke Penjualan</a></div></div>";
    exit;
}

// Ambil data produk yang ada di cart
$ids = implode(',', array_keys($cart));
$products_res = mysqli_query($conn, "SELECT id,name,price,stock FROM products WHERE id IN ($ids)");
$products = [];
while($p = mysqli_fetch_assoc($products_res)){
    $products[$p['id']] = $p;
}

// Proses checkout
if(isset($_POST['checkout'])){
    $total = 0;
    foreach($cart as $pid=>$qty){
        $total += $products[$pid]['price'] * $qty;
    }
    
    $invoice_no = "INV-".date('YmdHis');
    mysqli_query($conn, "INSERT INTO sales(user_id,invoice_no,total_amount,created_at) VALUES(".$_SESSION['user']['id'].",'$invoice_no',$total,NOW())");
    $sale_id = mysqli_insert_id($conn);

    // Insert sale_items
    foreach($cart as $pid=>$qty){
        $price = $products[$pid]['price'];
        $subtotal = $price * $qty;
        mysqli_query($conn, "INSERT INTO sale_items(sale_id,product_id,qty,price,subtotal) VALUES($sale_id,$pid,$qty,$price,$subtotal)");
    }

    // **Kurangi stok & catat mutasi**
    foreach($cart as $pid=>$qty){
        // Kurangi stok
        mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id = $pid");

        // Catat mutasi keluar
        mysqli_query($conn, "
            INSERT INTO stock_mutations (product_id, type, change_qty, created_at)
            VALUES ($pid, 'out', $qty, NOW())
        ");
    }

    // Kosongkan cart
    $_SESSION['cart'] = [];
    header("Location: receipt.php?id=$sale_id");
    exit;
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Keranjang Belanja</title>
<link rel="stylesheet" href="style.css">
<style>
.content{margin-left:260px; padding:20px; margin-top:40px;}
.card{padding:25px; border-radius:12px; background:#0f111a; box-shadow:0 0 20px rgba(0,0,0,0.35);}
.table{width:100%; border-collapse:collapse;}
.table th, .table td{padding:12px; border-bottom:1px solid #292c35; color:#e4e7ec;}
.table th{background:#11131c; text-align:left;}
.btn{padding:10px 14px; background:#2563eb; color:#fff; border:none; border-radius:8px; cursor:pointer;}
.btn:hover{background:#1e4ecb;}
.total-row td{font-weight:bold; text-align:right;}
</style>
</head>
<body>

<div class="content">
<div class="card">
<h3>Keranjang Belanja</h3>

<form method="post">
<table class="table">
<thead>
<tr>
    <th>Produk</th>
    <th>Qty</th>
    <th>Harga</th>
    <th>Subtotal</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody>
<?php 
$total = 0;
foreach($cart as $pid=>$qty):
    $p = $products[$pid];
    $subtotal = $p['price'] * $qty;
    $total += $subtotal;
?>
<tr>
    <td><?= htmlspecialchars($p['name']) ?></td>
    <td><?= $qty ?></td>
    <td>Rp <?= number_format($p['price'],0,',','.') ?></td>
    <td>Rp <?= number_format($subtotal,0,',','.') ?></td>
    <td>
        <a class="btn btn-remove" href="cart.php?remove=<?= $pid ?>" onclick="return confirm('Hapus item ini dari keranjang?')">Hapus</a>
    </td>
</tr>
<?php endforeach; ?>
<tr class="total-row">
    <td colspan="3">Total</td>
    <td>Rp <?= number_format($total,0,',','.') ?></td>
    <td></td>
</tr>
</tbody>
</table>

<br>
<button class="btn" type="submit" name="checkout">Bayar & Buat Nota</button>
<br><br>
<a class="btn" href="sales.php" style="background:#059669;">Kembali</a>
</form>
</div>
</div>

</body>
</html>
