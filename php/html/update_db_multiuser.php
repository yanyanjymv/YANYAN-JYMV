<?php
include("login_connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $departement = $_POST['departement'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $level = $_POST['level'];

    // Folder untuk menyimpan file
    $target_dir = "img/";
    $profil_picture = "img.jpg"; // Set default jika tidak ada file diunggah

    // Jika file diupload
    if (!empty($_FILES["profil_picture"]["name"])) {
        $profil_picture = basename($_FILES["profil_picture"]["name"]);
        $target_file = $target_dir . $profil_picture;
        $uploadOk = 1;

        // Cek apakah file adalah gambar
        $check = getimagesize($_FILES["profil_picture"]["tmp_name"]);
        if ($check === false) {
            echo "File yang diunggah bukan gambar.";
            $uploadOk = 0;
        }

        // Cek jika ada kesalahan upload
        if ($uploadOk === 1) {
            if (!move_uploaded_file($_FILES["profil_picture"]["tmp_name"], $target_file)) {
                echo "Terjadi kesalahan saat mengunggah file.";
                $profil_picture = "img.jpg"; // Tetap gunakan default jika gagal upload
            }
        }
    }

    // Simpan data ke database
    $sql = "INSERT INTO admin (nama, departement, username, password, level, profile_picture) 
            VALUES ('$nama', '$departement', '$username', '$password', '$level', '$profil_picture')";

    if ($koneksi->query($sql) === TRUE) {
        echo "Pengguna berhasil ditambahkan.";
        header("Location: user.php");
    } else {
        echo "Error: " . $sql . "<br>" . $koneksi->error;
    }
}
?>
