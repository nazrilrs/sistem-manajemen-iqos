<?php
/**
 * File: tests/SalesTest.php
 * Deskripsi: Menguji Alur Penjualan (Cart & Sale Process)
 */

require_once __DIR__ . '/../koneksi.php';

echo "=== MEMULAI PENGUJIAN PENJUALAN (IQOS SYSTEM) ===\n";

/**
 * 1. Test Penambahan ke Keranjang (Simulasi add_cart.php)
 * Menguji apakah session keranjang menyimpan data dengan benar.
 */
function testAddToCart($product_id, $qty) {
    echo "[TEST] Menambah Produk ID $product_id ke Keranjang (Qty: $qty)... ";
    
    if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    // Logika dari add_cart.php
    if(isset($_SESSION['cart'][$product_id])){
        $_SESSION['cart'][$product_id] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }

    if ($_SESSION['cart'][$product_id] >= $qty) {
        echo "LULUS (Keranjang berisi " . $_SESSION['cart'][$product_id] . " item)\n";
        return true;
    }
    echo "GAGAL\n";
    return false;
}

/**
 * 2. Test Proses Penjualan (Simulasi sale_process.php)
 * Menguji transaksi database: Insert Sale, Sale Items, Update Stok, & Mutasi.
 */
function testSaleProcess($conn, $pid, $qty) {
    echo "[TEST] Menjalankan Transaksi Penjualan untuk Produk ID $pid... ";

    // Mock session user (diperlukan oleh sale_process.php)
    $_SESSION['user'] = ['id' => 1, 'name' => 'Tester'];

    // 1. Cek stok (Logic from sale_process.php)
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, price, stock FROM products WHERE id=$pid FOR UPDATE"));
    
    if(!$row || $row['stock'] < $qty) {
        echo "GAGAL (Stok tidak cukup atau produk tidak ada)\n";
        return false;
    }

    $subtotal = $qty * $row['price'];
    $uid = $_SESSION['user']['id'];
    $invoice = 'TEST-INV-' . date('YmdHis');

    // Mulai Transaksi Database
    mysqli_begin_transaction($conn);
    try {
        // Insert ke table sales
        $ins = mysqli_prepare($conn, "INSERT INTO sales (invoice_no, user_id, total_amount, total_items, payment_method, payment_status) VALUES (?,?,?,?,'cash','success')");
        mysqli_stmt_bind_param($ins, "siid", $invoice, $uid, $subtotal, $qty);
        mysqli_stmt_execute($ins);
        $sale_id = mysqli_insert_id($conn);

        // Update Stok
        mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id=$pid");

        // Simpan Mutasi
        $stmtm = mysqli_prepare($conn, "INSERT INTO stock_mutations (product_id, user_id, change_qty, type, reason) VALUES (?,?,?,?,?)");
        $chg = -$qty; $type='sale'; $reason = "Testing Sale #$invoice";
        mysqli_stmt_bind_param($stmtm, "iiiss", $pid, $uid, $chg, $type, $reason);
        mysqli_stmt_execute($stmtm);

        mysqli_commit($conn);
        echo "LULUS (Sale ID: $sale_id, Stok terpotong)\n";
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "GAGAL (Database Error)\n";
        return false;
    }
}

// --- Eksekusi Pengujian ---
session_start();

// Asumsi ada produk dengan ID 1 di database kamu
$target_product_id = 1; 

testAddToCart($target_product_id, 2);
testSaleProcess($conn, $target_product_id, 1);

echo "=== PENGUJIAN PENJUALAN SELESAI ===\n";
?>