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
  <title>Transaksi Cabang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-dark bg-primary">
  <div class="container-fluid">
    <span class="navbar-brand">Transaksi Keuangan Cabang</span>
    <a href="dashboard_cabang.php" class="btn btn-outline-light btn-sm">Dashboard</a>
  </div>
</nav>


<main  class="flex-fill container my-4">
<div class="container mt-4">

<?php if (isset($_SESSION['success'])): ?>
  <div class="alert alert-success">
    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
  <div class="alert alert-danger">
    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
  </div>
<?php endif; ?>



  <!-- FORM INPUT -->
  <div class="card mb-4">
    <div class="card-header">Input Transaksi</div>
    <div class="card-body">
      <form action="../../backend/transaksi/create.php" method="POST">
        <div class="row">
          <div class="col-md-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Jenis</label>
            <select name="jenis" class="form-control" required>
              <option value="pemasukan">Pemasukan</option>
              <option value="pengeluaran">Pengeluaran</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Jumlah</label>
            <input type="number" name="jumlah" class="form-control" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan" class="form-control">
          </div>
        </div>

        <button class="btn btn-primary mt-3">Simpan Transaksi</button>
      </form>
    </div>
  </div>

  <div class="mb-3">
<a href="../../backend/report/laporan_cabang_pdf.php?start_date=<?= $_GET['start_date'] ?? '' ?>&end_date=<?= $_GET['end_date'] ?? '' ?>"
   class="btn btn-danger btn-sm" target="_blank">
  Export PDF
</a>

<a href="../../backend/report/laporan_cabang_excel.php?start_date=<?= $_GET['start_date'] ?? '' ?>&end_date=<?= $_GET['end_date'] ?? '' ?>"
   class="btn btn-success btn-sm" target="_blank">
  Export Excel
</a>
</div>

<form method="GET" class="row g-2 mb-3">
  <div class="col-md-4">
    <label class="form-label">Dari Tanggal</label>
    <input type="date" name="start_date" class="form-control"
           value="<?= $_GET['start_date'] ?? '' ?>">
  </div>

  <div class="col-md-4">
    <label class="form-label">Sampai Tanggal</label>
    <input type="date" name="end_date" class="form-control"
           value="<?= $_GET['end_date'] ?? '' ?>">
  </div>

  <div class="col-md-4 align-self-end">
    <button class="btn btn-secondary w-100">Filter</button>
  </div>
</form>

<?php
require "../../backend/config/database.php";

$cabang_id = $_SESSION['cabang_id'];
$start = $_GET['start_date'] ?? null;
$end   = $_GET['end_date'] ?? null;

$whereDate = "";
$params = [$cabang_id];
$types  = "i";

if ($start && $end) {
    $whereDate = "AND tanggal BETWEEN ? AND ?";
    $params[] = $start;
    $params[] = $end;
    $types .= "ss";
}

$querySummary = mysqli_prepare($conn, "
    SELECT
      SUM(CASE WHEN jenis='pemasukan' THEN jumlah ELSE 0 END) AS masuk,
      SUM(CASE WHEN jenis='pengeluaran' THEN jumlah ELSE 0 END) AS keluar
    FROM transaksi
    WHERE cabang_id = ?
    $whereDate
");

mysqli_stmt_bind_param($querySummary, $types, ...$params);
mysqli_stmt_execute($querySummary);
$summary = mysqli_fetch_assoc(mysqli_stmt_get_result($querySummary));

$masuk  = $summary['masuk'] ?? 0;
$keluar = $summary['keluar'] ?? 0;
$saldo  = $masuk - $keluar;
?>

<div class="row mb-4">
  <div class="col-md-4">
    <div class="card text-bg-success">
      <div class="card-body">
        <h6>Total Pemasukan</h6>
        <h5>Rp <?= number_format($masuk,0,',','.'); ?></h5>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card text-bg-danger">
      <div class="card-body">
        <h6>Total Pengeluaran</h6>
        <h5>Rp <?= number_format($keluar,0,',','.'); ?></h5>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card text-bg-primary">
      <div class="card-body">
        <h6>Saldo</h6>
        <h5>Rp <?= number_format($saldo,0,',','.'); ?></h5>
      </div>
    </div>
  </div>
</div>

  <!-- TABEL DATA -->
  <div class="card">
    <div class="card-header">Daftar Transaksi Cabang</div>
    <div class="card-body">
      <?php include "../../backend/transaksi/read.php"; ?>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</main>
<?php include "../partials/footer.php"; ?>
</body>
</html>