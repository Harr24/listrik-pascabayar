-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2025 at 08:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `listrikaja`
--

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nomor_meter` varchar(20) NOT NULL,
  `alamat` text NOT NULL,
  `id_tarif` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `id_user`, `nomor_meter`, `alamat`, `id_tarif`) VALUES
(2, 3, '15220220', 'cikarang', 2),
(4, 5, '152202303', 'depok barat', 2),
(5, 6, '16220230', 'depok beji timur', 2),
(6, 7, '123546678', 'cibinong', 1),
(7, 8, '1222222222', 'cibinong', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL,
  `tanggal_bayar` datetime NOT NULL,
  `biaya_admin` decimal(10,2) NOT NULL,
  `total_akhir` decimal(12,2) NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `id_admin` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_tagihan`, `tanggal_bayar`, `biaya_admin`, `total_akhir`, `bukti_bayar`, `id_admin`) VALUES
(1, 2, '2025-07-23 20:41:26', 2500.00, 1230495.00, '68812cd66d45d-erpr.jpg', 1),
(2, 4, '2025-07-24 07:30:59', 2500.00, 678500.00, '6881c5130b856-databes.png', NULL),
(3, 5, '2025-07-24 07:33:09', 2500.00, 678500.00, '6881c595cc16f-databes.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `penggunaan`
--

CREATE TABLE `penggunaan` (
  `id_penggunaan` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `bulan` int(2) NOT NULL,
  `tahun` year(4) NOT NULL,
  `meter_awal` float NOT NULL,
  `meter_akhir` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penggunaan`
--

INSERT INTO `penggunaan` (`id_penggunaan`, `id_pelanggan`, `bulan`, `tahun`, `meter_awal`, `meter_akhir`) VALUES
(1, 2, 1, '2025', 1000, 1150),
(2, 2, 7, '2025', 1150, 2000),
(3, 5, 7, '2025', 0, 300),
(4, 7, 7, '2025', 0, 500),
(5, 6, 7, '2025', 0, 500),
(6, 4, 7, '2025', 0, 900),
(7, 6, 8, '2025', 500, 1000);

-- --------------------------------------------------------

--
-- Table structure for table `tagihan`
--

CREATE TABLE `tagihan` (
  `id_tagihan` int(11) NOT NULL,
  `id_penggunaan` int(11) NOT NULL,
  `jumlah_meter` float NOT NULL,
  `total_bayar` decimal(12,2) NOT NULL,
  `status` enum('belum_lunas','diproses','lunas') NOT NULL DEFAULT 'belum_lunas',
  `tanggal_bayar` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tagihan`
--

INSERT INTO `tagihan` (`id_tagihan`, `id_penggunaan`, `jumlah_meter`, `total_bayar`, `status`, `tanggal_bayar`) VALUES
(1, 1, 150, 216705.00, 'belum_lunas', NULL),
(2, 2, 850, 1227995.00, 'lunas', NULL),
(3, 3, 300, 433410.00, 'belum_lunas', NULL),
(4, 4, 500, 676000.00, 'diproses', NULL),
(5, 5, 500, 676000.00, 'lunas', NULL),
(6, 6, 900, 1300230.00, 'belum_lunas', NULL),
(7, 7, 500, 676000.00, 'belum_lunas', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tarif`
--

CREATE TABLE `tarif` (
  `id_tarif` int(11) NOT NULL,
  `golongan_tarif` varchar(10) NOT NULL,
  `daya` int(11) NOT NULL,
  `tarif_per_kwh` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tarif`
--

INSERT INTO `tarif` (`id_tarif`, `golongan_tarif`, `daya`, `tarif_per_kwh`) VALUES
(1, 'R-1/TR', 900, 1352.00),
(2, 'R-1/TR', 1300, 1444.70);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','pelanggan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `role`) VALUES
(1, 'admin', '$2y$10$btmbyW9rBw.JqoVZSLyiP.jevTE6Dt9KKEzSy89nFcL6aBnU93hpO', 'Harry Setiawan', 'admin'),
(2, 'harryaja', '$2y$10$HMvmuE8IqQFAYL/GAArPF.AhzE6pRU3yV8P7Smwya5xxQPNpK/YU6', 'harryaja', 'pelanggan'),
(3, 'faisal', '$2y$10$744MRMQVEHc25hjm46/oWuUPIb.G97gpq9e1XWDU9x5JgyL5bjh4i', 'faisal', 'pelanggan'),
(4, 'satuaja', '$2y$10$4S6j3funf2kuZ29tXsAdEOt7bh777qyXm0we6NEmBs0YXr8YgiPKm', 'akui', 'pelanggan'),
(5, 'harry', '$2y$10$RSzAXwcq7ZPA2ufGy2td8uxQ.uWiFJsgW2.MeMWsQvvlC5LeX7x3O', 'Harry Setiawan', 'pelanggan'),
(6, 'azrial', '$2y$10$CGhBce/P6tS/LkQrMR5n/.aQ8pOPDa590JQDN7lUi37yqeKgYUP/S', 'azrial', 'pelanggan'),
(7, 'sofyan', '$2y$10$tT/6iJXKuqi4jMfzWRmo6eUR/6TTnEQI5PbhkRhxGsOGahYR9MDjW', 'sofyan', 'pelanggan'),
(8, 'rangga', '$2y$10$2M3PoM4r4nx9cAEDVpNUuuJJ7XxKdCeONYLqhJzaZ7KQvwW.Ndcj2', 'rangga', 'pelanggan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD UNIQUE KEY `nomor_meter` (`nomor_meter`),
  ADD KEY `pelanggan_ibfk_1` (`id_user`),
  ADD KEY `pelanggan_ibfk_2` (`id_tarif`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `pembayaran_ibfk_1` (`id_tagihan`);

--
-- Indexes for table `penggunaan`
--
ALTER TABLE `penggunaan`
  ADD PRIMARY KEY (`id_penggunaan`),
  ADD KEY `penggunaan_ibfk_1` (`id_pelanggan`);

--
-- Indexes for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD PRIMARY KEY (`id_tagihan`),
  ADD KEY `tagihan_ibfk_1` (`id_penggunaan`);

--
-- Indexes for table `tarif`
--
ALTER TABLE `tarif`
  ADD PRIMARY KEY (`id_tarif`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penggunaan`
--
ALTER TABLE `penggunaan`
  MODIFY `id_penggunaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tagihan`
--
ALTER TABLE `tagihan`
  MODIFY `id_tagihan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tarif`
--
ALTER TABLE `tarif`
  MODIFY `id_tarif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `pelanggan_ibfk_2` FOREIGN KEY (`id_tarif`) REFERENCES `tarif` (`id_tarif`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_tagihan`) REFERENCES `tagihan` (`id_tagihan`);

--
-- Constraints for table `penggunaan`
--
ALTER TABLE `penggunaan`
  ADD CONSTRAINT `penggunaan_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`);

--
-- Constraints for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD CONSTRAINT `tagihan_ibfk_1` FOREIGN KEY (`id_penggunaan`) REFERENCES `penggunaan` (`id_penggunaan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
