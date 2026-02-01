-- Buat database
CREATE DATABASE franchise_finance;
USE franchise_finance;

-- Tabel cabang
CREATE TABLE cabang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_cabang VARCHAR(100) NOT NULL,
    lokasi VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('pusat','cabang') NOT NULL,
    cabang_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cabang_id) REFERENCES cabang(id)
);

-- Tabel transaksi
CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cabang_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jenis ENUM('pemasukan','pengeluaran') NOT NULL,
    keterangan VARCHAR(255),
    jumlah DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cabang_id) REFERENCES cabang(id)
);

-- Tabel activity log
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    aktivitas VARCHAR(255),
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO cabang (nama_cabang, lokasi) VALUES
('Cabang Jakarta', 'Jakarta'),
('Cabang Bandung', 'Bandung');

-- Kepala Keuangan Pusat
INSERT INTO users (username, password, role, cabang_id) VALUES
('finance_pusat', '$2y$10$QH6xE1nQv0s1Zq9z1uXb1O8xGJ6J9qZkY2zE9u9k0s4hPZKZx', 'pusat', NULL);

-- Admin Cabang Jakarta
INSERT INTO users (username, password, role, cabang_id) VALUES
('admin_jakarta', '$2y$10$QH6xE1nQv0s1Zq9z1uXb1O8xGJ6J9qZkY2zE9u9k0s4hPZKZx', 'cabang', 1);

-- Admin Cabang Bandung
INSERT INTO users (username, password, role, cabang_id) VALUES
('admin_bandung', '$2y$10$QH6xE1nQv0s1Zq9z1uXb1O8xGJ6J9qZkY2zE9u9k0s4hPZKZx', 'cabang', 2);