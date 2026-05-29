<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
.sidebar{
    min-height:100vh;
    background:#212529;
}

.sidebar a{
    color:#adb5bd;
    text-decoration:none;
    display:block;
    padding:10px 15px;
    border-radius:6px;
    margin-bottom:5px;
}

.sidebar a:hover{
    background:#343a40;
    color:#fff;
}

.sidebar .active{
    background:#0d6efd;
    color:white;
}
</style>

<!-- Sidebar -->
<div class="col-md-2 sidebar p-3">

<h5 class="text-white text-center mb-3">ADMIN PANEL</h5>
<hr class="text-secondary">

<a href="index.php">
<i class="bi bi-speedometer2"></i> Dashboard
</a>

<a href="data_siswa.php">
<i class="bi bi-people"></i> Data Siswa
</a>

<a href="tampil.php">
<i class="bi bi-clipboard-check"></i> Absensi
</a>

<a href="register.php">
<i class="bi-person-add"></i> Daftar Akun
</a>

<hr class="text-secondary">

<a href="logout.php" class="text-danger">
<i class="bi bi-box-arrow-right"></i> Logout
</a>

</div>