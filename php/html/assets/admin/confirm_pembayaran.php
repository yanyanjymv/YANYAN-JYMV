<?php
include "../../koneksi.php";
session_start();

if($_SESSION['roles'] != 'admin'){
    echo "<script>alert('Forbidden Access'); location.href='../../index.php';</script>";
    exit();
}

if(isset($_GET['id'])){
    $id_pesanan = (int)$_GET['id'];

    $result = mysqli_query($konekdb, "SELECT status FROM tb_pesanan WHERE id_pesanan = $id_pesanan");
    if(!$result || mysqli_num_rows($result) == 0){
        echo "<script>alert('Pesanan tidak ditemukan'); location.href='pembayaran.php';</script>";
        exit();
    }
    $row = mysqli_fetch_assoc($result);
    $current_status = strtolower(trim($row['status']));

    if ($current_status === '') {
        echo "<script>alert('Status kosong di database!'); location.href='pembayaran.php';</script>";
        exit();
    }

    switch($current_status){
        case 'pending':
            $new_status = 'booking';
            break;
        case 'booking':
            $new_status = 'dp 30 persen';
            break;
        case 'dp 30 persen':
        case 'dp30 persen':
        case 'dp 30 persen':
            $new_status = 'lunas';
            break;
        case 'lunas':
            echo "<script>alert('Status sudah lunas. Tidak ada perubahan.'); location.href='pembayaran.php';</script>";
            exit();
        default:
            echo "<script>alert('Status saat ini tidak dikenali: $current_status'); location.href='pembayaran.php';</script>";
            exit();
    }

    $update = mysqli_query($konekdb, "UPDATE tb_pesanan SET status = '$new_status' WHERE id_pesanan = $id_pesanan");

    if($update){
        echo "<script>alert('Status berhasil diubah menjadi: $new_status'); location.href='pembayaran.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah status: ".mysqli_error($konekdb)."'); location.href='pembayaran.php';</script>";
    }

} else {
    echo "<script>alert('ID pesanan tidak ditemukan'); location.href='pembayaran.php';</script>";
}
?>
