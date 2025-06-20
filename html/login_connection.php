<?php
$host = "db";
$username = "root";  
$password = "1234";  
$database = "multiuser";




$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error);
}
?>
