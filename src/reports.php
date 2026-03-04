<?php
session_start(); 
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require 'koneksi.php';
include 'sidebar.php';

/* -------------------- DELETE MULTIPLE DATA -------------------- */
if(isset($_POST['delete_selected'])){
    $ids = $_POST['ids'] ?? [];
    if(count($ids) > 0){
        $ids = array_map('intval', $ids); // aman
        $ids_str = implode(',', $ids);
        mysqli_query($conn, "DELETE FROM sales WHERE id IN ($ids_str)");
        header("Location: reports.php");
        exit;
    }
}

$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to']   ?? date('Y-m-d');

$stmt = mysqli_prepare($conn,"
    SELECT s.*, u.name 
    FROM sales s 
    LEFT JOIN users u ON s.user_id = u.id 
    WHERE DATE(s.created_at) BETWEEN ? AND ? 
    ORDER BY s.created_at DESC
");
mysqli_stmt_bind_param($stmt, "ss", $from, $to);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="style.css">

<style>
.content { margin-top: 40px; margin-left: 260px; padding: 20px; }
.card { padding: 25px; border-radius: 12px; background: #0f111a; box-shadow: 0 0 20px rgba(0,0,0,0.35); }
.filter-box { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-bottom: 10px; }
.filter-box input { padding: 7px 10px; width: 150px; border-radius: 6px; border: 1px solid #3a3f4b; background: #1b1e27; color: #fff; font-size: 14px; }
.btn-filter, .btn-delete-selected { padding: 8px 14px; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
.btn-filter { background: #2563eb; }
.btn-filter:hover { background: #1e4ecb; }
.btn-delete-selected { background: #d9534f; }
.btn-delete-selected:hover { background: #c9302c; }
.table { width: 100%; border-collapse: collapse; }
.table th { padding: 12px; background: #11131c; color: #e4e7ec; font-size: 14px; border-bottom: 1px solid #292c35; text-align: left; }
.table td { padding: 11px; color: #c7c9d3; border-bottom: 1px solid #1d2029; }
.table tbody tr:hover { background: #161922; }
/* RESPONSIVE HP */
@media (max-width: 650px) {
    .content { margin-left: 0; }
    .filter-box { flex-direction: column; align-items: flex-start; }
    .filter-box input, .btn-filter, .btn-delete-selected { width: 100%; }
}
</style>

<script>
function toggleAll(source){
    checkboxes = document.getElementsByName('ids[]');
    for(var i=0, n=checkboxes.length;i<n;i++){
        checkboxes[i].checked = source.checked;
    }
}
</script>
</head>

<body>

<div class="header">
    <div class="title">Laporan</div>
</div>

<div class="content">
<div class="card">

    <!-- FILTER -->
    <form method="get" class="filter-box">
        <input type="date" name="from" value="<?=htmlspecialchars($from)?>">
        <span style="color:#c7c9d3;">s/d</span>
        <input type="date" name="to" value="<?=htmlspecialchars($to)?>">
        <button class="btn-filter" type="submit">Filter</button>
    </form>

    <!-- FORM MULTI DELETE -->
    <form method="post">
        <button class="btn-delete-selected" type="submit" name="delete_selected" onclick="return confirm('Yakin ingin menghapus semua yang dicentang?')">Hapus Terpilih</button>

        <!-- TABEL -->
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox" onclick="toggleAll(this)"></th>
                    <th>No</th>
                    <th>Invoice</th>
                    <th>Kasir</th>
                    <th>Total</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($r = mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td><input type="checkbox" name="ids[]" value="<?= $r['id'] ?>"></td>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($r['invoice_no']) ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td>Rp <?= number_format($r['total_amount'],0,',','.') ?></td>
                    <td><?= $r['created_at'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </form>

</div>
</div>

</body>
</html>
