
<?php
session_start();
$err = "";

$koneksi = new mysqli("sql110.infinityfree.com", "if0_39236930", "T9GazsQgsvDaKbT", "if0_39236930_db_catering");

if (isset($_POST['demo_login'])) {
    $_SESSION['user'] = 'demo';
    $_SESSION['id'] = 999;
    $_SESSION['role'] = 'pembeli';
    header("Location: Pembeli/homePembeli.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['demo_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT * FROM penjual WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($penjual = $result->fetch_assoc()) {
        $_SESSION['user'] = $penjual['username'];
        $_SESSION['id'] = $penjual['id'];
        $_SESSION['role'] = 'penjual';
        header("Location: Pemilik/homePenjual.php");
        exit;
    }

    $stmt = $koneksi->prepare("SELECT * FROM pembeli WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($pembeli = $result->fetch_assoc()) {
        $_SESSION['user'] = $pembeli['username'];
        $_SESSION['id'] = $pembeli['id'];
        $_SESSION['role'] = 'pembeli';
        header("Location: Pembeli/homePembeli.php");
        exit;
    }

    $err = "Login gagal. Username atau password salah.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Adeeva Kitchen</title>
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

<div class="container text-center shadow">
    <h1 class="mb-3">🍽️ Adeeva Kitchen</h1>
    <p class="lead"><strong>Makanan Selalu Fresh Setiap Harinya</strong></p>

    <form method="POST" class="mb-3">
        <input type="text" name="username" class="form-control mb-2" placeholder="Nama pengguna" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Kata sandi" required>
        <button type="submit" class="btn btn-success w-100">🔐 Login</button>
    </form>

    <?php if ($err): ?>
        <div class="alert alert-danger"><?= $err ?></div>
    <?php endif; ?>
    <form method="post" class="mb-2">
        <input type="hidden" name="demo_login" value="1">
        <button type="submit" class="btn btn-primary w-100">🛒 Masuk tanpa login</button>
    </form>

    <a href="daftar.php" class="btn btn-outline-secondary w-100">📝 Belum punya akun? Daftar</a>
</div>

</body>
</html>
