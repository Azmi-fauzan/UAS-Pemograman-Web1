<?php
include "../../backend/auth/check_session.php";


if ($_SESSION['role'] != 'cabang') {
    echo "Akses ditolak";
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard Cabang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100" >

<nav class="navbar navbar-dark bg-primary">
  <div class="container-fluid">
    <span class="navbar-brand">Admin Cabang</span>
    <a href="../../backend/auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
  </div>
</nav>


<div class="flex-fill container my-4">
  <h4>Dashboard Admin Cabang</h4>
  <p>Cabang ID: <?= $_SESSION['cabang_id']; ?></p>

  <div class="card">
    <div class="card-body">
      <p>Silakan input transaksi pemasukan dan pengeluaran cabang.</p>

      <!-- Tombol ke halaman transaksi -->
      <a href="transaksi_cabang.php" class="btn btn-primary mt-3">
        Kelola Transaksi
      </a>

    </div>
  </div>
</div>


<?php include "../partials/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>