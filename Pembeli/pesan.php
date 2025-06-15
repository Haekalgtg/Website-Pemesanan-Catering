<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID pesanan tidak valid.";
    exit();
}

$id_pesanan = intval($_GET['id']);
$id_pembeli = $_SESSION['id'];

// Ambil data pesanan
$query = $koneksi->query("SELECT * FROM pesanan WHERE id = $id_pesanan AND id_user = $id_pembeli");
$pesanan = $query->fetch_assoc();

if (!$pesanan) {
    echo "Pesanan tidak ditemukan.";
    exit();
}

// Ambil detail dan jadwal
$detail = $koneksi->query("SELECT menus.name, menus.image, pesanan_detail.* 
                           FROM pesanan_detail 
                           JOIN menus ON menus.id = pesanan_detail.id_menu 
                           WHERE id_pesanan = $id_pesanan")->fetch_assoc();

$jadwal = $koneksi->query("SELECT * FROM jadwal_pengiriman WHERE id_pesanan = $id_pesanan")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rincian Pesanan #<?= $pesanan['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Rincian Pesanan #<?= $pesanan['id'] ?></h4>
                </div>
                <div class="card-body">

                    <!-- Gambar menu -->
                    <?php if (!empty($detail['image'])): ?>
                        <div class="text-center mb-3">
                            <img src="../uploads/<?= htmlspecialchars($detail['image']) ?>" class="img-fluid rounded" style="max-height: 250px;" alt="Gambar Menu">
                        </div>
                    <?php endif; ?>

                    <!-- Informasi Pesanan -->
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Nama Menu:</strong> <?= htmlspecialchars($detail['name']) ?></li>
                        <li class="list-group-item"><strong>Jumlah:</strong> <?= $detail['jumlah'] ?> porsi</li>
                        <li class="list-group-item"><strong>Total Harga:</strong> Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></li>
                        <li class="list-group-item"><strong>Tanggal Kirim:</strong> <?= htmlspecialchars($jadwal['tanggal_kirim']) ?></li>
                        <li class="list-group-item"><strong>Alamat Pengiriman:</strong> <?= htmlspecialchars($jadwal['alamat']) ?></li>
                        <li class="list-group-item"><strong>Status Pesanan:</strong> 
                            <span class="badge bg-secondary"><?= ucfirst($pesanan['status']) ?></span>
                        </li>
                    </ul>

                    <div class="mt-3 text-end">
                        <?php if (!$pesanan['bukti_pembayaran']): ?>
                            <a href="bayar.php?id=<?= $pesanan['id'] ?>" class="btn btn-warning">Upload Pembayaran</a>
                        <?php else: ?>
                            <a href="../<?= $pesanan['bukti_pembayaran'] ?>" target="_blank">Lihat Bukti Pembayaran</a>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="card-footer text-muted text-end">
                    Dipesan pada: <?= $pesanan['created_at'] ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
