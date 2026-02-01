<?php
require __DIR__ . "/../config/database.php";

$start     = $_GET['start_date'] ?? null;
$end       = $_GET['end_date'] ?? null;
$cabang_id = $_GET['cabang_id'] ?? null;

/* ===============================
   FILTER DINAMIS
================================ */
$where  = "WHERE t.is_deleted = 0";
$params = [];
$types  = "";

/* filter cabang */
if ($cabang_id !== null && $cabang_id !== '') {
    $where .= " AND t.cabang_id = ?";
    $params[] = (int)$cabang_id;
    $types   .= "i";
}

/* filter tanggal */
if (!empty($start) && !empty($end)) {
    $where .= " AND t.tanggal BETWEEN ? AND ?";
    $params[] = $start;
    $params[] = $end;
    $types   .= "ss";
}

/* ===============================
   TOTAL PEMASUKAN & PENGELUARAN
================================ */
$stmtTotal = mysqli_prepare($conn, "
    SELECT
      SUM(CASE WHEN t.jenis='pemasukan' THEN t.jumlah ELSE 0 END) AS total_masuk,
      SUM(CASE WHEN t.jenis='pengeluaran' THEN t.jumlah ELSE 0 END) AS total_keluar
    FROM transaksi t
    $where
");

if (!empty($params)) {
    mysqli_stmt_bind_param($stmtTotal, $types, ...$params);
}
mysqli_stmt_execute($stmtTotal);
$total = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtTotal));

/* ===============================
   REKAP PER CABANG
================================ */
$sqlRekap = "
    SELECT 
      c.nama_cabang,
      SUM(CASE WHEN t.jenis='pemasukan' THEN t.jumlah ELSE 0 END) AS masuk,
      SUM(CASE WHEN t.jenis='pengeluaran' THEN t.jumlah ELSE 0 END) AS keluar
    FROM cabang c
    LEFT JOIN transaksi t 
      ON c.id = t.cabang_id 
      AND t.is_deleted = 0
";

$paramsRekap = [];
$typesRekap  = "";

/* filter cabang */
if ($cabang_id !== null && $cabang_id !== '') {
    $sqlRekap .= " AND t.cabang_id = ?";
    $paramsRekap[] = (int)$cabang_id;
    $typesRekap   .= "i";
}

/* filter tanggal */
if (!empty($start) && !empty($end)) {
    $sqlRekap .= " AND t.tanggal BETWEEN ? AND ?";
    $paramsRekap[] = $start;
    $paramsRekap[] = $end;
    $typesRekap   .= "ss";
}

$sqlRekap .= " GROUP BY c.id";

$stmtRekap = mysqli_prepare($conn, $sqlRekap);

if (!empty($paramsRekap)) {
    mysqli_stmt_bind_param($stmtRekap, $typesRekap, ...$paramsRekap);
}

mysqli_stmt_execute($stmtRekap);

/* 🔑 INI YANG PENTING */
$rekapResult = mysqli_stmt_get_result($stmtRekap);

$tahunAktif = $_GET['tahun'] ?? date('Y');

$queryGrafik = mysqli_query($conn, "
  SELECT 
    c.nama_cabang,
    MONTH(t.tanggal) AS bulan,
    SUM(t.jumlah) AS total
  FROM transaksi t
  JOIN cabang c ON t.cabang_id = c.id
  WHERE t.jenis = 'pemasukan'
    AND t.is_deleted = 0
    AND YEAR(t.tanggal) = '$tahunAktif'
  GROUP BY c.id, MONTH(t.tanggal)
  ORDER BY bulan
");

$grafikData = [];

while ($row = mysqli_fetch_assoc($queryGrafik)) {
    $grafikData[$row['nama_cabang']][(int)$row['bulan']] = (int)$row['total'];
}

$bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

$chartDatasets = [];
$warna = ['#fd7e14', '#0d6efd', '#198754', '#6f42c1'];
$i = 0;

foreach ($grafikData as $namaCabang => $dataBulanan) {
    $data = [];
    for ($b = 1; $b <= 12; $b++) {
        $data[] = $dataBulanan[$b] ?? 0;
    }

    $chartDatasets[] = [
        'label' => $namaCabang,
        'data' => $data,
        'backgroundColor' => $warna[$i++ % count($warna)]
    ];
}

$queryPengeluaran = mysqli_query($conn, "
  SELECT 
    c.nama_cabang,
    MONTH(t.tanggal) AS bulan,
    SUM(t.jumlah) AS total
  FROM transaksi t
  JOIN cabang c ON t.cabang_id = c.id
  WHERE t.jenis = 'pengeluaran'
    AND t.is_deleted = 0
    AND YEAR(t.tanggal) = '$tahunAktif'
  GROUP BY c.id, MONTH(t.tanggal)
  ORDER BY bulan
");

$grafikPengeluaran = [];

while ($row = mysqli_fetch_assoc($queryPengeluaran)) {
    $grafikPengeluaran[$row['nama_cabang']][(int)$row['bulan']] = (int)$row['total'];
}

$chartPengeluaranDatasets = [];
$warnaPengeluaran = ['#dc3545', '#6f42c1', '#fd7e14', '#0dcaf0'];

$i = 0;
foreach ($grafikPengeluaran as $namaCabang => $dataBulanan) {
    $data = [];
    for ($b = 1; $b <= 12; $b++) {
        $data[] = $dataBulanan[$b] ?? 0;
    }

    $chartPengeluaranDatasets[] = [
        'label' => $namaCabang,
        'data' => $data,
        'backgroundColor' => $warnaPengeluaran[$i++ % count($warnaPengeluaran)]
    ];
}