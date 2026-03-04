<?php
session_start();
if(!isset($_SESSION['user'])){ 
    header("Location: login.php"); 
    exit; 
}
require 'koneksi.php';
include 'sidebar.php';

// Ambil produk
$products = mysqli_query($conn, "SELECT id,name,price,stock FROM products ORDER BY name");

// Siapkan session cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Hitung total item di cart
$cart_count = array_sum($_SESSION['cart']);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Penjualan (IQOS)</title>
<link rel="stylesheet" href="style.css">
<style>
/* Pos wrapper untuk grid produk */
.pos-wrapper {
    margin-left:260px;
    padding:40px;
    margin-top:40px;
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
    gap:20px;
}

/* Kartu produk */
.product-card {
    background:#0f111a;
    padding:20px;
    border-radius:12px;
    box-shadow:0 0 20px rgba(0,0,0,0.35);
}
.product-name {
    font-size:18px;
    font-weight:bold;
    color:#e4e7ec;
    margin-bottom:6px;
}
.product-info {
    font-size:14px;
    color:#9aa0ad;
    margin-bottom:12px;
}
.qty-input {
    width:90px;
    padding:10px;
    border-radius:8px;
    border:none;
    margin-bottom:10px;
}
.btn {
    padding:10px 14px;
    background:#2563eb;
    color:#fff;
    border:none;
    border-radius:8px;
    cursor:pointer;
    width:100%;
    font-weight:bold;
}
.btn:hover { background:#1e4ecb; }

/* ======================
   Floating Cart Button
   ====================== */
.cart-btn {
    position: fixed;
    right: 20px;
    bottom: 20px;
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 24px;
    box-shadow: 0 0 15px rgba(0,0,0,0.6);
    z-index: 1000;
    text-decoration: none;
    background: linear-gradient(135deg, #2563eb, #4f46e5);
    color: #fff;
    transition: transform 0.2s, box-shadow 0.2s;
}

.cart-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 0 25px rgba(0,0,0,0.7);
}

/* Cart count bubble */
.cart-count {
    background:#fff;
    color:#2563eb;
    border-radius:50%;
    padding:4px 8px;
    font-size:14px;
    font-weight:bold;
    position:absolute;
    top:-8px;
    right:-8px;
    box-shadow: 0 0 4px rgba(0,0,0,0.3);
}

/* Responsive mobile */
@media(max-width:650px){
    .pos-wrapper{ margin-left:0; padding:20px; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:15px;}
}
</style>
</head>
<body>

<div class="header"><div class="title">Penjualan (IQOS)</div></div>

<!-- Floating Cart -->
<a class="cart-btn" href="#" onclick="checkCart(event)">
    🛒
    <?php if($cart_count>0): ?>
    <span class="cart-count"><?= $cart_count ?></span>
    <?php endif; ?>
</a>

<div class="pos-wrapper">
<?php while ($p = mysqli_fetch_assoc($products)): ?>
<div class="product-card">
    <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
    <div class="product-info">
        Rp <?= number_format($p['price'],0,',','.') ?><br>
        Stok: <?= (int)$p['stock'] ?>
    </div>

    <form method="post" action="add_cart.php">
        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
        <input type="number" name="qty" value="1" min="1" max="<?= $p['stock'] ?>" class="qty-input">
        <button class="btn" type="submit">Tambah ke Keranjang</button>
    </form>
</div>
<?php endwhile; ?>
</div>

<script>
// Klik cart: cek isi cart
function checkCart(e){
    e.preventDefault();
    let cartCount = <?= $cart_count ?>;
    if(cartCount > 0){
        window.location.href = 'cart.php'; // ada isi, buka cart.php
    } else {
        alert("Keranjang kosong!"); // kosong, notif
    }
}
</script>

</body>
</html>
