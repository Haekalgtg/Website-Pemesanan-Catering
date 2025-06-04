<?php
session_start();

// Proses logout jika ada parameter ?logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Cek apakah user adalah penjual
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../index.php");
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
            <a href="homePenjual.php?logout=true" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container text-center py-5">
    <h1 class="mb-4 text-primary">Selamat Datang, Pemilik Catering!</h1>
    <p class="text-muted mb-5">Kelola menu dan pesanan Anda dengan mudah.</p>

    <div class="row justify-content-center g-4">
        <div class="col-md-3">
            <a href="upload.php" class="text-decoration-none">
                <div class="card card-menu p-4">
                    <div class="icon text-success mb-3">➕</div>
                    <h5 class="text-dark">Tambah Menu</h5>
                    <p class="text-muted">Upload makanan baru.</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="menu.php" class="text-decoration-none">
                <div class="card card-menu p-4">
                    <div class="icon text-info mb-3">📋</div>
                    <h5 class="text-dark">Menu Saya</h5>
                    <p class="text-muted">Lihat & kelola menu Anda.</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="keuangan.php" class="text-decoration-none">
                <div class="card card-menu p-4">
                    <div class="icon text-primary mb-3">💰</div>
                    <h5 class="text-dark">Laporan Keuangan</h5>
                    <p class="text-muted">Catat pemasukan & pengeluaran.</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="manajemen_pesanan.php" class="text-decoration-none">
                <div class="card card-menu p-4">
                    <div class="icon text-danger mb-3">🧾</div>
                    <h5 class="text-dark">Manajemen Pesanan</h5>
                    <p class="text-muted">Kelola pesanan masuk.</p>
                </div>
            </a>
        </div>
    </div>
</div>


</body>
</html>
