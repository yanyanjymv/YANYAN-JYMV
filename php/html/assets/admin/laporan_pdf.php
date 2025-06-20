<?php
require('fpdf/vendor/setasign/fpdf/fpdf.php');  // Pastikan path benar ke fpdf.php
include '../../koneksi.php';
session_start();

if ($_SESSION['roles'] != 'admin') {
    echo "<script>alert('Forbidden Access'); location.href='../../index.php';</script>";
    exit();
}

$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

$where_clause_mobil = "";
$where_clause_pembayaran = "";

if (!empty($filter_date)) {
    if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $filter_date)) {
        // Filter tanggal lengkap dengan LIKE supaya cocok TIMESTAMP
        $where_clause_mobil = " WHERE tanggal LIKE '$filter_date%' ";
        $where_clause_pembayaran = " WHERE tanggal_pesan LIKE '$filter_date%' ";
    } elseif (preg_match("/^\d{4}-\d{2}$/", $filter_date)) {
        // Filter bulan-tahun
        $where_clause_mobil = " WHERE tanggal LIKE '$filter_date%' ";
        $where_clause_pembayaran = " WHERE tanggal_pesan LIKE '$filter_date%' ";
    }
}

$pdf = new FPDF();
$pdf->AddPage();

// Laporan Data Mobil
$pdf->SetFont('Arial','B',20);
$pdf->Cell(0,10,'LAPORAN DATA MOBIL ALIMRUGI',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);
$pdf->Cell(20,10,'No',1,0,'C',true);
$pdf->Cell(70,10,'id_mobil',1,0,'C',true);
$pdf->Cell(50,10,'Nama Mobil',1,0,'C',true);
$pdf->Cell(50,10,'Harga (Rp)',1,1,'C',true);

$pdf->SetFont('Arial','',10);

$mobilQuery = mysqli_query($konekdb, "SELECT * FROM tb_mobil $where_clause_mobil ORDER BY id_mobil ASC");
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
    $pdf->Cell(190,10,'Data mobil tidak ditemukan.',1,1,'C');
}

// Laporan Data Pembayaran
$pdf->AddPage();
$pdf->SetFont('Arial','B',20);
$pdf->Cell(0,10,'LAPORAN DATA PEMBAYARAN ALIMRUGI',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);
$pdf->Cell(5,10,'No',1,0,'C',true);
$pdf->Cell(20,10,'ID Mobil',1,0,'C',true);
$pdf->Cell(40,10,'Nama Pemesan',1,0,'C',true);
$pdf->Cell(50,10,'Alamat',1,0,'C',true);
$pdf->Cell(20,10,'Status',1,0,'C',true);
$pdf->Cell(50,10,'Tanggal Pesan',1,1,'C',true);

$pdf->SetFont('Arial','',13);

$pembayaranQuery = mysqli_query($konekdb, "
    SELECT p.*, b.nama_user 
    FROM tb_pesanan p 
    JOIN tb_bio b ON p.id_user = b.id_user
    $where_clause_pembayaran
    ORDER BY p.tanggal_pesan DESC
");

$no = 1;
if(mysqli_num_rows($pembayaranQuery) > 0) {
    while($p = mysqli_fetch_assoc($pembayaranQuery)) {
        $pdf->Cell(5,10,$no,1,0,'C');
        $pdf->Cell(20,10,$p['id_mobil'],1,0,'C');
        $pdf->Cell(40,10,$p['nama_pemesan'],1,0);
        $alamat = $p['alamat_pemesan'];
        $pdf->Cell(50,10,(strlen($alamat) > 30 ? substr($alamat,0,30).'...' : $alamat),1,0);
        $pdf->Cell(20,10,$p['status'],1,0,'C');
        $pdf->Cell(50,10,substr($p['tanggal_pesan'],0,10),1,1);
        $no++;
    }
} else {
    $pdf->Cell(185,10,'Data pembayaran tidak ditemukan.',1,1,'C');
}

$pdf->Output();