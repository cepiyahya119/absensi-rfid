<?php
session_start();
include "config/koneksi.php";

 $username = $_POST['username'];
 $password = md5($_POST['password']);

 $query = mysqli_query($conn, "SELECT * FROM users 
                               WHERE username='$username' 
                               AND password='$password'");

 $data = mysqli_fetch_assoc($query);

if ($data) {
    $_SESSION['username'] = $data['username'];
    header("Location: admin/index.php");
    exit;
} else {
    // Simpan pesan error ke session
    $_SESSION['login_error'] = "Login gagal! Username atau password salah.";
    header("Location: login.php");
    exit;
}
?>