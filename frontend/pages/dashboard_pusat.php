<?php
include "../../backend/auth/check_session.php";

if ($_SESSION['role'] !== 'pusat') {
    echo "Akses ditolak";
    exit;
}

require "../../backend/config/database.php";

/* dropdown cabang */
$cabangList = mysqli_query($conn, "SELECT id, nama_cabang FROM cabang");

/* LOGIC BACKEND */
require "../../backend/transaksi/summary_pusat.php";
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard Kepala Keuangan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>


<script>
document.addEventListener('DOMContentLoaded', function () {

  const ctx = document
    .getElementById('chartPendapatanBulanan')
    .getContext('2d');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($bulanLabels); ?>,
      datasets: <?= json_encode($chartDatasets); ?>
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom' // nama cabang di bawah
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Pendapatan (Rupiah)'
          },
          ticks: {
            callback: value => 'Rp ' + value.toLocaleString('id-ID')
          }
        },
        x: {
          title: {
            display: true,
            text: 'Bulan'
          }
        }
      }
    }
  });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

  const ctxPengeluaran = document
    .getElementById('chartPengeluaranBulanan')
    .getContext('2d');

  new Chart(ctxPengeluaran, {
    type: 'bar',
    data: {
      labels: <?= json_encode($bulanLabels); ?>,
      datasets: <?= json_encode($chartPengeluaranDatasets); ?>
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Pengeluaran (Rupiah)'
          },
          ticks: {
            callback: value => 'Rp ' + value.toLocaleString('id-ID')
          }
        },
        x: {
          title: {
            display: true,
            text: 'Bulan'
          }
        }
      }
    }
  });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<body>

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">

    <!-- KIRI -->
    <span class="navbar-brand">Keuangan Pusat - Waralaba X</span>

    <!-- KANAN -->
    <div class="d-flex gap-2">
      <a href="cabang_index.php" class="btn btn-outline-info btn-sm">
    🏬  Manage Cabang
    </a>

      <a href="../../backend/auth/logout.php" class="btn btn-outline-light btn-sm">
        Logout
      </a>
    </div>

  </div>
</nav>



<div class="container mt-4">

  <h4 class="mb-3">Dashboard Kepala Keuangan Pusat</h4>

<form method="GET" class="row g-2 mb-3">

  <div class="col-md-3">
    <label class="form-label">Cabang</label>
    <select name="cabang_id" class="form-select">
      <option value="">Semua Cabang</option>
      <?php while ($c = mysqli_fetch_assoc($cabangList)): ?>
        <option value="<?= $c['id']; ?>"
          <?= ($_GET['cabang_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
          <?= $c['nama_cabang']; ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="col-md-3">
    <label class="form-label">Dari Tanggal</label>
    <input type="date" name="start_date" class="form-control"
           value="<?= $_GET['start_date'] ?? '' ?>">
  </div>

  <div class="col-md-3">
    <label class="form-label">Sampai Tanggal</label>
    <input type="date" name="end_date" class="form-control"
           value="<?= $_GET['end_date'] ?? '' ?>">
  </div>

  <div class="col-md-3 align-self-end">
    <button class="btn btn-secondary w-100">Filter</button>
  </div>

</form>

<div class="mb-3">
  <a href="../../backend/report/laporan_keuangan_pdf.php?
      cabang_id=<?= $_GET['cabang_id'] ?? '' ?>
      &start_date=<?= $_GET['start_date'] ?? '' ?>
      &end_date=<?= $_GET['end_date'] ?? '' ?>"
     class="btn btn-danger btn-sm" target="_blank">
    Export PDF
  </a>

  <a href="../../backend/report/laporan_keuangan_excel.php?
      cabang_id=<?= $_GET['cabang_id'] ?? '' ?>
      &start_date=<?= $_GET['start_date'] ?? '' ?>
      &end_date=<?= $_GET['end_date'] ?? '' ?>"
     class="btn btn-success btn-sm" target="_blank">
    Export Excel
  </a>
</div>


<a href="activity_log.php" class="btn btn-outline-secondary mb-3">
  Lihat Activity Log
</a>

  <!-- KARTU RINGKASAN -->
  <div class="row">
    <div class="col-md-4">
      <div class="card text-bg-success mb-3">
        <div class="card-body">
          <h6>Total Pemasukan</h6>
          <h5>Rp <?= number_format($total['total_masuk'] ?? 0, 0, ',', '.'); ?></h5>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-bg-danger mb-3">
        <div class="card-body">
          <h6>Total Pengeluaran</h6>
          <h5>Rp <?= number_format($total['total_keluar'] ?? 0, 0, ',', '.'); ?></h5>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-bg-primary mb-3">
        <div class="card-body">
          <h6>Saldo</h6>
          <h5>
            Rp <?= number_format(
              ($total['total_masuk'] ?? 0) - ($total['total_keluar'] ?? 0),
              0, ',', '.'
            ); ?>
          </h5>
        </div>
      </div>
    </div>
  </div>

 <div class="card mt-4">
  <div class="card-header text-center">
    Pendapatan Tahun <?= $tahunAktif ?>
  </div>
  <div class="card-body">
    <canvas id="chartPendapatanBulanan" height="120"></canvas>
  </div>
</div>

<div class="card mt-4">
  <div class="card-header text-center">
    Pengeluaran Tahun <?= $tahunAktif ?>
  </div>
  <div class="card-body">
    <canvas id="chartPengeluaranBulanan" height="120"></canvas>
  </div>
</div>

  <!-- TABEL PER CABANG -->
  <div class="card mt-4">
    <div class="card-header">
      Rekap Keuangan per Cabang
    </div>
    <div class="card-body">
      <table class="table table-bordered table-striped">
        <thead class="table-light">
          <tr>
            <th>Nama Cabang</th>
            <th>Pemasukan</th>
            <th>Pengeluaran</th>
            <th>Saldo</th>
          </tr>
        </thead>
       <tbody>
<?php while ($row = mysqli_fetch_assoc($rekapResult)) { ?>
<tr>
  <td><?= $row['nama_cabang']; ?></td>
  <td>Rp <?= number_format($row['masuk'] ?? 0, 0, ',', '.'); ?></td>
  <td>Rp <?= number_format($row['keluar'] ?? 0, 0, ',', '.'); ?></td>
  <td>
    Rp <?= number_format(
      ($row['masuk'] ?? 0) - ($row['keluar'] ?? 0),
      0, ',', '.'
    ); ?>
  </td>
</tr>
<?php } ?>
</tbody>
      </table>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include "../partials/footer.php"; ?>
</body>
</html>