<?php
include "../../koneksi.php";
// Mengambil filter tanggal mulai, tanggal akhir, dan status (jika ada)
$filterTanggalStart = isset($_GET['filterTanggalStart']) ? $_GET['filterTanggalStart'] : '';
$filterTanggalEnd = isset($_GET['filterTanggalEnd']) ? $_GET['filterTanggalEnd'] : '';
$filterStatus = isset($_GET['filterStatus']) ? $_GET['filterStatus'] : '';

// Menentukan query berdasarkan filter tanggal dan status
$whereDate = '';
$whereStatus = '';

// Menambahkan kondisi tanggal jika valid
if ($filterTanggalStart && $filterTanggalEnd) {
    $whereDate = "p.tanggal_pesan BETWEEN '$filterTanggalStart' AND '$filterTanggalEnd'";
}

// Menambahkan kondisi status jika valid
if ($filterStatus) {
    $whereStatus = "p.status = '$filterStatus'";
}

// Membuat bagian WHERE dengan kondisi tanggal dan status
$whereClause = '';
if ($whereDate && $whereStatus) {
    $whereClause = "WHERE $whereDate AND $whereStatus";
} elseif ($whereDate) {
    $whereClause = "WHERE $whereDate";
} elseif ($whereStatus) {
    $whereClause = "WHERE $whereStatus";
}

// Total mobil
$totalMobilQ = "SELECT COUNT(*) AS total FROM tb_mobil";
$totalMobilR = $konekdb->query($totalMobilQ);
if (!$totalMobilR) {
    die("Query Error: " . mysqli_error($konekdb));
}
$totalMobil = $totalMobilR->fetch_assoc()['total'];

// Data mobil
$dataMobilQ = "SELECT * FROM tb_mobil ORDER BY tanggal DESC";
$dataMobilR = $konekdb->query($dataMobilQ);
if (!$dataMobilR) {
    die("Query Error: " . mysqli_error($konekdb));
}

// Mobil terjual (join tb_pesanan dan tb_mobil)
$mobilTerjualQ = "SELECT p.id_pesanan, p.id_mobil, m.nm_mobil, m.harga_mobil, p.nama_pemesan, p.status, p.tanggal_pesan
                  FROM tb_pesanan p
                  LEFT JOIN tb_mobil m ON p.id_mobil = m.id_mobil
                  $whereClause";  // Menggunakan $whereClause yang sudah diproses
$mobilTerjualR = $konekdb->query($mobilTerjualQ);
if (!$mobilTerjualR) {
    die("Query Error: " . mysqli_error($konekdb));
}

// Total pendapatan (jumlah harga mobil dari pesanan dengan status lunas)
$totalPendapatanQ = "SELECT SUM(p.harga_total) AS total_pendapatan
                     FROM tb_pesanan p
                     $whereClause";  // Menggunakan $whereClause yang sudah diproses
$totalPendapatanR = $konekdb->query($totalPendapatanQ);
if (!$totalPendapatanR) {
    die("Query Error: " . mysqli_error($konekdb));
}
$totalPendapatan = $totalPendapatanR->fetch_assoc()['total_pendapatan'] ?? 0;

// Status pemesanan
$statusCountQ = "SELECT status, COUNT(*) AS jumlah FROM tb_pesanan GROUP BY status";
$statusCountR = $konekdb->query($statusCountQ);
if (!$statusCountR) {
    die("Query Error: " . mysqli_error($konekdb));
}

$statusCounts = [];
while ($row = $statusCountR->fetch_assoc()) {
    $statusCounts[$row['status']] = $row['jumlah'];
}

// Menghitung sisa pembayaran untuk pesanan yang belum lunas
$sisaPembayaranQ = "SELECT p.id_pesanan, SUM(p.harga_sisa) AS sisa_pembayaran
                    FROM tb_pesanan p
                    $whereClause
                    GROUP BY p.id_pesanan";

$sisaPembayaranR = $konekdb->query($sisaPembayaranQ);
if (!$sisaPembayaranR) {
    die("Query Error: " . mysqli_error($konekdb));
}

