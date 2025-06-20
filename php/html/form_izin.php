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
    <title>Form Pengajuan Izin Kerja</title>
    <link rel="stylesheet" href="css/style.css">
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
    }if ($level == 'karyawan') {
            echo '<li><a href="home_karyawan.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>';    
    }
    
        ?> 
            <li><a href="form_cuti.php"><i class="fas fa-calendar-alt"></i> Ajukan Cuti</a></li>
            <li><a href="form_izin.php"><i class="fas fa-user-clock"></i> Ajukan Izin Kerja</a></li>
            <li><a href="status_cuti.php"><i class="fas fa-list-alt"></i> Status Cuti</a></li>
            <li><a href="status_izin.php"><i class="fas fa-clock"></i> Status Izin Kerja</a></li>
            <?php if ($level == 'spv') {
                if($departement == 'IT') {
                    echo '<li><a href="approval_cuti_it.php"><i class="fas fa-check-circle"></i> Approval Cuti</a></li>';
                    echo '<li><a href="approval_izin_it.php"><i class="fas fa-check"></i> Approval Izin Kerja</a></li>';
                } elseif($departement == 'MARKETING') {
                    echo '<li><a href="approval_cuti_MARKETING.php"><i class="fas fa-check-circle"></i> Approval Cuti</a></li>';
                    echo '<li><a href="approval_izin_MARKETING.php"><i class="fas fa-check"></i> Approval Izin Kerja</a></li>';
                }
            }
            ?>
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
    
    <form method="POST" action="">
    <h1>Form Pengajuan Izin Kerja</h1><br>
        <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
        <input type="hidden" name="departement" value="<?php echo htmlspecialchars ($departement); ?>">
        <input type="hidden" name="level" value="<?php echo htmlspecialchars ($level); ?>">
        Kategori :<label for="kategori"></label>
            <select name="kategori" required>
                <option value="Normal">Normal</option>
                <option value="Sakit">Sakit</option>
                <option value="Melahirkan">Melahirkan</option>
                <option value="Hari Raya Idul Fitri">Hari raya Idul Fitri</option>
                <option value="Umroh/Haji">Umroh/Haji</option>
            </select>
        Tanggal Mulai: <input type="date" name="tanggal_mulai" required>
        Tanggal Selesai: <input type="date" name="tanggal_selesai" required>
        Alasan: <textarea name="alasan" required></textarea>
        <input type="submit" value="Ajukan Cuti">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $departement = $_POST['departement'];
        $level = $_POST['level'];
        $kategori = $_POST['kategori'];
        $tanggal_mulai = $_POST['tanggal_mulai'];
        $tanggal_selesai = $_POST['tanggal_selesai'];
        $alasan = $_POST['alasan'];


        // Status ditentukan berdasarkan level
        $status = ($level == 'spv') ? 'Pending Admin Approval' : 'Pending Spv Approval';

        $stmt = $conn->prepare("INSERT INTO izin (username, departement, level, kategori, tanggal_mulai, tanggal_selesai, alasan, status, tanggal_pembuatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, now())");
        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars ($conn->error));
        }

        $stmt->bind_param("ssssssss",$username, $departement, $level, $kategori, $tanggal_mulai, $tanggal_selesai, $alasan,  $status);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        } else {
            echo "Pengajuan Izin Berhasil Dikirim.";
        }

        $stmt->close();
    }
    ?>
</body>
</html>
