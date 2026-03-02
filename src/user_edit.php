<?php
session_start(); 
if(!isset($_SESSION['user'])){ header("Location: login.php"); exit; }
require 'koneksi.php';

$id = intval($_GET['id'] ?? 0);

if(isset($_GET['del'])){
    $d = intval($_GET['del']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$d");
    header("Location: users.php");
    exit;
}

if($id){
    $r = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id=$id"));

    if(isset($_POST['save'])){
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']); // TANPA HASH
        $role = $_POST['role'];

        mysqli_query($conn,"
            UPDATE users 
            SET 
                name='$name',
                username='$username',
                password='$password',
                role='$role'
            WHERE id=$id
        ");

        header("Location: users.php");
        exit;
    }
}

include 'sidebar.php';
?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="style.css">

<style>
    .form-container {
        width: 50%;
        margin: 30px auto;
    }

    @media (max-width: 768px) {
        .form-container { width: 90%; }
    }

    .form-container .card {
        padding: 25px;
        border-radius: 12px;
    }

    .form-container input,
    .form-container select {
        width: 100%;
        padding: 12px;
        margin-top: 8px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }

    .btn {
        padding: 10px 18px;
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 15px;
    }

    .btn:hover { background: #1e4ecb; }
</style>

</head>
<body>

<div class="header">
  <div class="title">Edit User</div>
</div>

<div class="content">

  <div class="form-container">
    <div class="card">

      <?php if($id && $r): ?>
      <form method="post">

        <label>Nama</label>
        <input type="text" name="name" value="<?= htmlspecialchars($r['name']) ?>" required>

        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($r['username']) ?>" required>

        <label>Password</label>
        <input type="text" name="password" value="<?= htmlspecialchars($r['password']) ?>" required>

        <label>Role</label>
        <select name="role" required>
            <option value="kasir" <?= $r['role']=='kasir'?'selected':'' ?>>kasir</option>
            <option value="admin" <?= $r['role']=='admin'?'selected':'' ?>>admin</option>
            <option value="owner" <?= $r['role']=='owner'?'selected':'' ?>>owner</option>
        </select>

        <button class="btn" name="save">Simpan</button>

      </form>

      <?php else: ?>
        <p>User tidak ditemukan.</p>
      <?php endif; ?>

    </div>
  </div>

</div>

</body>
</html>
