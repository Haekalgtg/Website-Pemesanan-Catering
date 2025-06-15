<?php
session_start();
include '../koneksi.php';

$id_pembeli = $_SESSION['id']; // pastikan session id_pembeli sudah diset
$id_pesanan = $_GET['id'];     // ID pesanan dari URL

$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id = $id_pesanan AND id_user = $id_pembeli");
$pesanan = mysqli_fetch_assoc($query);
?>

<h2>Pembayaran Pesanan</h2>
<form action="proses_bayar.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_pesanan" value="<?= $id_pesanan ?>">

    <p>Total Harga: Rp<?= number_format($pesanan['total_harga']) ?></p>

    <label>Metode Pembayaran</label>
    <select name="metode_pembayaran" required>
        <option value="Transfer Bank">Transfer Bank</option>
        <option value="COD">Cash on Delivery</option>
    </select>

    <label>Upload Bukti Transfer</label>
    <input type="file" name="bukti_pembayaran" accept="image/*">

    <button type="submit">Kirim Pembayaran</button>
</form>
