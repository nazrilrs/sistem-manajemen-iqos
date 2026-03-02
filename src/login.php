<?php
session_start();
require 'koneksi.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = mysqli_prepare($conn, "SELECT id,name,username,password,role FROM users WHERE username = ? AND is_active = 1 LIMIT 1");
    mysqli_stmt_bind_param($stmt,"s",$username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);

    if ($user && $password === $user['password']) { // Untuk sementara plain text
        $_SESSION['user'] = [
            'id'=>$user['id'],
            'name'=>$user['name'],
            'username'=>$user['username'],
            'role'=>$user['role'] // owner / pegawai
        ];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah.";
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login IQOS</title>
<link rel="stylesheet" href="style.css">
<!-- Tambahkan Font Awesome untuk icon mata -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* agar icon mata menempel di input password */
.password-wrapper {
    position: relative;
}
.password-wrapper input {
    width: 100%;
    padding-right: 40px; /* space untuk icon */
}
.password-wrapper .toggle-pass {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #9aa0ad;
}
</style>
</head>
<body>
<div class="login-center">
  <div class="login-card">
   <div class="login-logo">
    <img src="Asset_IQOS-Logo.png" alt="IQOS Logo">
   </div>

    <?php if($error) echo "<p style='color:#ff9b9b;'>$error</p>"; ?>

    <form method="post">
      <input type="text" name="username" placeholder="Username" required>
      <div class="password-wrapper">
        <input type="password" name="password" id="password" placeholder="Password" required>
        <i class="fa-solid fa-eye toggle-pass" onclick="togglePassword()"></i>
      </div>
      <button class="btn" type="submit">Masuk</button>
    </form>

    <p class="small">Gunakan akun owner/pegawai</p>
  </div>
</div>

<script>
function togglePassword(){
    const pass = document.getElementById('password');
    const icon = document.querySelector('.toggle-pass');
    if(pass.type === "password"){
        pass.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        pass.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
</body>
</html>
