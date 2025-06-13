<?php
$err = $sukses = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['name'];
    $password = $_POST['password'];

    $conn = new mysqli("localhost", "root", "", "db_catering");
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $cek = $conn->prepare("SELECT * FROM pembeli WHERE name = ?");
    $cek->bind_param("s", $nama);
    $cek->execute();
    $res = $cek->get_result();

    if ($res->num_rows > 0) {
        $err = "Nama pengguna sudah digunakan.";
    } else {
        $stmt = $conn->prepare("INSERT INTO pembeli (name, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama, $password);
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
        <input type="text" name="name" class="form-control mb-3" placeholder="Nama pengguna" required>
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
