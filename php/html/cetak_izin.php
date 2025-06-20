<?php
require('fpdf/vendor/autoload.php'); // Pastikan autoload dari FPDF tersedia
include('db_connection.php');

// Query data cuti
$sql = "SELECT * FROM izin;";
$result = $conn->query($sql);

// Inisialisasi FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Judul PDF
$pdf->Cell(0, 10, 'LAPORANDATA IZIN KARYAWAN PT DGEX INDONESIA', 0, 1, 'C');
$pdf->Ln(10); // Tambah spasi vertikal

// Header Tabel
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(200, 200, 200); // Warna latar header
$pdf->Cell(8, 10, 'No', 1, 0, 'C', true);
$pdf->Cell(28, 10, 'Tanggal Dibuat', 1, 0, 'C', true);
$pdf->Cell(13, 10, 'Nama', 1, 0, 'C', true);
$pdf->Cell(19, 10, 'Departemen', 1, 0, 'C', true);
$pdf->Cell(26, 10, 'Tanggal Mulai', 1, 0, 'C', true);
$pdf->Cell(29, 10, 'Tanggal Selesai', 1, 0, 'C', true);
$pdf->Cell(28, 10, 'Kategori', 1, 0, 'C', true);
$pdf->Cell(37, 10, 'Status', 1, 1, 'C', true);

// Isi Tabel
$pdf->SetFont('Arial', '', 8);
$no = 1;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(8, 10, $no, 1, 0, 'C');
        $pdf->Cell(28, 10, $row['tanggal_pembuatan'], 1, 0, 'C');
        $pdf->Cell(13, 10, $row['username'], 1, 0, 'C');
        $pdf->Cell(19, 10, $row['departement'], 1, 0, 'C');
        $pdf->Cell(26, 10, $row['tanggal_mulai'], 1, 0, 'C');
        $pdf->Cell(29, 10, $row['tanggal_selesai'], 1, 0, 'C');
        $pdf->Cell(28, 10, $row['Kategori'], 1, 0, 'C');
        $pdf->Cell(37, 10, $row['status'], 1, 1, 'C');
        $no++;
    }
} else {
    $pdf->Cell(0, 10, 'Tidak ada data cuti.', 1, 1, 'C');
}

// Output PDF
$pdf->Output();
?>
