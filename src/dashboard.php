<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

require 'koneksi.php';
include 'sidebar.php';

$role = $_SESSION['user']['role'];

// Ambil tanggal filter, default hari ini
$selectedDate = $_GET['tgl'] ?? date('Y-m-d');

// Statistik utama
$totalProducts = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM products"))['c'];
$rev = mysqli_fetch_assoc(mysqli_query($conn,"SELECT IFNULL(SUM(total_amount),0) as rev FROM sales WHERE DATE(created_at)='$selectedDate'"))['rev'];

// Ambil stok saat ini = stock + total_in - total_out
$lowStockRes = mysqli_query($conn,"
    SELECT 
        p.id,
        p.name AS nama_produk,
        p.stock +
        IFNULL((SELECT SUM(change_qty) FROM stock_mutations WHERE product_id = p.id AND type='in'),0) -
        IFNULL((SELECT SUM(change_qty) FROM stock_mutations WHERE product_id = p.id AND type='out'),0) AS stok,
        p.min_stock
    FROM products p
    HAVING stok <= min_stock
    ORDER BY stok ASC
");

// Hitung jumlah produk stok menipis untuk statistik
$lowStock = $lowStockRes->num_rows;

// Siapkan data chart
$chartLabels = [];
$chartData = [];
$chartColors = [];
while($row = mysqli_fetch_assoc($lowStockRes)){
    $chartLabels[] = $row['nama_produk'];
    $chartData[] = $row['stok'];
    $chartColors[] = $row['stok'] == 0 ? 'rgba(255, 99, 132, 0.7)' : 'rgba(54, 162, 235, 0.6)';
}

// Reset pointer result supaya bisa digunakan lagi untuk list
$lowStockRes->data_seek(0);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Dashboard IQOS</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.stats-grid{
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(260px,1fr));
    gap:18px;
    margin-top:20px;
}
.stat-card{
    background:linear-gradient(180deg,#0f1022,#161836,#21004d);
    padding:20px;
    border-radius:14px;
    box-shadow:0 10px 40px rgba(0,0,0,0.55);
    color:#eef;
    display:flex;
    align-items:center;
    gap:18px;
}
.stat-card i{
    font-size:42px;
    color:#9fbfff;
    min-width:45px;
}
.stat-text h3{ margin:0; font-size:18px; font-weight:600; color:#9fbfff; }
.stat-text p{ margin:8px 0 0 0; font-size:28px; font-weight:700; color:#fff; }
.card{background:#121623;padding:20px;border-radius:12px;margin-top:20px;}
.list-group-item{display:flex;justify-content:space-between;align-items:center;padding:8px 12px;border-bottom:1px solid #292c35;color:#e4e7ec;}
.list-group-item span{font-weight:bold;}
.chart-container{
    max-width:600px;
    margin:0 auto;
    height:250px;
}
.date-filter{
    margin-bottom:15px;
    display:flex;
    align-items:center;
    gap:10px;
    color:#eef;
}
.date-filter input[type="date"]{
    padding:6px 10px;
    border-radius:6px;
    border:none;
}
.date-filter button{
    padding:6px 12px;
    border-radius:6px;
    border:none;
    background:#2563eb;
    color:white;
    cursor:pointer;
}
</style>
</head>
<body>
<div class="header"><div class="title">Dashboard</div></div>
<div class="content">
    <!-- Greeting -->
    <div class="card">
        <h2>Selamat Datang <?= htmlspecialchars($_SESSION['user']['name']) ?></h2>
        <p>Berikut ringkasan data hari ini:</p>
    </div>

    <!-- Statistik -->
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fa-solid fa-boxes"></i>
            <div class="stat-text">
                <h3>Total Produk</h3>
                <p><?= $totalProducts ?></p>
            </div>
        </div>

        <div class="stat-card">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div class="stat-text">
                <h3>Stok Menipis</h3>
                <p><?= $lowStock ?></p>
            </div>
        </div>

        <div class="stat-card">
            <i class="fa-solid fa-coins"></i>
            <div class="stat-text">
                <h3>Pendapatan Hari Ini</h3>
                <!-- Form filter tanggal -->
                <form method="GET" class="date-filter">
                    <input type="date" name="tgl" value="<?= htmlspecialchars($selectedDate) ?>">
                    <button type="submit">Filter</button>
                </form>
                <p>Rp <?= number_format($rev,0,',','.') ?></p>
            </div>
        </div>
    </div>

    <!-- Card chart stok menipis -->
    <div class="card">
        <h3>Detail Produk Stok Menipis</h3>
        <?php if($lowStock > 0): ?>
            <div class="chart-container">
                <canvas id="lowStockChart"></canvas>
            </div>
            <ul class="list-group mt-3">
                <?php while($row = $lowStockRes->fetch_assoc()): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($row['nama_produk']) ?>
                        <span><?= $row['stok'] ?></span>
                    </li>
                <?php endwhile; ?>
            </ul>

            <script>
            const labels = <?= json_encode($chartLabels) ?>;
            const data = <?= json_encode($chartData) ?>;
            const colors = <?= json_encode($chartColors) ?>;

            const ctx = document.getElementById('lowStockChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Stok Saat Ini',
                        data: data,
                        backgroundColor: colors,
                        borderColor: colors.map(c => c.replace('0.6','1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context){
                                    return context.dataset.label + ': ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });
            </script>
        <?php else: ?>
            <p>Tidak ada produk yang stoknya menipis.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
