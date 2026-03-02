<?php
session_start(); 
if(!isset($_SESSION['user'])){ header("Location: login.php"); exit; }
require 'koneksi.php';

if(isset($_POST['save'])){
    $name=$_POST['name']; 
    $username=$_POST['username']; 
    $pass=$_POST['password']; 
    $role=$_POST['role'];

    $ins = mysqli_prepare($conn,"INSERT INTO users (name,username,password,role) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($ins,"ssss",$name,$username,$pass,$role); 
    mysqli_stmt_execute($ins);

    header("Location: users.php"); 
    exit;
}

include 'sidebar.php';
?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="style.css">

<style>
    /* Samakan styling dengan Tambah Produk */

    .form-container {
        width: 50%;
        margin: 30px auto;
    }

    @media (max-width: 768px) {
        .form-container {
            width: 90%;
        }
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

    .btn:hover {
        background: #1e4ecb;
    }
</style>

</head>
<body>

<div class="header">
  <div class="title">Tambah User</div>
</div>

<div class="content">

  <div class="form-container">
    <div class="card">

      <form method="post">

        <label>Nama</label>
        <input type="text" name="name" placeholder="Nama" required>

        <label>Username</label>
        <input type="text" name="username" placeholder="Username" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Password" required>

        <label>Role</label>
        <select name="role" required>
            <option value="kasir">kasir</option>
            <option value="admin">admin</option>
            <option value="owner">owner</option>
        </select>

        <button class="btn" name="save">Simpan</button>

      </form>

    </div>
  </div>

</div>

</body>
</html>
