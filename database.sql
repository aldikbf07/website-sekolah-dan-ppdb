-- ========================================
-- Database: db_sekolah_mi
-- MI Muhammadiyah Bojongsana
-- ========================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `guru`
-- --------------------------------------------------------
CREATE TABLE `guru` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `mapel` varchar(100) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nip` (`nip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `berita`
-- --------------------------------------------------------
CREATE TABLE `berita` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `kategori` varchar(50) DEFAULT 'Umum',
  `status` enum('draft','published') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `galeri`
-- --------------------------------------------------------
CREATE TABLE `galeri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gambar` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `kategori` varchar(50) DEFAULT 'Kegiatan',
  `tanggal` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `prestasi`
-- --------------------------------------------------------
CREATE TABLE `prestasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `kategori` varchar(50) DEFAULT 'Akademik',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `fasilitas`
-- --------------------------------------------------------
CREATE TABLE `fasilitas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `pengaturan`
-- --------------------------------------------------------
CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Insert default data for `pengaturan`
-- --------------------------------------------------------
INSERT INTO `pengaturan` (`key`, `value`) VALUES
('visi', 'Menjadi lembaga pendidikan Islam yang unggul dalam membentuk generasi Qurani, berakhlak mulia, dan berprestasi.'),
('misi', '1. Menyelenggarakan pendidikan berbasis Al-Quran dan Sunnah\n2. Mengembangkan potensi akademik dan non-akademik siswa\n3. Membentuk karakter Islami melalui pembiasaan ibadah\n4. Menciptakan lingkungan belajar yang islami dan menyenangkan\n5. Menjalin kerjasama dengan orang tua dan masyarakat'),
('sejarah', 'MI Muhammadiyah Bojongsana didirikan pada tahun 1980 oleh para tokoh Muhammadiyah setempat. Berawal dari keprihatinan terhadap pendidikan Islam di wilayah Bojongsana, para pendiri bertekad mendirikan lembaga pendidikan yang mampu mencetak generasi Muslim yang berkualitas.\n\nSejak awal berdirinya, madrasah ini telah mengalami berbagai perkembangan. Dari gedung sederhana dengan jumlah siswa yang terbatas, kini MI Muhammadiyah Bojongsana telah memiliki gedung permanen dengan fasilitas yang memadai dan jumlah siswa yang terus bertambah setiap tahunnya.'),
('alamat', 'Jl. Raya Bojongsana No. 123, Kecamatan Suradadi, Kabupaten Tegal, Jawa Tengah 52182'),
('telepon', '(0283) 123456'),
('email', 'info@mimuhammadiyahbojongsana.sch.id');

-- --------------------------------------------------------
-- Insert sample data for `guru`
-- --------------------------------------------------------
INSERT INTO `guru` (`nama`, `nip`, `mapel`, `jabatan`) VALUES
('Ahmad Fauzi, S.Pd.I', '198001012010011001', 'Al-Quran Hadits', 'Kepala Sekolah'),
('Siti Aminah, S.Pd', '198502022011012002', 'Matematika', 'Waka Kurikulum'),
('Muhammad Rizki, S.Pd.I', '198803032012011003', 'Fiqih', 'Guru Mapel');

-- --------------------------------------------------------
-- Insert sample data for `berita`
-- --------------------------------------------------------
INSERT INTO `berita` (`judul`, `isi`, `tanggal`, `kategori`, `status`) VALUES
('Penerimaan Peserta Didik Baru Tahun Ajaran 2024/2025', 'MI Muhammadiyah Bojongsana membuka pendaftaran peserta didik baru untuk tahun ajaran 2024/2025. Pendaftaran dibuka mulai tanggal 1 Mei hingga 30 Juni 2024.\n\nSyarat pendaftaran:\n1. Usia minimal 6 tahun\n2. Fotokopi akta kelahiran\n3. Fotokopi kartu keluarga\n4. Pas foto 3x4 (2 lembar)\n\nSegera daftarkan putra-putri Anda!', '2024-05-01', 'Akademik', 'published'),
('Kegiatan Pesantren Kilat Ramadhan 1445 H', 'Alhamdulillah, MI Muhammadiyah Bojongsana telah sukses menyelenggarakan kegiatan Pesantren Kilat Ramadhan 1445 H. Kegiatan ini diikuti oleh seluruh siswa dari kelas 1 hingga kelas 6.\n\nKegiatan meliputi:\n- Tadarus Al-Quran\n- Hafalan surat pendek\n- Praktek ibadah\n- Ceramah agama\n- Buka puasa bersama', '2024-03-20', 'Keagamaan', 'published');

COMMIT;