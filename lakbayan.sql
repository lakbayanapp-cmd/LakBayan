-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 08, 2025 at 12:40 PM
-- Server version: 10.4.33-MariaDB-log
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lakbayan`
--

-- --------------------------------------------------------

--
-- Table structure for table `terminals`
--

CREATE TABLE `terminals` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `base_rate` decimal(10,2) NOT NULL DEFAULT 11.00,
  `per_km_rate` decimal(10,2) NOT NULL DEFAULT 1.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `terminals`
--

INSERT INTO `terminals` (`id`, `name`, `latitude`, `longitude`, `image`, `base_rate`, `per_km_rate`, `created_at`) VALUES
(1, 'SIGNAL Terminal (SUBVTODA)', 14.50884900, 121.06541900, 'assets/images/signal.jpg', 11.00, 1.00, '2025-09-08 10:50:52'),
(2, 'HAGONOY Terminal (BBEVTODA)', 14.53024523, 121.05693677, 'assets/images/hagonoy.jpg', 11.00, 1.00, '2025-09-08 10:50:52'),
(3, 'LOWER BICUTAN Terminal (People\'s Market)', 14.48796356, 121.06062852, 'assets/images/lowerbicutan.jpg', 11.00, 1.00, '2025-09-08 10:50:52'),
(4, 'USUSAN Terminal', 14.53230000, 121.06950000, 'assets/images/usuan.jpg', 11.00, 1.00, '2025-09-08 10:50:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 0,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `profile` varchar(255) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `type`, `email`, `password`, `number`, `gender`, `profile`, `address`, `birthdate`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 1, 'admin@gmail.com', '$2y$10$8d77NMe2W7DH4lCwJub2cuk0eqNlFOmCpD.U0EoJNfeGNSgffwbYu', '6395331809252', 'Male', '1757332694_t.png', '123 Rizal Avenue, Pasay City, Metro Manila, 1300, Philippines', '2004-07-12', '2025-07-20 20:45:05', '2025-09-08 11:58:14'),
(2, 'User', 0, 'user@gmail.com', '$2a$12$.IBIMWLVhU04uwmm/NCVX.DtRVHZuP4EMO1H6Qv36uTbSoGEn5ROi', '639533180925', 'Male', 'avatar-6.jpg', '123 Rizal Avenue, Pasay City, Metro Manila, 1300, Philippines', '2004-07-07', '2025-07-20 20:45:05', '2025-07-24 20:32:18'),
(3, 'Sean', 0, 'seancvpugosa@gmail.com', '$2y$10$XMDjJDXurRCpg3u3Rjfab.wuKCByngbWpnpPQUfl0RhCErsZATsa.', '639533180925', NULL, NULL, 'Address', '2025-04-04', '2025-07-21 21:05:06', '2025-09-02 14:23:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `terminals`
--
ALTER TABLE `terminals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `terminals`
--
ALTER TABLE `terminals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
