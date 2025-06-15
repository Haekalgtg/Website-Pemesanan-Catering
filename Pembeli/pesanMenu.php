<?php
session_start();
include '../koneksi.php';

// Cek login pembeli
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../index.php");
    exit();
}

$id_pembeli = $_SESSION['id'];

// Ambil hari ini dalam bahasa Indonesia
$hari_ini = date('l');
$map_hari = [
    'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
];
$hari = $map_hari[$hari_ini];

<<<<<<< HEAD
// Ambil menu berdasarkan hari
$menus = $conn->query("SELECT menus.*, penjual.name as penjual FROM menus JOIN penjual ON penjual.id = menus.user_id WHERE day = '$hari'");
=======
// Proses jika tombol pesan ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_menu = intval($_POST['id_menu']);
    $jumlah = intval($_POST['jumlah']);
    $tanggal_kirim = $_POST['tanggal_kirim'];
    $alamat = trim($_POST['alamat']);

    if ($jumlah < 1 || empty($tanggal_kirim) || empty($alamat)) {
        $error = "Semua field wajib diisi.";
    } else {
        // Ambil data menu
        $menu = $koneksi->query("SELECT * FROM menus WHERE id = $id_menu")->fetch_assoc();
        if (!$menu) {
            $error = "Menu tidak ditemukan.";
        } else {
            $subtotal = $menu['price'] * $jumlah;

            // Buat pesanan
            $koneksi->query("INSERT INTO pesanan (id_user, tanggal_pesan, total_harga) 
                             VALUES ($id_pembeli, CURDATE(), $subtotal)");
            $id_pesanan = $koneksi->insert_id;

            // Detail menu
            $koneksi->query("INSERT INTO pesanan_detail (id_pesanan, id_menu, jumlah, subtotal) 
                             VALUES ($id_pesanan, $id_menu, $jumlah, $subtotal)");

            // Jadwal pengiriman (waktu default 12:00:00 siang)
            $koneksi->query("INSERT INTO jadwal_pengiriman (id_pesanan, tanggal_kirim, waktu_kirim, alamat) 
                             VALUES ($id_pesanan, '$tanggal_kirim', '12:00:00', '$alamat')");

            // Redirect ke pesan.php untuk melihat ringkasan
            header("Location: pesan.php?id=$id_pesanan");
            exit();
        }
    }
}

// Ambil menu hari ini
$menus = $koneksi->query("SELECT menus.*, penjual.username AS penjual 
                          FROM menus 
                          JOIN penjual ON penjual.id = menus.user_id 
                          WHERE menus.day = '$hari'");
>>>>>>> 01fd5b3490fe86772839125cd5ca72e1d1fd555c
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
    <p>Pilih makanan untuk dipesan hari ini.</p>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row">
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
                            <button type="submit" class="btn btn-primary w-100">Pesan Sekarang</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
