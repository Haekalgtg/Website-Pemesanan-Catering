<?php
session_start();
include '../koneksi.php';

// Cek login pembeli
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../index.php");
    exit();
}

// Ambil hari ini (contoh default)
$hari_ini = date('l'); // e.g. 'Monday'
$map_hari = [
    'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
];
$hari = $map_hari[$hari_ini];

// Ambil menu berdasarkan hari
$menus = $koneksi->query("SELECT menus.*, penjual.name as penjual FROM menus JOIN penjual ON penjual.id = menus.user_id WHERE day = '$hari'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Menu - <?= $hari ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h2>Menu Hari <?= $hari ?></h2>
    <p>Pilih makanan untuk dipesan hari ini atau hari lain.</p>

    <div class="row">
        <?php while ($menu = $menus->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <?php if ($menu['image']): ?>
                        <img src="../uploads/<?= $menu['image'] ?>" class="card-img-top" alt="menu">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= $menu['name'] ?></h5>
                        <p class="card-text"><?= $menu['description'] ?></p>
                        <p class="text-muted">Rp <?= number_format($menu['price'], 0, ',', '.') ?></p>
                        <form method="post" action="proses_pesan.php">
                            <input type="hidden" name="id_menu" value="<?= $menu['id'] ?>">
                            <input type="number" name="jumlah" class="form-control mb-2" value="1" min="1" required>
                            <label>Tanggal Kirim:</label>
                            <input type="date" name="tanggal_kirim" class="form-control mb-2" required>
                            <label>Alamat Kirim:</label>
                            <textarea name="alamat" class="form-control mb-2" required></textarea>
                            <a href="pesan.php?id=<?= $menu['id'] ?>" class="btn btn-primary w-100">Pesan Sekarang</a>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
