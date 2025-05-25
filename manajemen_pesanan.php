<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'penyedia') {
    header("Location: login.php");
    exit;
}

$id_penyedia = $_SESSION['user']['id'];

// Update status pesanan
if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $koneksi->query("UPDATE pesanan SET status='$status' WHERE id=$id");
}

// Hapus pesanan
if (isset($_POST['hapus_pesanan'])) {
    $id = $_POST['id'];
    $koneksi->query("DELETE FROM pesanan WHERE id=$id");
}

// Ambil pesanan yang berkaitan dengan penyedia ini
$query = "SELECT p.*, m.nama_menu, u.nama AS nama_konsumen 
          FROM pesanan p 
          JOIN menu m ON p.id_menu = m.id 
          JOIN users u ON p.id_konsumen = u.id 
          WHERE m.id_penyedia = $id_penyedia 
          ORDER BY p.tanggal_pesan DESC";
$result = $koneksi->query($query);
?>

<h2>Manajemen Pesanan</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nama Konsumen</th>
        <th>Menu</th>
        <th>Tgl Pesan</th>
        <th>Jadwal Kirim</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php while ($pesanan = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $pesanan['id'] ?></td>
            <td><?= $pesanan['nama_konsumen'] ?></td>
            <td><?= $pesanan['nama_menu'] ?></td>
            <td><?= $pesanan['tanggal_pesan'] ?></td>
            <td><?= $pesanan['jadwal_pengiriman'] ?></td>
            <td>
                <form method="post">
                    <input type="hidden" name="id" value="<?= $pesanan['id'] ?>">
                    <select name="status">
                        <option value="Menunggu" <?= $pesanan['status'] == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                        <option value="Diproses" <?= $pesanan['status'] == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                        <option value="Dikirim" <?= $pesanan['status'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                        <option value="Selesai" <?= $pesanan['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                    <button type="submit" name="update_status">Update</button>
                </form>
            </td>
            <td>
                <form method="post" onsubmit="return confirm('Hapus pesanan ini?')">
                    <input type="hidden" name="id" value="<?= $pesanan['id'] ?>">
                    <button type="submit" name="hapus_pesanan">Hapus</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>
