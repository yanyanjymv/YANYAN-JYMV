<?php
include '../../koneksi.php';
session_start();

if ($_SESSION['roles'] != 'user') {
    echo "<script>alert('Forbidden Access'); document.location.href='../../index.php';</script>";
    exit();
}

$id_user = $_SESSION['id_user'];
$ambildata = mysqli_query($konekdb, "SELECT * FROM tb_bio WHERE id_user='$id_user'");

if ($ambildata && mysqli_num_rows($ambildata) > 0) {
    $row = mysqli_fetch_array($ambildata);
} else {
    $row = null;
}

// Proses upload bukti bayar
if (isset($_POST['upload_bukti'])) {
    $id_pesanan = $_POST['id_pesanan'] ?? '';
    if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['bukti_bayar']['tmp_name'];
        $fileName = $_FILES['bukti_bayar']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png'];

        if (in_array($fileExtension, $allowedExt)) {
            $uploadDir = '../../uploads/bukti_bayar/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $newFileName = uniqid('bukti_', true) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

           if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Update hanya bukti bayar, status TIDAK diubah
                $update = mysqli_prepare($konekdb, "UPDATE tb_pesanan SET bukti_bayar=? WHERE id_pesanan=? AND id_user=?");
                mysqli_stmt_bind_param($update, "sis", $newFileName, $id_pesanan, $id_user);
                mysqli_stmt_execute($update);

                if (mysqli_stmt_affected_rows($update) > 0) {
                    echo "<script>alert('Bukti bayar berhasil diupload, tunggu konfirmasi admin.'); window.location.href='pembayaran.php';</script>";
                    exit;
                } else {
                    $error = "Gagal mengupdate data pesanan.";
                }
            } else {
                $error = "Gagal memindahkan file bukti bayar.";
            }
        } else {
            $error = "Format file harus JPG, JPEG, atau PNG.";
        }
    } else {
        $error = "File bukti bayar belum diupload atau terjadi error.";
    }
}

// Query data pesanan user
$pesananQuery = mysqli_query($konekdb, "SELECT * FROM tb_pesanan WHERE id_user='$id_user' ORDER BY tanggal_pesan DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Pembayaran - Alimrugi</title>
<link rel="stylesheet" href="../../css/custom.css" />
<link rel="stylesheet" href="../../css/custom2.css" />
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.min.css" />
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

.table-pesanan {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
    font-family: "Web-Segoe-SemiBold";
    color: #333;
}
.table-pesanan th, .table-pesanan td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: center;
}
.table-pesanan th {
    background-color: #00ccff;
    color: #fff;
}
.btn-upload {
    background-color: #4CAF50;
    color: white;
    padding: 7px 14px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.btn-upload:hover {
    background-color: #45a049;
}
.error-message {
    color: red;
    text-align: center;
    margin-top: 10px;
}
</style>
</head>
<body>
    <div id="header-user">
        <div style="float: left;font-family:'Web-Segoe-Light';margin:15px 0 0 15px;">
            <a href="settingprofil.php" style="color:#fff; text-decoration:none;">Setting Profile</a>
        </div>
        <div class="header-profile">
            <?php if ($row && isset($row['nama_user'])): ?>
                <?php echo htmlspecialchars($row['nama_user']); ?> | <a href="logout.php">Logout</a>
            <?php else: ?>
                Silakan lengkapi profil Anda di <a href="settingprofil.php">sini</a> | <a href="logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>

    <div id="header">
        <div class="center">
            <div class="logo">
                <a href="index.php"><img src="../../img/alimrugi.jpg" alt="Logo Alimrugi" /></a>
            </div>
            <div class="navbar">
                <ul>
                    <li><a href="pesanan.php"><i>Pesanan</i></a></li>
                    <li><a href="pembayaran.php"><i>Pembayaran</i></a></li>
                    <li><a href="penawaran_mobil.php"><i>Penawaran Mobil</i></a></li>
                </ul>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div id="slider">
        <div class="center">
            <h1>Menu Pembayaran<h1>
        <h2>Ketentuan<h2>
        <h2>Anda Harus Membooking Dengan Harga Rp.500.000 untuk tanda memesan dengan waktu 7 hari dan harus membayar dp 30% dari harga mobil<h2>
        <h2>setelah anda membayar dp 30% dari harga mobil kami memberi waktu 14 hari untuk melakukan pelunasan<h2> 
        <h2>setelah anda membayar dp 30% kami akan mengurus stnk dan bpkb<h2>

        <h2>jangan lupa sertakan foto ktp juga ya<h2>
        <h1>Terima Kasih<h1>
        </div>
    </div>

    <div style="width:95%; margin: 20px auto; font-family:'Web-Segoe-SemiBold';">
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (mysqli_num_rows($pesananQuery) == 0): ?>
            <p style="text-align:center;">Belum ada pembayaran</p>
        <?php else: ?>
            <table class="table-pesanan">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pemesan</th>
                        <th>Alamat</th>
                        <th>No WA</th>
                        <th>Email</th>
                        <th>Bukti Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                while ($pesanan = mysqli_fetch_assoc($pesananQuery)):
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($pesanan['nama_pemesan']); ?></td>
                        <td><?php echo htmlspecialchars($pesanan['alamat_pemesan']); ?></td>
                        <td><?php echo htmlspecialchars($pesanan['no_wa']); ?></td>
                        <td><?php echo htmlspecialchars($pesanan['email']); ?></td>
                        <td>
                            <?php if ($pesanan['bukti_bayar']): ?>
                                <a href="../../uploads/bukti_bayar/<?php echo htmlspecialchars($pesanan['bukti_bayar']); ?>" target="_blank">Lihat Bukti</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $statusNow = strtolower($pesanan['status']);
                            $allowedUpload = ['pending', 'booking', 'dp 30 persen', 'dp'];
                            if (in_array($statusNow, $allowedUpload)): ?>
                                <form method="post" enctype="multipart/form-data" style="margin:0;">
                                    <input type="hidden" name="id_pesanan" value="<?php echo $pesanan['id_pesanan']; ?>" />
                                    <input type="file" name="bukti_bayar" accept="image/*" required />
                                    <button type="submit" name="upload_bukti" class="btn-upload">Upload Bukti</button>	
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div id="footer">
        <div class="isi">
            <div class="copyright">
                Copyright &copy; by kelompok 2
            </div>
        </div>
    </div>
</body>
</html>
