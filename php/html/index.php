<?php 
include("login_connection.php");
session_start();

$username = "";
$password = "";
$err = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if ($username == '' || $password == '') {
        $err .= "<li>Both username and password are required.</li>";
    }

    if (empty($err)) {
        $stmt = $koneksi->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (md5($password) === $user['password']) {
                $_SESSION['admin_username'] = $username;
                $_SESSION['level'] = $user['level'];
                $_SESSION['departement'] = $user['departement'];
                
                if ($user['level'] === "admin") {
                    header("Location: home_hrd.php");
                } elseif ($user['level'] === "spv" && $user['departement'] === "IT") {
                    header("Location: home_spv_it.php");
                } elseif ($user['level'] === "karyawan") {
                    header("Location: home_karyawan.php");
                } elseif ($user['level'] === "spv" && $user['departement'] === "MARKETING") {
                    header("Location: home_spv_marketing.php");
                }
                exit();
            } else {
                $err .= "<li>Invalid username or password</li>";
            }
        } else {
            $err .= "<li>Invalid username or password</li>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<div class="container">
    <div class="box form-box">
        <header>Login</header>
        <?php if($err): ?>
            <ul class="error-message"><?php echo $err; ?></ul>
        <?php endif; ?>
        <form action="" method="post">
            <div class="field input">
                <input type="text" name="username" placeholder="username" required>
            </div>
            <div class="field input">
                <input type="password" name="password" placeholder="password" required>
            </div>  
            <div class="field"> 
                <input type="submit" class="btn" name="login" value="LOGIN">
            </div>
        </form>
    </div>        
</div>   
</body>
</html>
