<?php
include "../koneksi.php";
session_start();

// Ambil ID User dari sesi
$id_user = $_SESSION['id_user'];

// Pastikan data dikirim
if (!isset($_POST['nama_user'], $_POST['jk_user'], $_POST['alamat_email'])) {
    echo "<script>alert('Data tidak lengkap!'); location.href='settingprofil.php';</script>";
    exit();
}

$nama_user = mysqli_real_escape_string($konekdb, $_POST['nama_user']);
$jk_user = mysqli_real_escape_string($konekdb, $_POST['jk_user']);
$alamat_email = mysqli_real_escape_string($konekdb, $_POST['alamat_email']);

// Cek apakah ID user sudah ada di tb_bio
$cek_user = mysqli_query($konekdb, "SELECT * FROM tb_bio WHERE id_user = '$id_user'");

if (mysqli_num_rows($cek_user) > 0) {
    // Jika sudah ada, lakukan UPDATE
    $query = "UPDATE tb_bio SET 
        nama_user = '$nama_user',
        jk_user = '$jk_user',
        alamat_email = '$alamat_email'
        WHERE id_user = '$id_user'";
} else {
    // Jika belum ada, lakukan INSERT
    $query = "INSERT INTO tb_bio (id_user, nama_user, jk_user, alamat_email) 
        VALUES ('$id_user', '$nama_user', '$jk_user', '$alamat_email')";
}

// Jalankan query
if (mysqli_query($konekdb, $query)) {
    echo "<script> alert('Data berhasil disimpan!'); location.href='../login.php'; </script>";
} else {
    echo "<script> alert('Data gagal disimpan!'); location.href='settingprofil.php'; </script>";
}
?>
