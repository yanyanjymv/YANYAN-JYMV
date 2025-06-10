<?php
include '../../koneksi.php';
session_start();

if ($_SESSION['roles'] != 'user') {
    echo "<script>alert('Forbidden Access'); document.location.href='../../index.php';</script>";
    exit();
}

$id_user = $_SESSION['id_user'];
$ambildata = mysqli_query($konekdb, "SELECT * FROM tb_bio WHERE id_user='$id_user'");
$row = ($ambildata && mysqli_num_rows($ambildata) > 0) ? mysqli_fetch_array($ambildata) : null;

$pesananQuery = mysqli_query($konekdb, "SELECT * FROM tb_pesanan WHERE id_user='$id_user' ORDER BY tanggal_pesan DESC");

date_default_timezone_set('Asia/Jakarta');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Pesanan Saya - Alimrugi</title>
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
    vertical-align: middle;
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
img.mobil-thumb {
    max-width: 100px;
    max-height: 60px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
a {
    color: #00ccff;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
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
            <?= htmlspecialchars($row['nama_user']); ?> | <a href="logout.php">Logout</a>
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
        <h1>Menu Pesanan<h1>
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
        <p class="error-message"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (mysqli_num_rows($pesananQuery) == 0): ?>
        <p style="text-align:center;">Belum ada pesanan.</p>
    <?php else: ?>
        <table class="table-pesanan">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pemesan</th>
                    <th>Nama Mobil</th>
                    <th>Alamat</th>
                    <th>No WA</th>
                    <th>Email</th>
                    <th>Harga Mobil</th>
                    <th>Yang Harus Dibayar</th>
                    <th>Status</th>
                    <th>Bukti Bayar</th>
                    <th>Sisa Waktu</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $no = 1;
            while ($pesanan = mysqli_fetch_assoc($pesananQuery)):
                $id_mobil = (int)$pesanan['id_mobil'];
                $mobilQuery = mysqli_query($konekdb, "SELECT nm_mobil, harga_mobil, gambar FROM tb_mobil WHERE id_mobil = $id_mobil");
                $mobil = mysqli_fetch_assoc($mobilQuery);

                $status = strtolower($pesanan['status']);
                $tanggalPesan = $pesanan['tanggal_pesan'];
                $now = new DateTime();
                $tanggalPesanObj = new DateTime($tanggalPesan);

                // Sisa waktu hanya untuk status booking, pending
                if ($status == 'booking') {
                $jatuhTempo = clone $tanggalPesanObj;
                $jatuhTempo->modify('+7 days');
                } elseif ($status == 'dp 30 persen' || $status == 'dp 30 persen' || $status == 'dp 30 persen') {
                $jatuhTempo = clone $tanggalPesanObj;
                $jatuhTempo->modify('+14 days');
                } else {
                $jatuhTempo = null;
                }


                if ($jatuhTempo) {
                    $interval = $now->diff($jatuhTempo);
                    $daysLeft = (int)$interval->format('%r%a');
                } else {
                    $daysLeft = null;
                }

                $harga = (float) $mobil['harga_mobil'];
                $dp30 = $harga * 0.3;
                $sisaPembayaran = $harga * 0.7; // 70% dari harga mobil

                // Tentukan nilai yang harus dibayar sesuai status
                if ($status == 'pending') {
                    $harusDibayar = 500000;
                } elseif ($status == 'booking') {
                    $harusDibayar = $dp30;
                } elseif ($status == 'dp 30 persen' || $status == 'dp30 persen' || $status == 'dp 30 persen') {
                    $harusDibayar = $sisaPembayaran;  // 
                } elseif ($status == 'lunas') {
                    $harusDibayar = 0;
                } else {
                    $harusDibayar = null;
                }
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($pesanan['nama_pemesan']); ?></td>
                <td>
                    <?= htmlspecialchars($mobil['nm_mobil'] ?? '-'); ?>
                    <?php if (!empty($mobil['gambar']) && file_exists('../../uploads/mobil/' . $mobil['gambar'])): ?>
                        <br>
                        <img src="../../uploads/mobil/<?= htmlspecialchars($mobil['gambar']); ?>" alt="Foto Mobil" style="max-width:100px; max-height:60px; margin-top:5px; border:1px solid #ccc; border-radius:5px;">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($pesanan['alamat_pemesan']); ?></td>
                <td><?= htmlspecialchars($pesanan['no_wa']); ?></td>
                <td><?= htmlspecialchars($pesanan['email']); ?></td>
                <td>Rp <?= number_format($harga, 0, ',', '.'); ?></td>
                <td>
                    <?php
                    if ($status == 'lunas') {
                        echo "<span style='color:green;font-weight:bold;'>LUNAS</span>";
                    } elseif ($harusDibayar !== null) {
                        echo "Rp " . number_format($harusDibayar, 0, ',', '.');
                    } else {
                        echo "-";
                    }
                    ?>
                </td>
                <td><?= htmlspecialchars($pesanan['status']); ?></td>
                <td>
                    <?php if ($pesanan['bukti_bayar']): ?>
                        <a href="../../uploads/bukti_bayar/<?= htmlspecialchars($pesanan['bukti_bayar']); ?>" target="_blank">Lihat Bukti</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                    if ($status == 'lunas') {
                    echo "<span style='color:green;font-weight:bold;'>LUNAS</span>";
                    } elseif ($status == 'pending') {
                    echo "-";
                    } elseif ($daysLeft !== null) {
                    if ($daysLeft >= 0) {
                    echo "Sisa waktu: <strong>$daysLeft hari</strong>";
                    } else {
                    echo "<span style='color:red;'>Waktu habis!</span>";
                     }
                    } else {
                    echo "-";
                    }
                    ?>
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
