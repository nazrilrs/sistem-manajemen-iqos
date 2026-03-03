<?php
session_start(); 
if(!isset($_SESSION['user'])){ 
    header("Location: login.php"); 
    exit; 
}

require 'koneksi.php';
include 'sidebar.php';

$pid = intval($_GET['pid'] ?? 0);

if(isset($_POST['save'])){
    $product_id = intval($_POST['product_id']);
    $change     = intval($_POST['change_qty']);
    $type       = $_POST['type'];
    $reason     = mysqli_real_escape_string($conn, $_POST['reason']);

    // Ambil stok sekarang
    $cur = mysqli_fetch_assoc(mysqli_query($conn,"SELECT stock FROM products WHERE id=$product_id"));
    if(!$cur){
        echo "Produk tidak ditemukan!";
        exit;
    }

    // Tentukan perubahan stok berdasarkan tipe
    if($type === 'out'){
        $change_signed = -$change; // Keluar mengurangi stok
    } else {
        $change_signed = $change;  // Masuk/koreksi menambah stok
    }

    $new_stock = $cur['stock'] + $change_signed;

    // Validasi stok tidak negatif
    if($new_stock < 0){
        echo "<script>alert('Stok tidak boleh kurang dari 0!'); window.history.back();</script>";
        exit;
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    // Update stok
    mysqli_query($conn,"UPDATE products SET stock=$new_stock WHERE id=$product_id");

    // Insert mutasi
    $stmt = mysqli_prepare($conn,"
        INSERT INTO stock_mutations (product_id,user_id,change_qty,type,reason) 
        VALUES (?,?,?,?,?)
    ");
    $uid = $_SESSION['user']['id'];
    mysqli_stmt_bind_param($stmt,"iiiss",$product_id,$uid,$change,$type,$reason);
    mysqli_stmt_execute($stmt);

    mysqli_commit($conn);

    header("Location: stock.php");
    exit;
}

// Ambil daftar produk untuk dropdown
$prods = mysqli_query($conn,"SELECT id,name,stock FROM products ORDER BY name");
?>

<!doctype html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <style>
        .page-wrapper {
            margin-left: 260px; 
            padding: 60px;
            display: flex;
            justify-content: center;
            margin-top: 60px; 
        }
        .form-card {
            width: 100%;
            max-width: 600px;
        }
        .form-card .card {
            background: #0f111a;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.4);
        }
        .form-label {
            color: #cdd3df;
            font-size: 14px;
            margin-bottom: 4px;
            display: block;
        }
        .form-card input,
        .form-card select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            margin-bottom: 18px;
            font-size: 14px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 5px;
        }
        .btn:hover { background: #1e4ecb; }
    </style>
</head>
<body>

<div class="header">
    <div class="title">Update Stok / Mutasi</div>
</div>

<div class="page-wrapper">
    <div class="form-card">
        <div class="card">
            <form method="post">

                <label class="form-label">Pilih Produk</label>
                <select name="product_id" required>
                    <?php while($p = mysqli_fetch_assoc($prods)):
                        $sel = ($p['id']==$pid)?'selected':''; ?>
                        <option value="<?= $p['id'] ?>" <?= $sel ?>>
                            <?= htmlspecialchars($p['name']) ?> (Stok: <?= $p['stock'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>

                <label class="form-label">Jumlah Perubahan</label>
                <input type="number" name="change_qty" placeholder="Masuk: +angka | Keluar: angka positif" required>

                <label class="form-label">Tipe Mutasi</label>
                <select name="type" required>
                    <option value="in">Masuk</option>
                    <option value="out">Keluar</option>
                    <option value="correction">Koreksi</option>
                </select>

                <label class="form-label">Catatan</label>
                <input type="text" name="reason" placeholder="Opsional">

                <button class="btn" name="save">Simpan Mutasi</button>

            </form>
        </div>
    </div>
</div>

</body>
</html>
