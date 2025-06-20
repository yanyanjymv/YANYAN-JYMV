<?php
include "../../koneksi.php";
session_start();

// Ambil data dari POST
$id_user       = $_POST['id_user'] ?? '';
$id_mobil      = $_POST['id_mobil'] ?? '';
$nama_pemesan  = $_POST['nama_pemesan'] ?? '';
$alamat_pemesan= $_POST['alamat_pemesan'] ?? '';
$no_wa         = $_POST['no_wa'] ?? '';
$email         = $_POST['email'] ?? '';

// Validasi sederhana (bisa dikembangkan sesuai kebutuhan)
if (!$id_user || !$id_mobil || !$nama_pemesan || !$alamat_pemesan || !$no_wa || !$email) {
    die('Data tidak lengkap.');
}

// Proses upload file foto_ktp
if (isset($_FILES['foto_ktp']) && $_FILES['foto_ktp']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['foto_ktp']['tmp_name'];
    $fileName = $_FILES['foto_ktp']['name'];
    $fileSize = $_FILES['foto_ktp']['size'];
    $fileType = $_FILES['foto_ktp']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedfileExtensions = array('jpg', 'jpeg', 'png');

    if (in_array($fileExtension, $allowedfileExtensions)) {
        // Tentukan folder upload
        $uploadFileDir = '../../uploads/ktp/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        $newFileName = uniqid('ktp_', true) . '.' . $fileExtension;
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Simpan data ke database
            // Gunakan prepared statement untuk keamanan
            $query = "INSERT INTO tb_pesanan 
                (id_user, id_mobil, nama_pemesan, alamat_pemesan, no_wa, email, foto_ktp)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($konekdb, $query);
            if ($stmt === false) {
                die('Prepare failed: ' . mysqli_error($konekdb));
            }

            mysqli_stmt_bind_param(
                $stmt,
                "sssssss",
                $id_user,
                $id_mobil,
                $nama_pemesan,
                $alamat_pemesan,
                $no_wa,
                $email,
                $newFileName
            );

           if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            echo "<script>
            alert('Pesanan berhasil disimpan');
            window.location.href='pembayaran.php';
            </script>";
    exit;
            } else {
                echo "Error saat menyimpan data: " . mysqli_error($konekdb);
            }
        } else {
            echo "Error saat memindahkan file upload.";
        }
    } else {
        echo "Upload gagal. Format file harus JPG, JPEG, atau PNG.";
    }
} else {
    echo "File KTP belum diupload atau terjadi kesalahan upload.";
}
?>
