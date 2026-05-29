<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

if (isset($_POST['simpan'])) {

    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $rfid_uid = $_POST['rfid_uid'];

    mysqli_query($conn,"INSERT INTO siswa (nis,nama,kelas,rfid_uid)
                        VALUES ('$nis','$nama','$kelas','$rfid_uid')");

    header("Location: data_siswa.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Tambah Data Siswa</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body style="background:#f4f6f9;">

<div class="container-fluid">
<div class="row">

<!-- Sidebar -->
<?php include "sidebar.php"; ?>

<!-- Konten -->
<div class="col-md-10 p-4">

<div class="card shadow">

<div class="card-header">
<h4>Tambah Data Siswa</h4>
</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">
<label class="form-label">NIS</label>
<input type="text" name="nis" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Nama</label>
<input type="text" name="nama" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Kelas</label>
<input type="text" name="kelas" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">RFID UID</label>
<input type="text" name="rfid_uid" class="form-control" placeholder="Tempelkan kartu RFID" required>
</div>

<button type="submit" name="simpan" class="btn btn-primary">
Simpan
</button>

<a href="data_siswa.php" class="btn btn-secondary">
Kembali
</a>

</form>

</div>
</div>

</div>
</div>
</div>

</body>
</html>