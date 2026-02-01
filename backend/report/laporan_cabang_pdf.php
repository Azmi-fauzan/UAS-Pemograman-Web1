<?php
session_start();
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../helpers/activity_log.php";

use Dompdf\Dompdf;

/* Hanya admin cabang */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'cabang') {
    echo "Akses ditolak";
    exit;
}

$cabang_id = $_SESSION['cabang_id'];

$start = $_GET['start_date'] ?? null;
$end   = $_GET['end_date'] ?? null;

$whereDate = "";
$params = [$cabang_id];
$types  = "i";

/* kalau pakai filter tanggal */
if ($start && $end) {
    $whereDate = "AND tanggal BETWEEN ? AND ?";
    $params[] = $start;
    $params[] = $end;
    $types .= "ss";
}

$query = mysqli_prepare($conn, "
    SELECT tanggal, jenis, jumlah, keterangan
    FROM transaksi
    WHERE cabang_id = ?
      AND is_deleted = 0
      $whereDate
    ORDER BY tanggal
");

/* INI BAGIAN PENTING */
mysqli_stmt_bind_param($query, $types, ...$params);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);

/* HTML PDF */
$html = '
<h3 style="text-align:center;">Laporan Keuangan Cabang</h3>
<p>Cabang ID: '.$cabang_id.'</p>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
<tr>
  <th>Tanggal</th>
  <th>Jenis</th>
  <th>Jumlah</th>
  <th>Keterangan</th>
</tr>';

while ($row = mysqli_fetch_assoc($result)) {
    $html .= '
    <tr>
      <td>'.$row['tanggal'].'</td>
      <td>'.ucfirst($row['jenis']).'</td>
      <td>Rp '.number_format($row['jumlah'], 0, ',', '.').'</td>
      <td>'.$row['keterangan'].'</td>
    </tr>';
}

logActivity(
    $conn,
    $_SESSION['user_id'],
    "Mengekspor laporan PDF cabang"
);

$html .= '</table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("laporan_keuangan_cabang.pdf", ["Attachment" => true]);