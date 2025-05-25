<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Beranda Pembeli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5 text-center">
    <h2 class="mb-4">🛒 Selamat Datang di Menu Pembeli</h2>

    <?php if (isset($_SESSION['pembeli'])): ?>
        <p class="text-success">👋 Halo, <?= htmlspecialchars($_SESSION['pembeli']) ?>! Anda sudah login.</p>
    <?php else: ?>
        <p class="text-muted">Anda belum login. Login untuk menikmati fitur tambahan.</p>
    <?php endif; ?>

    <div class="d-grid gap-3 col-6 mx-auto mt-4">
        <a href="menu.php" class="btn btn-outline-primary btn-lg">📋 Lihat Daftar Menu</a>

        <?php if (!isset($_SESSION['pembeli'])): ?>
            <a href="login.php" class="btn btn-outline-success btn-lg">🔑 Login</a>
        <?php else: ?>
            <a href="logout.php" class="btn btn-warning btn-lg">🚪 Logout</a>
        <?php endif; ?>

        <a href="index.php" class="btn btn-danger mt-4">🔙 Kembali ke Halaman Utama</a>
    </div>
</div>
</body>
</html>
