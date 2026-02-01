<?php
session_start();
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../../vendor/autoload.php";

use Dompdf\Dompdf;

/* Cek role (khusus pusat) */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pusat') {
    echo "Akses ditolak";
    exit;
}

/* Ambil filter tanggal (opsional) */
$start     = $_GET['start_date'] ?? null;
$end       = $_GET['end_date'] ?? null;
$cabang_id = $_GET['cabang_id'] ?? null;

$where  = "WHERE t.is_deleted = 0";
$params = [];
$types  = "";

/* ✅ FILTER CABANG — BERDIRI SENDIRI */
if ($cabang_id !== null && $cabang_id !== '') {
    $where .= " AND t.cabang_id = ?";
    $params[] = (int)$cabang_id;
    $types   .= "i";
}

/* ✅ FILTER TANGGAL — BERDIRI SENDIRI */
if (!empty($start) && !empty($end)) {
    $where .= " AND t.tanggal BETWEEN ? AND ?";
    $params[] = $start;
    $params[] = $end;
    $types   .= "ss";
}

$query = mysqli_prepare($conn, "
    SELECT 
      c.nama_cabang,
      t.tanggal,
      t.jenis,
      t.jumlah,
      t.keterangan
    FROM transaksi t
    JOIN cabang c ON t.cabang_id = c.id
    $where
    ORDER BY c.nama_cabang, t.tanggal
");

if (!empty($params)) {
    mysqli_stmt_bind_param($query, $types, ...$params);
}

mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);

/* HTML untuk PDF */
$html = '
<h3 style="text-align:center;">Laporan Keuangan Waralaba X</h3>
<p>Dicetak oleh: Kepala Keuangan Pusat</p>';

if ($start && $end) {
    $html .= '<p>Periode: '.$start.' s.d '.$end.'</p>';
}

$html .= '
<table border="1" cellspacing="0" cellpadding="5" width="100%">
  <thead>
    <tr>
      <th>Cabang</th>
      <th>Tanggal</th>
      <th>Jenis</th>
      <th>Jumlah</th>
      <th>Keterangan</th>
    </tr>
  </thead>
  <tbody>
';

while ($row = mysqli_fetch_assoc($result)) {
    $html .= '
    <tr>
      <td>'.$row['nama_cabang'].'</td>
      <td>'.$row['tanggal'].'</td>
      <td>'.ucfirst($row['jenis']).'</td>
      <td>Rp '.number_format($row['jumlah'], 0, ',', '.').'</td>
      <td>'.$row['keterangan'].'</td>
    </tr>';
}

$html .= '
  </tbody>
</table>
';

/* Generate PDF */
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("laporan_keuangan_waralaba.pdf", ["Attachment" => true]);
exit;