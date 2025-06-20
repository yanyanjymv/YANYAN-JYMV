<?php 
include 'db_connection.php';
include 'login_connection.php';

session_start();

$id = $_GET['id'];
$status = $_GET['status'];
$level = $_SESSION['level'];
$departement = $_SESSION['departement'];

// Validasi parameter dan sesi
if (!isset($_SESSION['level']) || !isset($_GET['id']) || !isset($_GET['status'])) {
    die("Akses tidak valid: parameter atau sesi hilang.");
}


// Validasi level dan status
if (!in_array($status, ['Pending Admin Approval', 'Approved', 'Rejected'])) {
    die("Status tidak valid.");
}
if (!in_array($level, ['spv', 'admin'])) {
    die("Level user tidak valid.");
}

// Query untuk mendapatkan data cuti
$sql = "";
if ($level === 'spv') {
    $sql = "SELECT username, tanggal_mulai, tanggal_selesai, status FROM cuti WHERE id = '$id' AND status = 'Pending Spv Approval'";
} elseif ($level === 'admin') {
    $sql = "SELECT username, tanggal_mulai, tanggal_selesai, status FROM cuti WHERE id = '$id' AND status = 'Pending Admin Approval'";
}

$result = $conn->query($sql);


// Proses jika data ditemukan
$row = $result->fetch_assoc();
$username = $row['username'];
$tanggal_mulai = $row['tanggal_mulai'];
$tanggal_selesai = $row['tanggal_selesai'];
$status_sebelumnya = $row['status']; // Simpan status sebelumnya

// Hitung jumlah hari cuti
$start_date = new DateTime($tanggal_mulai);
$end_date = new DateTime($tanggal_selesai);
$jumlah_hari_cuti = $start_date->diff($end_date)->days + 1;

// Validasi aksi berdasarkan level
if ($level === 'spv') {
    if ($status !== 'Pending Admin Approval' && $status !== 'Rejected') {
        die("SPV hanya dapat mengubah status ke Pending Admin Approval dan Rejected.");
    }
} elseif ($level === 'admin') {
    // Pastikan pengurangan saldo hanya dilakukan ketika status Approved
    if ($status === 'Approved' && $status_sebelumnya === 'Pending Admin Approval') {
        // Debugging: Log sebelum pengurangan saldo
        error_log("Mengurangi saldo cuti untuk $username sebanyak $jumlah_hari_cuti hari.");

        // Pastikan tabel yang benar untuk saldo cuti
        $sql_update_saldo = "UPDATE `admin` SET saldo_cuti = saldo_cuti - $jumlah_hari_cuti WHERE username = '$username' AND saldo_cuti >= $jumlah_hari_cuti";
        
        if (!$koneksi->query($sql_update_saldo)) {
            die("Gagal memperbarui saldo cuti: " . $koneksi->error);
        }
    } elseif ($status === 'Pending Admin Approval') {
        die("Tidak dapat mengurangi saldo: Status sebelumnya bukan Pending Admin Approval.");
    }
}

// Update status cuti
$sql_update_status = "UPDATE cuti SET status = '$status' WHERE id = '$id'";
if (!$conn->query($sql_update_status)) {
    die("Gagal memperbarui status cuti: " . $conn->error);
}

// Redirect berdasarkan level
if ($level === 'spv') {
    if ($departement === 'IT') {
        header("Location: approval_cuti_it.php?message=spv_updated");
        exit();
    } elseif ($departement === 'MARKETING') {
        header("Location: approval_cuti_marketing.php?message=spv_updated");
        exit();
    }
} elseif ($level === 'admin') {
    if ($departement === 'HRD') {
        header("Location: approval_cuti.php?message=admin_updated");
        exit();
    }
}

?>
