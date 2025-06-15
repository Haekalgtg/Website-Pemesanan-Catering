<?php
session_start();
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['id_pembeli'])) {
        header("Location: login.php");
        exit();
    }

    $id_pembeli = $_SESSION['id_pembeli'];
    $id_menu = $_POST['id_menu'];
    $jumlah = $_POST['jumlah'];
    $tanggal_kirim = $_POST['tanggal_kirim'];
    $alamat = $_POST['alamat'];

    // Ambil harga menu
    $stmt = $conn->prepare("SELECT price FROM menus WHERE id = ?");
    $stmt->bind_param("i", $id_menu);
    $stmt->execute();
    $result = $stmt->get_result();
    $menu = $result->fetch_assoc();

    if (!$menu) {
        echo "Menu tidak ditemukan.";
        exit();
    }

    $harga = $menu['price'];
    $total = $harga * $jumlah;

    // Simpan ke tabel pesanan
    $stmt = $conn->prepare("INSERT INTO pesanan (id_user, tanggal_pesan, total_harga) VALUES (?, NOW(), ?)");
    $stmt->bind_param("id", $id_pembeli, $total);
    $stmt->execute();
    $id_pesanan = $stmt->insert_id;

    // Simpan detail pesanan
    $stmt = $conn->prepare("INSERT INTO pesanan_detail (id_pesanan, id_menu, jumlah, subtotal) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $id_pesanan, $id_menu, $jumlah, $total);
    $stmt->execute();

    // Simpan jadwal pengiriman
    $stmt = $conn->prepare("INSERT INTO jadwal_pengiriman (id_pesanan, tanggal_kirim, alamat) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_pesanan, $tanggal_kirim, $alamat);
    $stmt->execute();

    // Simpan notifikasi pembeli
    $isi = "Pesanan Anda untuk menu ID $id_menu telah berhasil dikirim.";
    $stmt = $conn->prepare("INSERT INTO notifikasi_pembeli (id_pembeli, isi) VALUES (?, ?)");
    $stmt->bind_param("is", $id_pembeli, $isi);
    $stmt->execute();

    // Redirect ke form pembayaran
    header("Location: bayar.php?id=" . $id_pesanan);
    exit();
} else {
    header("Location: pesanMenu.php");
    exit();
}
?>
