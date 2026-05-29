<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';
$data = mysqli_query($conn, "SELECT * FROM siswa ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Siswa</title>

<!-- Bootstrap 5 -->
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

<div class="card-header d-flex justify-content-between align-items-center">
<h4 class="mb-0">Data Siswa</h4>

<a href="tambah_siswa.php" class="btn btn-primary btn-sm">
+ Tambah Data
</a>
</div>

<div class="card-body">

<table class="table table-bordered table-striped table-hover">

<thead class="table-dark">
<tr>
<th>No</th>
<th>NIS</th>
<th>Nama</th>
<th>Kelas</th>
<th>RFID UID</th>
<th width="150">Aksi</th>
</tr>
</thead>

<tbody>
<?php $no=1; while($row = mysqli_fetch_assoc($data)) : ?>
<tr>
<td><?= $no++; ?></td>
<td><?= $row['nis']; ?></td>
<td><?= $row['nama']; ?></td>
<td><?= $row['kelas']; ?></td>
<td><?= $row['rfid_uid']; ?></td>
<td>

<a href="edit_siswa.php?id=<?= $row['id']; ?>" 
class="btn btn-warning btn-sm">Edit</a>

<a href="delete_siswa.php?id=<?= $row['id']; ?>" 
class="btn btn-danger btn-sm"
onclick="return confirm('Yakin hapus data?')">Hapus</a>

</td>
</tr>
<?php endwhile; ?>
</tbody>

</table>

<a href="index.php" class="btn btn-secondary btn-sm">← Kembali</a>

</div>
</div>

</div>
</div>
</div>

</body>
</html>