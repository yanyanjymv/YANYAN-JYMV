<?php 
include("db_connection.php");
include("login_connection.php");

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
    <title>Form Pengajuan Cuti</title>
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
    <h1>Form Pengajuan Cuti</h1><br>
        <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
        <input type="hidden" name="departement" value="<?php echo htmlspecialchars ($departement); ?>">
        <input type="hidden" name="level" value="<?php echo htmlspecialchars ($level); ?>">
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
        $tanggal_mulai = isset($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : null;
        $tanggal_selesai = isset($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : null;
        $alasan = $_POST['alasan'];
        
    
        // Validasi input
        if (!$tanggal_mulai || !$tanggal_selesai) {
            die("Tanggal mulai dan tanggal selesai harus diisi.");
        }
    
        // Hitung jumlah hari cuti
        $tanggal_mulai_date = new DateTime($tanggal_mulai);
        $tanggal_selesai_date = new DateTime($tanggal_selesai);
        $jumlah_hari_cuti = $tanggal_mulai_date->diff($tanggal_selesai_date)->days + 1;
    
        // Ambil saldo cuti dari database
        $query_saldo = $koneksi->prepare("SELECT saldo_cuti FROM `admin` WHERE username = ?");
        if (!$query_saldo) {
            die("Query gagal disiapkan: " . $koneksi->error);
        }
    
        $query_saldo->bind_param("s", $username);
        $query_saldo->execute();
        $query_saldo->bind_result($saldo_cuti);
        $query_saldo->fetch();
        $query_saldo->close();
    
        if (!$saldo_cuti || $saldo_cuti < $jumlah_hari_cuti) {
            die("Saldo cuti tidak mencukupi. Anda hanya memiliki saldo $saldo_cuti ");
        }
        
        // Status ditentukan berdasarkan level
        $status = ($level == 'spv') ? 'Pending Admin Approval' : 'Pending Spv Approval';
        // Proses pengajuan cuti
        $stmt = $conn->prepare("INSERT INTO cuti (username, departement, level, tanggal_mulai, tanggal_selesai, alasan, status, tanggal_pembuatan) VALUES (?, ?, ?, ?, ?, ?, ?, now())");
        if (!$stmt) {
            die("Query gagal disiapkan: " . $conn->error);
        }
    
        $stmt->bind_param("sssssss", $username, $departement, $level, $tanggal_mulai, $tanggal_selesai, $alasan, $status);
        if (!$stmt->execute()) {
            die("Gagal menyimpan pengajuan cuti: " . $stmt->error);
        } else {
            echo "<div class='success-message'>Pengajuan cuti berhasil dikirim!</div>";
        }
    
        $stmt->close();
    }
    ?>


</body>
</html>