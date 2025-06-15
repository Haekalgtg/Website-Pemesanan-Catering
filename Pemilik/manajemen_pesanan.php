<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $koneksi->query("UPDATE pesanan SET status='$status' WHERE id=$id");
}

$pesanan = $koneksi->query("
    SELECT p.*, u.username, GROUP_CONCAT(m.name, ' (x', d.jumlah, ')') AS daftar_menu
    FROM pesanan p
    JOIN pembeli u ON p.id_user = u.id
    JOIN pesanan_detail d ON p.id = d.id_pesanan
    JOIN menus m ON d.id_menu = m.id
    GROUP BY p.id
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-badge {
            text-transform: capitalize;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .menunggu { background-color: #ffc107; color: #000; }
        .diproses { background-color: #0d6efd; color: #fff; }
        .dikirim { background-color: #17a2b8; color: #fff; }
        .selesai { background-color: #28a745; color: #fff; }
        .dibatalkan { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4 text-center text-primary">Manajemen Pesanan</h2>
    <a href="homePenjual.php" class="btn btn-secondary mb-3">← Kembali ke Beranda</a>

    <div class="table-responsive">
    <table class="table table-bordered bg-white table-hover align-middle">
        <thead class="table-primary text-center">
            <tr>
                <th>Pembeli</th>
                <th>Tanggal</th>
                <th>Menu</th>
                <th>Total</th>
                <th>Status</th>
                <th>Ubah Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $pesanan->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= $row['tanggal_pesan'] ?></td>
                <td><?= $row['daftar_menu'] ?></td>
                <td>Rp<?= number_format($row['total_harga'], 2, ',', '.') ?></td>
                <td class="text-center">
                    <span class="status-badge <?= $row['status'] ?>"><?= $row['status'] ?></span>
                </td>
                <td>
                    <form method="POST" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <select name="status" class="form-select form-select-sm" required>
                            <?php
                            $opsi = ['menunggu','diproses','dikirim','selesai','dibatalkan'];
                            foreach ($opsi as $s) {
                                $selected = ($row['status'] == $s) ? 'selected' : '';
                                echo "<option value='$s' $selected>$s</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-success">✔</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>

</body>

</html>
