<?php
session_start();
include("login_connection.php");

if (!isset($_SESSION['admin_username'])) {
    header("location: index.php");
    exit();
}

$username = $_SESSION['admin_username'];



function getProfilePicture($username, $koneksi) {
    $stmt = $koneksi->prepare("SELECT profile_picture FROM admin WHERE username = ? ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['profile_picture'] ? $row['profile_picture'] : 'img.jpg';
    } else {
        return 'img.jpg';
    }   
}



$profile_picture = getProfilePicture($username, $koneksi);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sistem Cuti Online</title>
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

    <div class="content">
    <p class="p"><h1>Selamat datang, <?php echo htmlspecialchars($username); ?>!</h1>
    <hr>
    <P class="p"> <img src="img/<?php echo htmlspecialchars($profile_picture); ?>" alt="Foto Profil" style="width: 150px; height: 150px; border-radius: 60%;"></p>
    <hr><br>
        <div class="box">
            <?php 
            include 'db_connection.php'; 
            ?>
            <h2>Data Cuti Karyawan</h2>
            <table class="data-table">
                <tr>
                    <th>No</th>
                    <th>Tanggal Dibuat</th>
                    <th>Nama</th>
                    <th>Departemen</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Alasan</th>
                    <th>Status</th>
                </tr>

                <?php
                $sql = "SELECT * FROM cuti;";
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
                            </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='8'>Belum ada pengajuan cuti.</td></tr>";
                }
                ?>
            </table> <br>
            <button class='approve'>
            <a href='cetak_cuti.php'>CETAK</a>
        </div><br><br>

        <div class="box">
            <?php 
            include 'db_connection.php'; 
            ?>
            <h2>Data Izin Karyawan</h2>
            <table class="data-table">
                <tr>
                    <th>No</th>
                    <th>Tanggal Dibuat</th>
                    <th>Nama</th>
                    <th>Departemen</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Kategori</th>
                    <th>Alasan</th>
                    <th>Status</th>
                </tr>

                <?php
                $sql = "SELECT * FROM izin;";
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
                                <td>{$row['Kategori']}</td>
                                <td>{$row['alasan']}</td>
                                <td>{$row['status']}</td>
                            </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='8'>Belum ada pengajuan cuti.</td></tr>";
                }
                ?>
            </table><br>
            <button class='approve'>
            <a href='cetak_izin.php'>CETAK</a>
        </div>
    </div>
</body>
</html>