-- Database Schema untuk Sistem Pendukung Keputusan Pemilihan Laptop
-- Metode: ELECTRE

CREATE DATABASE IF NOT EXISTS spk_electre_laptop;
USE spk_electre_laptop;

-- Tabel Kriteria
CREATE TABLE IF NOT EXISTS kriteria (
    id_kriteria INT PRIMARY KEY AUTO_INCREMENT,
    kode_kriteria VARCHAR(10) NOT NULL UNIQUE,
    nama_kriteria VARCHAR(100) NOT NULL,
    tipe_kriteria ENUM('benefit', 'cost') NOT NULL,
    bobot_default DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Alternatif (Laptop)
CREATE TABLE IF NOT EXISTS alternatif (
    id_alternatif INT PRIMARY KEY AUTO_INCREMENT,
    kode_alternatif VARCHAR(10) NOT NULL UNIQUE,
    nama_alternatif VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Nilai Alternatif per Kriteria
CREATE TABLE IF NOT EXISTS nilai_alternatif (
    id_nilai INT PRIMARY KEY AUTO_INCREMENT,
    id_alternatif INT NOT NULL,
    id_kriteria INT NOT NULL,
    nilai DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_alternatif) REFERENCES alternatif(id_alternatif) ON DELETE CASCADE,
    FOREIGN KEY (id_kriteria) REFERENCES kriteria(id_kriteria) ON DELETE CASCADE,
    UNIQUE KEY unique_alt_krit (id_alternatif, id_kriteria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel User
CREATE TABLE IF NOT EXISTS users (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Preset Kebutuhan (untuk filter user)
CREATE TABLE IF NOT EXISTS preset_kebutuhan (
    id_preset INT PRIMARY KEY AUTO_INCREMENT,
    nama_preset VARCHAR(50) NOT NULL UNIQUE,
    deskripsi TEXT,
    bobot_harga DECIMAL(5,2) DEFAULT 0.20,
    bobot_prosesor DECIMAL(5,2) DEFAULT 0.20,
    bobot_ram DECIMAL(5,2) DEFAULT 0.15,
    bobot_storage DECIMAL(5,2) DEFAULT 0.15,
    bobot_gpu DECIMAL(5,2) DEFAULT 0.15,
    bobot_baterai DECIMAL(5,2) DEFAULT 0.15,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Data Kriteria Default
INSERT INTO kriteria (kode_kriteria, nama_kriteria, tipe_kriteria, bobot_default) VALUES
('C1', 'Harga', 'cost', 0.20),
('C2', 'Prosesor', 'benefit', 0.20),
('C3', 'RAM', 'benefit', 0.15),
('C4', 'Storage', 'benefit', 0.15),
('C5', 'GPU', 'benefit', 0.15),
('C6', 'Daya Tahan Baterai', 'benefit', 0.15);

-- Insert Data Alternatif (Laptop Mid-Level ke Atas)
INSERT INTO alternatif (kode_alternatif, nama_alternatif, deskripsi) VALUES
('A1', 'ASUS ROG Strix G15', 'Laptop gaming high-end dengan Ryzen 9 dan RTX 3070'),
('A2', 'Lenovo ThinkPad X1 Carbon Gen 9', 'Laptop bisnis premium dengan Intel i7 gen 11 dan layar 4K'),
('A3', 'Acer Predator Helios 300', 'Laptop gaming mid-high level dengan Intel i7 dan RTX 3060'),
('A4', 'HP Envy 15', 'Laptop mid-level dengan Intel i7 dan MX450 untuk produktivitas dan desain'),
('A5', 'Dell XPS 15 OLED', 'Laptop premium untuk creative professional dengan layar OLED 4K');

-- Insert Nilai Alternatif (Laptop Mid-Level ke Atas)
-- A1: ASUS ROG Strix G15 (High-End Gaming)
INSERT INTO nilai_alternatif (id_alternatif, id_kriteria, nilai) VALUES
(1, 1, 16500000), -- Harga: 16.5 juta (cost - semakin kecil越好)
(1, 2, 88),       -- Prosesor: 88/100 (benefit)
(1, 3, 16),       -- RAM: 16 GB (benefit)
(1, 4, 512),      -- Storage: 512 GB (benefit)
(1, 5, 92),       -- GPU: 92/100 (benefit)
(1, 6, 7);        -- Baterai: 7 jam (benefit)

-- A2: Lenovo ThinkPad X1 Carbon Gen 9 (Premium Business)
INSERT INTO nilai_alternatif (id_alternatif, id_kriteria, nilai) VALUES
(2, 1, 22000000), -- Harga: 22 juta
(2, 2, 85),       -- Prosesor: 85/100
(2, 3, 16),       -- RAM: 16 GB
(2, 4, 512),      -- Storage: 512 GB
(2, 5, 70),       -- GPU: 70/100 (integrated)
(2, 6, 13);       -- Baterai: 13 jam

-- A3: Acer Predator Helios 300 (Mid-High Level Gaming)
INSERT INTO nilai_alternatif (id_alternatif, id_kriteria, nilai) VALUES
(3, 1, 14500000), -- Harga: 14.5 juta
(3, 2, 82),       -- Prosesor: 82/100
(3, 3, 16),       -- RAM: 16 GB
(3, 4, 512),      -- Storage: 512 GB
(3, 5, 88),       -- GPU: 88/100
(3, 6, 6);        -- Baterai: 6 jam

-- A4: HP Envy 15 (Mid-Level)
INSERT INTO nilai_alternatif (id_alternatif, id_kriteria, nilai) VALUES
(4, 1, 13500000), -- Harga: 13.5 juta
(4, 2, 83),       -- Prosesor: 83/100
(4, 3, 16),       -- RAM: 16 GB
(4, 4, 512),      -- Storage: 512 GB
(4, 5, 75),       -- GPU: 75/100
(4, 6, 9);        -- Baterai: 9 jam

-- A5: Dell XPS 15 OLED (Premium)
INSERT INTO nilai_alternatif (id_alternatif, id_kriteria, nilai) VALUES
(5, 1, 28000000), -- Harga: 28 juta
(5, 2, 92),       -- Prosesor: 92/100
(5, 3, 32),       -- RAM: 32 GB
(5, 4, 1024),     -- Storage: 1 TB
(5, 5, 95),       -- GPU: 95/100
(5, 6, 11);       -- Baterai: 11 jam

-- Insert User Default (Admin)
-- Password default: admin123 (HARUS DIGANTI SETELAH INSTALASI!)
-- Untuk membuat password baru, jalankan: php -r "echo password_hash('password_anda', PASSWORD_DEFAULT);"
INSERT INTO users (username, email, password, nama_lengkap, role) VALUES
('admin', 'admin@spkelectre.com', '$2y$10$KkDmOgjnyloIae1KkBb2JO.mzY5fAQBrpa3ioM.9NoNpqB47O2R0u', 'Administrator', 'admin');

-- Insert Preset Kebutuhan
INSERT INTO preset_kebutuhan (nama_preset, deskripsi, bobot_harga, bobot_prosesor, bobot_ram, bobot_storage, bobot_gpu, bobot_baterai) VALUES
('Gaming', 'Prioritas tinggi pada GPU dan Prosesor untuk gaming', 0.15, 0.25, 0.15, 0.10, 0.30, 0.05),
('Editing', 'Prioritas pada Prosesor, RAM, dan Storage untuk editing video/gambar', 0.15, 0.25, 0.20, 0.20, 0.15, 0.05),
('Kantor', 'Prioritas pada Harga, Baterai, dan Prosesor untuk produktivitas', 0.30, 0.20, 0.15, 0.10, 0.05, 0.20),
('Mahasiswa', 'Seimbang antara Harga, Baterai, dan performa dasar', 0.25, 0.15, 0.15, 0.15, 0.10, 0.20),
('Desain Grafis', 'Prioritas pada Prosesor, RAM, GPU, dan Storage', 0.10, 0.25, 0.20, 0.20, 0.20, 0.05),
('Programming', 'Prioritas pada Prosesor, RAM, dan Storage', 0.20, 0.25, 0.25, 0.15, 0.05, 0.10);

