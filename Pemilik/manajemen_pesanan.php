<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'penjual') {
    header("Location: ../index.php");
    exit;
}

$id_penyedia = $_SESSION['id'];

if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $koneksi->query("UPDATE pesanan SET status='$status' WHERE id=$id");
}

if (isset($_POST['hapus_pesanan'])) {
    $id = $_POST['id'];
    $koneksi->query("DELETE FROM pesanan WHERE id=$id");
}

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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2 class="mb-4">📋 Manajemen Pesanan</h2>

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
                                    <option value="menunggu" <?= $pesanan['status'] == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                    <option value="diproses" <?= $pesanan['status'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                    <option value="dikirim" <?= $pesanan['status'] == 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                    <option value="selesai" <?= $pesanan['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">✔</button>
                            </form>
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
</body>
</html>
