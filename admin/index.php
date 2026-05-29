<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}
/* Hitung jumlah siswa */
include '../config/koneksi.php';
$querySiswa = mysqli_query($conn,"SELECT COUNT(*) AS total FROM siswa");
$dataSiswa  = mysqli_fetch_assoc($querySiswa);
$totalSiswa = $dataSiswa['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
}
.content{
    padding:30px;
}
</style>

</head>
<body>

<div class="container-fluid">
<div class="row">

<!-- Sidebar -->
<?php include "sidebar.php"; ?>

<!-- Konten -->
<div class="col-md-10 content p-4">

<div class="card shadow">
<div class="card-body text-center">

<h3>Selamat datang, <?= $_SESSION['username']; ?></h3>
<p>Ini adalah halaman dashboard sistem absensi.</p>

<hr>

<h4 class="text-primary">Jumlah Data Siswa Terdaftar</h4>

<h1 class="display-4 fw-bold">
<?= $totalSiswa; ?>
</h1>

<p class="text-muted">Siswa telah terdaftar dalam sistem RFID</p>

</div>
</div>

</div>

</div>
</div>

</body>
</html>