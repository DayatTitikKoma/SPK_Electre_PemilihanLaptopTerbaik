-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2026 at 10:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spk_electre_laptop`
--

-- --------------------------------------------------------

--
-- Table structure for table `alternatif`
--

CREATE TABLE `alternatif` (
  `id_alternatif` int(11) NOT NULL,
  `kode_alternatif` varchar(10) NOT NULL,
  `nama_alternatif` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alternatif`
--

INSERT INTO `alternatif` (`id_alternatif`, `kode_alternatif`, `nama_alternatif`, `deskripsi`, `created_at`) VALUES
(1, 'A1', 'Lenovo Ideapad 1', '', '2025-12-26 17:23:31'),
(2, 'A2', 'Lenovo lOQ', '', '2025-12-26 17:23:31'),
(3, 'A3', 'ASUS TUF', '', '2025-12-26 17:23:31'),
(4, 'A4', 'Lenovo ThinkPad', '', '2025-12-26 17:23:31');

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id_kriteria` int(11) NOT NULL,
  `kode_kriteria` varchar(10) NOT NULL,
  `nama_kriteria` varchar(100) NOT NULL,
  `tipe_kriteria` enum('benefit','cost') NOT NULL,
  `bobot_default` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id_kriteria`, `kode_kriteria`, `nama_kriteria`, `tipe_kriteria`, `bobot_default`, `created_at`) VALUES
(1, 'C1', 'Harga', 'cost', 0.20, '2025-12-26 17:23:31'),
(2, 'C2', 'Prosesor', 'benefit', 0.20, '2025-12-26 17:23:31'),
(3, 'C3', 'RAM', 'benefit', 0.15, '2025-12-26 17:23:31'),
(4, 'C4', 'Storage', 'benefit', 0.15, '2025-12-26 17:23:31'),
(5, 'C5', 'GPU', 'benefit', 0.15, '2025-12-26 17:23:31'),
(6, 'C6', 'Daya Tahan Baterai', 'benefit', 0.15, '2025-12-26 17:23:31');

-- --------------------------------------------------------

--
-- Table structure for table `nilai_alternatif`
--

