<?php
require "../../backend/auth/check_session.php";
require "../../backend/config/database.php";

$cabang = mysqli_query($conn, "SELECT * FROM cabang ORDER BY nama_cabang");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manajemen Cabang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
<main class="flex-fill container my-4">
<h4>Manajemen Cabang</h4>

<a href="cabang_tambah.php" class="btn btn-success mb-3">
  + Tambah Cabang
</a>

<table class="table table-bordered">
  <tr>
    <th>No</th>
    <th>Nama Cabang</th>
  </tr>
  <?php $no=1; while($c=mysqli_fetch_assoc($cabang)) { ?>
  <tr>
    <td><?= $no++ ?></td>
    <td><?= $c['nama_cabang'] ?></td>
  </tr>
  <?php } ?>
</table>

<a href="dashboard_pusat.php" class="btn btn-secondary">← Kembali</a>
</main>
<?php include "../partials/footer.php"; ?>
</body>
</html>