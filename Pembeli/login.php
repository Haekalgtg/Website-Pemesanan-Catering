<?php
include '../koneksi.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM pembeli WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $pembeli = $stmt->get_result()->fetch_assoc();

    if ($pembeli && password_verify($password, $pembeli['password'])) {
        $_SESSION['user_id'] = $pembeli['id'];
        $_SESSION['id_pembeli'] = $pembeli['id'];
        $_SESSION['pembeli'] = $pembeli['name'];
        $_SESSION['role'] = 'pembeli'; 

        header("Location: homePembeli.php");
    } else {
        $error = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Pembeli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 col-md-4">
    <h3 class="text-center">Login</h3>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input class="form-control mb-2" name="email" type="email" placeholder="Email" required>
        <input class="form-control mb-2" name="password" type="password" placeholder="Password" required>
        <button class="btn btn-success w-100" type="submit">Login</button>
    </form>

    <p class="text-center mt-3">
        Belum punya akun?
        <a href="registrasi.php" class="text-decoration-none">Daftar di sini</a>
    </p>
</div>
</body>
</html>
