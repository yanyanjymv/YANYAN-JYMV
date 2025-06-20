<?php
session_start();
include "../../koneksi.php";

// Pastikan user sudah login dan ada session id_user
if (!isset($_SESSION['id_user']) || $_SESSION['roles'] != 'user') {
    echo "<script>alert('Forbidden Access'); window.location='../../index.php';</script>";
    exit;
}

$id_user = $_SESSION['id_user'];
$id_penawaran = $_POST['id_penawaran'] ?? '';
$nm_mobil = mysqli_real_escape_string($konekdb, $_POST['nm_mobil'] ?? '');
$informasi = mysqli_real_escape_string($konekdb, $_POST['informasi'] ?? '');
$harga_mobil = $_POST['harga_mobil'] ?? '';

$sumber = $_FILES['gambar']['tmp_name'] ?? '';
$nama_gambar = $_FILES['gambar']['name'] ?? '';
$target = '../../img/mobil/';

// Query insert data, gunakan prepared statement jika memungkinkan untuk keamanan lebih baik
$sql = "INSERT INTO tb_penawaran (id_penawaran, nm_mobil, informasi, harga_mobil, gambar, id_user) 
        VALUES ('$id_penawaran', '$nm_mobil', '$informasi', '$harga_mobil', '$nama_gambar', '$id_user')";
$query = mysqli_query($konekdb, $sql);

if ($query) {
    if ($sumber && $nama_gambar) {
        $upload = move_uploaded_file($sumber, $target . $nama_gambar);
        if ($upload) {
            echo "<script>
                alert('Data berhasil disimpan dan Gambar berhasil diupload');
                window.location.href = 'penawaran_mobil.php';
            </script>";
        } else {
            echo "<script>
                alert('Data berhasil disimpan tapi Gambar gagal diupload!');
                window.location.href = 'penawaran_mobil.php';
            </script>";
        }
    } else {
        // Jika tidak ada file gambar yang diupload
        echo "<script>
            alert('Data berhasil disimpan');
            window.location.href = 'penawaran_mobil.php';
        </script>";
    }
} else {
    echo "<script>
        alert('Data gagal disimpan: " . mysqli_error($konekdb) . "');
        window.location.href = 'penawaran_mobil.php';
    </script>";
}
?>
