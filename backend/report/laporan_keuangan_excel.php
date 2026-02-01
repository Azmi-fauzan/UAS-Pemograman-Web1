<?php
session_start();
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/* Cek role pusat */
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

/* Spreadsheet */
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

/* Header */
$sheet->setCellValue('A1', 'Cabang');
$sheet->setCellValue('B1', 'Tanggal');
$sheet->setCellValue('C1', 'Jenis');
$sheet->setCellValue('D1', 'Jumlah');
$sheet->setCellValue('E1', 'Keterangan');

/* Isi data */
$rowNum = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A'.$rowNum, $row['nama_cabang']);
    $sheet->setCellValue('B'.$rowNum, $row['tanggal']);
    $sheet->setCellValue('C'.$rowNum, ucfirst($row['jenis']));
    $sheet->setCellValue('D'.$rowNum, $row['jumlah']);
    $sheet->setCellValue('E'.$rowNum, $row['keterangan']);
    $rowNum++;
}

/* Auto width */
foreach (range('A','E') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

/* Output */
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_keuangan_waralaba.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;