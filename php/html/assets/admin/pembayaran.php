<?php
include "../../koneksi.php";
session_start();

if($_SESSION['roles'] != 'admin'){
    echo "<script>alert('Forbidden Access'); location.href='../../index.php';</script>";
    exit();
}

date_default_timezone_set('Asia/Jakarta');

$batas = 5;
$hal = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$posisi = ($hal > 1) ? ($hal - 1) * $batas : 0;

$queryPesanan = "SELECT * FROM tb_pesanan ORDER BY id_pesanan DESC LIMIT $posisi,$batas";
$ambildatapembeli = mysqli_query($konekdb, $queryPesanan);

$totalDataResult = mysqli_query($konekdb, "SELECT COUNT(*) as total FROM tb_pesanan");
$totalDataRow = mysqli_fetch_assoc($totalDataResult);
$totalData = (int)$totalDataRow['total'];
$jml_hal = ceil($totalData / $batas);
?>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Alimrugi | Online shop for automobile</title>
<link rel="stylesheet" href="../../css/custom.css" />
<link rel="stylesheet" href="../../css/custom2.css" />
<style>
    body { background-color: #f2f2f2; }
    .btn-2.btn-sm { padding: 5px 10px; font-size: 14px; }
    .btn-confirm { background-color: #28a745; color: white; padding: 5px 10px; border-radius: 5px; text-decoration: none; margin-left: 5px; }
    .btn-confirm:hover { background-color: #218838; }
</style>
</head>
<body>

<div id="headeradmin">				
    <div class="centeradmin">				
        <div class="judul">
            <a href="index.php"> Home </span> </a>
        </div>

        <div class="adminbar">
            <?php 
            $id_show = $_SESSION['id_user'];
            $ambildata = mysqli_query($konekdb, "SELECT * from tb_bio where id_user='$id_show'");
            $row = mysqli_fetch_array($ambildata);   
            ?>
            <span> <?php echo htmlspecialchars($row['nama_user']); ?> </span>
            &nbsp;<a href="logout.php"> [LOGOUT] </a>
            <div class="clear"></div>
        </div>
    </div> <!-- END CLASS centeradmin -->
</div>

<div id="menuadmin">
    <div class="menutitle">
         <a> MENU </a> 
    </div>
    <div class="menubody">
        <li><a href="datamobil.php"> Data Mobil </a></li><br>
        <li><a class="active" href="#"> Pembayaran </a></li><br>
        <li><a href="penawaran_mobil.php"> Penawaran </a></li><br>	
        <li><a href="laporan.php"> Laporan </a></li><br>
        <li><a href="settingprofil.php"> Setting Profil </a></li>
    </div>
</div>

<!-- ISI CONTENT  -->
<div id="content" name="content" >
    <div class="contentheader"> Dashboard Admin</div>
    <div class="contentbody" align="center"> 
        <div class="table-responsive">
            <table class="table">
                <br>
                <tr>
                    <th> No </th>
                    <th> ID Mobil </th>
                    <th> Nama User </th>
					<th> Alamat </th>
					<th> no wa </th>
					<th> email </th>
					<th> Foto ktp </th>
                    <th> Tgl Beli </th>
                    <th> Status </th>
                    <th> Aksi </th>
                </tr>

                <?php
                $no = 1 + $posisi;
                while($data = mysqli_fetch_array($ambildatapembeli)){
                    // Ambil nama user berdasarkan id_user di pesanan
                    $id_user = $data['id_user'];
                    $ambilbiodatapembeli = mysqli_query($konekdb, "SELECT * FROM tb_pesanan WHERE id_user = $id_user");
                    $data2 = mysqli_fetch_array($ambilbiodatapembeli);

                    $buktiBayar = $data['bukti_bayar'];
                    $status = strtolower($data['status']);
                ?>
                <tr>
                    <td> <?php echo $data['id_pesanan']; ?> </td>
                    <td> <?php echo $data['id_mobil']; ?> </td>
                    <td> <?php echo htmlspecialchars($data2['nama_pemesan']); ?> </td>
					<td> <?php echo $data['alamat_pemesan']; ?> </td>
					<td> <?php echo $data['no_wa']; ?> </td>
					<td> <?php echo $data['email']; ?> </td>
					<td>
<?php 
    if (!empty($data2['foto_ktp']) && file_exists("../../uploads/ktp/" . $data2['foto_ktp'])) {
        $foto_ktp = $data2['foto_ktp'];
        echo '<a href="../../uploads/ktp/' . urlencode($foto_ktp) . '" target="_blank">';
        echo '<img src="../../uploads/ktp/' . urlencode($foto_ktp) . '" alt="Foto KTP" style="max-width:80px; max-height:50px; border:1px solid #ccc;" />';
        echo '</a>';
    } else {
        echo '<span style="color:gray;">Tidak ada foto</span>';
    }
?>
</td>

                    <td> <?php echo $data['tanggal_pesan']; ?> </td>
                    <td> <?php echo $data['status']; ?> </td>
                    <td>
                        <?php if ($buktiBayar && file_exists("../../uploads/bukti_bayar/".$buktiBayar)): ?>
                            <a href="../../uploads/bukti_bayar/<?php echo urlencode($buktiBayar); ?>" target="_blank" class="btn-2 btn-sm">Lihat Bukti</a><br><br>
                        <?php else: ?>
                            <span style="color:gray;">Tidak ada bukti</span>
                        <?php endif; ?>

                        <?php if ($status != 'lunas'): ?>
                            <a href="confirm_pembayaran.php?id=<?php echo $data['id_pesanan']; ?>" 
                               onclick="return confirm('Konfirmasi pembayaran untuk pembeli <?php echo htmlspecialchars($data2['nama_pemesan']); ?>?');"
                               class="btn-confirm">Konfirmasi</a>
                        <?php else: ?>
                            <span style="color:green; font-weight:bold; margin-left:10px;">LUNAS</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php 
                    $no++;
                } 
                ?>
            </table>
        </div>

        <!-- PAGINATION -->
        <div style="margin:5px 25px 50px 0px; float:right;"> 
            <?php
            for($i=1; $i<=$jml_hal; $i++){
                if($i != $hal){
                    echo "<a href='?hal=$i'><button class='btn-pagination1'>$i</button></a> ";
                }else{
                    echo "<button class='btn-pagination2'><b>$i</b></button> ";
                }
            }
            ?>
        </div>
    </div>
</div>
<!-- END ISI CONTENT -->

</body>
</html>
