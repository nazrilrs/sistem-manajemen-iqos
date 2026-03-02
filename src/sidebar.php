<?php
if(session_status()===PHP_SESSION_NONE) session_start();
$role = $_SESSION['user']['role'];
?>

<!-- Load Font Awesome -->
<link rel="stylesheet" 
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="sidebar">
  <div class="brand">
    <img src="Asset_IQOS-Logo.png" alt="IQOS Logo">
  </div>

  <!-- Semua role bisa -->
  <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> <span>Dashboard</span></a>

  <!-- OWNER saja yang boleh lihat Produk -->
  <?php if($role === 'owner'): ?>
      <a href="produk.php"><i class="fa-solid fa-box-open"></i> <span>Produk</span></a>
  <?php endif; ?>

  <!-- Stok: Owner & Kasir boleh, tapi nanti stok.php-nya dibatasi -->
  <a href="stock.php"><i class="fa-solid fa-layer-group"></i> <span>Stok</span></a>

  <!-- Semua role bisa -->
  <a href="sales.php"><i class="fa-solid fa-cart-shopping"></i> <span>Penjualan</span></a>
  

  <!-- Owner saja -->
  <?php if($role === 'owner'): ?>
      <a href="reports.php"><i class="fa-solid fa-chart-line"></i> <span>Laporan</span></a>
      <a href="users.php"><i class="fa-solid fa-users"></i> <span>Pengguna</span></a>
  <?php endif; ?>

  <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
</div>

<style>
.sidebar {
  width: 220px;
  background: #111827;
  color: white;
  height: 100vh;
  padding-top: 20px;
  display: flex;
  flex-direction: column;
  position: fixed;
}
.sidebar .brand { text-align:center; margin-bottom:20px; }
.sidebar .brand img { width:120px; height:auto; }
.sidebar a { display:flex; align-items:center; gap:12px; padding:12px 18px; color:#d1d5db; text-decoration:none; font-size:15px; transition:0.3s; }
.sidebar a:hover { background:#1f2937; color:#ffffff; }
.sidebar a i { width:20px; font-size:16px; }
.sidebar a.logout { color:#ff9b9b; margin-top:auto; }
</style>
