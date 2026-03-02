<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['user']['role'];

// BLOCK KASIR
if ($role !== 'owner') {
    header("Location: dashboard.php");
    exit;
}

require 'koneksi.php';
include 'sidebar.php';

// Ambil search query jika ada
$search = $_GET['search'] ?? '';
$search_sql = $search ? "WHERE name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'" : "";

$res = mysqli_query($conn, "SELECT * FROM products $search_sql ORDER BY id DESC");
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
        .delete-btn { background: #ef4444; }
        .action-btn i { margin-right: 4px; }

        /* Search form kanan atas */
        .search-form {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
            gap: 8px;
        }
        .search-form input[type="text"]{
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 200px;
        }
        .search-form button{
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            background: #2563eb;
            color: white;
            cursor: pointer;
        }
        .search-form button i { margin-right: 4px; }
    </style>
</head>

<body>

<div class="header">
    <div class="title">Produk</div>
</div>

<div class="content">
    <div class="card">

        <!-- Tombol Tambah Produk -->
        <a class="add-btn" href="produk_add.php">
            <i class="fa-solid fa-plus"></i> Tambah Produk
        </a>

        <!-- Form Search -->
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Cari nama produk..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
        </form>

        <!-- Tabel Produk -->
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>SKU</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php $i = 1; while ($r = mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($r['sku']) ?></td>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= number_format($r['price'], 0, ',', '.') ?></td>
                    <td><?= $r['stock'] ?></td>

                    <td>
                        <a class="action-btn edit-btn"
                           href="produk_edit.php?id=<?= $r['id'] ?>">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </a>

                        <a class="action-btn delete-btn"
                           href="produk_edit.php?del=<?= $r['id'] ?>"
                           onclick="return confirm('Hapus produk ini?');">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>
