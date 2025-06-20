<?php
include 'db_connection.php';
session_start();


if (!isset($_SESSION['admin_username']) || !isset($_POST['id'])) {
    header("Location: status_cuti.php");
    exit();
}

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM cuti WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: status_cuti.php");
} else {
    header("Location: status_cuti.php");
}

$stmt->close();
$conn->close();
?>
