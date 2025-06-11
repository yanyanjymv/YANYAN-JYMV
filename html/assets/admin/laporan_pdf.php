<?php
require('fpdf/vendor/setasign/fpdf/fpdf.php');  // Pastikan path benar ke fpdf.php
include '../../koneksi.php';
session_start();

if ($_SESSION['roles'] != 'admin') {
    echo "<script>alert('Forbidden Access'); location.href='../../index.php';</script>";
    exit();
}

// Query data mobil
$mobilQuery = mysqli_query($konekdb, "SELECT * FROM tb_mobil ORDER BY id_mobil ASC");

// Query data pembayaran + nama user
$pembayaranQuery = mysqli_query($konekdb, "
    SELECT p.*, b.nama_user 
    FROM tb_pesanan p 
    JOIN tb_bio b ON p.id_user = b.id_user
    ORDER BY p.tanggal_pesan DESC
");

$pdf = new FPDF();
$pdf->AddPage();

// Judul Data Mobil
$pdf->SetFont('Arial','B',20);
$pdf->Cell(0,10,'LAPORAN DATA MOBIL ALIMRUGI',0,1,'C');
$pdf->Ln(5);

// Header tabel mobil
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);
$pdf->Cell(20,10,'No',1,0,'C',true);
$pdf->Cell(70,10,'id_mobil',5,0,'C',true);
$pdf->Cell(50,10,'Nama Mobil',1,0,'C',true);
$pdf->Cell(50,10,'Harga (Rp)',1,1,'C',true);

$pdf->SetFont('Arial','',10);
$no = 1;
if(mysqli_num_rows($mobilQuery) > 0) {
    while($mobil = mysqli_fetch_assoc($mobilQuery)) {
        $pdf->Cell(20,10,$no,1,0,'C');
        $pdf->Cell(70,10,$mobil['id_mobil'],1,0);
        $pdf->Cell(50,10,$mobil['nm_mobil'],1,0);
        $pdf->Cell(50,10,number_format($mobil['harga_mobil'],0,',','.'),1,1,'R');
        $no++;
    }
} else {
    $pdf->Cell(180,10,'Data mobil tidak ditemukan.',1,1,'C');
}

$pdf->AddPage();
// Judul Data Pembayaran
$pdf->SetFont('Arial','B',20);
$pdf->Cell(0,10,'LAPORAN DATA PEMBAYARAN ALIMRUGI',0,1,'C');
$pdf->Ln(5);

// Header tabel pembayaran
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);
$pdf->Cell(5,10,'No',1,0,'C',true);
$pdf->Cell(20,10,'ID Mobil',1,0,'C',true);
$pdf->Cell(40,10,'Nama Pemesan',1,0,'C',true);
$pdf->Cell(50,10,'Alamat',1,0,'C',true);
$pdf->Cell(20,10,'Status',1,0,'C',true);
$pdf->Cell(50,10,'Tanggal Pesan',1,1,'C',true);

$pdf->SetFont('Arial','',13);
$no = 1;
if(mysqli_num_rows($pembayaranQuery) > 0) {
    while($p = mysqli_fetch_assoc($pembayaranQuery)) {
        $pdf->Cell(5,10,$no,1,0,'C');
        $pdf->Cell(20,10,$p['id_mobil'],1,0,'C');
        $pdf->Cell(40,10,$p['nama_pemesan'],1,0);
        $pdf->Cell(50,10,substr($p['alamat_pemesan'],0,30).(strlen($p['alamat_pemesan'])>30?'...':''),1,0);
        $pdf->Cell(20,10,$p['status'],1,0,'C');
        $pdf->Cell(50,10,$p['tanggal_pesan'],1,1);
        $no++;
    }
} else {
    $pdf->Cell(210,10,'Data pembayaran tidak ditemukan.',1,1,'C');
}

$pdf->Output();
