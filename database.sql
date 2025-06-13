-- Buat database
CREATE DATABASE IF NOT EXISTS db_catering CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_catering;

-- Tabel users (pemilik & konsumen)
CREATE TABLE penjual (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE pembeli (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    image VARCHAR(255),
    day ENUM('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'),
    FOREIGN KEY (user_id) REFERENCES penjual(id)
);

-- Tabel pesanan (catat pesanan konsumen)
CREATE TABLE pesanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT,
  tanggal_pesan DATE,
  status ENUM('menunggu','diproses','dikirim','selesai','dibatalkan') DEFAULT 'menunggu',
  total_harga DECIMAL(10,2),
  metode_pembayaran VARCHAR(50),
  bukti_pembayaran VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_user) REFERENCES pembeli(id)
);

-- Tabel pesanan_detail (detail menu per pesanan)
CREATE TABLE pesanan_detail (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_pesanan INT,
  id_menu INT,
  jumlah INT,
  subtotal DECIMAL(10,2),
  FOREIGN KEY (id_pesanan) REFERENCES pesanan(id),
  FOREIGN KEY (id_menu) REFERENCES menus(id)
);

-- Tabel jadwal_pengiriman (atur pengiriman pesanan)
CREATE TABLE jadwal_pengiriman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_pesanan INT,
  tanggal_kirim DATE,
  waktu_kirim TIME,
  alamat TEXT,
  status ENUM('terjadwal','dikirim','terkirim') DEFAULT 'terjadwal',
  FOREIGN KEY (id_pesanan) REFERENCES pesanan(id)
);

-- Tabel notifikasi (notifikasi untuk pemilik)
CREATE TABLE notifikasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT,
  isi TEXT,
  status ENUM('belum_dibaca','dibaca') DEFAULT 'belum_dibaca',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_user) REFERENCES penjual(id)
);

-- Tabel notifikasi_pembeli (notifikasi untuk pembeli)
CREATE TABLE notifikasi_pembeli (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_pembeli INT,
  isi TEXT,
  status ENUM('belum_dibaca', 'dibaca') DEFAULT 'belum_dibaca',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_pembeli) REFERENCES pembeli(id)
);

-- Tabel ulasan (penilaian & komentar konsumen)
CREATE TABLE ulasan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_user INT,
  id_pesanan INT,
  rating INT CHECK(rating BETWEEN 1 AND 5),
  komentar TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_user) REFERENCES pembeli(id),
  FOREIGN KEY (id_pesanan) REFERENCES pesanan(id)
);

-- Tabel keuangan (laporan keuangan sederhana)
CREATE TABLE keuangan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATE,
  keterangan TEXT,
  jenis ENUM('pemasukan', 'pengeluaran'),
  jumlah DECIMAL(10,2),
  created_by INT,
  FOREIGN KEY (created_by) REFERENCES penjual(id)
);
