<?php  
	include "../../koneksi.php";
	session_start();

	// Pastikan pengguna sudah login
	if (!isset($_SESSION['roles']) || $_SESSION['roles'] != 'user') {
		echo "<script>alert('Forbidden Access'); location.href='../../index.php';</script>";
		exit();
	}

	$id_show = $_SESSION['id_user'];

	// Ambil data user dari tb_admin
	$cek_admin = mysqli_query($konekdb, "SELECT * FROM tb_admin WHERE id_user='$id_show'");
	$admin_data = mysqli_fetch_assoc($cek_admin);

	if (!$admin_data) {
		echo "<script>alert('ID User tidak terdaftar sebagai admin!'); location.href='../../index.php';</script>";
		exit();
	}

	// Cek apakah ID user ada di tb_bio
	$ambildata = mysqli_query($konekdb, "SELECT * FROM tb_bio WHERE id_user='$id_show'");
	$row = mysqli_fetch_assoc($ambildata);

	// Jika belum ada di tb_bio, gunakan data dari tb_admin
	if (!$row) {
		$row = [
			'id_user' => $admin_data['id_user'],
			'nama_user' => '', // Sesuaikan dengan kolom di tb_admin
			'jk_user' => '',
			'alamat_user' => '',
			'notelp_user' => '',
            'alamat_email' => ''
		];
	}
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Alimrugi | Online shop for automobile</title>
    <link rel="stylesheet" href="../../css/custom.css">
</head>

<body>

    <div class="center" align="center">
        <div id="content" name="content" style="float: none;">
            <div class="contentheader">Setting Profil</div>

            <div class="contentbody" align="center"> 
                <form name="setting" method="post" action="../settingprofil_proses.php">
                    <div class="table-responsive">
                        <table class="table">
                                
                            <tr>
                                <th>Nama Lengkap</th>
                                <td>:</td>
                                <td>
                                    <input type="text" name="nama_user" value="<?php echo htmlspecialchars($row['nama_user']); ?>" required>
                                </td>    
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td>:</td>
                                <td> 
                                    <label> 
                                        <input type='radio' name='jk_user' value='L' style='width: auto;' <?php echo ($row['jk_user'] == "L") ? "checked" : ""; ?> required> Laki-laki
                                    </label>
                                    <label>
                                        <input type='radio' name='jk_user' value='P' style='width: auto;' <?php echo ($row['jk_user'] == "P") ? "checked" : ""; ?> required> Perempuan 
                                    </label>
                                </td>    
                            </tr>
                            <tr>
                                <th>Alamat Email</th>
                                <td>:</td>
                                <td>
                                    <input type="text" name="alamat_email" value="<?php echo htmlspecialchars($row['alamat_email']); ?>" required>
                                </td>    
                            </tr>
                        </table>
                        <button  type="submit">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</body>
</html>