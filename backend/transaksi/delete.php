<?php
session_start();
include __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../helpers/activity_log.php";

$id = $_GET['id'];
$cabang_id = $_SESSION['cabang_id'];

$query = mysqli_prepare(
    $conn,
    "UPDATE transaksi SET is_deleted = 1 WHERE id = ? AND cabang_id = ?"
);
mysqli_stmt_bind_param($query, "ii", $id, $cabang_id);
mysqli_stmt_execute($query);

logActivity(
    $conn,
    $_SESSION['user_id'],
    "Soft delete transaksi ID {$id}"
);

$_SESSION['success'] = "Transaksi berhasil dihapus.";
header("Location: ../../frontend/pages/transaksi_cabang.php");
exit;