<?php
session_start(); 
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

require 'koneksi.php';

$id = intval($_GET['id'] ?? 0);
if(!$id) { echo "Invalid"; exit; }

// Ambil data penjualan
$s = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT s.*, u.name as cashier 
    FROM sales s 
    LEFT JOIN users u ON s.user_id=u.id 
    WHERE s.id=$id
"));

// Ambil detail item penjualan
$items = mysqli_query($conn,"
    SELECT si.*, p.name 
    FROM sale_items si 
    JOIN products p ON si.product_id=p.id 
    WHERE si.sale_id=$id
");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Nota Penjualan</title>
<link rel="stylesheet" href="style.css">

<style>
body{background:#111; color:#e4e7ec; font-family:sans-serif; padding:20px;}
.receipt-card{
    background:#0f111a;
    padding:30px;
    border-radius:12px;
    max-width:600px;
    margin:0 auto;
    box-shadow:0 0 20px rgba(0,0,0,0.35);
}
.receipt-card h3, .receipt-card p, .receipt-card td, .receipt-card th{color:#e4e7ec;}
.table{width:100%; border-collapse:collapse; margin-top:20px;}
.table th{background:rgba(255,255,255,0.05); text-align:left; padding:10px;}
.table td{padding:10px; border-bottom:1px solid rgba(255,255,255,0.1);}
.button-row{display:flex; justify-content:center; gap:15px; margin-top:20px;}
.btn{padding:10px 20px; background:#2563eb; color:#fff; border:none; border-radius:8px; cursor:pointer; text-decoration:none;}
.btn:hover{background:#1e4ecb;}

/* PRINT KHUSUS NOTA SAJA */
@media print {
    body * {visibility:hidden;}
    .receipt-card, .receipt-card * {visibility:visible;}
    .receipt-card{position:absolute; left:0; top:0; width:100%; box-shadow:none; margin:0;}
}
</style>
</head>
<body>

<div class="receipt-card">
    <h3>Invoice: <?= $s['invoice_no'] ?></h3>
    <p>Kasir: <?= htmlspecialchars($s['cashier']) ?> | Tanggal: <?= $s['created_at'] ?></p>

    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while($it = mysqli_fetch_assoc($items)): ?>
            <tr>
                <td><?= htmlspecialchars($it['name']) ?></td>
                <td><?= $it['qty'] ?></td>
                <td>Rp <?= number_format($it['price'],0,',','.') ?></td>
                <td>Rp <?= number_format($it['subtotal'],0,',','.') ?></td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" style="text-align:right;font-weight:700">Total</td>
                <td>Rp <?= number_format($s['total_amount'],0,',','.') ?></td>
            </tr>
        </tbody>
    </table>

    <div class="button-row">
        <a class="btn" href="sales.php">Selesai</a>
        <button class="btn" onclick="window.print()">Cetak</button>
    </div>
</div>

</body>
</html>
