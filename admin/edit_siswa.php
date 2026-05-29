<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: data_siswa.php");
    exit;
}

$id = $_GET['id'];

$data = mysqli_query($conn, "SELECT * FROM siswa WHERE id='$id'");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "Data tidak ditemukan";
    exit;
}

if (isset($_POST['update'])) {

    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $rfid_uid = $_POST['rfid_uid'];

    mysqli_query($conn,"UPDATE siswa SET 
        nis='$nis',
        nama='$nama',
        kelas='$kelas',
        rfid_uid='$rfid_uid'
        WHERE id='$id'
    ");

    header("Location: data_siswa.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Data Siswa</title>

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
<h4>Edit Data Siswa</h4>
</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">
<label class="form-label">NIS</label>
<input type="text" name="nis" class="form-control" value="<?= $row['nis']; ?>" required>
</div>

<div class="mb-3">
<label class="form-label">Nama</label>
<input type="text" name="nama" class="form-control" value="<?= $row['nama']; ?>" required>
</div>

<div class="mb-3">
<label class="form-label">Kelas</label>
<input type="text" name="kelas" class="form-control" value="<?= $row['kelas']; ?>" required>
</div>

<div class="mb-3">
<label class="form-label">RFID UID</label>
<input type="text" name="rfid_uid" class="form-control" value="<?= $row['rfid_uid']; ?>" required>
</div>

<button type="submit" name="update" class="btn btn-primary">
Update
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