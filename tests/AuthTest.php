<?php
/**
 * File: tests/AuthTest.php
 * Deskripsi: Menguji fitur Autentikasi (Login & Logout)
 */

// Sertakan file koneksi dari folder root
require_once __DIR__ . '/../koneksi.php';

echo "=== MEMULAI PENGUJIAN AUTENTIKASI (IQOS SYSTEM) ===\n";

/**
 * 1. Test Koneksi Database
 */
function testDatabaseConnection($conn) {
    echo "[TEST] Menguji Koneksi Database... ";
    if ($conn) {
        echo "BERHASIL\n";
        return true;
    }
    echo "GAGAL\n";
    return false;
}

/**
 * 2. Test Fungsi Login (Simulasi Logika di login.php)
 * Menguji apakah user dengan username tertentu ada dan password cocok.
 */
function testLoginProcess($conn, $user_input, $pass_input) {
    echo "[TEST] Menguji Login untuk User: '$user_input'... ";
    
    // Logika yang disesuaikan dari login.php
    $stmt = mysqli_prepare($conn, "SELECT id, name, username, password, role FROM users WHERE username = ? AND is_active = 1 LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $user_input);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);

    if ($user && $pass_input === $user['password']) {
        echo "LULUS (User ditemukan & Password cocok)\n";
        return true;
    } else {
        echo "GAGAL (Kredensial tidak valid)\n";
        return false;
    }
}

/**
 * 3. Test Logout (Simulasi logika di logout.php)
 */
function testLogoutProcess() {
    echo "[TEST] Menguji Logika Logout... ";
    
    // Simulasi session
    $_SESSION['user'] = ['id' => 1, 'name' => 'Admin'];
    
    // Logika dari logout.php
    unset($_SESSION['user']);
    session_destroy();
    
    if (!isset($_SESSION['user'])) {
        echo "LULUS (Session berhasil dihapus)\n";
        return true;
    }
    echo "GAGAL\n";
    return false;
}

// Menjalankan Rangkaian Test
if (testDatabaseConnection($conn)) {
    // Pastikan username 'admin' sudah ada di database kamu untuk test ini
    // Jika belum ada, sesuaikan dengan data di tabel 'users' milikmu
    testLoginProcess($conn, 'admin', 'admin123'); 
    
    // Test login salah
    testLoginProcess($conn, 'wronguser', 'wrongpass');
    
    // Test logout
    testLogoutProcess();
}

echo "=== PENGUJIAN SELESAI ===\n";
?>