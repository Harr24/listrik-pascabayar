-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2025 at 12:18 PM
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
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id_event` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `keterangan` text NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `tanggal_posting` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id_event`, `judul`, `keterangan`, `gambar`, `tanggal_posting`) VALUES
(1, 'Selalu melakukan Maintenance tepat waktu', 'kami selalu berupaya cepat tanggap dalam melayani masyarakat dan menjaganya agar membuat pelanggan kami nyaman', '6883554596bfc-electrician-3645069_1280.jpg', '2025-07-25 09:58:29'),
(2, 'Senyuman kalian itu prioritas kami', 'kami selalu berupaya menjaga agar tetap membuat pelanggan pengguna kami nyaman bahagia', '688355afc8aeb-power-pole-4090105_1280.jpg', '2025-07-25 10:00:15');

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
(1, 2, '12222222', 'cibinong, bogor', 1),
(2, 3, '11111111', 'ambar,cibinong,bogor', 2),
(3, 4, '1220230', 'bogor kota', 3),
(4, 5, '12345678', 'cikarang', 1);

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
(1, 3, '2025-07-25 10:41:42', 2500.00, 146970.00, '68834346ee44c-diagram-export-7-16-2025-8_50_43-PM.png', 1),
(2, 4, '2025-07-25 10:42:11', 2500.00, 137700.00, '68834363155ee-icon-meter.png', 1);

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
(1, 4, 7, '2025', 0, 100),
(2, 3, 7, '2025', 0, 100),
(3, 2, 7, '2025', 0, 100),
(4, 1, 7, '2025', 0, 100);

-- --------------------------------------------------------

--
-- Table structure for table `tagihan`
--

CREATE TABLE `tagihan` (
  `id_tagihan` int(11) NOT NULL,
  `id_penggunaan` int(11) NOT NULL,
  `jumlah_meter` float NOT NULL,
  `total_bayar` decimal(12,2) NOT NULL,
  `status` enum('belum_lunas','diproses','lunas') NOT NULL DEFAULT 'belum_lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tagihan`
--

INSERT INTO `tagihan` (`id_tagihan`, `id_penggunaan`, `jumlah_meter`, `total_bayar`, `status`) VALUES
(1, 1, 100, 135200.00, 'belum_lunas'),
(2, 2, 100, 169953.00, 'belum_lunas'),
(3, 3, 100, 144470.00, 'lunas'),
(4, 4, 100, 135200.00, 'lunas');

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
(2, 'R-1/TR', 1300, 1444.70),
(3, 'R-2/TR', 3500, 1699.53);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','pelanggan') NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `role`, `reset_token`, `token_expiry`) VALUES
(1, 'admin', '$2y$10$f7Y/pyrGfxveT.vqLd8tPePJf3PMa3D2gfWrFT98f75V5jvPsaT5S', 'Harry setiawan', 'admin', NULL, NULL),
(2, 'sofyan', '$2y$10$sGl6.P1hAXFOys3l3z4aMejOBU2M.cQiwouhyms4NbOkEJ/g/Xc4m', 'sofyan', 'pelanggan', NULL, NULL),
(3, 'rangga', '$2y$10$s1hXk9GcF9HT.QPnph7.beVvcgemQij0w.FULAQpRbVPF9ApFuy5a', 'rangga ', 'pelanggan', NULL, NULL),
(4, 'Harry', '$2y$10$CJJYrEXYODR2Sf6nwuE/gOnMEww4e4zD1NqaIwyqXBtB10Lk/QmNu', 'Harry', 'pelanggan', NULL, NULL),
(5, 'faisal', '$2y$10$b2aaBrUUG0IBSWACHdj0Je4dV0M3AjSn2dulgNHeQsRvNTYnEODlC', 'faisal', 'pelanggan', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id_event`);

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
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `penggunaan`
--
ALTER TABLE `penggunaan`
  MODIFY `id_penggunaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tagihan`
--
ALTER TABLE `tagihan`
  MODIFY `id_tagihan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tarif`
--
ALTER TABLE `tarif`
  MODIFY `id_tarif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
