<?php
include 'login_connection.php';
session_start();


if (!isset($_SESSION['admin_username']) || !isset($_POST['id'])) {
    header("Location: user.php");
    exit();
}

$id = $_POST['id'];

$stmt = $koneksi->prepare("DELETE FROM `admin` WHERE login_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        header("Location: user.php?status=success");
    } else {
        die("Tidak ada data yang ditemukan untuk dihapus.");
    }
} else {
    die("Gagal menjalankan query: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
