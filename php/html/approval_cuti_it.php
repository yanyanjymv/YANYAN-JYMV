<?php 
include("db_connection.php");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
} 
session_start();


if (!isset($_SESSION['admin_username']) || !isset($_SESSION['departement']) || !isset($_SESSION['level'])) {
    header("location: index.php");
    exit();
}

$username = $_SESSION['admin_username'];
$departement = $_SESSION['departement'];
$level = $_SESSION['level'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approval Cuti</title>
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
        <?php if ($level == 'spv') {
            if($departement == 'IT') {
            echo '<li><a href="home_spv_it.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>';
        }elseif($departement == 'MARKETING'){
            echo '<li><a href="home_spv_marketing.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>';
        }
    }
        ?> 
            <li><a href="form_cuti.php"><i class="fas fa-calendar-alt"></i> Ajukan Cuti</a></li>
            <li><a href="form_izin.php"><i class="fas fa-user-clock"></i> Ajukan Izin Kerja</a></li>
            <li><a href="status_cuti.php"><i class="fas fa-list-alt"></i> Status Cuti</a></li>
            <li><a href="status_izin.php"><i class="fas fa-clock"></i> Status Izin Kerja</a></li>
            <li><a href="approval_cuti_it.php"><i class="fas fa-check-circle"></i> Approval Cuti</a></li>
            <li><a href="approval_izin_it.php"><i class="fas fa-check"></i> Approval Izin Kerja</a></li>
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

    <div class="content">
<div class="box">
    <h1>Approval Cuti IT</h1>
    <table class="data-table">
        <tr>
            <th>no</th>
            <th>Tanggal Dibuat</th>
            <th>Nama</th>
            <th>departement</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Alasan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php
        $sql = "SELECT * FROM cuti where `departement`='IT' and `level`='karyawan' and `status` in ('Pending Spv Approval');";
        $result = $conn->query($sql);

        $no = 1;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['tanggal_pembuatan']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['departement']}</td>
                        <td>{$row['tanggal_mulai']}</td>
                        <td>{$row['tanggal_selesai']}</td>
                        <td>{$row['alasan']}</td>
                        <td>{$row['status']}</td>
                        <td>
                            <button class='approve'>
                            <a href='update_status_cuti.php?id={$row['id']}&status=Pending Admin Approval'>Approve</a> 
                            </button> | 
                            <button class='reject'>
                            <a href='update_status_cuti.php?id={$row['id']}&status=Rejected'>Reject</a>
                        </td>
                    </tr>";
                    $no++;
            }
        } else {
            echo "<tr><td colspan='8'>Belum ada pengajuan cuti.</td></tr>";
        }
        ?>

    </table>
</body>
</html>
