<?php
session_start();
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../helpers/activity_log.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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


logActivity(
    $conn,
    $_SESSION['user_id'],
    "Mengekspor laporan Excel cabang"
);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

/* Header */
$sheet->setCellValue('A1', 'Tanggal');
$sheet->setCellValue('B1', 'Jenis');
$sheet->setCellValue('C1', 'Jumlah');
$sheet->setCellValue('D1', 'Keterangan');

$rowNum = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A'.$rowNum, $row['tanggal']);
    $sheet->setCellValue('B'.$rowNum, ucfirst($row['jenis']));
    $sheet->setCellValue('C'.$rowNum, $row['jumlah']);
    $sheet->setCellValue('D'.$rowNum, $row['keterangan']);
    $rowNum++;
}

/* Auto size */
foreach (range('A','D') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

/* Output */
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_keuangan_cabang.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;