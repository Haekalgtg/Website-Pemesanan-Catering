<?php
session_start();
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['id_pembeli']) ||
    !isset($_SESSION['pembeli'])
) {
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <?php
    require_once '../koneksi.php'; 
    $id_pembeli = $_SESSION['id_pembeli'];
    $notifikasi_q = $conn->prepare("SELECT COUNT(*) as jumlah FROM notifikasi_pembeli WHERE id_pembeli = ? AND status = 'belum_dibaca'");
    $notifikasi_q->bind_param("i", $id_pembeli);
    $notifikasi_q->execute();
    $notifikasi_result = $notifikasi_q->get_result()->fetch_assoc();
    $jumlah_notifikasi = $notifikasi_result['jumlah'];
    ?>

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
<nav class="navbar navbar-expand-lg navbar-light navbar-custom mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Adeeva Kitchen</a>

        <div class="ms-auto d-flex align-items-center">
            <a href="lihat_notifikasi.php" class="btn btn-outline-secondary position-relative me-3">
                🔔
                <?php if ($jumlah_notifikasi > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $jumlah_notifikasi ?>
                    </span>
                <?php endif; ?>
            </a>

            <span class="me-3">👋 Halo, <strong><?= htmlspecialchars($_SESSION['pembeli']) ?></strong></span>

            <a href="logout.php" class="btn btn-warning btn-sm">📒 Logout</a>
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
