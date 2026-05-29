<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

include "../config/koneksi.php";

$filter = "";
$judul  = "Semua_Data_Absensi";

if (!empty($_GET['tanggal'])) {
    $tanggal = $_GET['tanggal'];
    $filter = "WHERE tanggal='$tanggal'";
    $judul = "Laporan_Tanggal_$tanggal";
}

if (!empty($_GET['bulan'])) {
    $bulan = $_GET['bulan'];
    $filter = "WHERE MONTH(tanggal)='$bulan'";
    $judul = "Laporan_Bulan_$bulan";
}

$query = mysqli_query($conn,"SELECT * FROM absensi $filter ORDER BY tanggal DESC");

header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=$judul.xls");

?>

<table border="1">

<tr>
<th>No</th>
<th>Nama</th>
<th>Tanggal</th>
<th>Jam Masuk</th>
<th>Jam Pulang</th>
<th>Status</th>
</tr>

<?php

$no=1;

if(mysqli_num_rows($query) > 0){

while($data = mysqli_fetch_assoc($query)){

?>

<tr>
<td><?= $no++; ?></td>
<td><?= $data['nama']; ?></td>
<td><?= $data['tanggal']; ?></td>
<td><?= $data['jam_masuk']; ?></td>
<td><?= $data['jam_pulang']; ?></td>
<td><?= $data['status']; ?></td>
</tr>

<?php
}

}else{

?>

<tr>
<td colspan="6">Data tidak ditemukan</td>
</tr>

<?php } ?>

</table>