-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2026 at 12:35 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ukk_2026`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_guru`
--

CREATE TABLE `t_guru` (
  `id` int(11) NOT NULL,
  `nip` varchar(30) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_guru`
--

INSERT INTO `t_guru` (`id`, `nip`, `nama`, `email`, `status_aktif`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '198010900001', 'Wawan Kurniawan, S.Pd.I', 'guru1@ukk2026.test', 1, 178, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(2, '198020900002', 'Ai Nuryaidah, S.Pd', 'guru2@ukk2026.test', 1, 179, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(3, '198030900003', 'Maya Permata Endah, S.Pd', 'guru3@ukk2026.test', 1, 180, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(4, '198040900004', 'Dody Jaenudin, S.Pd', 'guru4@ukk2026.test', 1, 181, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(5, '198050900005', 'Dedi Kardiman, S.Pd', 'guru5@ukk2026.test', 1, 182, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(6, '198060900006', 'Uca Musa Yusenda, S.Pd', 'guru6@ukk2026.test', 1, 183, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(7, '198070900007', 'Drs. Ahmad Nur Mukhlis', 'guru7@ukk2026.test', 1, 184, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(8, '198080900008', 'Drs. Heri', 'guru8@ukk2026.test', 1, 185, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(9, '198090900009', 'Asep Bastaman', 'guru9@ukk2026.test', 1, 186, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(10, '198100900010', 'H. Encep Maemun, S.T., M.Pd', 'guru10@ukk2026.test', 1, 187, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(11, '198110900011', 'Opik', 'guru11@ukk2026.test', 1, 188, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(12, '198120900012', 'Ida Farida, S.Pd', 'guru12@ukk2026.test', 1, 189, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(13, '198130900013', 'Idah Mahmudah, S.Pd', 'guru13@ukk2026.test', 1, 190, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(14, '198140900014', 'Drs. Muhamad Ali', 'guru14@ukk2026.test', 1, 191, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(15, '198150900015', 'Helmi Ali Akbar, S.Pd.I', 'guru15@ukk2026.test', 1, 192, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(16, '198160900016', 'Syeni Suswarini, S.Pd', 'guru16@ukk2026.test', 1, 193, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(17, '198170900017', 'Wiwid Yulianti, S.Pd', 'guru17@ukk2026.test', 1, 194, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(18, '198180900018', 'Ansor Muhajir, S.T', 'guru18@ukk2026.test', 1, 195, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(19, '198190900019', 'Ogi Gaosun Gias, S.Pd.I', 'guru19@ukk2026.test', 1, 196, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(20, '198200900020', 'Sandi Mulyana, S.Pd', 'guru20@ukk2026.test', 1, 197, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(21, '198210900021', 'Erna Suryani, S.Pd', 'guru21@ukk2026.test', 1, 198, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(22, '198220900022', 'Ai Wahidah, S.Ag', 'guru22@ukk2026.test', 1, 199, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(23, '198230900023', 'Chandra Aditya Yusup, S.Pd', 'guru23@ukk2026.test', 1, 200, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(24, '198240900024', 'Arif Kusmawan, S.Pd', 'guru24@ukk2026.test', 1, 201, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(25, '198250900025', 'Asep Taufiq Hidayat, S.Kom', 'guru25@ukk2026.test', 1, 202, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(26, '198260900026', 'Rendy Ichsan Kusumah, S.T', 'guru26@ukk2026.test', 1, 203, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(27, '198270900027', 'Feri Rahmawan, S.Pd', 'guru27@ukk2026.test', 1, 204, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(28, '198280900028', 'Ari Sandriana, S.Kom', 'guru28@ukk2026.test', 1, 205, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(29, '198290900029', 'Yaneu Nurdian, S.Pd, M.Pd', 'guru29@ukk2026.test', 1, 206, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(30, '198300900030', 'Ucu Muhammad Khoer N, S.Pd', 'guru30@ukk2026.test', 1, 207, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(31, '198310900031', 'Mira Dwi Rahmawati, S.Ag', 'guru31@ukk2026.test', 1, 208, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(32, '198320900032', 'Aldi Maulana, S.Pd', 'guru32@ukk2026.test', 1, 209, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(33, '198330900033', 'Eli Rosliah, S.Pd', 'guru33@ukk2026.test', 1, 210, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(34, '198340900034', 'Isni Nur Fitriani, S.Pd', 'guru34@ukk2026.test', 1, 211, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(35, '198350900035', 'Aanisah Putri Nur Aini, S.Pd', 'guru35@ukk2026.test', 1, 212, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(36, '198360900036', 'Yusi Luthfiani, S.Sos.I', 'guru36@ukk2026.test', 1, 213, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(37, '198370900037', 'Lilis Rustandy Mawardi, S.Pd., M.Pd', 'guru37@ukk2026.test', 1, 214, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(38, '198380900038', 'Yusrin Noer Sabrina, S.Pd', 'guru38@ukk2026.test', 1, 215, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(39, '198390900039', 'Senny Indayani, S.Pd', 'guru39@ukk2026.test', 1, 216, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(40, '198400900040', 'Ilma Amalia, S.Pd', 'guru40@ukk2026.test', 1, 217, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(41, '198410900041', 'Alvira Sofwatunnisa, S.Pd', 'guru41@ukk2026.test', 1, 218, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(42, '198420900042', 'Risna Andriyani, S.Pd', 'guru42@ukk2026.test', 1, 219, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(43, '198430900043', 'Dra. Tintin Noerhayati', 'guru43@ukk2026.test', 1, 220, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(44, '198440900044', 'Rizkia Fahira, S.Pd', 'guru44@ukk2026.test', 1, 221, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(45, '198450900045', 'Frasetio', 'guru45@ukk2026.test', 1, 222, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(46, '198460900046', 'Ratnaningsih Hatil Aeni, S.Pd.', 'guru46@ukk2026.test', 1, 223, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(47, '198470900047', 'Dheny Darajatul Hasani, S.Pd.', 'guru47@ukk2026.test', 1, 224, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(48, '198480900048', 'Fahrullah, S.Pd', 'guru48@ukk2026.test', 1, 225, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(49, '198490900049', 'Aan Andriana, S.T', 'guru49@ukk2026.test', 1, 226, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(50, '198500900050', 'Fitri Fauziah, S.T', 'guru50@ukk2026.test', 1, 227, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(56, 'WALI2026-004', 'Riesy Putri Navia, S.Kom', 'wali004@ukk2026.test', 1, 228, '2026-11-06 04:40:02', '2026-11-06 04:40:02'),
(79, 'WALI2026-028', 'Irvan Rizal, S.Pd', 'wali028@ukk2026.test', 1, 229, '2026-11-06 04:40:02', '2026-11-06 04:40:02'),
(81, 'WALI2026-030', 'Nida Aulia, S.Pd', 'wali030@ukk2026.test', 1, 230, '2026-11-06 04:40:02', '2026-11-06 04:40:02'),
(82, 'WALI2026-031', 'Sri Nurrizki Putri Krisnadi, S.Pd', 'wali031@ukk2026.test', 1, 231, '2026-11-06 04:40:02', '2026-11-06 04:40:02'),
(83, 'WALI2026-032', 'Rurry Febriani Maryadi, S.Pd', 'wali032@ukk2026.test', 1, 232, '2026-11-06 04:40:02', '2026-11-06 04:40:02');

-- --------------------------------------------------------

--
-- Table structure for table `t_kelas`
--

CREATE TABLE `t_kelas` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tingkat` varchar(20) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_kelas`
--

INSERT INTO `t_kelas` (`id`, `nama`, `tingkat`, `jurusan`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'XTKJ 1', 'X', 'TKJ', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(2, 'XBD', 'X', 'BD', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(3, 'XI BD', 'XI', 'BD', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(4, 'XI RPL 1', 'XI', 'RPL', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(5, 'XRPL 1', 'X', 'RPL', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(6, 'XII RPL 1', 'XII', 'RPL', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(7, 'XII TKJ 4', 'XII', 'TKJ', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(8, 'XRPL 2', 'X', 'RPL', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(9, 'XTKJ 2', 'X', 'TKJ', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(10, 'XII TKJ 1', 'XII', 'TKJ', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(11, 'XI TKR 2', 'XI', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(12, 'XII TKJ 3', 'XII', 'TKJ', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(13, 'XII RPL 2', 'XII', 'RPL', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(14, 'XTKJ 3', 'X', 'TKJ', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(15, 'XTKR 3', 'X', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(16, 'XI TKJ 1', 'XI', 'TKJ', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(17, 'XTSM 1', 'X', 'TSM', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(18, 'XII TKJ 2', 'XII', 'TKJ', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(19, 'XII BD', 'XII', 'BD', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(20, 'XTKR 4', 'X', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(21, 'XII TKR 4', 'XII', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(22, 'XTKR 1', 'X', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(23, 'XTSM 2', 'X', 'TSM', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(24, 'XI TKJ 2', 'XI', 'TKJ', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(25, 'XII TKR 5', 'XII', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(26, 'XII TSM 1', 'XII', 'TSM', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(27, 'XI TSM 2', 'XI', 'TSM', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(28, 'XTKR 2', 'X', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(29, 'XTSM 3', 'X', 'TSM', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(30, 'XII TSM 2', 'XII', 'TSM', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(31, 'XII TSM 3', 'XII', 'TSM', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(32, 'XII TKR 2', 'XII', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(33, 'XTKR 5', 'X', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(34, 'XI TKR 1', 'XI', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(35, 'XII TKR 1', 'XII', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(36, 'XI TKR 3', 'XI', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(37, 'XII TKR 3', 'XII', 'TKR', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(38, 'XI TSM 1', 'XI', 'TSM', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `t_kelas_siswa`
--

CREATE TABLE `t_kelas_siswa` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_kelas_siswa`
--

INSERT INTO `t_kelas_siswa` (`id`, `siswa_id`, `tahun_ajaran_id`, `kelas_id`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(2, 2, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(3, 3, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(4, 4, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(5, 5, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(6, 6, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(7, 7, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(8, 8, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(9, 9, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(10, 10, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(11, 11, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(12, 12, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(13, 13, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(14, 14, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(15, 15, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(16, 16, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(17, 17, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(18, 18, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(19, 19, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(20, 20, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(21, 21, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(22, 22, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(23, 23, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(24, 24, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(25, 25, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(26, 26, 1, 13, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(52, 54, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(53, 55, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(54, 56, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(55, 57, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(56, 58, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(57, 59, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(58, 60, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(59, 61, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(60, 62, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(61, 63, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(62, 64, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(63, 65, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(64, 66, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(65, 67, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(66, 68, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(67, 69, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(68, 70, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(69, 71, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(70, 72, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(71, 73, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(72, 74, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(73, 75, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(74, 76, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(75, 77, 1, 6, '2026-07-01', '2027-06-30', 1, '2026-11-09 07:42:21', '2026-11-09 07:42:21'),
(83, 78, 1, 31, '2024-05-07', '2027-02-04', 1, '2026-11-09 09:24:00', '2026-11-09 09:24:00'),
(85, 51, 2, 31, '2026-07-02', '2027-05-02', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(87, 79, 2, 31, '2026-07-20', '2027-06-11', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(89, 80, 2, 31, '2026-07-21', '2027-06-07', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(91, 81, 2, 31, '2026-07-09', '2027-05-13', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(92, 82, 1, 31, '2026-07-06', '2027-05-20', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(94, 83, 1, 31, '2026-07-28', '2027-06-23', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(97, 84, 2, 31, '2026-07-10', '2027-06-11', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(98, 85, 1, 31, '2026-07-14', '2027-05-10', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(100, 86, 1, 31, '2026-07-22', '2027-05-06', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(102, 87, 1, 31, '2026-07-15', '2027-05-28', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(105, 88, 2, 31, '2026-07-07', '2027-05-21', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(107, 89, 2, 31, '2026-07-11', '2027-05-05', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(108, 90, 1, 31, '2026-07-11', '2027-06-02', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(110, 91, 1, 31, '2026-07-09', '2027-05-04', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(112, 92, 1, 31, '2026-07-20', '2027-06-17', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(115, 93, 2, 31, '2026-07-26', '2027-06-15', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(117, 94, 2, 31, '2026-07-28', '2027-06-28', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(118, 95, 1, 31, '2026-07-06', '2027-05-05', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(121, 96, 2, 31, '2026-07-01', '2027-05-16', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(122, 97, 1, 31, '2026-07-07', '2027-05-18', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(124, 98, 1, 31, '2026-07-02', '2027-05-17', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(127, 99, 2, 31, '2026-07-24', '2027-05-20', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(129, 100, 2, 31, '2026-07-09', '2027-06-17', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(130, 101, 1, 31, '2026-07-02', '2027-06-26', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(132, 102, 1, 31, '2026-07-30', '2027-06-14', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(134, 103, 1, 31, '2026-07-10', '2027-05-19', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(136, 104, 1, 31, '2026-07-01', '2027-06-02', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(138, 105, 1, 31, '2026-07-08', '2027-05-20', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(140, 106, 1, 31, '2026-07-25', '2027-05-09', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(142, 107, 1, 31, '2026-07-11', '2027-06-22', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45'),
(144, 108, 1, 31, '2026-07-31', '2027-06-25', 1, '2026-11-09 09:26:45', '2026-11-09 09:26:45');

-- --------------------------------------------------------

--
-- Table structure for table `t_pelanggaran`
--

CREATE TABLE `t_pelanggaran` (
  `id` int(11) NOT NULL,
  `pelanggaran_kategori_id` int(11) NOT NULL,
  `kode` varchar(30) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `poin` int(11) NOT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_pelanggaran`
--

INSERT INTO `t_pelanggaran` (`id`, `pelanggaran_kategori_id`, `kode`, `nama`, `poin`, `deskripsi`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 1, 'PLG-001', 'Pelanggaran 1', 5, 'Detail ketentuan pelanggaran nomor 1.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(2, 2, 'PLG-002', 'Pelanggaran 2', 10, 'Detail ketentuan pelanggaran nomor 2.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(3, 3, 'PLG-003', 'Pelanggaran 3', 15, 'Detail ketentuan pelanggaran nomor 3.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(4, 4, 'PLG-004', 'Pelanggaran 4', 20, 'Detail ketentuan pelanggaran nomor 4.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(5, 5, 'PLG-005', 'Pelanggaran 5', 25, 'Detail ketentuan pelanggaran nomor 5.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(6, 6, 'PLG-006', 'Pelanggaran 6', 30, 'Detail ketentuan pelanggaran nomor 6.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(7, 7, 'PLG-007', 'Pelanggaran 7', 35, 'Detail ketentuan pelanggaran nomor 7.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(8, 8, 'PLG-008', 'Pelanggaran 8', 40, 'Detail ketentuan pelanggaran nomor 8.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(9, 9, 'PLG-009', 'Pelanggaran 9', 45, 'Detail ketentuan pelanggaran nomor 9.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(10, 10, 'PLG-010', 'Pelanggaran 10', 50, 'Detail ketentuan pelanggaran nomor 10.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(11, 1, 'PLG-011', 'Pelanggaran 11', 5, 'Detail ketentuan pelanggaran nomor 11.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(12, 2, 'PLG-012', 'Pelanggaran 12', 10, 'Detail ketentuan pelanggaran nomor 12.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(13, 3, 'PLG-013', 'Pelanggaran 13', 15, 'Detail ketentuan pelanggaran nomor 13.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(14, 4, 'PLG-014', 'Pelanggaran 14', 20, 'Detail ketentuan pelanggaran nomor 14.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(15, 5, 'PLG-015', 'Pelanggaran 15', 25, 'Detail ketentuan pelanggaran nomor 15.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(16, 6, 'PLG-016', 'Pelanggaran 16', 30, 'Detail ketentuan pelanggaran nomor 16.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(17, 7, 'PLG-017', 'Pelanggaran 17', 35, 'Detail ketentuan pelanggaran nomor 17.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(18, 8, 'PLG-018', 'Pelanggaran 18', 40, 'Detail ketentuan pelanggaran nomor 18.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(19, 9, 'PLG-019', 'Pelanggaran 19', 45, 'Detail ketentuan pelanggaran nomor 19.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(20, 10, 'PLG-020', 'Pelanggaran 20', 50, 'Detail ketentuan pelanggaran nomor 20.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(21, 1, 'PLG-021', 'Pelanggaran 21', 5, 'Detail ketentuan pelanggaran nomor 21.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(22, 2, 'PLG-022', 'Pelanggaran 22', 10, 'Detail ketentuan pelanggaran nomor 22.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(23, 3, 'PLG-023', 'Pelanggaran 23', 15, 'Detail ketentuan pelanggaran nomor 23.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(24, 4, 'PLG-024', 'Pelanggaran 24', 20, 'Detail ketentuan pelanggaran nomor 24.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(25, 5, 'PLG-025', 'Pelanggaran 25', 25, 'Detail ketentuan pelanggaran nomor 25.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(26, 6, 'PLG-026', 'Pelanggaran 26', 30, 'Detail ketentuan pelanggaran nomor 26.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(27, 7, 'PLG-027', 'Pelanggaran 27', 35, 'Detail ketentuan pelanggaran nomor 27.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(28, 8, 'PLG-028', 'Pelanggaran 28', 40, 'Detail ketentuan pelanggaran nomor 28.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(29, 9, 'PLG-029', 'Pelanggaran 29', 45, 'Detail ketentuan pelanggaran nomor 29.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(30, 10, 'PLG-030', 'Pelanggaran 30', 50, 'Detail ketentuan pelanggaran nomor 30.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(31, 1, 'PLG-031', 'Pelanggaran 31', 5, 'Detail ketentuan pelanggaran nomor 31.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(32, 2, 'PLG-032', 'Pelanggaran 32', 10, 'Detail ketentuan pelanggaran nomor 32.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(33, 3, 'PLG-033', 'Pelanggaran 33', 15, 'Detail ketentuan pelanggaran nomor 33.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(34, 4, 'PLG-034', 'Pelanggaran 34', 20, 'Detail ketentuan pelanggaran nomor 34.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(35, 5, 'PLG-035', 'Pelanggaran 35', 25, 'Detail ketentuan pelanggaran nomor 35.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(36, 6, 'PLG-036', 'Pelanggaran 36', 30, 'Detail ketentuan pelanggaran nomor 36.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(37, 7, 'PLG-037', 'Pelanggaran 37', 35, 'Detail ketentuan pelanggaran nomor 37.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(38, 8, 'PLG-038', 'Pelanggaran 38', 40, 'Detail ketentuan pelanggaran nomor 38.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(39, 9, 'PLG-039', 'Pelanggaran 39', 45, 'Detail ketentuan pelanggaran nomor 39.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(40, 10, 'PLG-040', 'Pelanggaran 40', 50, 'Detail ketentuan pelanggaran nomor 40.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(41, 1, 'PLG-041', 'Pelanggaran 41', 5, 'Detail ketentuan pelanggaran nomor 41.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(42, 2, 'PLG-042', 'Pelanggaran 42', 10, 'Detail ketentuan pelanggaran nomor 42.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(43, 3, 'PLG-043', 'Pelanggaran 43', 15, 'Detail ketentuan pelanggaran nomor 43.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(44, 4, 'PLG-044', 'Pelanggaran 44', 20, 'Detail ketentuan pelanggaran nomor 44.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(45, 5, 'PLG-045', 'Pelanggaran 45', 25, 'Detail ketentuan pelanggaran nomor 45.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(46, 6, 'PLG-046', 'Pelanggaran 46', 30, 'Detail ketentuan pelanggaran nomor 46.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(47, 7, 'PLG-047', 'Pelanggaran 47', 35, 'Detail ketentuan pelanggaran nomor 47.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(48, 8, 'PLG-048', 'Pelanggaran 48', 40, 'Detail ketentuan pelanggaran nomor 48.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(49, 9, 'PLG-049', 'Pelanggaran 49', 45, 'Detail ketentuan pelanggaran nomor 49.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(50, 10, 'PLG-050', 'Pelanggaran 50', 50, 'Detail ketentuan pelanggaran nomor 50.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `t_pelanggaran_kategori`
--

CREATE TABLE `t_pelanggaran_kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_pelanggaran_kategori`
--

INSERT INTO `t_pelanggaran_kategori` (`id`, `nama`, `deskripsi`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 'Terlambat 1', 'Datang ke sekolah setelah jam masuk.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(2, 'Tidak Memakai Seragam 2', 'Tidak menggunakan seragam sesuai ketentuan.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(3, 'Atribut Tidak Lengkap 3', 'Atribut seragam tidak lengkap.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(4, 'Tidak Mengerjakan Tugas 4', 'Tugas sekolah tidak dikerjakan.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(5, 'Tidak Membawa Buku 5', 'Tidak membawa buku pelajaran.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(6, 'Ribut di Kelas 6', 'Mengganggu kegiatan belajar mengajar.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(7, 'Membolos 7', 'Tidak mengikuti pembelajaran tanpa izin.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(8, 'Merokok 8', 'Merokok di lingkungan sekolah.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(9, 'Berkelahi 9', 'Terlibat perkelahian dengan siswa lain.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(10, 'Merusak Fasilitas 10', 'Merusak fasilitas sekolah.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(11, 'Terlambat 11', 'Datang ke sekolah setelah jam masuk.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(12, 'Tidak Memakai Seragam 12', 'Tidak menggunakan seragam sesuai ketentuan.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(13, 'Atribut Tidak Lengkap 13', 'Atribut seragam tidak lengkap.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(14, 'Tidak Mengerjakan Tugas 14', 'Tugas sekolah tidak dikerjakan.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(15, 'Tidak Membawa Buku 15', 'Tidak membawa buku pelajaran.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(16, 'Ribut di Kelas 16', 'Mengganggu kegiatan belajar mengajar.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(17, 'Membolos 17', 'Tidak mengikuti pembelajaran tanpa izin.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(18, 'Merokok 18', 'Merokok di lingkungan sekolah.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(19, 'Berkelahi 19', 'Terlibat perkelahian dengan siswa lain.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(20, 'Merusak Fasilitas 20', 'Merusak fasilitas sekolah.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(21, 'Terlambat 21', 'Datang ke sekolah setelah jam masuk.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(22, 'Tidak Memakai Seragam 22', 'Tidak menggunakan seragam sesuai ketentuan.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(23, 'Atribut Tidak Lengkap 23', 'Atribut seragam tidak lengkap.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(24, 'Tidak Mengerjakan Tugas 24', 'Tugas sekolah tidak dikerjakan.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(25, 'Tidak Membawa Buku 25', 'Tidak membawa buku pelajaran.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(26, 'Ribut di Kelas 26', 'Mengganggu kegiatan belajar mengajar.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(27, 'Membolos 27', 'Tidak mengikuti pembelajaran tanpa izin.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(28, 'Merokok 28', 'Merokok di lingkungan sekolah.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(29, 'Berkelahi 29', 'Terlibat perkelahian dengan siswa lain.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(30, 'Merusak Fasilitas 30', 'Merusak fasilitas sekolah.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(31, 'Terlambat 31', 'Datang ke sekolah setelah jam masuk.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(32, 'Tidak Memakai Seragam 32', 'Tidak menggunakan seragam sesuai ketentuan.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(33, 'Atribut Tidak Lengkap 33', 'Atribut seragam tidak lengkap.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(34, 'Tidak Mengerjakan Tugas 34', 'Tugas sekolah tidak dikerjakan.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(35, 'Tidak Membawa Buku 35', 'Tidak membawa buku pelajaran.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(36, 'Ribut di Kelas 36', 'Mengganggu kegiatan belajar mengajar.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(37, 'Membolos 37', 'Tidak mengikuti pembelajaran tanpa izin.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(38, 'Merokok 38', 'Merokok di lingkungan sekolah.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(39, 'Berkelahi 39', 'Terlibat perkelahian dengan siswa lain.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(40, 'Merusak Fasilitas 40', 'Merusak fasilitas sekolah.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(41, 'Terlambat 41', 'Datang ke sekolah setelah jam masuk.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(42, 'Tidak Memakai Seragam 42', 'Tidak menggunakan seragam sesuai ketentuan.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(43, 'Atribut Tidak Lengkap 43', 'Atribut seragam tidak lengkap.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(44, 'Tidak Mengerjakan Tugas 44', 'Tugas sekolah tidak dikerjakan.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(45, 'Tidak Membawa Buku 45', 'Tidak membawa buku pelajaran.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(46, 'Ribut di Kelas 46', 'Mengganggu kegiatan belajar mengajar.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(47, 'Membolos 47', 'Tidak mengikuti pembelajaran tanpa izin.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(48, 'Merokok 48', 'Merokok di lingkungan sekolah.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(49, 'Berkelahi 49', 'Terlibat perkelahian dengan siswa lain.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(50, 'Merusak Fasilitas 50', 'Merusak fasilitas sekolah.', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `t_pelanggaran_siswa`
--

CREATE TABLE `t_pelanggaran_siswa` (
  `id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `nama_siswa` varchar(150) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `nama_kelas` varchar(100) NOT NULL,
  `pelanggaran_id` int(11) NOT NULL,
  `nama_pelanggaran` varchar(150) NOT NULL,
  `pelanggaran_kategori_id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `nama_guru` varchar(150) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text NOT NULL,
  `poin` int(11) NOT NULL,
  `tindakan` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_pelanggaran_siswa`
--

INSERT INTO `t_pelanggaran_siswa` (`id`, `tahun_ajaran_id`, `siswa_id`, `nama_siswa`, `kelas_id`, `nama_kelas`, `pelanggaran_id`, `nama_pelanggaran`, `pelanggaran_kategori_id`, `guru_id`, `nama_guru`, `tanggal`, `keterangan`, `poin`, `tindakan`, `status`, `created_at`, `updated_at`) VALUES
(53, 1, 90, 'MOCHAMAD RIZQI ADITYA', 31, 'XII TSM 3', 13, 'Pelanggaran 13', 3, 32, 'Aldi Maulana, S.Pd', '2026-02-21', 'dfgdf', 15, 'Teguran', 'Diproses', '2026-11-10 01:44:16', '2026-11-10 01:44:16'),
(54, 1, 20, 'SAHRA FIRAJ', 13, 'XII RPL 2', 42, 'Pelanggaran 42', 2, 28, 'Ari Sandriana, S.Kom', '2026-06-07', '', 10, 'Teguran Lisan', 'Ditindaklanjuti', '2026-11-10 04:53:43', '2026-11-10 04:53:43'),
(58, 1, 2, 'AGNIA ROSYA NOERILLAH', 13, 'XII RPL 2', 8, '0', 8, 32, '0', '2026-11-11', 'gyugug', 40, 'Panggilan Orang Tua', 'Selesai', '2026-11-11 11:26:17', '2026-11-11 11:26:17');

-- --------------------------------------------------------

--
-- Table structure for table `t_siswa`
--

CREATE TABLE `t_siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(30) NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jenis_kelamin` char(1) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_siswa`
--

INSERT INTO `t_siswa` (`id`, `nis`, `nisn`, `nama`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, '261001', '0026000001', 'ADITIA NUR LANTIP', 'L', '2008-01-01', 'Jl. Pendidikan No. 1, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(2, '261002', '0026000002', 'AGNIA ROSYA NOERILLAH', 'P', '2009-02-02', 'Jl. Pendidikan No. 2, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(3, '261003', '0026000003', 'ANISA AMIRULLA', 'L', '2010-03-03', 'Jl. Pendidikan No. 3, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(4, '261004', '0026000004', 'ARADEA KANDI', 'P', '2008-04-04', 'Jl. Pendidikan No. 4, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(5, '261005', '0026000005', 'ARFA DIESA OKMALELIZA', 'L', '2009-05-05', 'Jl. Pendidikan No. 5, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(6, '261006', '0026000006', 'FITRI NUR SYIAMI', 'P', '2010-06-06', 'Jl. Pendidikan No. 6, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(7, '261007', '0026000007', 'JUNITA PUTRI NUR RAHMAN', 'L', '2008-07-07', 'Jl. Pendidikan No. 7, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(8, '261008', '0026000008', 'MOHAMMAD ATALARIK ALI SYAHBANA', 'P', '2009-08-08', 'Jl. Pendidikan No. 8, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(9, '261009', '0026000009', 'MUHAMAD FAHRIL FAHRUROZI', 'L', '2010-09-09', 'Jl. Pendidikan No. 9, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(10, '261010', '0026000010', 'MUHAMMAD ADNAN SYAEFUL ISLAMI', 'P', '2008-10-10', 'Jl. Pendidikan No. 10, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(11, '261011', '0026000011', 'MUHAMMAD ZAHRU AQILA FIKRI', 'L', '2009-11-11', 'Jl. Pendidikan No. 11, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(12, '261012', '0026000012', 'NADIA ZULFA SORAYA', 'P', '2010-12-12', 'Jl. Pendidikan No. 12, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(13, '261013', '0026000013', 'NAZRIL ILHAM', 'L', '2008-01-13', 'Jl. Pendidikan No. 13, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(14, '261014', '0026000014', 'PUSPITA DEWI', 'P', '2009-02-14', 'Jl. Pendidikan No. 14, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(15, '261015', '0026000015', 'RAKHA MUHAMMAD HAIKAL', 'L', '2010-03-15', 'Jl. Pendidikan No. 15, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(16, '261016', '0026000016', 'RANI MELANI', 'P', '2008-04-16', 'Jl. Pendidikan No. 16, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(17, '261017', '0026000017', 'REIHAN MUZAKI', 'L', '2009-05-17', 'Jl. Pendidikan No. 17, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(18, '261018', '0026000018', 'RESA JULIANSAH', 'P', '2010-06-18', 'Jl. Pendidikan No. 18, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(19, '261019', '0026000019', 'RIZKI PERMATA SUGIANTO', 'L', '2008-07-19', 'Jl. Pendidikan No. 19, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(20, '261020', '0026000020', 'SAHRA FIRAJ', 'P', '2009-08-20', 'Jl. Pendidikan No. 20, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(21, '261021', '0026000021', 'SENDI MARDIAN', 'L', '2010-09-21', 'Jl. Pendidikan No. 21, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(22, '261022', '0026000022', 'SHAHAIDAN NIZZAR', 'P', '2008-10-22', 'Jl. Pendidikan No. 22, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(23, '261023', '0026000023', 'SHERLY SATRIANI', 'L', '2009-11-23', 'Jl. Pendidikan No. 23, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(24, '261024', '0026000024', 'SOFI NURJANAH', 'P', '2010-12-24', 'Jl. Pendidikan No. 24, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(25, '261025', '0026000025', 'SURAYA SALSABILA AZZAHRA', 'L', '2008-01-25', 'Jl. Pendidikan No. 25, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(26, '261026', '0026000026', 'TONI ANTONIO', 'P', '2009-02-26', 'Jl. Pendidikan No. 26, Tasikmalaya', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(51, '1122334455', '1223334444', 'ZULFIKOR TULTHI', 'L', '2009-03-19', 'Jl. K.H Zainal Musthofa', 1, '2026-11-05 01:55:05', '2026-11-05 01:55:05'),
(54, '102410520', '0026110026', 'ALFIN RAHADIAN', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(55, '102410521', '0026110027', 'ALISA RAMADHANI', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(56, '102410522', '0026110028', 'ALMA AURA LIVIA', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(57, '102410523', '0026110029', 'ANIS KHOERUNNISA NUR RAMADANI', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(58, '102410524', '0026110030', 'AZMIL AMIRA', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(59, '102410525', '0026110031', 'AZRIL AKBAR FAWJI ESDILLAH', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(60, '102410527', '0026110032', 'ERLANGGA SAPUTRA HIDAYAT', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(61, '102410528', '0026110033', 'HILMA HUMMA TUNNAJAH', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(62, '102410529', '0026110034', 'ILMA NAJYA', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(63, '102410531', '0026110035', 'JULIANDA DHARMA PANGESTU', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(64, '102410532', '0026110036', 'KAYLA NUR FADILAH', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(65, '102410533', '0026110037', 'MOZA DINA NABILLAH', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(66, '102410534', '0026110038', 'MUHAMAD ALFARIZI', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(67, '102410535', '0026110039', 'MUHAMAD FAHRIN ADAM', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(68, '102410536', '0026110040', 'MUHAMAD ZIDANE MAULIDAN', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(69, '102410537', '0026110041', 'MUHAMMAD ZULFAN NURALFANSYAH', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(70, '102410538', '0026110042', 'NATIYA NUR AZKIYA', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(71, '102410539', '0026110043', 'NOVITA PUSPITASARI', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(72, '102410540', '0026110044', 'NUCKY ANDREAS FEBRIAN', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(73, '102410743', '0026110045', 'RADITYA JETI PRASETYA', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(74, '102410541', '0026110046', 'RANGGA RAMADIYANTO NURHAKIM', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(75, '102410542', '0026110047', 'RISQI MUHAMAD IHWANI', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(76, '102410753', '0026110048', 'SALWA SABILATUL BARAKAH', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(77, '102410543', '0026110049', 'TAZKIA ZAHRA', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:38:18', '2026-11-09 07:38:18'),
(78, '102410484', '0026110050', 'AKBAR MAULANA', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(79, '102410526', '0026110051', 'DAVIN REYSYA PUTRA', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(80, '102410489', '0026110052', 'DIKRI ERIKA', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(81, '102410490', '0026110053', 'DIPTA SEPTIANA', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(82, '102410491', '0026110054', 'DZIKRI WIGUNA', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(83, '102410492', '0026110055', 'FABIAN MAULIDAN SUSANTO', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(84, '102410493', '0026110056', 'FARID NURUL HUDA', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(85, '102410494', '0026110057', 'FIRMAN NUR ALAMSYAH', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(86, '102410495', '0026110058', 'INDRA IRANA PUTRA', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(87, '102410496', '0026110059', 'KINKIN NURUL YAKIN', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(88, '102410497', '0026110060', 'M IRSYAD NURFALLAH', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(89, '102410498', '0026110061', 'MOCHAMAD FATHAN MUYASAR', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(90, '102410499', '0026110062', 'MOCHAMAD RIZQI ADITYA', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(91, '102410500', '0026110063', 'MUHAMAD FAHRUL HENDRAWAN', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(92, '102410501', '0026110064', 'MUHAMAD RIZKI SYAPUTRA', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(93, '102410502', '0026110065', 'MUHAMMAD DIVRIAN', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(94, '102410504', '0026110066', 'MUHAMMAD RIDHO AL FATH', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(95, '102410505', '0026110067', 'MUHAMMAD ZAMZAM AL FARIZHI', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(96, '102410506', '0026110068', 'RAFFA RADITYA RASYID', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(97, '102410507', '0026110069', 'RAIHAN RIZKULLOH', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(98, '102410508', '0026110070', 'RANGGA ADITYA', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(99, '102410509', '0026110071', 'RAYHAN FIRDAUS RACHIMAT', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(100, '102410510', '0026110072', 'REHAN SYEHABULDIN', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(101, '102410511', '0026110073', 'RIDWANULLOH SETIAWAN', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(102, '102410513', '0026110074', 'RIZKY NUR AKBAR', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(103, '102410514', '0026110075', 'SALMAN ABDULLAH MAULAY', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(104, '102410515', '0026110076', 'SENDY DIMASTIAN', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(105, '102410516', '0026110077', 'SYAHAL SAID AL FAJR', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(106, '102410517', '0026110078', 'TACHA NURFADILAH', 'P', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(107, '102410518', '0026110079', 'WAWAN NOPIANA HIDAYAT', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49'),
(108, '102410519', '0026110080', 'YUDI SYAEPUL ANWAR', 'L', '2009-01-01', 'Tasikmalaya', 1, '2026-11-09 07:47:49', '2026-11-09 07:47:49');

-- --------------------------------------------------------

--
-- Table structure for table `t_tahun_ajaran`
--

CREATE TABLE `t_tahun_ajaran` (
  `id` int(11) NOT NULL,
  `nama` varchar(20) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_tahun_ajaran`
--

INSERT INTO `t_tahun_ajaran` (`id`, `nama`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, '2026/2027', '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(2, '2026/2027', '2026-07-01', '2027-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(3, '2027/2028', '2027-07-01', '2028-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(4, '2027/2028', '2027-07-01', '2028-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(5, '2028/2029', '2028-07-01', '2029-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(6, '2028/2029', '2028-07-01', '2029-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(7, '2029/2030', '2029-07-01', '2030-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(8, '2029/2030', '2029-07-01', '2030-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(9, '2030/2031', '2030-07-01', '2031-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(10, '2030/2031', '2030-07-01', '2031-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(11, '2031/2032', '2031-07-01', '2032-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(12, '2031/2032', '2031-07-01', '2032-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(13, '2032/2033', '2032-07-01', '2033-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(14, '2032/2033', '2032-07-01', '2033-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(15, '2033/2034', '2033-07-01', '2034-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(16, '2033/2034', '2033-07-01', '2034-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(17, '2034/2035', '2034-07-01', '2035-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(18, '2034/2035', '2034-07-01', '2035-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(19, '2035/2036', '2035-07-01', '2036-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(20, '2035/2036', '2035-07-01', '2036-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(21, '2036/2037', '2036-07-01', '2037-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(22, '2036/2037', '2036-07-01', '2037-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(23, '2037/2038', '2037-07-01', '2038-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(24, '2037/2038', '2037-07-01', '2038-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(25, '2038/2039', '2038-07-01', '2039-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(26, '2038/2039', '2038-07-01', '2039-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(27, '2039/2040', '2039-07-01', '2040-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(28, '2039/2040', '2039-07-01', '2040-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(29, '2040/2041', '2040-07-01', '2041-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(30, '2040/2041', '2040-07-01', '2041-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(31, '2041/2042', '2041-07-01', '2042-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(32, '2041/2042', '2041-07-01', '2042-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(33, '2042/2043', '2042-07-01', '2043-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(34, '2042/2043', '2042-07-01', '2043-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(35, '2043/2044', '2043-07-01', '2044-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(36, '2043/2044', '2043-07-01', '2044-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(37, '2044/2045', '2044-07-01', '2045-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(38, '2044/2045', '2044-07-01', '2045-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(39, '2045/2046', '2045-07-01', '2046-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(40, '2045/2046', '2045-07-01', '2046-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(41, '2046/2047', '2046-07-01', '2047-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(42, '2046/2047', '2046-07-01', '2047-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(43, '2047/2048', '2047-07-01', '2048-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(44, '2047/2048', '2047-07-01', '2048-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(45, '2048/2049', '2048-07-01', '2049-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(46, '2048/2049', '2048-07-01', '2049-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(47, '2049/2050', '2049-07-01', '2050-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(48, '2049/2050', '2049-07-01', '2050-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(49, '2050/2051', '2050-07-01', '2051-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(50, '2050/2051', '2050-07-01', '2051-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `t_users`
--

CREATE TABLE `t_users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT current_timestamp(),
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_users`
--

INSERT INTO `t_users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `created_at`, `updated_at`) VALUES
(1, 'User 1', 'user1@ukk2026.test', NULL, 'password123', '', 'admin', '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(178, 'Wawan Kurniawan, S.Pd.I', 'guru1@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(179, 'Ai Nuryaidah, S.Pd', 'guru2@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(180, 'Maya Permata Endah, S.Pd', 'guru3@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(181, 'Dody Jaenudin, S.Pd', 'guru4@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(182, 'Dedi Kardiman, S.Pd', 'guru5@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(183, 'Uca Musa Yusenda, S.Pd', 'guru6@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(184, 'Drs. Ahmad Nur Mukhlis', 'guru7@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(185, 'Drs. Heri', 'guru8@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(186, 'Asep Bastaman', 'guru9@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(187, 'H. Encep Maemun, S.T., M.Pd', 'guru10@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(188, 'Opik', 'guru11@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(189, 'Ida Farida, S.Pd', 'guru12@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(190, 'Idah Mahmudah, S.Pd', 'guru13@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(191, 'Drs. Muhamad Ali', 'guru14@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(192, 'Helmi Ali Akbar, S.Pd.I', 'guru15@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(193, 'Syeni Suswarini, S.Pd', 'guru16@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(194, 'Wiwid Yulianti, S.Pd', 'guru17@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(195, 'Ansor Muhajir, S.T', 'guru18@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(196, 'Ogi Gaosun Gias, S.Pd.I', 'guru19@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(197, 'Sandi Mulyana, S.Pd', 'guru20@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(198, 'Erna Suryani, S.Pd', 'guru21@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(199, 'Ai Wahidah, S.Ag', 'guru22@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(200, 'Chandra Aditya Yusup, S.Pd', 'guru23@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(201, 'Arif Kusmawan, S.Pd', 'guru24@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(202, 'Asep Taufiq Hidayat, S.Kom', 'guru25@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(203, 'Rendy Ichsan Kusumah, S.T', 'guru26@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(204, 'Feri Rahmawan, S.Pd', 'guru27@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(205, 'Ari Sandriana, S.Kom', 'guru28@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(206, 'Yaneu Nurdian, S.Pd, M.Pd', 'guru29@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(207, 'Ucu Muhammad Khoer N, S.Pd', 'guru30@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(208, 'Mira Dwi Rahmawati, S.Ag', 'guru31@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(209, 'Aldi Maulana, S.Pd', 'guru32@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(210, 'Eli Rosliah, S.Pd', 'guru33@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(211, 'Isni Nur Fitriani, S.Pd', 'guru34@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(212, 'Aanisah Putri Nur Aini, S.Pd', 'guru35@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(213, 'Yusi Luthfiani, S.Sos.I', 'guru36@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(214, 'Lilis Rustandy Mawardi, S.Pd., M.Pd', 'guru37@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(215, 'Yusrin Noer Sabrina, S.Pd', 'guru38@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(216, 'Senny Indayani, S.Pd', 'guru39@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(217, 'Ilma Amalia, S.Pd', 'guru40@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(218, 'Alvira Sofwatunnisa, S.Pd', 'guru41@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(219, 'Risna Andriyani, S.Pd', 'guru42@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(220, 'Dra. Tintin Noerhayati', 'guru43@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(221, 'Rizkia Fahira, S.Pd', 'guru44@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(222, 'Frasetio', 'guru45@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(223, 'Ratnaningsih Hatil Aeni, S.Pd.', 'guru46@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(224, 'Dheny Darajatul Hasani, S.Pd.', 'guru47@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(225, 'Fahrullah, S.Pd', 'guru48@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(226, 'Aan Andriana, S.T', 'guru49@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(227, 'Fitri Fauziah, S.T', 'guru50@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(228, 'Riesy Putri Navia, S.Kom', 'wali004@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(229, 'Irvan Rizal, S.Pd', 'wali028@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(230, 'Nida Aulia, S.Pd', 'wali030@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(231, 'Sri Nurrizki Putri Krisnadi, S.Pd', 'wali031@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59'),
(232, 'Rurry Febriani Maryadi, S.Pd', 'wali032@ukk2026.test', '2026-11-11 05:02:59', 'guru123', NULL, 'guru', '2026-11-11 05:02:59', '2026-11-11 05:02:59');

-- --------------------------------------------------------

--
-- Table structure for table `t_wali_kelas`
--

CREATE TABLE `t_wali_kelas` (
  `id` int(11) NOT NULL,
  `tahun_ajaran_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status_aktif` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `t_wali_kelas`
--

INSERT INTO `t_wali_kelas` (`id`, `tahun_ajaran_id`, `kelas_id`, `guru_id`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 21, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(2, 1, 2, 27, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(3, 1, 3, 16, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(4, 1, 4, 56, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(5, 1, 5, 18, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(6, 1, 6, 28, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(7, 1, 7, 41, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(8, 1, 8, 38, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(9, 1, 9, 48, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(10, 1, 10, 40, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(11, 1, 11, 11, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(12, 1, 12, 47, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(13, 1, 13, 32, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(14, 1, 14, 34, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(15, 1, 15, 44, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(16, 1, 16, 39, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(17, 1, 17, 50, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(18, 1, 18, 2, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(19, 1, 19, 29, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(20, 1, 20, 24, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(21, 1, 21, 24, '2026-07-01', '2027-06-30', 0, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(22, 1, 22, 15, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(23, 1, 23, 35, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(24, 1, 24, 26, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(25, 1, 25, 46, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(26, 1, 26, 22, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(27, 1, 27, 49, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(28, 1, 28, 79, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(29, 1, 29, 37, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(30, 1, 30, 81, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(31, 1, 31, 82, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(32, 1, 32, 83, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(33, 1, 33, 33, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(34, 1, 34, 31, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(35, 1, 35, 36, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(36, 1, 36, 30, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(37, 1, 37, 42, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00'),
(38, 1, 38, 23, '2026-07-01', '2027-06-30', 1, '2026-01-01 01:00:00', '2026-01-01 01:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_guru`
--
ALTER TABLE `t_guru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_guru_ibfk_1` (`user_id`);

--
-- Indexes for table `t_kelas`
--
ALTER TABLE `t_kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_kelas_siswa_ibfk_1` (`siswa_id`),
  ADD KEY `t_kelas_siswa_ibfk_2` (`tahun_ajaran_id`),
  ADD KEY `t_kelas_siswa_ibfk_3` (`kelas_id`);

--
-- Indexes for table `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_pelanggaran_ibfk_1` (`pelanggaran_kategori_id`);

--
-- Indexes for table `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `siswa_id_2` (`siswa_id`),
  ADD KEY `siswa_id_3` (`siswa_id`),
  ADD KEY `t_pelanggaran_siswa_ibfk_1` (`tahun_ajaran_id`),
  ADD KEY `t_pelanggaran_siswa_ibfk_3` (`kelas_id`),
  ADD KEY `t_pelanggaran_siswa_ibfk_4` (`pelanggaran_id`),
  ADD KEY `t_pelanggaran_siswa_ibfk_5` (`pelanggaran_kategori_id`),
  ADD KEY `t_pelanggaran_siswa_ibfk_6` (`guru_id`);

--
-- Indexes for table `t_siswa`
--
ALTER TABLE `t_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_users`
--
ALTER TABLE `t_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_wali_kelas_ibfk_1` (`tahun_ajaran_id`),
  ADD KEY `t_wali_kelas_ibfk_2` (`kelas_id`),
  ADD KEY `t_wali_kelas_ibfk_3` (`guru_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_guru`
--
ALTER TABLE `t_guru`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `t_kelas`
--
ALTER TABLE `t_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_pelanggaran_kategori`
--
ALTER TABLE `t_pelanggaran_kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `t_siswa`
--
ALTER TABLE `t_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `t_tahun_ajaran`
--
ALTER TABLE `t_tahun_ajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `t_users`
--
ALTER TABLE `t_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `t_guru`
--
ALTER TABLE `t_guru`
  ADD CONSTRAINT `t_guru_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `t_users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `t_kelas_siswa`
--
ALTER TABLE `t_kelas_siswa`
  ADD CONSTRAINT `t_kelas_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `t_siswa` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_kelas_siswa_ibfk_2` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_kelas_siswa_ibfk_3` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `t_pelanggaran`
--
ALTER TABLE `t_pelanggaran`
  ADD CONSTRAINT `t_pelanggaran_ibfk_1` FOREIGN KEY (`pelanggaran_kategori_id`) REFERENCES `t_pelanggaran_kategori` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `t_pelanggaran_siswa`
--
ALTER TABLE `t_pelanggaran_siswa`
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_1` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_2` FOREIGN KEY (`siswa_id`) REFERENCES `t_siswa` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_3` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_4` FOREIGN KEY (`pelanggaran_id`) REFERENCES `t_pelanggaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_5` FOREIGN KEY (`pelanggaran_kategori_id`) REFERENCES `t_pelanggaran_kategori` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_pelanggaran_siswa_ibfk_6` FOREIGN KEY (`guru_id`) REFERENCES `t_guru` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `t_wali_kelas`
--
ALTER TABLE `t_wali_kelas`
  ADD CONSTRAINT `t_wali_kelas_ibfk_1` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `t_tahun_ajaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_wali_kelas_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `t_kelas` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_wali_kelas_ibfk_3` FOREIGN KEY (`guru_id`) REFERENCES `t_guru` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
