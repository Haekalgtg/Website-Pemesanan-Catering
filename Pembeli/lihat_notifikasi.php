<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['id_pembeli'])) {
    header("Location: login.php");
    exit();
}

$id_pembeli = $_SESSION['id_pembeli'];

// Ambil notifikasi
$stmt = $conn->prepare("SELECT * FROM notifikasi_pembeli WHERE id_pembeli = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $id_pembeli);
$stmt->execute();
$notif_result = $stmt->get_result();

$update_stmt = $conn->prepare("UPDATE notifikasi_pembeli SET status = 'dibaca' WHERE id_pembeli = ?");
if ($update_stmt) {
    $update_stmt->bind_param("i", $id_pembeli);
    $update_stmt->execute();
    $update_stmt->close();
} else {
    die("Gagal mempersiapkan query update notifikasi: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifikasi Pembeli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container py-4">
    <h2>📩 Notifikasi Anda</h2>
    <ul class="list-group">
        <?php while ($row = $notif_result->fetch_assoc()): ?>
            <li class="list-group-item">
                <?= htmlspecialchars($row['isi']) ?> <br>
                <small class="text-muted"><?= $row['created_at'] ?></small>
            </li>
        <?php endwhile; ?>
    </ul>
    <a href="homePembeli.php" class="btn btn-secondary mt-3">🔙 Kembali</a>
</body>
</html>
