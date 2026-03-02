<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: login.php"); 
    exit;
}

$role = $_SESSION['user']['role'];

// Hanya owner bisa akses halaman ini
if ($role !== 'owner') {
    header("Location: dashboard.php");
    exit;
}

require 'koneksi.php';
include 'sidebar.php';

// Ambil semua user, terbaru di atas
$res = mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");
?>

<!doctype html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .add-btn { background: #2563eb; padding: 10px 16px; display: inline-block; text-decoration: none; color: white; border-radius: 6px; margin-bottom: 15px; }
        .add-btn i { margin-right: 6px; }

        .action-btn { padding: 6px 10px; border-radius: 5px; color: white; text-decoration: none; font-size: 13px; margin-right: 6px; }
        .edit-btn { background: #10b981; }   /* HIJAU */
        .delete-btn { background: #ef4444; } /* MERAH */
        .action-btn i { margin-right: 4px; }
    </style>
</head>

<body>
<div class="header"><div class="title">Pengguna</div></div>

<div class="content">
  <div class="card">

    <a class="add-btn" href="user_add.php">
        <i class="fa-solid fa-plus"></i> Tambah User
    </a>

    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Username</th>
          <th>Password</th>
          <th>Aksi</th>
        </tr>
      </thead>

      <tbody>
      <?php 
      $no = 1; // counter untuk nomor urut tabel
      while($r=mysqli_fetch_assoc($res)): 
      ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><?= htmlspecialchars($r['username']) ?></td>
          <td><?= htmlspecialchars($r['password']) ?></td>

          <td>
            <a class="action-btn edit-btn" href="user_edit.php?id=<?= $r['id'] ?>">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>

            <a class="action-btn delete-btn"
               href="user_edit.php?del=<?= $r['id'] ?>"
               onclick="return confirm('Hapus user ini?');">
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
