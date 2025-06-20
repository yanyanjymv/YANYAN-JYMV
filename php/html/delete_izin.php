<?php
include 'db_connection.php';
session_start();


if (!isset($_SESSION['admin_username']) || !isset($_POST['id'])) {
    header("Location: status_izin.php");
    exit();
}

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM izin WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: status_izin.php");
} else {
    header("Location: status_izin.php");
}

$stmt->close();
$conn->close();
?>
