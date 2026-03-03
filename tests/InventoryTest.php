<?php
/**
 * File: tests/InventoryTest.php
 * Deskripsi: Menguji Fitur Produk & Manajemen Stok (CRUD & Mutasi)
 */

require_once __DIR__ . '/../koneksi.php';

echo "=== MEMULAI PENGUJIAN INVENTORY (IQOS SYSTEM) ===\n";

/**
 * 1. Test Tambah Produk Baru (Create)
 * Mensimulasikan logika dari produk_add.php
 */
function testAddProduct($conn, $sku, $name, $price, $stock) {
    echo "[TEST] Menambah Produk Baru: '$name'... ";
    
    $sku_esc  = mysqli_real_escape_string($conn, $sku);
    $name_esc = mysqli_real_escape_string($conn, $name);
    
    $query = "INSERT INTO products (sku, name, price, stock) VALUES ('$sku_esc', '$name_esc', $price, $stock)";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $last_id = mysqli_insert_id($conn);
        echo "LULUS (ID: $last_id)\n";
        return $last_id;
    }
    echo "GAGAL\n";
    return false;
}

/**
 * 2. Test Update Produk (Update)
 * Mensimulasikan logika dari produk_edit.php
 */
function testUpdateProduct($conn, $id, $new_name) {
    echo "[TEST] Update Nama Produk ID $id menjadi '$new_name'... ";
    
    $name_esc = mysqli_real_escape_string($conn, $new_name);
    $query = "UPDATE products SET name = '$name_esc' WHERE id = $id";
    mysqli_query($conn, $query);
    
    $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM products WHERE id = $id"));
    if ($check['name'] === $new_name) {
        echo "LULUS\n";
        return true;
    }
    echo "GAGAL\n";
    return false;
}

/**
 * 3. Test Mutasi Stok (Stock Adjustment)
 * Mensimulasikan logika dari stock_add.php (Transaksi stok keluar/kurang)
 */
function testStockMutation($conn, $product_id, $change, $type) {
    echo "[TEST] Mutasi Stok ($type) sebesar $change unit... ";
    
    // Ambil stok awal
    $cur = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stock FROM products WHERE id = $product_id"));
    $initial_stock = $cur['stock'];
    
    // Logika perhitungan (seperti di stock_add.php)
    $change_signed = ($type === 'out') ? -$change : $change;
    $new_stock = $initial_stock + $change_signed;

    if ($new_stock < 0) {
        echo "LULUS (Sistem menolak stok negatif secara logis)\n";
        return false;
    }

    mysqli_begin_transaction($conn);
    $update = mysqli_query($conn, "UPDATE products SET stock = $new_stock WHERE id = $product_id");
    
    // Simulasi insert mutasi (minimalis)
    $uid = 1; // Contoh ID user dummy
    $reason = "Test Pengujian";
    $stmt = mysqli_prepare($conn, "INSERT INTO stock_mutations (product_id, user_id, change_qty, type, reason) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "iiiss", $product_id, $uid, $change, $type, $reason);
    $mutation = mysqli_stmt_execute($stmt);

    if ($update && $mutation) {
        mysqli_commit($conn);
        echo "LULUS (Stok baru: $new_stock)\n";
        return true;
    } else {
        mysqli_rollback($conn);
        echo "GAGAL\n";
        return false;
    }
}

// --- Menjalankan Rangkaian Test ---

// 1. Tambah Barang
$new_id = testAddProduct($conn, 'IQOS-HEETS-01', 'Heets Bronze Selection', 35000, 100);

if ($new_id) {
    // 2. Edit Barang
    testUpdateProduct($conn, $new_id, 'Heets Bronze Selection (Promo)');
    
    // 3. Mutasi Stok Keluar
    testStockMutation($conn, $new_id, 10, 'out');
    
    // 4. Mutasi Stok Masuk
    testStockMutation($conn, $new_id, 5, 'in');
}

echo "=== PENGUJIAN INVENTORY SELESAI ===\n";
?>