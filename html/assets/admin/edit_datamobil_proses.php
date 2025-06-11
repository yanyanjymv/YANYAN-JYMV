<?php

include "../../koneksi.php";

extract($_POST);
extract($_FILES);

$sumber = $_FILES['gambar']['tmp_name'];
$target = '../../img/mobil/';
$nama_gambar = $_FILES['gambar']['name'];

// Ambil nama gambar lama dari database
$query_gambar = mysqli_query($konekdb, "SELECT gambar FROM tb_mobil WHERE id_mobil = '$id_mobil'");
$data_gambar = mysqli_fetch_assoc($query_gambar);
$gambar_lama = $data_gambar['gambar'];

// Jika tidak ada file baru yang diunggah, gunakan gambar lama
if (empty($nama_gambar)) {
    $nama_gambar = $gambar_lama;
}

$query = mysqli_query($konekdb, "UPDATE tb_mobil SET 
                                nm_mobil = '$nm_mobil',
                                spek_mobil = '$spek_mobil',
                                harga_mobil = '$harga_mobil',
                                gambar = '$nama_gambar'
                                WHERE id_mobil = '$id_mobil'");

if ($query) {
    // Hanya pindahkan file jika ada gambar baru yang diunggah
    if (!empty($_FILES['gambar']['name'])) {
        $movefile = move_uploaded_file($sumber, $target . $nama_gambar);
        if ($movefile) {
            echo "<script> alert('Data berhasil disimpan dan Gambar berhasil diupload');
                  document.location.href='datamobil.php'; </script>";
        } else {
            echo "<script> alert('Data berhasil disimpan tetapi Gambar gagal diupload');
                  document.location.href='datamobil.php'; </script>";
        }
    } else {
        echo "<script> alert('Data berhasil disimpan');
              document.location.href='datamobil.php'; </script>";
    }
} else {
    echo "<script> alert('Data gagal disimpan');
          document.location.href='datamobil.php'; </script>";
}
?>
