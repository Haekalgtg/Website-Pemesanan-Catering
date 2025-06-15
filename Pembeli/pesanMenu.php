<?php
session_start();
include '../koneksi.php';

// Cek login pembeli
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../index.php");
    exit();
}

$id_pembeli = $_SESSION['id'];

// Hari dalam bahasa Indonesia
$hari_ini = date('l');
$map_hari = [
    'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
];
$hari = $map_hari[$hari_ini];

// Inisialisasi keranjang
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Hapus item dari keranjang
if (isset($_GET['hapus'])) {
    $index = intval($_GET['hapus']);
    if (isset($_SESSION['keranjang'][$index])) {
        unset($_SESSION['keranjang'][$index]);
        $_SESSION['keranjang'] = array_values($_SESSION['keranjang']);
    }
}

// Tambah item ke keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $id_menu = intval($_POST['id_menu']);
    $jumlah = intval($_POST['jumlah']);
    $tanggal_kirim = $_POST['tanggal_kirim'];
    $alamat = trim($_POST['alamat']);

    if ($jumlah < 1 || empty($tanggal_kirim) || empty($alamat)) {
        $error = "Semua field wajib diisi.";
    } else {
        $menu = $koneksi->query("SELECT * FROM menus WHERE id = $id_menu")->fetch_assoc();
        if ($menu) {
            $_SESSION['keranjang'][] = [
                'id_menu' => $id_menu,
                'jumlah' => $jumlah,
                'tanggal_kirim' => $tanggal_kirim,
                'alamat' => $alamat,
                'name' => $menu['name'],
                'price' => $menu['price']
            ];
        }
    }
}

// Ambil menu sesuai hari
$menus = $koneksi->query("SELECT menus.*, penjual.username AS penjual 
                       FROM menus 
                       JOIN penjual ON penjual.id = menus.user_id 
                       WHERE menus.day = '$hari'");
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
    <p>Pilih makanan untuk ditambahkan ke daftar pesanan.</p>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Kotak Tambah Pesan -->
        <div class="col-md-4 mb-4">
            <a href="pesan.php" style="text-decoration: none;">
                <div class="card h-100 border-primary text-center d-flex justify-content-center align-items-center p-4">
                    <div>
                        <h5 class="text-primary">+ Tambah Pesan</h5>
                        <p class="text-muted">Lihat detail atau riwayat pesanan Anda</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Daftar Menu Hari Ini -->
        <?php while ($menu = $menus->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if ($menu['image']): ?>
                        <img src="../uploads/<?= htmlspecialchars($menu['image']) ?>" class="card-img-top" alt="menu">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($menu['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($menu['description']) ?></p>
                        <p class="text-muted">Dibuat oleh: <?= htmlspecialchars($menu['penjual']) ?></p>
                        <p class="text-success fw-bold">Rp <?= number_format($menu['price'], 0, ',', '.') ?></p>

                        <form method="post" class="mt-auto">
                            <input type="hidden" name="id_menu" value="<?= $menu['id'] ?>">
                            <div class="mb-2">
                                <label>Jumlah:</label>
                                <input type="number" name="jumlah" class="form-control" value="1" min="1" required>
                            </div>
                            <div class="mb-2">
                                <label>Tanggal Kirim:</label>
                                <input type="date" name="tanggal_kirim" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label>Alamat Kirim:</label>
                                <textarea name="alamat" class="form-control" required></textarea>
                            </div>
                            <button type="submit" name="tambah" class="btn btn-success w-100">Tambah Pesan</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <hr class="my-4">

    <h4>Daftar Pesanan Anda</h4>
    <?php if (!empty($_SESSION['keranjang'])): ?>
        <ul class="list-group mb-3">
            <?php 
            $total = 0;
            foreach ($_SESSION['keranjang'] as $index => $item): 
                $subtotal = $item['price'] * $item['jumlah'];
                $total += $subtotal;
            ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="me-auto">
                        <div><strong><?= htmlspecialchars($item['name']) ?></strong> x<?= $item['jumlah'] ?></div>
                        <div><small><?= htmlspecialchars($item['alamat']) ?> (<?= $item['tanggal_kirim'] ?>)</small></div>
                    </div>
                    <div class="text-end">
                        <div>Rp <?= number_format($subtotal, 0, ',', '.') ?></div>
                        <a href="?hapus=<?= $index ?>" class="btn btn-sm btn-outline-danger mt-1">Hapus</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="d-flex justify-content-between align-items-center">
            <h5>Total: Rp <?= number_format($total, 0, ',', '.') ?></h5>
            <a href="pembayaran.php" class="btn btn-primary">Bayar</a>
        </div>
    <?php else: ?>
        <p>Belum ada menu yang ditambahkan.</p>
    <?php endif; ?>
</body>
</html>
