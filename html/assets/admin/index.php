<?php
include "../../koneksi.php";
session_start();
if ($_SESSION['roles'] == 'admin') {
?>
<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial scale=0.1" />
<head>
    <title> Alimrugi | Online shop for automobile </title>
    <link rel="stylesheet" href="../../css/custom.css">
    <style type="text/css">
        body {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>        
    <div id="headeradmin">                
        <div class="centeradmin">
            <div class="judul">
                <a href="index.php"> Home </span> </a>
            </div>

            <!-- ADMINBAR -->
            <div class="adminbar">

            <!-- PENGAMBILAN DATA -->
            <?php 
            $id_show = $_SESSION['id_user'] ?? null;
            $row = null; // Inisialisasi variabel sebelum query

            // Pastikan ID user tidak kosong sebelum query
            if ($id_show) {
                $ambildata = mysqli_query($konekdb, "SELECT * FROM tb_bio WHERE id_user='$id_show'");

                // Cek apakah query berhasil dan data ditemukan
                if ($ambildata && mysqli_num_rows($ambildata) > 0) {
                    $row = mysqli_fetch_array($ambildata);
                    $nama_user = $row['nama_user'] ?? "Nama tidak tersedia";
                } else {
                    $nama_user = "Nama tidak tersedia";
                }
            } else {
                $nama_user = "Nama tidak tersedia";
            }
            ?>
            <!-- PEMANGGILAN DATA -->
            <span> <?php echo $nama_user; ?> </span>

            &nbsp;<a href="logout.php"> [LOGOUT] </a>
            
            <div class="clear"></div>
            </div>
            <!-- ADMINBAR -->

        </div> <!-- END CLASS centeradmin -->
    </div>

    <div id="menuadmin">
        <div class="menutitle">
             <a> MENU </a> 
        </div>
            <div class="menubody">
                            <li> <a href="datamobil.php"> Data Mobil </a> </li> <br>
							<li> <a href="pembayaran.php"> Pembayaran </a> </li><br>
							<li> <a href="penawaran_mobil.php"> Penawaran </a> </li><br>
							<li> <a href="laporan.php"> Laporan </a> </li><br>
							<li> <a href="settingprofil.php"> Setting Profil </a> </li>
            </div>
    </div>

    <!-- ISI CONTENT  -->
    <div id="content" name="content" >
        <div class="contentheader"> Dashboard Admin</div>

        <div class="contentbody" align="center"> 
        <?php
        if (!$row || empty($row['nama_user'])) {
            echo "
            <h2 align='center' style='padding-top:125px;'> 
            <a href='settingprofil.php' style='text-decoration:none;'> SETTING PROFIL TERLEBIH DAHULU <br>[KLIK DISINI]</a> 
            </h2>
            ";
        } else { 
            ?>
        <table class="table">
            <tr>
                <th> ID User </th>
                <td>  : </td>
                <td> <?php echo $row['id_user'] ?? '-'; ?> </td>    
            </tr>    
            <tr>
                <th> Nama Lengkap</th>
                <td>  : </td>
                <td> <?php echo $row['nama_user'] ?? '-'; ?> </td>    
            </tr>
            <tr>
                <th> Jenis Kelamin </th>
                <td>  : </td>
                <td> <?php echo ($row['jk_user'] ?? '') == 'L' ? "Laki-laki" : "Perempuan"; ?> </td>    
            </tr>
            <tr>
                <th> Alamat Email</th>
                <td>  : </td>
                <td> <?php echo $row['alamat_email'] ?? '-'; ?> </td>    
            </tr>
        </table>
        <?php } ?>
        </div>
    </div>

    <!-- ISI CONTENT  -->
</body>
</html>

<?php
} else {
    echo "<script> alert('Forbidden Access');
          location.href='../../index.php';
          </script>";
    exit();
}
?>
