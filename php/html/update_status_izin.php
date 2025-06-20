<?php
include 'db_connection.php';
session_start(); // Mulai sesi

// Aktifkan debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Validasi parameter dan sesi
if (!isset($_SESSION['level']) || !isset($_SESSION['departement']) || !isset($_GET['id']) || !isset($_GET['status'])) {
    die("Akses tidak valid: parameter atau sesi hilang.");
}

$id = $_GET['id'];
$status = $_GET['status'];
$level = $_SESSION['level'];
$departement = $_SESSION['departement'];

// Validasi level dan status
if (!in_array($status, ['Pending Admin Approval', 'Approved', 'Rejected'])) {
    die("Status tidak valid.");
}
if (!in_array($level, ['spv', 'admin'])) {
    die("Level user tidak valid.");
}

// Query untuk mendapatkan data izin
$sql = "";
if ($level === 'spv') {
    $sql = "SELECT username, tanggal_mulai, tanggal_selesai, status FROM izin WHERE id = '$id' AND status = 'Pending Spv Approval'";
} elseif ($level === 'admin') {
    $sql = "SELECT username, tanggal_mulai, tanggal_selesai, status FROM izin WHERE id = '$id' AND status = 'Pending Admin Approval'";
}

$result = $conn->query($sql);
if ($result->num_rows === 0) {
    die("Data izin tidak ditemukan atau status tidak sesuai.");
}

// Proses jika data ditemukan
$row = $result->fetch_assoc();
$username = $row['username'];
$tanggal_mulai = $row['tanggal_mulai'];
$tanggal_selesai = $row['tanggal_selesai'];
$status_sebelumnya = $row['status']; // Simpan status sebelumnya

// Validasi aksi berdasarkan level
if ($level === 'spv') {
    if ($status !== 'Pending Admin Approval' && $status !== 'Rejected') {
        die("SPV hanya dapat mengubah status ke Pending Admin Approval dan Rejected.");
    }
    

    // Query untuk mengupdate status
    $sql = "UPDATE izin SET status = '$status' WHERE id = '$id'";
    if (!$conn->query($sql)) {
        die("Error updating record: " . $conn->error);
    }
} elseif ($level === 'admin') {
    if (!in_array($status, ['Approved', 'Rejected'])) {
        die("Admin hanya dapat mengubah status ke Approved atau Rejected.");
    }

    // Query untuk mengupdate status
    $sql = "UPDATE izin SET status = '$status' WHERE id = '$id'";
    if (!$conn->query($sql)) {
        die("Error updating record: " . $conn->error);
    }
}

// Redirect berdasarkan level dan departement
if ($level === 'spv') {
    if ($departement === 'IT') {
        header("Location: approval_izin_it.php?message=spv_updated");
        exit();
    } elseif ($departement === 'MARKETING') {
        header("Location: approval_izin_marketing.php?message=spv_updated");
        exit();
    } else {
        die("Departement tidak dikenali.");
    }
} elseif ($level === 'admin') {
    header("Location: approval_izin.php?message=admin_updated");
    exit();
}
?>
