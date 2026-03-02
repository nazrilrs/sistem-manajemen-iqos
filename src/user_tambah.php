<?php
include "koneksi.php";

if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    mysqli_query($conn, 
    "INSERT INTO users(name, username, password_hash, role) 
     VALUES('$name', '$username', '$password', '$role')");

    header("Location: user_list.php");
}
?>

<h2>Tambah User</h2>
<form method="POST">
Nama: <input type="text" name="name"><br><br>
Username: <input type="text" name="username"><br><br>
Password: <input type="password" name="password"><br><br>
Role: 
<select name="role">
    <option value="kasir">Kasir</option>
    <option value="admin">Admin</option>
    <option value="owner">Owner</option>
</select><br><br>
<button name="save">Simpan</button>
</form>
