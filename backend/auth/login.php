<?php
session_start();
include "../config/database.php";
require_once __DIR__ . "/../helpers/activity_log.php";

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($query, "s", $username);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['role']      = $user['role'];
    $_SESSION['cabang_id'] = $user['cabang_id'];

    // Redirect sesuai role
    if ($user['role'] == 'pusat') {
        header("Location: ../../frontend/pages/dashboard_pusat.php");
    } else {
        header("Location: ../../frontend/pages/dashboard_cabang.php");
    }

} else {
    echo "Login gagal. Username atau password salah.";
}

 setcookie(
    "ff_user",               // nama cookie
    $user['username'],       // isi cookie
    time() + 3600,           // 1 jam
    "/"                      // berlaku di seluruh website
  );

logActivity(
    $conn,
    $user['id'],
    "Login ke sistem sebagai role {$user['role']}"
);