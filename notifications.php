<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'koneksi.php'; // koneksi ke database

header('Content-Type: application/json');

$response = ['count' => 0];

if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];

file_put_contents("log.txt", print_r($_SESSION, true));

    if ($role === 'penjual') {
     $query = "SELECT COUNT(*) as jumlah FROM pesanan WHERE status = 'baru'";
    } elseif ($role === 'pembeli' && isset($_SESSION['id_pembeli'])) {
        $id_pembeli = $_SESSION['id_pembeli'];
        $query = "SELECT COUNT(*) as jumlah FROM pesanan WHERE status = 'baru' AND id_pembeli = ?";
    }

    if (isset($query)) {
        $stmt = $conn->prepare($query);

        if ($role === 'pembeli') {
            $stmt->bind_param("i", $id_pembeli);
        }

        $stmt->execute();
        $result = $stmt->get_resu…