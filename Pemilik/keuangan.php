<?php
session_start();
include '../koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'penjual') {
    header("Location: ../index.php");
    exit();
}

$id_penjual = $_SESSION['id'];

if (isset($_POST['tambah'])) {
    $tanggal = $_POST['tanggal'];
    $keterangan = $_POST['keterangan'];
    $jenis = $_POST['jenis'];
    $jumlah = $_POST['jumlah'];

    $stmt = $koneksi->prepare("INSERT INTO keuangan (tanggal, keterangan, jenis, jumlah, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdi", $tanggal, $keterangan, $jenis, $jumlah, $id_penjual);
    $stmt->execute();
}

$query = $koneksi->query("SELECT * FROM keuangan WHERE created_by = $id_penjual ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">Adeeva Kitchen</a>
        <div>
            <a href="homePenjual.php" class="btn btn-outline-light btn-sm me-2">🏠 Beranda</a>
            <a href="../index.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<form method="post" class="row g-3 mb-4">
    <div class="col-md-2">
        <input type="date" name="tanggal" class="form-control" required>
    </div>
    <div class="col-md-3">
        <input type="text" name="keterangan" class="form-control" placeholder="Keterangan" required>
    </div>
    <div class="col-md-2">
        <select name="jenis" class="form-select" required>
            <option value="pemasukan">Pemasukan</option>
            <option value="pengeluaran">Pengeluaran</option>
        </select>
    </div>
    <div class="col-md-2">
        <input type="number" step="0.01" name="jumlah" class="form-control" placeholder="Jumlah" required>
    </div>
    <div class="col-md-2">
        <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Jenis</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0; ?>
        <?php while ($row = $query->fetch_assoc()): ?>
            <tr>
                <td><?= $row['tanggal'] ?></td>
                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                <td><?= ucfirst($row['jenis']) ?></td>
                <td>Rp <?= number_format($row['jumlah'], 2, ',', '.') ?></td>
            </tr>
            <?php
                $total += $row['jenis'] === 'pemasukan' ? $row['jumlah'] : -$row['jumlah'];
            ?>
        <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr class="table-secondary fw-bold">
            <td colspan="3">Total Saldo</td>
            <td>Rp <?= number_format($total, 2, ',', '.') ?></td>
        </tr>
    </tfoot>
</table>

</body>
</html>
