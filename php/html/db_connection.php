<?php
$host = "db";
$user = "root";  
$password = "1234";  
$database = "cuti_online";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
?>
