<?php
session_start();
include("login_connection.php");

if (!isset($_SESSION['admin_username'])) {
    header("location: index.php");
    exit();
}

$username = $_SESSION['admin_username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pengguna - Sistem Cuti Online</title>
    <link rel="stylesheet" type="text/css" href="style/style_home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h1>PT DGEX INDONESIA</h1>
        <div id="current-time" style="text-align: center; margin-bottom: 20px; font-size: 20px;"></div>
        <div id="current-date" style="text-align: center; margin-bottom: 20px; font-size: 14px;"></div>
        <hr>
        <ul class="menu">
        <li><a href="home_hrd.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="approval_cuti.php"><i class="fas fa-check-circle"></i> Approval Cuti</a></li>
            <li><a href="approval_izin.php"><i class="fas fa-check"></i> Approval Izin Kerja</a></li>
            <li><a href="user.php"><i class="fas fa-user"></i> Users</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <script>
    // Fungsi untuk mendapatkan tanggal saat ini
    function getCurrentDate() {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const today = new Date();
        return today.toLocaleDateString('id-ID', options);
    }
    document.getElementById('current-date').textContent = getCurrentDate();

    // Fungsi untuk mendapatkan waktu saat ini
    function updateTime() {
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const timeString = now.toLocaleTimeString('id-ID', options);
        document.getElementById('current-time').textContent = timeString;
    }
    // Memperbarui waktu setiap detik
    setInterval(updateTime, 1000);
    updateTime(); // Memanggil fungsi pertama kali agar tidak menunggu interval
    </script>

    
        <h1></h1>
        <div class="content">
        <div class="box">
        <h2>Tambah Pengguna Baru</h2>
        <form action="update_db_multiuser.php" method="POST" enctype="multipart/form-data">
            <label for="nama">Nama:</label>
            <input type="text" name="nama" required>
            <label for="departement">Departemen:</label>
            <select name="departement" required>
                <option value="HRD">HRD</option>
                <option value="MARKETING">MARKETING</option>
                <option value="IT">IT</option>
            </select>
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <label for="level">level:</label>
            <select name="level" required>
                <option value="admin">admin</option>
                <option value="spv">spv</option>
                <option value="karyawan">karyawan</option>
            </select>
            <label for="profil_picture">Upload Foto Profil:</label>
            <input type="file" name="profil_picture" accept="image/*" >

            <input type="submit" value="Tambah Pengguna">
        </form>
    </div>
</div>
</body>
</html>