<?php
include '../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO pembeli (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    $stmt->execute();
    header("Location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 col-md-4">
    <h3>Registrasi Pelanggan Catering</h3>
    <form method="POST">
        <input class="form-control mb-2" name="name" placeholder="Nama">
        <input class="form-control mb-2" name="email" type="email" placeholder="Email">
        <input class="form-control mb-2" name="password" type="password" placeholder="Password">
        <button class="btn btn-primary w-100" type="submit">Daftar</button>
    </form>
</div>
</body>
</html>
