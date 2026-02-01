<?php
include __DIR__ . "/../config/database.php";

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

$query = mysqli_prepare($conn, "
    SELECT * FROM transaksi
    WHERE cabang_id = ?
    AND is_deleted = 0
    $whereDate
    ORDER BY tanggal DESC
");

mysqli_stmt_bind_param($query, $types, ...$params);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
?>

<table class="table table-bordered table-striped">
  <thead class="table-light">
    <tr>
      <th>Tanggal</th>
      <th>Jenis</th>
      <th>Jumlah</th>
      <th>Keterangan</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?= $row['tanggal']; ?></td>
        <td><?= ucfirst($row['jenis']); ?></td>
        <td>Rp <?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
        <td><?= $row['keterangan']; ?></td>
        <td>
          <a href="../../backend/transaksi/delete.php?id=<?= $row['id']; ?>"
             class="btn btn-danger btn-sm"
             onclick="return confirm('Hapus transaksi?')">
            Hapus
          </a>
        </td>
      </tr>
    <?php } ?>
  </tbody>
</table>