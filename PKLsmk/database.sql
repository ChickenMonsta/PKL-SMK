CREATE DATABASE IF NOT EXISTS pkldb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE pkldb;

-- 1. Tabel jurusan
CREATE TABLE jurusan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_jurusan VARCHAR(10) NOT NULL UNIQUE,
  nama_jurusan VARCHAR(100) NOT NULL,
  keterangan TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Tabel perusahaan
CREATE TABLE perusahaan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_perusahaan VARCHAR(20) NOT NULL UNIQUE,
  nama_perusahaan VARCHAR(200) NOT NULL,
  alamat TEXT NOT NULL,
  telepon VARCHAR(20) NULL,
  email VARCHAR(100) NULL,
  pic_nama VARCHAR(100) NULL,
  pic_telepon VARCHAR(20) NULL,
  kuota INT DEFAULT 5,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Tabel users (harus dibuat sebelum FK mengarah ke sini)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NULL,
  role ENUM('admin','siswa') DEFAULT 'siswa',
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Tabel pendaftaran_pkl
CREATE TABLE pendaftaran_pkl (
  id INT AUTO_INCREMENT PRIMARY KEY,
  siswa_id INT NOT NULL,
  jurusan_id INT NOT NULL,
  perusahaan_id INT NOT NULL,
  tahun_ajaran VARCHAR(10) NOT NULL,
  semester ENUM('ganjil','genap') NOT NULL,
  tanggal_mulai DATE NOT NULL,
  tanggal_selesai DATE NOT NULL,
  status ENUM('menunggu','diterima','ditolak','dibatalkan') DEFAULT 'menunggu',
  catatan_admin TEXT NULL,
  tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  tanggal_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (siswa_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (jurusan_id) REFERENCES jurusan(id),
  FOREIGN KEY (perusahaan_id) REFERENCES perusahaan(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Tabel pengumuman
CREATE TABLE pengumuman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(200) NOT NULL,
  isi TEXT NOT NULL,
  penulis_id INT NOT NULL,
  is_important TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (penulis_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ========================================
-- DATA DUMMY (langsung masuk semua)
-- ========================================

INSERT INTO jurusan (kode_jurusan, nama_jurusan, keterangan) VALUES
('TKJ', 'Teknik Komputer dan Jaringan', 'Jurusan bidang jaringan komputer dan hardware'),
('RPL', 'Rekayasa Perangkat Lunak', 'Jurusan bidang programming dan software development'),
('MM', 'Multimedia', 'Jurusan bidang desain grafis dan multimedia');

INSERT INTO perusahaan (kode_perusahaan, nama_perusahaan, alamat, telepon, email, pic_nama, pic_telepon, kuota, is_active) VALUES
('PT-001', 'PT. Teknologi Indonesia', 'Jl. Sudirman No. 123, Jakarta', '021-1234567', 'hrd@techindonesia.co.id', 'Budi Santoso', '081234567890', 10, 1),
('PT-002', 'CV. Creative Solution', 'Jl. Thamrin No. 45, Bandung', '022-7654321', 'info@creative-solution.com', 'Sari Dewi', '081298765432', 8, 1),
('PT-003', 'PT. Bank Nasional', 'Jl. Gatot Subroto No. 78, Jakarta', '021-8889999', 'recruitment@banknasional.co.id', 'Ari Wibowo', '081377788899', 15, 1),
('PT-004', 'Studio Grafika Media', 'Jl. Merdeka No. 56, Surabaya', '031-4445555', 'contact@studiografika.com', 'Rina Melati', '081566677788', 6, 1),
('PT-005', 'PT. Retail Mart', 'Jl. Pasar Baru No. 90, Jakarta', '021-3332222', 'hr@retailmart.co.id', 'Dedi Pratama', '081499988877', 12, 1);

INSERT INTO users (username, password, nama, email, role, is_active) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator System', 'admin@smk.example', 'admin', 1),
('siswa1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ahmad Rizki', 'ahmad@example.com', 'siswa', 1),
('siswa2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti Aminah', 'siti@example.com', 'siswa', 1),
('syarel', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Syarel', 'syarel@gm.com', 'admin', 1);

INSERT INTO pendaftaran_pkl (siswa_id, jurusan_id, perusahaan_id, tahun_ajaran, semester, tanggal_mulai, tanggal_selesai, status, catatan_admin) VALUES
(2, 3, 3, '2025/2026', 'ganjil', '2025-12-11', '2026-05-11', 'diterima', 'Pendaftaran disetujui oleh admin');
