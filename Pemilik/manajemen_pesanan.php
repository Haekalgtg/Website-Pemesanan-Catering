<?php
session_start();
include 'koneksi.php';

$err = "";

// Logout jika diminta
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Admin (penjual)
    if ($username === 'admin' && $password === '12345') {
        $_SESSION['user'] = 'admin';
        $_SESSION['id'] = 0;
        $_SESSION['role'] = 'penjual';
        header("Location: index.php");
        exit;
    }

    // Pembeli dari database
    $stmt = $koneksi->prepare("SELECT * FROM pembeli WHERE name = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $_SESSION['user'] = $userData['name'];
        $_SESSION['id'] = $userData['id'];
        $_SESSION['role'] = 'pembeli';
        header("Location: index.php");
        exit;
    } else {
        $err = "Login gagal. Silakan coba lagi.";
    }
}

// Update atau hapus pesanan (jika penjual)
if (isset($_SESSION['role']) && $_SESSION['role'] == 'penjual') {
    $id_penyedia = $_SESSION['id'];

    if (isset($_POST['update_status'])) {
        $id = $_POST['id'];
        $status = $_POST['status'];
        $koneksi->query("UPDATE pesanan SET status='$status' WHERE id=$id");

        if ($status === 'dikirim') {
            $result = $koneksi->query("SELECT total_harga FROM pesanan WHERE id=$id");
            $pesanan = $result->fetch_assoc();
            $jumlah = $pesanan['total_harga'];

            $koneksi->query("INSERT INTO keuangan (tanggal, keterangan, jenis, jumlah, created_by) 
                             VALUES (NOW(), 'Pemasukan dari pesanan #$id', 'pemasukan', $jumlah, $id_penyedia)");
        }
    }

    if (isset($_POST['hapus_pesanan'])) {
        $id = $_POST['id'];
        $koneksi->query("DELETE FROM pesanan WHERE id=$id");
    }

    // Ambil data pesanan
    $query = "SELECT p.id, b.name AS nama_konsumen, m.name AS nama_menu, p.tanggal_pesan, 
                     j.tanggal_kirim AS jadwal_pengiriman, p.status
              FROM pesanan p
              JOIN pembeli b ON p.id_user = b.id
              JOIN pesanan_detail pd ON p.id = pd.id_pesanan
              JOIN menus m ON pd.id_menu = m.id
              JOIN jadwal_pengiriman j ON p.id = j.id_pesanan
              WHERE m.user_id = $id_penyedia
              ORDER BY p.tanggal_pesan DESC";
    $result = $koneksi->query($query);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adeevea Kitchen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('makanan.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }
        .container-box {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-top: 50px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center flex-column">

<div class="container col-md-8 container-box">

    <?php if (!isset($_SESSION['role'])): ?>
        <h2 class="text-center mb-4">🍽️ Selamat Datang di Adeeva Kitchen</h2>

        <form method="POST" class="mb-3">
            <input type="text" name="username" class="form-control mb-2" placeholder="Nama pengguna" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Kata sandi" required>
            <button type="submit" name="login" class="btn btn-primary w-100">🔐 Login</button>
        </form>

        <?php if ($err): ?>
            <div class="alert alert-danger"><?= $err ?></div>
        <?php endif; ?>

        <div class="d-grid gap-2 mt-3">
            <a href="?role=guest" class="btn btn-success">🛒 Masuk tanpa login</a>
            <a href="daftar.php" class="btn btn-outline-secondary">📝 Belum punya akun? Daftar</a>
        </div>

    <?php elseif ($_SESSION['role'] === 'penjual'): ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>📋 Manajemen Pesanan (Penjual)</h3>
            <a href="?logout=true" class="btn btn-danger btn-sm">Logout</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Konsumen</th>
                        <th>Menu</th>
                        <th>Tanggal Pesan</th>
                        <th>Jadwal Kirim</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($pesanan = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $pesanan['id'] ?></td>
                        <td><?= $pesanan['nama_konsumen'] ?></td>
                        <td><?= $pesanan['nama_menu'] ?></td>
                        <td><?= $pesanan['tanggal_pesan'] ?></td>
                        <td><?= $pesanan['jadwal_pengiriman'] ?></td>
                        <td>
                            <form method="post" class="d-flex gap-2 justify-content-center">
                                <input type="hidden" name="id" value="<?= $pesanan['id'] ?>">
                                <select name="status" class="form-select form-select-sm w-auto">
                                    <?php
                                    $statuses = ['menunggu', 'diproses', 'dikirim', 'selesai'];
                                    foreach ($statuses as $status) {
                                        echo "<option value=\"$status\" " . ($pesanan['status'] == $status ? 'selected' : '') . ">$status</option>";
                                    }
                                    ?>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">✔</button>
                            </form>
                        </td>
                        <td>
                            <?= $pesanan['metode_pembayaran'] ?><br>
                            <?php if ($pesanan['bukti_pembayaran']): ?>
                                <a href="../<?= $pesanan['bukti_pembayaran'] ?>" target="_blank">Lihat Bukti</a>
                            <?php else: ?>
                                <small class="text-muted">Belum ada</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" onsubmit="return confirm('Hapus pesanan ini?')">
                                <input type="hidden" name="id" value="<?= $pesanan['id'] ?>">
                                <button type="submit" name="hapus_pesanan" class="btn btn-sm btn-danger">🗑️</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($_SESSION['role'] === 'pembeli' || isset($_GET['role']) && $_GET['role'] === 'guest'): ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>👤 Halo, <?= $_SESSION['user'] ?? 'Tamu' ?> (Pembeli)</h3>
            <a href="?logout=true" class="btn btn-danger btn-sm">Logout</a>
        </div>
        <p>Ini adalah halaman beranda pembeli. Anda bisa menambahkan fitur seperti melihat menu, keranjang, dll.</p>
    <?php endif; ?>
</div>

</body>
</html>
