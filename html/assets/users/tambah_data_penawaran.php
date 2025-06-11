<?php
	include "../../koneksi.php";
?>
<html>
	<head>
		<title> Alimrugi | Online shop for automobile </title>

		<link rel="stylesheet" href="../../css/custom.css">
		<link rel="stylesheet" href="../../css/custom2.css">
		<style type="text/css">
			body{
				background-color: #00ccff;
			}
			td,th{
				color: #fff;
				font-family: "Web-Segoe-SemiBold";
			}
		</style>
	</head>

	<body>
	<h1 align="center" style="margin-top: 50px;margin-bottom: 50px;font-family: Web-Segoe-SemiBold ;color:#fff;"> FORM TAMBAH MOBIL </h1>
		<div class="table-responsive">
		<?php
			$carikode = mysqli_query($konekdb, 'SELECT max(id_mobil) from tb_mobil') or die (mysql_error());
			$datakode = mysqli_fetch_array($carikode);
			if($datakode){
				$nilaikode = substr($datakode[0], 3);
				$kode = (int) $nilaikode;
				$kode = $kode + 1;
				$hasilkode = "MBL".str_pad($kode, 3 , "0" , STR_PAD_LEFT);
			}else{
				$hasilkode = "MBL001";
			}
		?>
			<form action="tambah_data_penawaran_proses.php" method="post" enctype="multipart/form-data">
			<table class="table" align="center">
				<tr>
				<tr style="display:none;">
				        <td colspan="3">
						<td> <input type="hidden" name="id_mobil" value="<?php echo $hasilkode; ?>" readonly required > </td>
				</tr>
				<tr>
						<th> Nama Mobil </th>
						<td>  : </td>
						<td> <input type="text" name="nm_mobil" required> </td>	
				</tr>	
				<tr>
						<th> Informasi </th>
						<td>  : </td>
						<td> <TEXTAREA type="text" name="informasi" style="width: 100% ;height: auto;" required> </TEXTAREA>  </td>	
				</tr>	
				<tr>
						<th> Harga </th>
						<td>  : </td>
						<td> <input type="text" name="harga_mobil" required>  </td>	
				</tr>	
				<tr>
						<th> Gambar </th>
						<td>  : </td>
						<td> <input type="file" name="gambar" >  </td>	
				</tr>
                        <tr style="display:none;">
				        <td colspan="3">
					    <input type="hidden" name="id_user" value="<?php echo $id_user; ?>">
				</td>
			</tr>		
				<tr>
						<td> </td>
						<td> </td>
						<td> <button class="btn-danger btn-sm" type="submit"> Submit </button> </td>	
				</tr>		
			</table>
			</form>
		</div>

	</body>


</html>