<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Beranda Pemilik Catering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5 text-center">
    <h2 class="mb-4">Selamat Datang di Sistem Katering</h2>
    <div class="d-grid gap-3 col-6 mx-auto">
        <a href="menu.php" class="btn btn-outline-primary btn-lg">📋 Lihat Daftar Menu</a>
        <a href="upload.php" class="btn btn-outline-success btn-lg">➕ Tambah Menu Baru</a>
        <a href="logout.php" class="btn btn-danger mt-4">Logout</a>
    </div>
</div>
</body>
</html>
