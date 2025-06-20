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
$status_raw    = $_POST['status'] ?? 'pending';
$harga_total   = $_POST['harga_total'] ?? '';
$harga_sisa   = $_POST['harga_sisa'] ?? '';

// Normalisasi status: trim dan lowercase
$status = strtolower(trim($status_raw));

// Validasi sederhana
if (!$id_user || !$id_mobil || !$nama_pemesan || !$alamat_pemesan || !$no_wa || !$email) {
    die('Data tidak lengkap.');
}

// Ambil harga mobil dari database
$stmt = $konekdb->prepare("SELECT harga_mobil FROM tb_mobil WHERE id_mobil = ?");
$stmt->bind_param("s", $id_mobil);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die("Mobil tidak ditemukan.");
}
$mobil = $res->fetch_assoc();
$harga_mobil = (float)$mobil['harga_mobil'];

// Hitung harga total dan harga sisa sesuai status
$booking_fee = 500000;

switch ($status) {
    case 'pending':
        $harga_total = 0;
        $harga_sisa = $booking_fee + $harga_mobil; // 500000 + harga_mobil untuk pending
        break;
    case 'booking':
        $harga_total = $booking_fee;
        $harga_sisa = $harga_mobil - $booking_fee; // harga_mobil - 500000 untuk booking
        break;
    case 'dp 30 persen':
        $harga_total = $booking_fee + ($harga_mobil * 0.3);
        $harga_sisa = $harga_mobil - ($harga_mobil * 0.3); // harga_mobil - 30% untuk dp 30 persen
        break;
    case 'lunas':
        $harga_total = $booking_fee + $harga_mobil;
        $harga_sisa = $harga_mobil - ($harga_mobil * 0.7); // harga_mobil - 70% untuk lunas
        break;
    default:
        $harga_total = $booking_fee;
        $harga_sisa = $harga_mobil; // Default untuk kasus lainnya
}

// Proses upload foto KTP
if (isset($_FILES['foto_ktp']) && $_FILES['foto_ktp']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['foto_ktp']['tmp_name'];
    $fileName = $_FILES['foto_ktp']['name'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    $allowedfileExtensions = ['jpg', 'jpeg', 'png'];

    if (in_array($fileExtension, $allowedfileExtensions)) {
        $uploadFileDir = '../../uploads/ktp/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        $newFileName = uniqid('ktp_', true) . '.' . $fileExtension;
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $query = "INSERT INTO tb_pesanan 
                (id_user, id_mobil, nama_pemesan, alamat_pemesan, no_wa, email, foto_ktp, status, harga_total, harga_sisa)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($konekdb, $query);
            if ($stmt === false) {
                die('Prepare failed: ' . mysqli_error($konekdb));
            }

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssdd",
                $id_user,
                $id_mobil,
                $nama_pemesan,
                $alamat_pemesan,
                $no_wa,
                $email,
                $newFileName,
                $status,
                $harga_total,
                $harga_sisa
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
