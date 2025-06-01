<?php
session_start();
$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Login sebagai admin / penjual (sementara hardcode)
    if ($username === 'admin' && $password === '12345') {
        $_SESSION['user'] = 'admin';
        $_SESSION['id'] = 0; // id dummy (karena tidak dari DB)
        $_SESSION['role'] = 'penjual';
        header("Location: Pemilik/homePenjual.php");
        exit;
    }

    // Login sebagai pembeli dari database
    $conn = new mysqli("localhost", "root", "", "db_catering");
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM pembeli WHERE name = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        $_SESSION['user'] = $userData['name'];
        $_SESSION['id'] = $userData['id'];
        $_SESSION['role'] = 'pembeli';
        header("Location: Pembeli/homePembeli.php");
        exit;
    } else {
        $err = "Login gagal. Coba lagi.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Selamat Datang di Sistem Katering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('makanan.jpg'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9); 
            border-radius: 15px;
            padding: 30px;
            max-width: 500px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

<div class="container text-center">
    <h1 class="mb-3">🍽️ Adeeva Kitchen</h1>
    <p class="lead">Masuk sebagai penjual atau pembeli</p>

    <form method="POST" class="mb-3">
        <input type="text" name="username" class="form-control mb-2" placeholder="Nama pengguna" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Kata sandi" required>
        <button type="submit" class="btn btn-primary w-100">🔐 Login</button>
    </form>

    <?php if ($err): ?>
        <div class="alert alert-danger"><?= $err ?></div>
    <?php endif; ?>

    <div class="d-grid gap-2 mt-3">
        <a href="Pembeli/homePembeli.php" class="btn btn-success">🛒 Masuk tanpa login</a>
        <a href="Pembeli/daftar.php" class="btn btn-outline-secondary">📝 Belum punya akun? Daftar</a>
    </div>
</div>

</body>
</html>
