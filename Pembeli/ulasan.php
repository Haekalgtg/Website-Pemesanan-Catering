<?php
include '../koneksi.php';
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}

$id_user = $_SESSION['id'];
$user_type = $_SESSION['role'];

if ($user_type !== 'pembeli') {
    echo "❌ Hanya pembeli yang dapat memberikan ulasan.";
    exit();
}

if (!isset($_GET['id_pesanan'])) {
    header("Location: pesanMenu.php");
    exit();
}

$id_pesanan = intval($_GET['id_pesanan']);

$cek = $koneksi->prepare("SELECT id FROM pesanan WHERE id = ? AND id_user = ?");
$cek->bind_param("ii", $id_pesanan, $id_user);
$cek->execute();
$res = $cek->get_result();

if ($res->num_rows === 0) {
    echo "❌ Pesanan tidak ditemukan atau bukan milik Anda.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $komentar = trim($_POST['komentar']);

    if ($rating < 1 || $rating > 5 || empty($komentar)) {
        echo "❌ Rating harus antara 1–5 dan komentar tidak boleh kosong.";
        exit();
    }

    $stmt = $koneksi->prepare("INSERT INTO ulasan (id_user, id_pesanan, rating, komentar) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $id_user, $id_pesanan, $rating, $komentar);
    $stmt->execute();

    header("Location: pesanMenu.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Beri Ulasan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">📝 Beri Ulasan Pesanan</h2>

    <form method="POST" class="col-md-6">
        <div class="mb-3">
            <label for="rating">Rating (1–5)</label>
            <select name="rating" class="form-select" id="rating" required>
                <option disabled selected>Pilih rating</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?> ⭐</option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="komentar">Komentar</label>
            <textarea name="komentar" id="komentar" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Kirim Ulasan</button>
        <a href="pesanMenu.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>