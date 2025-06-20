<?php
session_start();
include("login_connection.php");
include("db_connection.php");

if (!isset($_SESSION['admin_username'])){
    header("location: index.php");
    exit();
}

$username = $_SESSION['admin_username'];
$departement = $_SESSION['departement'];
$level = $_SESSION['level'];

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

<?php
    if (!$conn) {
        die("Koneksi database gagal: " . $conn->connect_error);
    }

    if (empty($departement) || empty($username)) {
        die("Error: Variabel 'departement' atau 'username' kosong.");
    }
    // Menghitung jumlah approved, rejected, pending, dan saldo cuti
    $stmtApproved = $conn->prepare("SELECT COUNT(*) as Approved FROM cuti WHERE `departement` = ? AND `username` = ? AND `status` = 'Approved'");
    if (!$stmtApproved) {
        die("Query Approved gagal disiapkan: " . $conn->error);
    }
    $stmtApproved->bind_param("ss", $departement, $username);
    $stmtApproved->execute();
    $approved = $stmtApproved->get_result()->fetch_assoc()['Approved'];
    
    $stmtRejected = $conn->prepare("SELECT COUNT(*) as Rejected FROM cuti WHERE `departement` = ? AND `username` = ? AND `status` = 'Rejected'");
    if (!$stmtRejected) {
        die("Query Rejected gagal disiapkan: " . $conn->error);
    }
    $stmtRejected->bind_param("ss", $departement, $username);
    $stmtRejected->execute();
    $rejected = $stmtRejected->get_result()->fetch_assoc()['Rejected'];
    
    $stmtPending = $conn->prepare("SELECT COUNT(*) as Pending FROM cuti WHERE `departement` = ? AND `username` = ? AND `status` = 'Pending Admin Approval'");
    if (!$stmtPending) {
        die("Query Pending gagal disiapkan: " . $conn->error);
    }$stmtPending->bind_param("ss", $departement, $username);
    $stmtPending->execute();
    $pending = $stmtPending->get_result()->fetch_assoc()['Pending'];

    // saldo cuti disimpan dalam tabel admin
    $stmtSaldo = $koneksi->prepare("SELECT saldo_cuti FROM admin WHERE username = ?");
    $stmtSaldo->bind_param("s", $username);
    $stmtSaldo->execute();
    $saldo = $stmtSaldo->get_result()->fetch_assoc()['saldo_cuti'];

    $stmtApproved->close();
    $stmtRejected->close();
    $stmtPending->close();
    $stmtSaldo->close();
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
    <li><a href="home_spv_it.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li> 
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
    // tanggal saat ini
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
    <p class="p"><h1>Selamat datang, <?php echo htmlspecialchars($username); ?>!</h1>
    <hr>
    <P class="p"> <img src="img/<?php echo htmlspecialchars($profile_picture); ?>" alt="Foto Profil" style="width: 150px; height: 150px; border-radius: 60%;"></p>
    
    <hr>
    </div><br>

    <div class="stats-container">
    <div class="stats-box approved">
        <h3>Approved</h3>
        <p><?php echo $approved; ?> Pengajuan</p>
    </div>
    <div class="stats-box rejected">
        <h3>Rejected</h3>
        <p><?php echo $rejected; ?> Pengajuan</p>
    </div>
    <div class="stats-box pending">
        <h3>Pending</h3>
        <p><?php echo $pending; ?> Pengajuan</p>
    </div>
    <div class="stats-box saldo">
        <h3>Saldo Cuti</h3>
        <p><?php echo $saldo; ?> Hari</p>
    </div>
</div>

    <div class="box">
            <h2>Data Cuti</h2>
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
                $stmt = $conn->prepare("select * from cuti where `departement` =? and `username` = ? and `level` = ? and `status` in ('Approved', 'Rejected')");
                $stmt->bind_param("sss", $departement, $username, $level);
                $stmt->execute();
                $result = $stmt->get_result();

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
                $stmt->close();
                ?>
            </table>
        </div><br><br>

        <div class="box">
            <?php 
            include 'db_connection.php'; 
            ?>
            <h2>Data Izin</h2>
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
                $stmt = $conn->prepare("select * from izin where `departement` =? and `username` = ? and `level` = ? and `status` in ('Approved', 'Rejected')");
                $stmt->bind_param("sss", $departement, $username, $level);
                $stmt->execute();
                $result = $stmt->get_result();

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
                $stmt->close();
                ?>
            </table>
        </div>
    

</body>
</html>
