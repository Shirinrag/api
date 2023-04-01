-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2023 at 05:29 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `parking_adda`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pass_days`
--

CREATE TABLE `tbl_pass_days` (
  `id` int(11) NOT NULL,
  `no_of_days` varchar(100) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_pass_days`
--

INSERT INTO `tbl_pass_days` (`id`, `no_of_days`, `sequence`, `created_at`, `updated_at`) VALUES
(1, '7 days', 1, '2023-04-01 08:59:15', '2023-04-01 08:59:15'),
(2, '30 Days', 2, '2023-04-01 08:59:15', '2023-04-01 08:59:15'),
(3, '90 Days', 3, '2023-04-01 08:59:15', '2023-04-01 08:59:15'),
(4, '180 Days', 4, '2023-04-01 08:59:15', '2023-04-01 08:59:15'),
(5, '365 Days', 5, '2023-04-01 08:59:15', '2023-04-01 08:59:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_pass_days`
--
ALTER TABLE `tbl_pass_days`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_pass_days`
--
ALTER TABLE `tbl_pass_days`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
