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
    <meta charset="UTF-8" />
    <title>Beranda Pembeli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card-menu {
            transition: transform 0.2s ease-in-out;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .card-menu:hover {
            transform: scale(1.03);
        }
        .icon {
            font-size: 40px;
        }
        .btn-lg {
            border-radius: 12px;
        }
        /* Navbar */
        .navbar-custom {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-custom px-4">
    <div class="container-fluid">
        <a class="navbar-brand fs-4" href="#">Adeeva Kitchen</a>
        <div class="d-flex">
            <span class="me-3 align-self-center">
                👋 Halo, <strong><?= isset($_SESSION['pembeli']) ? htmlspecialchars($_SESSION['pembeli']) : 'Pembeli' ?></strong>
            </span>
            <a href="logout.php" class="btn btn-warning btn-sm">🚪 Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="text-center mb-5">
        <h2>🛒 Selamat Datang di Halaman Pembeli</h2>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-md-4">
            <a href="pesanMenu.php" class="text-decoration-none text-dark">
                <div class="card card-menu p-4 text-center bg-white">
                    <div class="icon text-success mb-3">🍱</div>
                    <h4>Pesan Menu</h4>
                    <p class="text-muted">Pilih menu berdasarkan hari & atur jadwal pengiriman.</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="menu.php" class="text-decoration-none text-dark">
                <div class="card card-menu p-4 text-center bg-white">
                    <div class="icon text-primary mb-3">📋</div>
                    <h4>Lihat Daftar Menu</h4>
                    <p class="text-muted">Lihat semua menu yang tersedia untuk dipesan.</p>
                </div>
            </a>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="../index.php" class="btn btn-danger btn-lg">🔙 Kembali ke Halaman Utama</a>
    </div>
</div>
</body>
</html>
