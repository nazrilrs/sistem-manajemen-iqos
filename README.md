Sistem Manajemen Iqos
Sistem Informasi Manajemen Penjualan IQOS adalah aplikasi berbasis web yang dirancang untuk mengoptimalkan operasional bisnis pada IQOS Partner R.E. Martadinata. Sistem ini mengotomatisasi pengelolaan stok, transaksi penjualan, dan manajemen pengguna untuk meningkatkan efisiensi dan akurasi data.

Anggota Kelompok & Peran
 Nazril Rusydi Suhendar (Team Lead): Fitur autentikasi dan manajemen user, Fitur aktivitas dan tampitan, dan Testing
 Dalva Daliah Mutiara M. M.: Database administartor, Fitur user
 Suci Maesani: Fitur produk, Fitur keranjang dan proses penjualan
 Muhamad Yusa Dardha: Fitur stok

Fitur Utama (Alur IPO):
 aplikasi ini mengimplementasikan alur:
 Input: Registrasi user (RBAC), entri data produk IQOS, dan pengisian stok baru.
 Proses: Validasi login, logika keranjang belanja (cart), pemotongan stok otomatis saat transaksi, dan pencatatan mutasi stok.
 Output: Dasbor ringkasan, struk pembayaran (receipt), dan laporan riwayat penjualan.

Persyaratan Sistem
 Web Server: Apache (XAMPP).
 Bahasa: PHP v7.4 / v8.x.
 Database: MySQL.
 Version Control: Git.

Langkah Instalasi
 Clone Repositori:
 git clone https://github.com/nazrilrs/sistem-manajemen-iqos.git
 
 Persiapan Database:
 Buat database bernama iqos di phpMyAdmin.
 Import file SQL yang tersedia di direktori docs/ atau root
 
 Konfigurasi:
 Sesuaikan koneksi.php dengan kredensial database lokal Anda.
 
 Akses:
 Buka http://localhost/sistem-manajemen-iqos/login.php.

Pengujian (Unit Testing)
 Setiap fitur utama telah diuji secara mandiri melalui skrip pengujian di folder tests/.
 Cara menjalankan test:
 php tests/AuthTest.php
 php tests/InventoryTest.php
 php tests/SalesTest.php
 php tests/UserManagementTest.php

Struktur Repositori
 docs/       : Dokumen SRS & Manual
 src/        : Source code aplikasi (PHP, CSS, JS)
 tests/      : File pengujian unit fitur
 README.md   : Dokumentasi utama