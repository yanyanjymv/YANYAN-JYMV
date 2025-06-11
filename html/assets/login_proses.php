<?php
// Hubungkan ke database
include "../koneksi.php";
session_start();

// Pastikan input sudah diterima
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($konekdb, $_POST['username']);
    $password = mysqli_real_escape_string($konekdb, $_POST['password']);

    // Query untuk mengecek login
    $query = "SELECT * FROM tb_admin WHERE username = '$username' AND password = '$password'";
    $proses = mysqli_query($konekdb, $query);

    if (mysqli_num_rows($proses) > 0) {
        $row = mysqli_fetch_assoc($proses);

        // Simpan data ke session
        $_SESSION['username'] = $row['username'];
        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['roles'] = $row['roles'];

        // Arahkan ke halaman sesuai role
        if ($row['roles'] == "admin") {
            header("Location: admin/index.php");
            exit();
        } elseif ($row['roles'] == "user") {
            header("Location: users/index.php");
            exit();
        } else {
            session_destroy();
            header("Location: ../login.php?error=2");
            exit();
        }
    } else {
        header("Location: ../login.php?error=1");
        exit();
    }
}
?>