$totalPiutang = 0; // Inisialisasi total piutang
$sisaPembayaran = [];
while ($row = $sisaPembayaranR->fetch_assoc()) {
    $sisaPembayaran[$row['id_pesanan']] = $row['sisa_pembayaran'];
    $totalPiutang += $row['sisa_pembayaran']; // Menjumlahkan total piutang
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Laporan Sistem Jual Beli Mobil Bekas</title>
    <link rel="stylesheet" href="../../css/custom.css">
    <style type="text/css">
        body {
            background-color: #f2f2f2;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            color: #2c3e50;
        }
        table {
            border-collapse: collapse;
            width: 95%;
            margin-bottom: 25px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgb(0 0 0 / 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px 15px;
            text-align: left;
        }
        th {
            background-color: #2980b9;
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f9fbfc;
        }
        .summary {
            background-color: #2980b9;
            color: white;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            max-width: 600px;
        }
        .status-list span {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 5px 12px;
            margin-right: 10px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
        }
        .status-list span.pending { background-color: #e67e22; }
        .status-list span.booking { background-color: #8e44ad; }
        .status-list span.dp { background-color: #27ae60; }
        .status-list span.lunas { background-color: #2980b9; }
    </style>
</head>
<body>
    <div id="headeradmin">
        <div class="centeradmin">
            <div class="judul">
                <a href="index.php"> Home </span> </a>
            </div>

            <div class="adminbar">
                <!-- PENGAMBILAN DATA -->
                <?php 
                $id_show = $_SESSION['id_user'] ?? null;
                $row = null; // Inisialisasi variabel sebelum query

                if ($id_show) {
                    $ambildata = mysqli_query($konekdb, "SELECT * FROM tb_bio WHERE id_user='$id_show'");

                    if ($ambildata && mysqli_num_rows($ambildata) > 0) {
                        $row = mysqli_fetch_array($ambildata);
                        $nama_user = $row['nama_user'] ?? "Nama tidak tersedia";
                    } else {
                        $nama_user = "Nama tidak tersedia";
                    }
                } else {
                    $nama_user = "Nama tidak tersedia";
                }
                ?>
                <span> <?php echo $nama_user; ?> </span>
                &nbsp;<a href="logout.php"> [LOGOUT] </a>
                <div class="clear"></div>
            </div>
        </div> 
    </div>

    <div id="menuadmin">
        <div class="menutitle">
            <a> MENU </a> 
        </div>
        <div class="menubody">
            <li> <a href="datamobil.php"> Data Mobil </a> </li> <br>
            <li> <a href="pembayaran.php"> Pembayaran </a> </li><br>
            <li> <a href="penawaran_mobil.php"> Penawaran </a> </li><br>
            <li> <a href="laporan.php"> Laporan </a> </li><br>
            <li> <a href="settingprofil.php"> Setting Profil </a> </li>
        </div>
    </div>

    <!-- ISI CONTENT -->
    <div id="content" name="content">
        <div class="contentheader"> Laporan Sistem Jual Beli Mobil Bekas </div>

        <div class="contentbody" align="center"> 
            <form method="GET" action="laporan.php">
                <label for="filterTanggalStart">Tanggal Mulai:</label>
                <input type="date" id="filterTanggalStart" name="filterTanggalStart" value="<?= htmlspecialchars($filterTanggalStart) ?>" />
                <label for="filterTanggalEnd">Tanggal Akhir:</label>
                <input type="date" id="filterTanggalEnd" name="filterTanggalEnd" value="<?= htmlspecialchars($filterTanggalEnd) ?>" />
                <label for="filterStatus">Status:</label>
                <select id="filterStatus" name="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= $filterStatus == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="booking" <?= $filterStatus == 'booking' ? 'selected' : '' ?>>Booking</option>
                    <option value="dp 30 persen" <?= $filterStatus == 'dp 30 persen' ? 'selected' : '' ?>>DP 30%</option>
                    <option value="lunas" <?= $filterStatus == 'lunas' ? 'selected' : '' ?>>Lunas</option>
                </select>
                <button type="submit">Filter</button>
            </form>

            <div class="summary">
                <p><strong>Total Mobil:</strong> <?= $totalMobil ?></p>
                <p><strong>Total Pendapatan mobil+booking:</strong> Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></p>
                <p><strong>Total piutang yang belum lunas:</strong> Rp <?= number_format($totalPiutang, 0, ',', '.') ?></p>
                <p><strong>Status Pemesanan:</strong></p>
                <div class="status-list">
                    <span class="pending">Pending: <?= $statusCounts['pending'] ?? 0 ?></span>
                    <span class="booking">Booking: <?= $statusCounts['booking'] ?? 0 ?></span>
                    <span class="dp">DP 30%: <?= $statusCounts['dp 30 persen'] ?? 0 ?></span>
                    <span class="lunas">Lunas: <?= $statusCounts['lunas'] ?? 0 ?></span>
                </div>
            </div>

            <h2>Daftar Mobil di Inventaris</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Mobil</th>
                        <th>Nama Mobil</th>
                        <th>Harga Mobil</th>
                        <th>Tanggal Input</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($mobil = $dataMobilR->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($mobil['id_mobil']) ?></td>
                            <td><?= htmlspecialchars($mobil['nm_mobil']) ?></td>
                            <td><?= number_format($mobil['harga_mobil'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($mobil['tanggal']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Mobil Terjual / Dalam Proses Pembelian</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>ID Mobil</th>
                        <th>Nama Mobil</th>
                        <th>Harga Mobil(belum booking)</th>
                        <th>Nama Pembeli</th>
                        <th>Status</th>
                        <th>Sisa Pembayaran (Rp)</th>
                        <th>Tanggal Pesan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($mobilTerjualR->num_rows > 0): ?>
                        <?php while($jual = $mobilTerjualR->fetch_assoc()): ?>
                            <tr>
                                <td><?= $jual['id_pesanan'] ?></td>
                                <td><?= htmlspecialchars($jual['id_mobil']) ?></td>
                                <td><?= htmlspecialchars($jual['nm_mobil'] ?? '-') ?></td>
                                <td><?= isset($jual['harga_mobil']) ? number_format($jual['harga_mobil'], 0, ',', '.') : '-' ?></td>
                                <td><?= htmlspecialchars($jual['nama_pemesan']) ?></td>
                                <td><?= htmlspecialchars(ucwords($jual['status'])) ?></td>
                                <td><?= isset($sisaPembayaran[$jual['id_pesanan']]) ? number_format($sisaPembayaran[$jual['id_pesanan']], 0, ',', '.') : '-' ?></td>
                                <td><?= htmlspecialchars($jual['tanggal_pesan']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align:center;">Belum ada data mobil terjual atau pemesanan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</body>
</html>
