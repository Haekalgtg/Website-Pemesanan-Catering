<?php
$err = $sukses = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

$koneksi = new mysqli("sql110.infinityfree.com", "if0_39236930", "T9GazsQgsvDaKbT", "if0_39236930_db_catering");

    $cek = $koneksi->prepare("SELECT * FROM pembeli WHERE username = ?");
    $cek->bind_param("s", $username);
    $cek->execute();
    $res = $cek->get_result();

    if ($res->num_rows > 0) {
        $err = "Nama pengguna sudah digunakan.";
    } else {
        $stmt = $koneksi->prepare("INSERT INTO pembeli (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            $sukses = "Berhasil daftar! Silakan login.";
        } else {
            $err = "Gagal daftar. Coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun Pembeli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">📝 Daftar Akun Pembeli</h2>
    
    <form method="POST">
        <input type="text" name="username" class="form-control mb-3" placeholder="Nama pengguna" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Kata sandi" required>
        <button type="submit" class="btn btn-primary w-100">Daftar</button>
    </form>

    <?php if ($err): ?>
        <div class="alert alert-danger mt-3"><?= $err ?></div>
    <?php elseif ($sukses): ?>
        <div class="alert alert-success mt-3"><?= $sukses ?></div>
    <?php endif; ?>

    <div class="mt-5 text-center">
        <a href="index.php">🔙 Kembali ke login</a>
    </div>
</div>

</body>
</html>
