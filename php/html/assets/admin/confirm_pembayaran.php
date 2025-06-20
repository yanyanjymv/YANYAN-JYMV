<?php
include "../../koneksi.php";
session_start();

if ($_SESSION['roles'] != 'admin') {
    echo "<script>alert('Forbidden Access'); location.href='../../index.php';</script>";
    exit();
}

if (isset($_GET['id'])) {
    $id_pesanan = (int)$_GET['id'];

    // Ambil status sekarang dan id_mobil pesanan
    $result = mysqli_query($konekdb, "SELECT status, id_mobil FROM tb_pesanan WHERE id_pesanan = $id_pesanan");
    if (!$result || mysqli_num_rows($result) == 0) {
        echo "<script>alert('Pesanan tidak ditemukan'); location.href='pembayaran.php';</script>";
        exit();
    }
    $row = mysqli_fetch_assoc($result);
    $current_status = strtolower(trim($row['status']));
    $id_mobil = $row['id_mobil'];

    if ($current_status === '') {
        echo "<script>alert('Status kosong di database!'); location.href='pembayaran.php';</script>";
        exit();
    }

    // Tentukan status baru sesuai flow
    switch ($current_status) {
        case 'pending':
            $new_status = 'booking';
            break;
        case 'booking':
            $new_status = 'dp 30 persen';
            break;
        case 'dp 30 persen':
        case 'dp30 persen':
            $new_status = 'lunas';
            break;
        case 'lunas':
            echo "<script>alert('Status sudah lunas. Tidak ada perubahan.'); location.href='pembayaran.php';</script>";
            exit();
        default:
            echo "<script>alert('Status saat ini tidak dikenali: $current_status'); location.href='pembayaran.php';</script>";
            exit();
    }

    // Ambil harga mobil
    $stmt = $konekdb->prepare("SELECT harga_mobil FROM tb_mobil WHERE id_mobil = ?");
    $stmt->bind_param("s", $id_mobil);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo "<script>alert('Mobil tidak ditemukan'); location.href='pembayaran.php';</script>";
        exit();
    }
    $mobil = $result->fetch_assoc();
    $harga_mobil = (float)$mobil['harga_mobil'];

    // Hitung harga_total sesuai status baru
    $booking_fee = 500000;
    switch ($new_status) {
        case 'pending':
            $harga_total = 0;
            break;
        case 'booking':
            $harga_total = $booking_fee;
            break;
        case 'dp 30 persen':
            $harga_total = $booking_fee + ($harga_mobil * 0.3);
            break;
        case 'lunas':
            $harga_total = $booking_fee + $harga_mobil;
            break;
        default:
            $harga_total = $booking_fee;
    }

    // Hitung harga_sisa sesuai status baru
    // Jika status adalah 'pending', maka harga_sisa = 500000 + harga_mobil
    $harga_sisa = 0;
    switch ($new_status) {
        case 'pending':
            $harga_sisa = $booking_fee + $harga_mobil;  // 500000 + harga_mobil
            break;
        case 'booking':
            $harga_sisa = $harga_mobil;
            break;
        case 'dp 30 persen':
            $harga_sisa = $harga_mobil - ($harga_mobil * 0.3);
            break;
        case 'lunas':
            $harga_sisa = $harga_mobil - $harga_mobil;
            break;
        default:
            $harga_sisa = $harga_mobil; // Set default jika status tidak sesuai
    }

    // Update status, harga_total, dan harga_sisa sekaligus
    $stmt = $konekdb->prepare("UPDATE tb_pesanan SET status = ?, harga_total = ?, harga_sisa = ? WHERE id_pesanan = ?");
    $stmt->bind_param("sdii", $new_status, $harga_total, $harga_sisa, $id_pesanan);
    if ($stmt->execute()) {
        echo "<script>alert('Status berhasil diubah menjadi: $new_status'); location.href='pembayaran.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah status: " . $stmt->error . "'); location.href='pembayaran.php';</script>";
    }
} else {
    echo "<script>alert('ID pesanan tidak ditemukan'); location.href='pembayaran.php';</script>";
}
?>
