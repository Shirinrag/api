-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2023 at 04:38 AM
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
-- Table structure for table `tbl_pass_price_slab`
--

CREATE TABLE `tbl_pass_price_slab` (
  `id` int(11) NOT NULL,
  `fk_place_id` int(11) DEFAULT NULL,
  `fk_vehicle_type_id` int(11) DEFAULT NULL,
  `no_of_days` varchar(100) DEFAULT NULL,
  `cost` double DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_pass_price_slab`
--
ALTER TABLE `tbl_pass_price_slab`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_place_id` (`fk_place_id`),
  ADD KEY `id` (`id`),
  ADD KEY `fk_vehicle_type_id` (`fk_vehicle_type_id`),
  ADD KEY `no_of_days` (`no_of_days`),
  ADD KEY `cost` (`cost`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_pass_price_slab`
--
ALTER TABLE `tbl_pass_price_slab`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
