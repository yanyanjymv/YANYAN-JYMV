<?php
	include "../../koneksi.php";
	session_start();
	if($_SESSION['roles'] == 'admin'){

?>
<html>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial scale=0.1" />
	<head>
		<title> Alimrugi | Online shop for automobile </title>

		<link rel="stylesheet" href="../../css/custom.css">
		<link rel="stylesheet" href="../../css/custom2.css">
	<style type="text/css">
		body{
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
				$id_show = $_SESSION['id_user'] ;
				$ambildata = mysqli_query($konekdb, "SELECT * from tb_bio where id_user='$id_show' ") ;
				$row = mysqli_fetch_array($ambildata);   
				?>
				<!-- PEMANGGILAN DATA -->
				<span> <?php echo $row['nama_user']; ?> </span>

				&nbsp;<a href="logout.php"> [LOGOUT] </a>
				

				<div class="clear"> </div>
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
							<li> <a class="active"  href="#"> Penawaran </a> </li><br>
							<li> <a href="laporan.php"> Laporan </a> </li><br>
							<li> <a href="settingprofil.php"> Setting Profil </a> </li>
						
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
						<th> nama </th>
						<th>  Nama Mobil </th>
						<th> Informasi </th>
						<th> Harga </th>
						<th> Gambar </th>
					</tr>
				
				<!-- SCRIPT PHP -->
				
				<?php 
					// PAGINATION
					$no = 1;
					$batas = 5;
					$hal = @$_GET['hal'];
					if(empty($hal)){
						$posisi = 0;
						$hal = 1;
					}else{
						$posisi = ($hal - 1) * $batas;
					}

					$ambildatamobil = mysqli_query($konekdb, "
    SELECT p.*, b.nama_user 
    FROM tb_penawaran p
    JOIN tb_bio b ON p.id_user = b.id_user
    LIMIT $posisi, $batas
");

					$no = $posisi + 1;
					$cek = mysqli_num_rows($ambildatamobil);
					if($cek < 1){
						echo "DATA TIDAK DITEMUKAN";
					}else{
					while($data = mysqli_fetch_array($ambildatamobil)){   	
				?>	
				<!-- SCRIPT PHP -->
					<tr>
						<td> <?php echo $no; ?> </td>
						<td> <?php echo $data['nama_user'];    ?> </td>
						<td> <?php echo $data['nm_mobil'];    ?> </td>
						<td> <?php echo $data['informasi'];  ?> </td>	
						<td> Rp.<?php echo number_format($data['harga_mobil'],0,",",","); ?> </td>
						<td> <img width="100px" height="75px" src="../../img/mobil/<?php echo $data['gambar']; ?> "></td>	
					</tr>
				<?php $no++;} } ?> <!-- PHP TUTUP -->

				
				</table>
				</div>
				<div style="margin:5px 0px 10px 25px; float:left;"> 
				<?php
				$jml = mysqli_num_rows(mysqli_query($konekdb, "SELECT * from tb_penawaran"));
				
				?>
				</div>
				<div style="margin:5px 25px 50px 0px; float:right;"> 
				<?php
				$jml_hal = ceil($jml / $batas);
				for($i=1; $i<=$jml_hal;$i++){
					if($i != $hal){
						echo "<a href='datamobil.php?hal=$i'><button class='btn-pagination1'>$i</button> </a>";
					}else{
						echo "<button class='btn-pagination2'><b> $i</b> </button>";
					}
				}
				?>

				</div>

				</div>

			</div>

			<!-- ISI CONTENT  -->
		</body>

</html>

<?php

}else {
	echo "<script> alert('Forbidden Access');
		  location.href='../../index.php';
		  </script>";
		
		exit();
}
?>