<?php
include '../koneksi.php';

$id_pesanan = $_POST['id_pesanan'];
$metode = $_POST['metode_pembayaran'];
$bukti = '';

if ($_FILES['bukti_pembayaran']['name']) {
    $folder = "../uploads/";
    if (!is_dir($folder)) mkdir($folder);

    $nama_file = time() . "_" . basename($_FILES['bukti_pembayaran']['name']);
    $path = $folder . $nama_file;
    move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $path);
    $bukti = "uploads/" . $nama_file;
}

$query = "UPDATE pesanan SET metode_pembayaran='$metode', bukti_pembayaran='$bukti', status='diproses' WHERE id=$id_pesanan";
mysqli_query($conn, $query);

header("Location: homePembeli.php?pesan=berhasil_bayar");
