<?php
session_start();
include "../../koneksi.php";

$id_user = $_SESSION['id_user'] ?? '';

// Ambil id_mobil dari GET, pastikan diatur dari URL
$id_mobil = $_GET['id_mobil'] ?? '';

if (!$id_mobil) {
    die("ID mobil tidak ditemukan. Silakan pilih mobil terlebih dahulu.");
}

// Cek apakah id_mobil valid
$stmt = $konekdb->prepare("SELECT nm_mobil FROM tb_mobil WHERE id_mobil = ?");
$stmt->bind_param("s", $id_mobil);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Mobil dengan ID tersebut tidak ditemukan.");
}
$mobil = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Alimrugi | Online shop for automobile</title>
	<link rel="stylesheet" href="../../css/custom.css">
	<link rel="stylesheet" href="../../css/custom2.css">
	<style type="text/css">
		body { background-color: #00ccff; }
		td, th { color: #fff; font-family: "Web-Segoe-SemiBold"; }
		.table { margin-top: 20px; max-width: 600px; margin-left: auto; margin-right: auto; background-color: rgba(0,0,0,0.3); border-radius: 8px; padding: 20px; }
		.table input, .table textarea {
			width: 100%;
			padding: 8px;
			border-radius: 5px;
			border: 1px solid #ddd;
			box-sizing: border-box;
		}
		.btn-danger {
			background-color: #f44336;
			color: white;
			padding: 10px 20px;
			border: none;
			cursor: pointer;
			border-radius: 5px;
			margin-top: 15px;
		}
		.btn-danger:hover {
			background-color: #d32f2f;
		}
		th {
			text-align: left;
			padding-right: 10px;
			width: 140px;
		}
	</style>
</head>

<body>
	<h1 align="center" style="margin: 50px 0; font-family: Web-Segoe-SemiBold; color:#fff;">
        FORM PESANAN MOBIL: <?= htmlspecialchars($mobil['nm_mobil']) ?>
    </h1>

	<div class="table-responsive">
		<form action="tambah_pesanan_proses.php" method="post" enctype="multipart/form-data">
			<!-- Hidden Inputs -->
			<input type="hidden" name="id_user" value="<?= htmlspecialchars($id_user) ?>">
   			<input type="hidden" name="id_mobil" value="<?= htmlspecialchars($id_mobil) ?>">

			<table class="table" align="center">
				<tr>
					<th>Nama Lengkap</th>
					<td><input type="text" name="nama_pemesan" required></td>
				</tr>
				<tr>
					<th>Alamat Lengkap</th>
					<td><input type="text" name="alamat_pemesan" required></td>
				</tr>
				<tr>
					<th>Nomor Whatsapp</th>
					<td><input type="tel" name="no_wa" required pattern="[0-9+]{10,15}" placeholder="+6281234567890"></td>
				</tr>
				<tr>
					<th>Email</th>
					<td><input type="email" name="email" required></td>
				</tr>
				<tr>
					<th>Foto KTP</th>
					<td><input type="file" name="foto_ktp" required accept="image/*"></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<button class="btn-danger" type="submit">Submit</button>
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>
