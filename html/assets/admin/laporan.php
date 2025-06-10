<?php
include "../../koneksi.php";
session_start();
if($_SESSION['roles'] != 'admin'){
    echo "<script>alert('Forbidden Access'); location.href='../../index.php';</script>";
    exit();
}

$id_show = $_SESSION['id_user'];
$ambildata = mysqli_query($konekdb, "SELECT * FROM tb_bio WHERE id_user='$id_show'");
$row = mysqli_fetch_array($ambildata);

// Pagination untuk data mobil
$batas = 5;
$hal = @$_GET['hal'];
if(empty($hal)){
    $posisi = 0;
    $hal = 1;
}else{
    $posisi = ($hal - 1) * $batas;
}

$mobilQuery = mysqli_query($konekdb, "SELECT * FROM tb_mobil LIMIT $posisi, $batas");
$jml_mobil = mysqli_num_rows(mysqli_query($konekdb, "SELECT * FROM tb_mobil"));
$jml_hal_mobil = ceil($jml_mobil / $batas);

// Ambil semua data pembayaran tanpa pagination (untuk laporan lengkap)
$pembayaranQuery = mysqli_query($konekdb, "
    SELECT p.*, b.nama_user 
    FROM tb_pesanan p 
    JOIN tb_bio b ON p.id_user = b.id_user
    ORDER BY p.tanggal_pesan DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Alimrugi | Laporan Data Mobil & Pembayaran</title>

<link rel="stylesheet" href="../../css/custom.css">
<link rel="stylesheet" href="../../css/custom2.css">
<style type="text/css">
	body{
		background-color: #f2f2f2;
	}
	/* Style khusus untuk tabel Data Mobil dan Data Pembayaran */
	table.laporan-table {
		width: 95%;
		margin: 20px auto;
		border-collapse: collapse;
		font-family: "Web-Segoe-SemiBold";
		box-shadow: 0 0 8px rgba(0,0,0,0.1);
		background-color: #fff;
	}
	table.laporan-table th, table.laporan-table td {
		border: 1px solid #ddd;
		padding: 12px 15px;
		text-align: center;
	}
	table.laporan-table th {
		background: linear-gradient(90deg, #00ccff, #0066cc);
		color: #fff;
		text-transform: uppercase;
		letter-spacing: 1px;
		font-size: 14px;
	}
	table.laporan-table tbody tr:nth-child(even) {
		background-color: #f9f9f9;
	}
	table.laporan-table tbody tr:hover {
		background-color: #d1ecf1;
		transition: background-color 0.3s ease;
	}
	h2.section-title {
		text-align: center;
		color: #0066cc;
		margin-top: 40px;
		font-weight: 700;
		font-family: "Web-Segoe-SemiBold";
	}
	a.btn-print-pdf {
		display: inline-block;
		margin: 15px auto 30px auto;
		padding: 10px 25px;
		background-color: #007bff;
		color: white;
		border-radius: 5px;
		text-decoration: none;
		font-weight: 600;
		font-family: "Web-Segoe-SemiBold";
		transition: background-color 0.3s ease;
	}
	a.btn-print-pdf:hover {
		background-color: #0056b3;
	}
	.pagination-wrapper {
		width: 95%;
		margin: 10px auto 30px auto;
		text-align: right;
	}
	button.btn-pagination1, button.btn-pagination2 {
		padding: 6px 12px;
		margin-left: 5px;
		border: none;
		border-radius: 4px;
		cursor: pointer;
		font-weight: 600;
	}
	button.btn-pagination1 {
		background-color: #2196F3;
		color: white;
	}
	button.btn-pagination2 {
		background-color: #ff4343;
		color: white;
	}
</style>
</head>
<body>

<div id="headeradmin">				
    <div class="centeradmin">
        <div class="judul">
            <a href="#"> Admin <span> CPanel </span> </a>
        </div>
        <div class="adminbar">
            <span><?php echo htmlspecialchars($row['nama_user']); ?></span>
            &nbsp;<a href="logout.php">[LOGOUT]</a>
        </div>
        <div style="clear:both;"></div>
    </div>
</div>

<div id="menuadmin">
    <div class="menutitle"><a href="#"> CPANEL </a></div>
    <div class="menubody">
        <li><a href="datamobil.php"> Data Mobil </a></li><br>
        <li><a href="pembayaran.php"> Pembayaran </a></li><br>
        <li><a href="penawaran_mobil.php"> Penawaran </a></li><br>
        <li><a class="active" href="laporan.php"> Laporan </a></li><br>
        <li><a href="settingprofil.php"> Setting </a></li>
    </div>
</div>

<div id="content" name="content">
    <div class="contentheader"> Laporan Data Mobil & Pembayaran </div>

    <div class="contentbody">

        <h2 class="section-title">Data Mobil</h2>
        <a href="laporan_pdf.php?type=mobil" target="_blank" class="btn-print-pdf">Cetak PDF</a>
        <div class="table-responsive">
            <table class="laporan-table">
                <tr>
                    <th>No</th>
                    <th>Nama Mobil</th>
                    <th>Spesifikasi Mobil</th>
                    <th>Harga</th>
                </tr>
                <?php
                $no = $posisi + 1;
                if(mysqli_num_rows($mobilQuery) == 0){
                    echo "<tr><td colspan='4'>DATA TIDAK DITEMUKAN</td></tr>";
                } else {
                    while($mobil = mysqli_fetch_array($mobilQuery)){
                        echo "<tr>
                            <td>{$no}</td>
                            <td>".htmlspecialchars($mobil['nm_mobil'])."</td>
                            <td>".htmlspecialchars($mobil['spek_mobil'])."</td>
                            <td>Rp ".number_format($mobil['harga_mobil'],0,",",".")."</td>
                        </tr>";
                        $no++;
                    }
                }
                ?>
            </table>
        </div>
        <div class="pagination-wrapper">
            <?php
            for($i=1; $i<=$jml_hal_mobil; $i++){
                if($i != $hal){
                    echo "<a href='laporan.php?hal=$i'><button class='btn-pagination1'>$i</button></a> ";
                } else {
                    echo "<button class='btn-pagination2'>$i</button> ";
                }
            }
            ?>
        </div>

        <h2 class="section-title">Data Pembayaran</h2>
        <div class="table-responsive" style="overflow-x:auto;">
            <table class="laporan-table">
                <tr>
                    <th>No</th>
                    <th>Nama User</th>
                    <th>ID Mobil</th>
                    <th>Nama Pemesan</th>
                    <th>Alamat</th>
                    <th>No WA</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Tanggal Pesan</th>
                </tr>
                <?php
                $no = 1;
                if(mysqli_num_rows($pembayaranQuery) == 0){
                    echo "<tr><td colspan='9'>DATA TIDAK DITEMUKAN</td></tr>";
                } else {
                    while($p = mysqli_fetch_array($pembayaranQuery)){
                        echo "<tr>
                            <td>{$no}</td>
                            <td>".htmlspecialchars($p['nama_user'])."</td>
                            <td>".htmlspecialchars($p['id_mobil'])."</td>
                            <td>".htmlspecialchars($p['nama_pemesan'])."</td>
                            <td>".htmlspecialchars($p['alamat_pemesan'])."</td>
                            <td>".htmlspecialchars($p['no_wa'])."</td>
                            <td>".htmlspecialchars($p['email'])."</td>
                            <td>".htmlspecialchars($p['status'])."</td>
                            <td>".htmlspecialchars($p['tanggal_pesan'])."</td>
                        </tr>";
                        $no++;
                    }
                }
                ?>
            </table>
        </div>
    </div>
</div>

</body>
</html>
