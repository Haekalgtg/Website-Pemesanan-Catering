<?php
include '../conn.php';
session_start();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $day = $_POST['day'];
    $image = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "upload/" . $image);

    $stmt = $conn->prepare("INSERT INTO menus (user_id, name, description, price, image, day) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdss", $user_id, $name, $desc, $price, $image, $day);
    $stmt->execute();
    header("Location: homePenjual.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 col-md-6">
    <h3>Upload Menu Harian</h3>
    <form method="POST" enctype="multipart/form-data">
        <input class="form-control mb-2" name="name" placeholder="Nama Menu" required>
        <textarea class="form-control mb-2" name="description" placeholder="Deskripsi" required></textarea>
        <input class="form-control mb-2" name="price" type="number" placeholder="Harga" required>
        <select class="form-select mb-2" name="day" required>
            <option disabled selected>Pilih Hari</option>
            <option value="Senin">Senin</option>
            <option value="Selasa">Selasa</option>
            <option value="Rabu">Rabu</option>
            <option value="Kamis">Kamis</option>
            <option value="Jumat">Jumat</option>
            <option value="Sabtu">Sabtu</option>
        </select>
        <input class="form-control mb-2" type="file" name="image" required>
        <button class="btn btn-primary w-100" type="submit">Upload</button>
    </form>
</div>
</body>
</html>
