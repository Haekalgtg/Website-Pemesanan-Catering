<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'koneksi.php'; // koneksi ke database

header('Content-Type: application/json');

$response = ['count' => 0];

if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
} else {
    // Role belum diset, langsung kirim response kosong
    echo json_encode($response);
    exit;
}

if ($role === 'penjual') {
    $query = "SELECT COUNT(*) as jumlah FROM pesanan WHERE status = 'baru'";
    $stmt = $conn->prepare($query);
} elseif ($role === 'pembeli' && isset($_SESSION['id_pembeli'])) {
    $id_pembeli = $_SESSION['id_pembeli'];
    $query = "SELECT COUNT(*) as jumlah FROM pesanan WHERE status = 'baru' AND id_pembeli = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_pembeli);
} else {
    // Role tidak valid atau id_pembeli tidak ada
    echo json_encode($response);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $response['count'] = (int)$row['jumlah'];
}

echo json_encode($response);
