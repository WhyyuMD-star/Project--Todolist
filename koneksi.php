<?php

session_start();

// Koneksi ke database
$errors = "";
$host = 'localhost'; // Nama host database
$dbname = 'tugas_akhir'; // Nama database
$username = 'root'; // Nama pengguna database
$password = ''; // Kata sandi database

// Membuat koneksi
$koneksi = new mysqli($host, $username, $password, $dbname);

// Memeriksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>