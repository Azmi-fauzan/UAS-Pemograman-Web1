<?php
session_start();
include __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../helpers/activity_log.php";


$cabang_id  = $_SESSION['cabang_id'];
$tanggal    = $_POST['tanggal'];
$jenis      = $_POST['jenis'];
$jumlah     = $_POST['jumlah'];
$keterangan = $_POST['keterangan'];

$query = mysqli_prepare(
    $conn,
    "INSERT INTO transaksi (cabang_id, tanggal, jenis, jumlah, keterangan)
     VALUES (?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param($query, "issds", $cabang_id, $tanggal, $jenis, $jumlah, $keterangan);
mysqli_stmt_execute($query);

logActivity(
    $conn,
    $_SESSION['user_id'],
    "Menambahkan transaksi {$jenis} sebesar Rp {$jumlah}"
);

header("Location: ../../frontend/pages/transaksi_cabang.php");