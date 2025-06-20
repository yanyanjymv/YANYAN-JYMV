<?php
	include "../../koneksi.php";

	// Contoh: dapatkan id_user dari session atau login (ganti sesuai implementasi Anda)
	session_start();
	$id_user = $_SESSION['id_user'] ?? ''; // pastikan session sudah diset saat login

	// Ambil ID mobil baru
	$carikode = mysqli_query($konekdb, 'SELECT max(id_mobil) from tb_mobil') or die (mysqli_error($konekdb));
	$datakode = mysqli_fetch_array($carikode);
	if ($datakode) {
		$nilaikode = substr($datakode[0], 3);
		$kode = (int) $nilaikode;
		$kode = $kode + 1;
		$hasilkode = "MBL" . str_pad($kode, 3 , "0" , STR_PAD_LEFT);
	} else {
		$hasilkode = "MBL001";
	}
?>

<html>
<head>
	<title>Alimrugi | Online shop for automobile</title>
	<link rel="stylesheet" href="../../css/custom.css">
	<link rel="stylesheet" href="../../css/custom2.css">
	<style type="text/css">
		body { background-color: #00ccff; }
		td, th { color: #fff; font-family: "Web-Segoe-SemiBold"; }
		.table { margin-top: 20px; }
		.table input, .table textarea {
			width: 100%;
			padding: 8px;
			border-radius: 5px;
			border: 1px solid #ddd;
		}
		.btn-danger {
			background-color: #f44336;
			color: white;
			padding: 10px 20px;
			border: none;
			cursor: pointer;
			border-radius: 5px;
		}
		.btn-danger:hover {
			background-color: #d32f2f;
		}
	</style>
</head>

<body>
	<h1 align="center" style="margin: 50px 0; font-family: Web-Segoe-SemiBold; color:#fff;">FORM PESANAN MOBIL</h1>

	<div class="table-responsive">
		<form action="tambah_pesanan_proses.php" method="post" enctype="multipart/form-data">
			<!-- Hidden Inputs -->
			<input type="hidden" name="id_user" value="<?php echo htmlspecialchars($id_user); ?>">
			<input type="hidden" name="id_mobil" value="<?php echo htmlspecialchars($hasilkode); ?>">

			<table class="table" align="center">
				<tr>
					<th> Nama Lengkap </th>
					<td> : </td>
					<td><input type="text" name="nama_pemesan" required></td>
				</tr>
				<tr>
					<th> Alamat Lengkap </th>
					<td> : </td>
					<td><input type="text" name="alamat_pemesan" required></td>
				</tr>
				<tr>
					<th> Nomor Whatsapp </th>
					<td> : </td>
					<td><input type="tel" name="no_wa" required pattern="[0-9+]{10,15}" placeholder="+6281234567890"></td>
				</tr>
				<tr>
					<th> Email </th>
					<td> : </td>
					<td><input type="email" name="email" required></td>
				</tr>
				<tr>
					<th> Foto KTP </th>
					<td> : </td>
					<td><input type="file" name="foto_ktp" required accept="image/*"></td>
				</tr>
				<tr>
					<td><button class="btn-danger" type="submit">Submit</button></td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>
