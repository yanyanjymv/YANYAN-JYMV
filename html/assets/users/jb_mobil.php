<?php
	include '../../koneksi.php';
	session_start();
	if($_SESSION['roles'] == "user"){

		$id_show = $_SESSION['id_user'] ;
		$ambildata = mysqli_query($konekdb, "SELECT * from tb_bio where id_user='$id_show' ") ;
		$row = mysqli_fetch_array($ambildata);   
				
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
				if($row['nama_user'] != null){
				echo $row['nama_user'] ; 
				echo "<a href='logout.php'> [ Logout ]</a>" ;
				}else{
				echo "Untuk kenyamanan berbelanja silahkan lengkapi data terlebih dahulu <a href='settingprofil.php'> >Klik disini< </a> ";
				echo "<a href='logout.php'> [ Logout ]</a>" ;
				}
				?>
			</div>
		</div>

		

		<div id="header">
			 <div class="center">

			 <!-- Logo -->
			 	<div class="logo">
			 		<img src="../../img/alimrugi.jpg">
			 	</div>
			 <!-- Logo -->

			 	<!-- Navbar -->
			 	<div class="navbar">
			 	<ul>
			 		<li> <a href="index.php"> Home </a></li>
			 		<li> <a href="transaksi.php"> <i> Transaksi>>></i></a></li>
			 		<li style="background-color:#ff4343 ;border-radius: 5%;">
			 	</ul>

			 	</div> 

			 	<!-- Tutup Navbar -->
			 	<div class="clear"> </div>
			 </div>
		</div>

		<div class="center">

		
		
		<div id="menumobil">
		<form action="" method="post">
			<input type="submit" name="submit" class="search btn-cari" value="Cari"> 
			<input type="search" name="search" class="search" placeholder="Masukkan nama mobil">
		</form>
			<div class="judulmenu"> Menu Mobil </div>	
		</div>

		<br>

		<?php 
		$submit = @$_POST['submit']; /* BUTTON CARI */
		$search = @$_POST['search'];

		// PAGINATION

			$no = 1;
			$batas = 6;
			$hal = @$_GET['hal'];
				if(empty($hal)){
					$posisi = 0;
					$hal = 1;
				}else{
					$posisi = ($hal - 1) * $batas;
				}


		if($submit){
			if($search != ""){
				$query = mysqli_query($konekdb, "SELECT * from tb_mobil where nm_mobil like '%$search%' ORDER by id_mobil asc");				
			}else{
				$query = mysqli_query($konekdb, "SELECT * from tb_mobil ORDER by id_mobil asc LIMIT $posisi,$batas ");
			}	
		}else{
			$query = mysqli_query($konekdb, "SELECT * from tb_mobil ORDER by id_mobil asc LIMIT $posisi,$batas ");
		}


		$no = $posisi + 1;
		$cek = mysqli_num_rows($query);

		if($cek < 1){
			echo "Data yang anda cari tidak ditemukan !";
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
				<tr> <td class="tdaksi"> <a class="btn-beli" href="belicash.php?id_mobil=<?php echo $data['id_mobil'];?>"> Beli Cash </a> 
										 
				</td> </tr>

			</table>

				</div>


			</div>

				
		
		<?php } ?>
		
			
		<div class="clear"> </div>

		<!-- PAGINATION -->
		
		

		</div>

		<!-- CONTENT -->
		<div id="layanan">
			<div class="center">

				<div class="konten">

						<a href="#" class="layanan-kami">
							<div class="isi">	
								<i class="fa fa-car fa-5x"> </i>	
								<div class="text-content"> 
									Jual Beli Mobil	
								</div>
							</div>						
						</a>


				
				</div>


				<div class="clear"> </div>
			</div>
		</div>
		

		<!-- CONTENT -->

		<!-- FOOTER -->
		<div id="footer">
			<div class="isi">

			
			
			<div class="copyright">
					Copyright &copy; by kelompok 2 
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