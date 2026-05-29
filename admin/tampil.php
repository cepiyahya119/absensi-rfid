<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include "../config/koneksi.php";

$filter = "";
$judul  = "Semua Data Absensi";

if (!empty($_GET['tanggal'])) {
    $tanggal = $_GET['tanggal'];
    $filter = "WHERE absensi.tanggal='$tanggal'";
    $judul = "Laporan Tanggal $tanggal";
}

$query = mysqli_query($conn, "
SELECT 
    absensi.*,
    siswa.kelas
FROM absensi
LEFT JOIN siswa ON absensi.nama = siswa.nama
$filter
ORDER BY absensi.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Absensi</title>

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
<h4 class="mb-0"><?= $judul; ?></h4>
</div>

<div class="card-body">

<!-- FILTER TANGGAL -->
<div class="row mb-4">

<div class="col-md-6">
<form method="GET" class="row g-2">

<div class="col-8">
<input type="date" name="tanggal" class="form-control">
</div>

<div class="col-4">
<button class="btn btn-primary w-100">
Filter Tanggal
</button>
</div>

</form>
</div>

</div>

<!-- TABLE -->
<div class="table-responsive">

<table class="table table-bordered table-striped table-hover">

<thead class="table-dark">
<tr>
<th>No</th>
<th>Nama</th>
<th>Kelas</th>
<th>Tanggal</th>
<th>Jam Masuk</th>
<th>Jam Pulang</th>
<th>Status</th>
</tr>
</thead>

<tbody>

<?php
$no = 1;

if (mysqli_num_rows($query) > 0) {

while ($data = mysqli_fetch_assoc($query)) {
?>

<tr>

<td><?= $no++; ?></td>

<td><?= $data['nama']; ?></td>

<td><?= $data['kelas'] ?? '-'; ?></td>

<td><?= $data['tanggal']; ?></td>

<td><?= $data['jam_masuk']; ?></td>

<td><?= $data['jam_pulang']; ?></td>

<td>
<?php
if ($data['status'] == "Hadir") {

    echo "<span class='badge bg-success'>Hadir</span>";

} elseif ($data['status'] == "Terlambat") {

    echo "<span class='badge bg-danger'>Terlambat</span>";

} else {

    echo "<span class='badge bg-secondary'>-</span>";
}
?>
</td>

</tr>

<?php
}
} else {
?>

<tr>
<td colspan="7" class="text-center">
Data tidak ditemukan
</td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

<!-- BUTTON -->
<div class="mt-3">

<a href="export_excel.php?tanggal=<?= $_GET['tanggal'] ?? '' ?>"
class="btn btn-success">
Download Excel
</a>

<a href="index.php" class="btn btn-secondary">
Kembali
</a>

</div>

</div>
</div>

</div>
</div>
</div>

</body>
</html>