<?php
include "../../koneksi.php";

$id_penawaran = $_POST['id_penawaran'] ?? null;
$nm_mobil = $_POST['nm_mobil'] ?? '';
$informasi = $_POST['informasi'] ?? '';
$harga_mobil = $_POST['harga_mobil'] ?? '';
$nama_gambar = $_FILES['gambar']['name'] ?? '';
$sumber = $_FILES['gambar']['tmp_name'] ?? '';
$target = '../../img/mobil/';

if (!$id_penawaran) {
    echo "<script>alert('ID Penawaran tidak ditemukan.'); window.history.back();</script>";
    exit();
}

// Ambil nama gambar lama
$query_gambar = mysqli_query($konekdb, "SELECT gambar FROM tb_penawaran WHERE id_penawaran = '$id_penawaran'");
if (!$query_gambar) {
    die("Query error: " . mysqli_error($konekdb));
}
$data_gambar = mysqli_fetch_assoc($query_gambar);
$gambar_lama = $data_gambar['gambar'] ?? '';

// Jika tidak upload gambar baru, pakai gambar lama
if (empty($nama_gambar)) {
    $nama_gambar = $gambar_lama;
}

$query = mysqli_query($konekdb, "UPDATE tb_penawaran SET 
                                nm_mobil = '$nm_mobil',
                                informasi = '$informasi',
                                harga_mobil = '$harga_mobil',
                                gambar = '$nama_gambar'
                                WHERE id_penawaran = '$id_penawaran'");

if ($query) {
    if (!empty($nama_gambar) && !empty($sumber)) {
        $movefile = move_uploaded_file($sumber, $target . $nama_gambar);
        if ($movefile) {
            echo "<script> alert('Data berhasil disimpan dan Gambar berhasil diupload');
                  document.location.href='penawaran_mobil.php'; </script>";
        } else {
            echo "<script> alert('Data berhasil disimpan tetapi Gambar gagal diupload');
                  document.location.href='penawaran_mobil.php'; </script>";
        }
    } else {
        echo "<script> alert('Data berhasil disimpan');
              document.location.href='penawaran_mobil.php'; </script>";
    }
} else {
    echo "<script> alert('Data gagal disimpan');
          document.location.href='penawaran_mobil.php'; </script>";
}
?>
