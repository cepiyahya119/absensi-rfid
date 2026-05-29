<?php
include "../config/koneksi.php";

 $message = "";
 $alert = "";

if(isset($_POST['register'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // VALIDASI
    if(empty($username) || empty($password)){
        $message = "Username dan password wajib diisi!";
        $alert = "danger";

    } else {

        // AMANKAN INPUT
        $username = mysqli_real_escape_string($conn, $username);
        $password = mysqli_real_escape_string($conn, $password);

        // CEK USERNAME
        $cek = mysqli_query($conn, "
            SELECT * FROM users 
            WHERE username='$username'
        ");

        if(mysqli_num_rows($cek) > 0){
            $message = "Username sudah digunakan!";
            $alert = "warning";

        } else {

            // ENCRYPT PASSWORD MD5
            $password_md5 = md5($password);

            // SIMPAN DATA — TABEL DIPERBAIKI: user → users
            $simpan = mysqli_query($conn, "
                INSERT INTO users(username, password)
                VALUES('$username', '$password_md5')
            ");

            if($simpan){
                $message = "Register berhasil!";
                $alert = "success";
            } else {
                $message = "Register gagal!";
                $alert = "danger";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register User</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background: #f1f5f9;
        }

        .sidebar{
            min-height: 100vh;
            background: #212529;
        }

        .sidebar a{
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 5px;
        }

        .sidebar a:hover{
            background: #343a40;
            color: white;
        }

        .sidebar .active{
            background: #0d6efd;
            color: white;
        }

        .card-register{
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 15px;
        }

        .card-body{
            padding: 35px;
        }

        .form-control{
            height: 45px;
            border-radius: 10px;
        }

        .btn-register{
            height: 45px;
            border-radius: 10px;
            font-weight: 600;
        }

    </style>

</head>
<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->
        <?php include "sidebar.php"; ?>

        <!-- CONTENT -->
        <div class="col-md-10">

            <div class="d-flex justify-content-center align-items-center" style="min-height:100vh;">

                <div class="card shadow card-register">

                    <div class="card-body">

                        <h3 class="text-center mb-2">
                            Register User
                        </h3>

                        <p class="text-center text-muted mb-4">
                            Tambahkan akun baru
                        </p>

                        <!-- ALERT -->
                        <?php if($message != ""){ ?>

                            <div class="alert alert-<?= $alert; ?> alert-dismissible fade show">

                                <?= $message; ?>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                            </div>

                        <?php } ?>

                        <!-- FORM -->
                        <form method="POST">

                            <div class="mb-3">

                                <label class="form-label">
                                    Username
                                </label>

                                <input 
                                    type="text" 
                                    name="username"
                                    class="form-control"
                                    placeholder="Masukkan username"
                                    required
                                >

                            </div>

                            <div class="mb-4">

                                <label class="form-label">
                                    Password
                                </label>

                                <input 
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Masukkan password"
                                    required
                                >

                            </div>

                            <button 
                                type="submit"
                                name="register"
                                class="btn btn-primary w-100 btn-register"
                            >
                                Register
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>