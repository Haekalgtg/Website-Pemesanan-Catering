<?php
session_start();
include '../conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Proses hapus jika ada parameter delete
if (isset($_GET['delete'])) {
    $menu_id = intval($_GET['delete']);

    // Pastikan menu milik user yang login
    $cek = $conn->prepare("SELECT image FROM menus WHERE id = ? AND user_id = ?");
    $cek->bind_param("ii", $menu_id, $user_id);
    $cek->execute();
    $result = $cek->get_result();

    if ($row = $result->fetch_assoc()) {
        if (file_exists("uploads/" . $row['image'])) {
            unlink("uploads/" . $row['image']);
        }

        $del = $conn->prepare("DELETE FROM menus WHERE id = ? AND user_id = ?");
        $del->bind_param("ii", $menu_id, $user_id);
        $del->execute();
        header("Location: menu.php");
        exit();
    }
}

// Ambil semua menu user berdasarkan hari Senin–Sabtu
$hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$allMenus = [];
foreach ($hariList as $hari) {
    $stmt = $conn->prepare("SELECT * FROM menus WHERE day = ? AND user_id = ?");
    $stmt->bind_param("si", $hari, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $allMenus[$hari] = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Menu Mingguan Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9fbfd;
        }
        .card {
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .hari-section {
            margin-top: 40px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">Adeeva Kitchen</a>
        <div>
            <a href="home.php" class="btn btn-outline-light btn-sm me-2">🏠 Beranda</a>
            <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <h2 class="text-center mb-4">Menu Mingguan Saya</h2>

    <?php foreach ($hariList as $hari): ?>
        <div class="hari-section">
            <h4 class="text-primary mb-3"><?= $hari ?></h4>
            <?php if (count($allMenus[$hari]) === 0): ?>
                <div class="alert alert-secondary">Belum ada menu untuk hari <?= $hari ?>.</div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($allMenus[$hari] as $menu): ?>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <img src="upload/<?= htmlspecialchars($menu['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($menu['name']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($menu['name']) ?></h5>
                                <p class="card-text"><?= nl2br(htmlspecialchars($menu['description'])) ?></p>
                                <p class="fw-bold text-success">Rp<?= number_format($menu['price'], 0, ',', '.') ?></p>
                                <a href="menu.php?delete=<?= $menu['id'] ?>" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Yakin ingin menghapus menu ini?')">Hapus</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
