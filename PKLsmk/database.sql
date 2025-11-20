-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 06:29 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pkl_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 5, 'REGISTER', 'User registrasi baru', '::1', NULL, '2025-11-17 18:07:11'),
(2, 5, 'LOGIN', 'User login', '::1', NULL, '2025-11-17 18:07:23'),
(3, 5, 'LOGOUT', 'User logout', '::1', NULL, '2025-11-19 03:15:33'),
(4, 6, 'REGISTER', 'User registrasi baru', '::1', NULL, '2025-11-19 03:17:27'),
(5, 6, 'LOGIN', 'User login', '::1', NULL, '2025-11-19 03:17:55'),
(6, 6, 'LOGOUT', 'User logout', '::1', NULL, '2025-11-19 03:23:20'),
(7, 6, 'LOGIN', 'User login', '::1', NULL, '2025-11-19 03:23:37'),
(8, 6, 'LOGOUT', 'User logout', '::1', NULL, '2025-11-19 03:23:49'),
(9, 5, 'LOGIN', 'User login', '::1', NULL, '2025-11-19 03:23:55');

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--

CREATE TABLE `jurusan` (
  `id` int(11) NOT NULL,
  `kode_jurusan` varchar(10) NOT NULL,
  `nama_jurusan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `kuota` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jurusan`
--

INSERT INTO `jurusan` (`id`, `kode_jurusan`, `nama_jurusan`, `deskripsi`, `kuota`, `status`, `created_at`) VALUES
(1, 'RPL', 'Rekayasa Perangkat Lunak', 'Jurusan yang mempelajari pengembangan software dan aplikasi menggunakan berbagai teknologi modern seperti PHP, JavaScript, Python, dan framework terkini. Siswa akan belajar pemrograman web, mobile, dan desktop.', 50, 'active', '2025-11-17 17:09:36'),
(2, 'TKJ', 'Teknik Komputer dan Jaringan', 'Jurusan yang fokus pada jaringan komputer, hardware, dan sistem infrastruktur IT. Mempelajari konfigurasi jaringan, keamanan siber, maintenance server, dan troubleshooting jaringan.', 45, 'active', '2025-11-17 17:09:36'),
(3, 'MM', 'Multimedia', 'Jurusan yang mempelajari desain grafis, animasi, video editing, dan produksi konten digital. Menggunakan tools seperti Adobe Photoshop, Illustrator, Premiere Pro, dan After Effects.', 40, 'active', '2025-11-17 17:09:36'),
(4, 'TKRO', 'Teknik Kendaraan Ringan Otomotif', 'Jurusan yang mempelajari teknik perbaikan dan maintenance kendaraan ringan. Fokus pada sistem mesin, kelistrikan, dan teknologi otomotif modern.', 35, 'active', '2025-11-17 17:09:36'),
(5, 'TBSM', 'Teknik Bisnis Sepeda Motor', 'Jurusan yang fokus pada bisnis dan teknik sepeda motor. Mempelajari service sepeda motor, manajemen bengkel, dan entrepreneurship di bidang otomotif.', 30, 'active', '2025-11-17 17:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran_pkl`
--

CREATE TABLE `pendaftaran_pkl` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `jurusan_id` int(11) NOT NULL,
  `perusahaan_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `alasan_pkl` text DEFAULT NULL,
  `status` enum('pending','diterima','ditolak') DEFAULT 'pending',
  `berkas_cv` varchar(255) DEFAULT NULL,
  `berkas_portofolio` varchar(255) DEFAULT NULL,
  `catatan_admin` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `perusahaan`
--

CREATE TABLE `perusahaan` (
  `id` int(11) NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `kontak` varchar(100) NOT NULL,
  `kuota` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `perusahaan`
--

INSERT INTO `perusahaan` (`id`, `nama_perusahaan`, `alamat`, `kontak`, `kuota`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PT. Teknologi Indonesia', 'Jl. Sudirman No. 123, Jakarta', '021-1234567', 10, 'active', '2025-11-17 17:09:36', '2025-11-17 17:09:36'),
(2, 'CV. Digital Solution', 'Jl. Thamrin No. 45, Bandung', '022-7654321', 8, 'active', '2025-11-17 17:09:36', '2025-11-17 17:09:36'),
(3, 'PT. Network Systems', 'Jl. Gatot Subroto No. 67, Surabaya', '031-9876543', 12, 'active', '2025-11-17 17:09:36', '2025-11-17 17:09:36'),
(4, 'PT. Otomotif Nusantara', 'Jl. Industri No. 89, Bekasi', '021-4567890', 15, 'active', '2025-11-17 17:09:36', '2025-11-17 17:09:36'),
(5, 'Studio Kreatif Media', 'Jl. Kreatif No. 56, Yogyakarta', '0274-123456', 6, 'active', '2025-11-17 17:09:36', '2025-11-17 17:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','siswa') DEFAULT 'siswa',
  `nama_lengkap` varchar(100) NOT NULL,
  `nis` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `nama_lengkap`, `nis`, `alamat`, `no_telepon`, `created_at`, `updated_at`) VALUES
(5, 'syarel', '$2y$10$pwgpguXItfG2kgwFcbJLy.iluwa8O79aG3chL9y1dTcchatjF5rIC', 'syarel@gmail.com', 'siswa', 'syarel', '123567', 'taman raya', '089797775', '2025-11-17 18:07:11', '2025-11-17 18:07:11'),
(6, 'admin', '$2y$10$2PgSiS9AfiOeRKhXiA5mcOrHHF8SZBncwhTQUyvYZHBIw16QVA0DO', 'admin@gmail.com', 'admin', 'adminstrator', '43734737', 'taman raya', '5373357', '2025-11-19 03:17:27', '2025-11-19 03:17:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_jurusan` (`kode_jurusan`);

--
-- Indexes for table `pendaftaran_pkl`
--
ALTER TABLE `pendaftaran_pkl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `jurusan_id` (`jurusan_id`),
  ADD KEY `perusahaan_id` (`perusahaan_id`);

--
-- Indexes for table `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pendaftaran_pkl`
--
ALTER TABLE `pendaftaran_pkl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pendaftaran_pkl`
--
ALTER TABLE `pendaftaran_pkl`
  ADD CONSTRAINT `pendaftaran_pkl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pendaftaran_pkl_ibfk_2` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`),
  ADD CONSTRAINT `pendaftaran_pkl_ibfk_3` FOREIGN KEY (`perusahaan_id`) REFERENCES `perusahaan` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
