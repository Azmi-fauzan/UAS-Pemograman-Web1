<?php
$host = "localhost";
$user = "sample";
$pass = "sample";
$db   = "franchise_finance";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal");
}