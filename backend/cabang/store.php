<?php
require "../config/database.php";

/* ambil data */
$nama     = $_POST['nama_cabang'];
$lokasi   = $_POST['lokasi'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

/* 1️⃣ insert cabang */
mysqli_query($conn, "
  INSERT INTO cabang (nama_cabang, lokasi)
  VALUES ('$nama', '$lokasi')
");

/* 2️⃣ ambil id cabang baru */
$cabang_id = mysqli_insert_id($conn);

/* 3️⃣ buat akun cabang */
mysqli_query($conn, "
  INSERT INTO users (username, password, role, cabang_id)
  VALUES ('$username', '$password', 'cabang', $cabang_id)
");

/* 4️⃣ redirect */
header("Location: ../../frontend/pages/cabang_index.php");