<?php
/**
 * File: tests/UserManagementTest.php
 * Deskripsi: Menguji Fitur Manajemen Pengguna (Tambah & Update User)
 */

require_once __DIR__ . '/../koneksi.php';

echo "=== MEMULAI PENGUJIAN MANAJEMEN USER (IQOS SYSTEM) ===\n";

/**
 * 1. Test Tambah User Baru (Create)
 * Mensimulasikan logika dari user_add.php
 */
function testAddUser($conn, $name, $username, $pass, $role) {
    echo "[TEST] Menambah User Baru: '$username'... ";
    
    $ins = mysqli_prepare($conn, "INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, "ssss", $name, $username, $pass, $role);
    $result = mysqli_stmt_execute($ins);

    if ($result) {
        $last_id = mysqli_insert_id($conn);
        echo "LULUS (ID User: $last_id)\n";
        return $last_id;
    }
    echo "GAGAL\n";
    return false;
}

/**
 * 2. Test Update Data User (Update)
 * Mensimulasikan logika dari user_edit.php
 */
function testUpdateUser($conn, $id, $new_name, $new_role) {
    echo "[TEST] Update Data User ID $id... ";
    
    $name_esc = mysqli_real_escape_string($conn, $new_name);
    $role_esc = mysqli_real_escape_string($conn, $new_role);
    
    $query = "UPDATE users SET name = '$name_esc', role = '$role_esc' WHERE id = $id";
    $result = mysqli_query($conn, $query);
    
    $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, role FROM users WHERE id = $id"));
    if ($check['name'] === $new_name && $check['role'] === $new_role) {
        echo "LULUS\n";
        return true;
    }
    echo "GAGAL\n";
    return false;
}

/**
 * 3. Test Hapus User (Delete)
 * Mensimulasikan logika hapus dari user_edit.php
 */
function testDeleteUser($conn, $id) {
    echo "[TEST] Menghapus User ID $id... ";
    
    $result = mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    
    $check = mysqli_query($conn, "SELECT id FROM users WHERE id = $id");
    if (mysqli_num_rows($check) === 0) {
        echo "LULUS\n";
        return true;
    }
    echo "GAGAL\n";
    return false;
}

// --- Jalankan Rangkaian Test ---

// 1. Tambah User (Contoh Role: Kasir)
$user_id = testAddUser($conn, 'Budi Kasir', 'budi_iqos', 'password123', 'kasir');

if ($user_id) {
    // 2. Edit User (Promosi jadi Admin)
    testUpdateUser($conn, $user_id, 'Budi Terpilih', 'admin');
    
    // 3. Hapus User (Pembersihan data test)
    testDeleteUser($conn, $user_id);
}

echo "=== PENGUJIAN MANAJEMEN USER SELESAI ===\n";
?>