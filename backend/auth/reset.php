<?php
include "../config/database.php";

$password = password_hash("password123", PASSWORD_DEFAULT);

mysqli_query($conn, "
  UPDATE users 
  SET password = '$password'
");

echo "Password semua user berhasil direset ke: password123";