<?php include 'db_connection.php'; ?>

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
<div class="box">
    <h1>Approval Izin</h1>
    <table class="data-table">
        <tr>
            <th>no</th>
            <th>Tanggal Dibuat</th>
            <th>Nama</th>
            <th>departement</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Kategori</th>
            <th>Alasan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php
        $sql = "SELECT * FROM izin where `status` in ('Pending Admin Approval');";
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
                        <td>
                            <button class='approve'>
                            <a href='update_status_izin.php?id={$row['id']}&status=Approved'>Approve</a>
                            </button> | 
                            <button class='reject'>
                            <a href='update_status_izin.php?id={$row['id']}&status=Rejected'>Reject</a>
                        </td>
                    </tr>";
                    $no++;
            }
        } else {
            echo "<tr><td colspan='7'>Belum ada pengajuan cuti.</td></tr>";
        }
        ?>

    </table>
</div>
</body>
</html>
