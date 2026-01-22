-- Update Schema untuk Menambahkan Sistem Login dan Filter Kebutuhan
-- Jalankan file ini jika database sudah ada sebelumnya

USE spk_electre_laptop;

-- Tabel User (jika belum ada)
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

-- Tabel Preset Kebutuhan (jika belum ada)
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

-- Insert User Default Admin (jika belum ada)
-- Password default: admin123 (HARUS DIGANTI SETELAH INSTALASI!)
-- Untuk membuat password baru, jalankan: php -r "echo password_hash('password_anda', PASSWORD_DEFAULT);"
INSERT IGNORE INTO users (username, email, password, nama_lengkap, role) VALUES
('admin', 'admin@spkelectre.com', '$2y$10$KkDmOgjnyloIae1KkBb2JO.mzY5fAQBrpa3ioM.9NoNpqB47O2R0u', 'Administrator', 'admin');

-- Insert Preset Kebutuhan (jika belum ada)
INSERT IGNORE INTO preset_kebutuhan (nama_preset, deskripsi, bobot_harga, bobot_prosesor, bobot_ram, bobot_storage, bobot_gpu, bobot_baterai) VALUES
('Gaming', 'Prioritas tinggi pada GPU dan Prosesor untuk gaming', 0.15, 0.25, 0.15, 0.10, 0.30, 0.05),
('Editing', 'Prioritas pada Prosesor, RAM, dan Storage untuk editing video/gambar', 0.15, 0.25, 0.20, 0.20, 0.15, 0.05),
('Kantor', 'Prioritas pada Harga, Baterai, dan Prosesor untuk produktivitas', 0.30, 0.20, 0.15, 0.10, 0.05, 0.20),
('Mahasiswa', 'Seimbang antara Harga, Baterai, dan performa dasar', 0.25, 0.15, 0.15, 0.15, 0.10, 0.20),
('Desain Grafis', 'Prioritas pada Prosesor, RAM, GPU, dan Storage', 0.10, 0.25, 0.20, 0.20, 0.20, 0.05),
('Programming', 'Prioritas pada Prosesor, RAM, dan Storage', 0.20, 0.25, 0.25, 0.15, 0.05, 0.10);

