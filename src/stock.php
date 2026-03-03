<?php
session_start();
if(!isset($_SESSION['user'])){ 
    header("Location: login.php"); 
    exit; 
}

require 'koneksi.php';
include 'sidebar.php';
$role = $_SESSION['user']['role'];

// Ambil filter tanggal & search
$date = $_GET['date'] ?? date('Y-m-d'); // default hari ini
$search = $_GET['search'] ?? '';
$search_sql = $search ? "AND p.name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'" : "";

// Ambil data produk beserta total mutasi Masuk & Keluar sesuai tanggal
$res = mysqli_query($conn,"
    SELECT 
        p.*,
        IFNULL(SUM(CASE WHEN sm.type='in' AND DATE(sm.created_at)='$date' THEN sm.change_qty END),0) as total_in,
        IFNULL(SUM(CASE WHEN sm.type='out' AND DATE(sm.created_at)='$date' THEN sm.change_qty END),0) as total_out,
        (SELECT COUNT(*) FROM stock_mutations sm2 WHERE sm2.product_id=p.id AND DATE(sm2.created_at)='$date') as mut
    FROM products p
    LEFT JOIN stock_mutations sm ON sm.product_id = p.id
    WHERE 1 $search_sql
    GROUP BY p.id
");
// Ambil catatan mutasi per produk untuk tanggal yang sama
$catatan_arr = [];
$cat_res = mysqli_query($conn,"
    SELECT product_id, GROUP_CONCAT(reason SEPARATOR ' | ') AS catatan
    FROM stock_mutations
    WHERE DATE(created_at)='$date'
    GROUP BY product_id
");
while($c = mysqli_fetch_assoc($cat_res)){
    $catatan_arr[$c['product_id']] = $c['catatan'];
}
?>

<!doctype html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.add-btn {
    background: #2563eb;
    padding: 10px 16px;
    display: inline-block;
    text-decoration: none;
    color: white;
    border-radius: 6px;
    margin-bottom: 15px;
}
.add-btn i { margin-right: 6px; }
.action-btn {
    padding: 6px 10px;
    border-radius: 5px;
    color: white;
    text-decoration: none;
    font-size: 13px;
    margin-right: 6px;
}
.edit-btn { background: #10b981; }
.low-stock { color: #f87171; font-weight: bold; }
.table th, .table td { padding: 10px; color: #e4e7ec; border-bottom:1px solid #292c35; }
.table th { background:#11131c; }

/* Filter gabung */
.filter-form {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-bottom: 15px;
}
.filter-form input[type="text"], .filter-form input[type="date"]{
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
.filter-form button{
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    background: #2563eb;
    color: white;
    cursor: pointer;
}
</style>
</head>
<body>

<div class="header"><div class="title">Manajemen Stok</div></div>
<div class="content">
<div class="card">

<?php if($role === 'owner'): ?>
<a class="add-btn" href="stock_add.php">
    <i class="fa-solid fa-plus"></i> Update Stok / Tambah Mutasi
</a>
<?php endif; ?>

<!-- Form Filter + Search -->
<form method="GET" class="filter-form">
    <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
    <input type="text" name="search" placeholder="Cari nama produk..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
</form>

<table class="table">
<thead>
<tr>
<th>No</th>
<th>Nama Produk</th>
<th>Stok Saat Ini</th>
<th>Masuk</th>
<th>Keluar</th>
<th>Total Mutasi</th>
<th>Catatan</th>
<th>Aksi</th>

</tr>
</thead>
<tbody>
<?php $i=1; while($r=mysqli_fetch_assoc($res)): ?>
<?php $low = $r['stock'] <= $r['min_stock']; ?>
<tr>
<td><?= $i++ ?></td>
<td><?= htmlspecialchars($r['name']) ?></td>
<td class="<?= $low?'low-stock':'' ?>"><?= $r['stock'] ?></td>
<td><?= $r['total_in'] ?></td>
<td><?= $r['total_out'] ?></td>
<td><?= $r['mut'] ?></td>
<td><?= htmlspecialchars($catatan_arr[$r['id']] ?? '-') ?></td>
<td>
<?php if($role === 'owner'): ?>
<a class="action-btn edit-btn" href="stock_add.php?pid=<?= $r['id'] ?>">
    <i class="fa-solid fa-rotate"></i> Update
</a>
<?php else: ?>-
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div></div>
</body>
</html>