CREATE TABLE `nilai_alternatif` (
  `id_nilai` int(11) NOT NULL,
  `id_alternatif` int(11) NOT NULL,
  `id_kriteria` int(11) NOT NULL,
  `nilai` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai_alternatif`
--

INSERT INTO `nilai_alternatif` (`id_nilai`, `id_alternatif`, `id_kriteria`, `nilai`, `created_at`) VALUES
(1, 1, 1, 5000000.00, '2025-12-26 17:23:32'),
(2, 1, 2, 50.00, '2025-12-26 17:23:32'),
(3, 1, 3, 8.00, '2025-12-26 17:23:32'),
(4, 1, 4, 256.00, '2025-12-26 17:23:32'),
(5, 1, 5, 60.00, '2025-12-26 17:23:32'),
(6, 1, 6, 3.00, '2025-12-26 17:23:32'),
(7, 2, 1, 12500000.00, '2025-12-26 17:23:32'),
(8, 2, 2, 85.00, '2025-12-26 17:23:32'),
(9, 2, 3, 16.00, '2025-12-26 17:23:32'),
(10, 2, 4, 512.00, '2025-12-26 17:23:32'),
(11, 2, 5, 80.00, '2025-12-26 17:23:32'),
(12, 2, 6, 13.00, '2025-12-26 17:23:32'),
(13, 3, 1, 12500000.00, '2025-12-26 17:23:32'),
(14, 3, 2, 80.00, '2025-12-26 17:23:32'),
(15, 3, 3, 16.00, '2025-12-26 17:23:32'),
(16, 3, 4, 512.00, '2025-12-26 17:23:32'),
(17, 3, 5, 88.00, '2025-12-26 17:23:32'),
(18, 3, 6, 6.00, '2025-12-26 17:23:32'),
(19, 4, 1, 5500000.00, '2025-12-26 17:23:32'),
(20, 4, 2, 65.00, '2025-12-26 17:23:32'),
(21, 4, 3, 16.00, '2025-12-26 17:23:32'),
(22, 4, 4, 512.00, '2025-12-26 17:23:32'),
(23, 4, 5, 95.00, '2025-12-26 17:23:32'),
(24, 4, 6, 9.00, '2025-12-26 17:23:32');

-- --------------------------------------------------------

--
-- Table structure for table `preset_kebutuhan`
--

CREATE TABLE `preset_kebutuhan` (
  `id_preset` int(11) NOT NULL,
  `nama_preset` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `bobot_harga` decimal(5,2) DEFAULT 0.20,
  `bobot_prosesor` decimal(5,2) DEFAULT 0.20,
  `bobot_ram` decimal(5,2) DEFAULT 0.15,
  `bobot_storage` decimal(5,2) DEFAULT 0.15,
  `bobot_gpu` decimal(5,2) DEFAULT 0.15,
  `bobot_baterai` decimal(5,2) DEFAULT 0.15,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preset_kebutuhan`
--

INSERT INTO `preset_kebutuhan` (`id_preset`, `nama_preset`, `deskripsi`, `bobot_harga`, `bobot_prosesor`, `bobot_ram`, `bobot_storage`, `bobot_gpu`, `bobot_baterai`, `created_at`) VALUES
(1, 'Gaming', 'Prioritas tinggi pada GPU dan Prosesor untuk gaming', 0.15, 0.25, 0.15, 0.10, 0.30, 0.05, '2025-12-26 17:38:16'),
(2, 'Editing', 'Prioritas pada Prosesor, RAM, dan Storage untuk editing video/gambar', 0.15, 0.25, 0.20, 0.20, 0.15, 0.05, '2025-12-26 17:38:16'),
(3, 'Kantor', 'Prioritas pada Harga, Baterai, dan Prosesor untuk produktivitas', 0.30, 0.20, 0.15, 0.10, 0.05, 0.20, '2025-12-26 17:38:16'),
(4, 'Mahasiswa', 'Seimbang antara Harga, Baterai, dan performa dasar', 0.25, 0.15, 0.15, 0.15, 0.10, 0.20, '2025-12-26 17:38:16'),
(5, 'Desain Grafis', 'Prioritas pada Prosesor, RAM, GPU, dan Storage', 0.10, 0.25, 0.20, 0.20, 0.20, 0.05, '2025-12-26 17:38:16'),
(6, 'Programming', 'Prioritas pada Prosesor, RAM, dan Storage', 0.20, 0.25, 0.25, 0.15, 0.05, 0.10, '2025-12-26 17:38:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `email`, `password`, `nama_lengkap`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@spkelectre.com', '$2y$10$KkDmOgjnyloIae1KkBb2JO.mzY5fAQBrpa3ioM.9NoNpqB47O2R0u', 'Administrator', 'admin', '2025-12-26 17:38:16', '2025-12-26 17:38:16'),
(2, 'user', 'user@spkelectre.com', '$2y$10$k6973iPzdLMIwaCzGcW.JObqyszBZVgKxaPQZ73zOr6x/1PYMCLXC', 'User Demo', 'user', '2025-12-26 17:38:16', '2025-12-26 17:38:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`id_alternatif`),
  ADD UNIQUE KEY `kode_alternatif` (`kode_alternatif`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id_kriteria`),
  ADD UNIQUE KEY `kode_kriteria` (`kode_kriteria`);

--
-- Indexes for table `nilai_alternatif`
--
ALTER TABLE `nilai_alternatif`
  ADD PRIMARY KEY (`id_nilai`),
  ADD UNIQUE KEY `unique_alt_krit` (`id_alternatif`,`id_kriteria`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indexes for table `preset_kebutuhan`
--
ALTER TABLE `preset_kebutuhan`
  ADD PRIMARY KEY (`id_preset`),
  ADD UNIQUE KEY `nama_preset` (`nama_preset`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alternatif`
--
ALTER TABLE `alternatif`
  MODIFY `id_alternatif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id_kriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `nilai_alternatif`
--
ALTER TABLE `nilai_alternatif`
  MODIFY `id_nilai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `preset_kebutuhan`
--
ALTER TABLE `preset_kebutuhan`
  MODIFY `id_preset` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `nilai_alternatif`
--
ALTER TABLE `nilai_alternatif`
  ADD CONSTRAINT `nilai_alternatif_ibfk_1` FOREIGN KEY (`id_alternatif`) REFERENCES `alternatif` (`id_alternatif`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_alternatif_ibfk_2` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id_kriteria`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
