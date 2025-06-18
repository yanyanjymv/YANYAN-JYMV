<?php
    include 'koneksi.php';
    session_start();
    if(isset($_SESSION['roles']) == null){
?>

<html>
<head> 
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Alimrugi | Online shop for automobile</title>

    <link rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="css/custom2.css">
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css"> 
</head>

<body>

    <div id="header">
        <div class="center">
            <!-- Logo -->
            <div class="logo">
                <img src="img/alimrugi.jpg">
            </div>
            <!-- Logo -->

            <!-- Navbar -->
            <div class="navbar">
                <ul>
                    <li> <a href="login.php"> Login </a></li>
                </ul>
            </div> 
            <div class="clear"></div>
        </div>
    </div>

    <!-- SLIDER -->
    <div id="slider">
        <div class="center">
            <h1> Selamat Datang di Alim Rugi </h1>
            <h2> Jual Beli Mobil Terbaik Secara Online</h2>
            <br>
            <h3> Untuk Memulai Silahkan Mendaftar Terlebih Dahulu </h3>
            <a href="signup.php" class="btn-primary"> REGISTRASI </a>
        </div>
    </div>
    <!-- END SLIDER -->     

    <div class="center">
        <div id="menumobil">
            <form action="" method="post">
                <input type="search" name="search" class="search" placeholder="Masukkan nama mobil">
                <input type="submit" name="submit" class="search btn-cari" value="Cari"> 
            </form>
            <div class="judulmenu"> Menu Mobil </div>    
        </div>

        <br>

        <?php 
            $submit = @$_POST['submit'];
            $search = @$_POST['search'];

            if($submit && $search != ""){
                $query = mysqli_query($konekdb, "SELECT * FROM tb_mobil WHERE nm_mobil LIKE '%$search%' ORDER BY id_mobil ASC");
            } else {
                $query = mysqli_query($konekdb, "SELECT * FROM tb_mobil ORDER BY id_mobil ASC"); // Menampilkan semua data
            }

            $cek = mysqli_num_rows($query);
            if($cek < 1){
                echo "Data yang anda cari tidak ditemukan!";
            }

            while($data = mysqli_fetch_array($query)){
        ?>

        <div class="kontenspan">
            <div class="table-responsive">
                <table class="tableisi">
                    <tr> <th> <?php echo $data['nm_mobil']; ?> </th> </tr>    
                    <tr> 
                        <td> 
                            <img class="spangambar" src="img/mobil/<?php echo $data['gambar']; ?>" alt="Foto"> 
                        </td> 
                    </tr>
                    <tr> <td class="tdspek"> <?php echo $data['spek_mobil']; ?> </td> </tr>
                    <tr> <td class="tdharga"> RP.<?php echo number_format($data['harga_mobil'],0,",","."); ?> </td> </tr>
                </table>
            </div>
        </div>

        <?php } ?>
        
        <div class="clear"></div>

        <div class="copyright">
            Copyright &copy; by KELOMPOK 2 
        </div>
    </div> <!-- PENUTUP ISI-->

</body>

</html>

<?php
    } else {
        if($_SESSION['roles'] == "admin"){
            header("location:assets/admin/index.php");
        } elseif($_SESSION['roles'] == "user"){    
            header("location:assets/users/index.php");
        } else {
            echo "User tidak ditemukan";
            session_destroy();
        }
    }
?>
