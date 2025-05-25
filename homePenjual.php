<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda Pemilik Catering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e2e6ea);
        }
        .card-menu {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 16px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }
        .card-menu:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .card-menu .icon {
            font-size: 40px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">KateringKu</a>
        <div>
            <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container text-center py-5">
    <h1 class="mb-4 text-primary">Selamat Datang, Pemilik Catering!</h1>
    <p class="text-muted mb-5">Kelola menu harian Anda dengan mudah.</p>

    <div class="row justify-content-center g-4">
        <div class="col-md-4">
            <a href="upload.php" class="text-decoration-none">
                <div class="card card-menu p-4">
                    <div class="icon text-success mb-3">➕</div>
                    <h4 class="text-dark">Tambah Menu Baru</h4>
                    <p class="text-muted">Upload makanan untuk hari tertentu.</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="menu.php" class="text-decoration-none">
                <div class="card card-menu p-4">
                    <div class="icon text-info mb-3">📋</div>
                    <h4 class="text-dark">Daftar Menu Saya</h4>
                    <p class="text-muted">Lihat dan kelola semua menu yang telah Anda buat.</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="dasboard.php" class="text-decoration-none">
                <div class="card card-menu p-4">
                    <div class="icon text-warning mb-3">📅</div>
                    <h4 class="text-dark">Menu Hari Ini</h4>
                    <p class="text-muted">Tampilkan menu sesuai hari saat ini.</p>
                </div>
            </a>
        </div>
    </div>
</div>

</body>
</html>
