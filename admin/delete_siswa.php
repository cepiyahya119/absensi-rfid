<?php
include "../config/koneksi.php";

$id = $_GET['id'];

// Hapus dulu absensinya
mysqli_query($conn, "DELETE FROM absensi WHERE siswa_id='$id'");

// Baru hapus siswa
mysqli_query($conn, "DELETE FROM siswa WHERE id='$id'");

header("Location: data_siswa.php");