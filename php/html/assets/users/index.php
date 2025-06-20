	<?php
		include '../../koneksi.php';
		session_start();
		if($_SESSION['roles'] == "user"){

			$id_show = $_SESSION['id_user'] ;
		$ambildata = mysqli_query($konekdb, "SELECT * from tb_bio where id_user='$id_show' ") ;
		
		// Periksa apakah query berhasil dan ada data
		if ($ambildata && mysqli_num_rows($ambildata) > 0) {
			$row = mysqli_fetch_array($ambildata);
		} else {
			$row = null; // Set null jika tidak ada data
		}
				
?>

<html>
	<head> 
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial scale=0.1" />
		<title> Alimrugi | Online shop for automobile </title>

	
	<link rel="stylesheet" href="../../css/custom.css">
	<link rel="stylesheet" href="../../css/custom2.css">
	<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.min.css">
	

	<style type="text/css">
		#header-user{
			background-color: #262626;
			height: 50px;
			color: #fff;
			box-shadow: 0px 5px 5px #cccccc;
		}
		.header-profile{
			float: right;
			margin-right: 25px;
			margin-top: 15px;
			font-family: "Web-Segoe-Light";
		}
		.header-profile a{
			color: #1a1aff;
			text-decoration: none;
		}

	</style>
	</head>

	<body>
		<div id="header-user">
			<div style="float: left;font-family:'Web-Segoe-Light';margin:15px 0px 0px 15px;"> 
			<a href="settingprofil.php" style="text-decoration: none;color: #fff;"> Setting Profile </a> </div>
			
			<div class="header-profile">	

			<?php
            if ($row && isset($row['nama_user'])) {
                echo htmlspecialchars($row['nama_user']); 
                echo " <a href='logout.php'>[ Logout ]</a>";
            } else {
                echo "Untuk kenyamanan berbelanja, silakan lengkapi data terlebih dahulu. 
                      <a href='settingprofil.php'> >Klik di sini< </a> ";
                echo " <a href='logout.php'>[ Logout ]</a>";
            }
        ?>
			</div>
		</div>


		<div id="header">
			<div class="center">

			<!-- Logo -->
				<div class="logo">
  					<a href="index.php">
    					<img src="../../img/alimrugi.jpg" alt="Logo Alimrugi">
  					</a>
				</div>

			<!-- Logo -->

				<!-- Navbar -->
				<div class="navbar">
				<ul>
					<li> <a href="pesanan.php"> <i> Pesanan</i></a></li>
					<li> <a href="pembayaran.php"> <i> Pembayaran</i></a></li>
					<li> <a href="penawaran_mobil.php"> <i> Penawaran Mobil</i></a></li>
					<li style="background-color:#ff4343 ;border-radius: 5%;">
				</ul>

				</div> 

				<!-- Tutup Navbar -->
				<div class="clear"> </div>
			</div>
		</div>

		<!-- SLIDER -->

		<div id="slider">
			<div class="center">
				<h1> Selamat Datang di AlimRugi </h1>
				<h2> Jual Beli Mobil Terbaik Secara Online</h2>
				<h2> di Alim Rugi anda bisa membeli mobil atau pun menawarkan mobil anda kepada kami<h2>
				<h3>note : Klik menu Penawaran Mobil Untuk Menawarkan Kepada Kami<h3>

				<br>
				
			</div>

		</div>

				

		<!-- CONTENT -->
		<div id="menumobil">
	<form action="" method="post">
		<input type="submit" name="submit" class="search btn-cari" value="Cari"> 
		<input type="search" name="search" class="search" placeholder="Masukkan nama mobil">
	</form>
		<div class="judulmenu"> Menu Mobil </div>	
	</div>

	<br>

	<?php 
        $submit = @$_POST['submit'];
        $search = @$_POST['search'];

        if($submit && $search != ""){
            $query = mysqli_query($konekdb, "SELECT * FROM tb_mobil WHERE nm_mobil LIKE '%$search%' ORDER BY id_mobil ASC");
        } else {
            $query = mysqli_query($konekdb, "SELECT * FROM tb_mobil ORDER BY id_mobil ASC"); // Menampilkan semua data
        }

        $cek = mysqli_num_rows($query);
        if($cek < 1){
            echo "Data yang anda cari tidak ditemukan!";
        }

        while($data = mysqli_fetch_array($query)){
    ?>
		
		
		<div class="kontenspan">
			<div class="table-responsive">
		
		<table class="tableisi">

			<tr> <th> <?php echo $data['nm_mobil']; ?> </th> </tr>	
			<tr> <td> <img class="spangambar" src="../../img/mobil/<?php echo $data['gambar'] ; ?> " alt="Foto" > 
			</td> </tr>
			<tr> <td class="tdspek"> <?php echo $data['spek_mobil']; ?> </td> </tr>
			<tr> <td class="tdharga"> RP.<?php echo number_format($data['harga_mobil'],0,",",".") ;?>  </td> </tr>
			<tr> <td class="tdaksi"> <a class	="btn-beli" href="form_pesanan.php?id_mobil=<?= urlencode($data['id_mobil']) ?>">Pesan</a> 
			
									 
			</td> </tr>

		</table>

			</div>


		</div>

			
	
	<?php } ?>
	
		
	<div class="clear"> </div>

		
			

		<!-- CONTENT -->

		<!-- FOOTER -->
		<div id="footer">
			<div class="isi">
			
			<div class="copyright">
					Copyright &copy;  by kelompok 2 
			</div>

			</div> <!-- PENUTUP ISI-->
		</div>
		<!-- FOOTER -->

	</body>
	<!-- <script type="text/javascript" src="../../js/jquery.min.js"> </script>
	<script type="text/javascript" src="../../js/customjs.js"> </script> -->
	

</html>

<?php
	}else{
		echo "<script> alert('Forbidden Access') ;
		document.location.href='../../index.php' </script> " ;
		exit();
	}

?>