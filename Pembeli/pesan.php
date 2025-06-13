<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Menu tidak ditemukan.";
    exit();
}

$id_menu = intval($_GET['id']);
$query = $koneksi->prepare("SELECT menus.*, penjual.name as penjual FROM menus JOIN penjual ON penjual.id = menus.user_id WHERE menus.id = ?");
$query->bind_param("i", $id_menu);
$query->execute();
$result = $query->get_result();
$menu = $result->fetch_assoc();

if (!$menu) {
    echo "Menu tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pesan Menu - <?= htmlspecialchars($menu['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4 col-md-6">
    <h3>Pesan Menu: <?= htmlspecialchars($menu['name']) ?></h3>
    <div class="card mb-3">
        <?php if ($menu['image']): ?>
            <img src="../uploads/<?= htmlspecialchars($menu['image']) ?>" class="card-img-top" alt="menu">
        <?php endif; ?>
        <div class="card-body">
            <p class="card-text"><?= htmlspecialchars($menu['description']) ?></p>
            <p class="text-muted">Dibuat oleh: <?= htmlspecialchars($menu['penjual']) ?></p>
            <p class="text-success fw-bold">Rp <?= number_format($menu['price'], 0, ',', '.') ?></p>
        </div>
    </div>

    <form method="post" action="proses_pesan.php">
        <input type="hidden" name="id_menu" value="<?= $menu['id'] ?>">
        <label>Jumlah:</label>
        <input type="number" name="jumlah" class="form-control mb-2" value="1" min="1" required>
        <label>Tanggal Kirim:</label>
        <input type="date" name="tanggal_kirim" class="form-control mb-2" required>
        <label>Alamat Kirim:</label>
        <textarea name="alamat" class="form-control mb-2" required></textarea>
        <button type="submit" class="btn btn-success w-100">Konfirmasi Pesanan</button>
    </form>
</div>
</body>
</html>
