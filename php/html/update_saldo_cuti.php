<?php
include("login_connection.php");

function updateSaldoCuti($koneksi) {
    $today = date("Y-m-d");

    $stmt = $koneksi->prepare("SELECT login_id, username, saldo_cuti, join_date FROM admin WHERE join_date IS NOT NULL");
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "Proses user: {$row['username']} (ID: {$row['login_id']})<br>";
        echo "Join date: {$row['join_date']}<br>";
        echo "Saldo cuti saat ini: {$row['saldo_cuti']}<br>";

        if (empty($row['join_date'])) {
            echo "join_date kosong untuk ID: {$row['id']}<br>";
            continue;
        }

        $join_date = new DateTime($row['join_date']);
        $current_date = new DateTime($today);
        $interval = $join_date->diff($current_date);
        $years = $interval->y;  // Menggunakan selisih tahun

        echo "Selisih tahun: $years<br>";

        if ($years >= 1) {  // Jika sudah 1 tahun atau lebih
            $new_saldo = $row['saldo_cuti'] + 12 * $years;  // Tambahkan saldo cuti 12 untuk setiap tahun
            $new_join_date = $join_date->modify("+{$years} years")->format("Y-m-d"); // Update join_date

            // Update saldo cuti dan join_date
            $updateStmt = $koneksi->prepare("UPDATE admin SET saldo_cuti = ?, join_date = ? WHERE login_id = ?");
            if ($updateStmt) {
                $updateStmt->bind_param("isi", $new_saldo, $new_join_date, $row['login_id']);
                if ($updateStmt->execute()) {
                    echo "Saldo cuti berhasil diperbarui untuk ID: {$row['login_id']}<br>";
                    echo "Join date diperbarui menjadi: $new_join_date<br>";
                } else {
                    echo "Execute failed: " . $updateStmt->error . "<br>";
                }
            } else {
                echo "Prepare failed: " . $koneksi->error . "<br>";
            }
        } else {
            echo "ID: {$row['login_id']} belum mencapai 1 tahun.<br>";
        }
    }

    $stmt->close();
    $koneksi->close();
}

updateSaldoCuti($koneksi);
?>
