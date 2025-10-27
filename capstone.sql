-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 25, 2025 at 10:02 AM
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
-- Database: `capstone`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `userid` int(11) NOT NULL,
  `userimg` varchar(220) DEFAULT NULL,
  `userName` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `passWord` varchar(20) NOT NULL,
  `role` varchar(10) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'not verified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`userid`, `userimg`, `userName`, `email`, `passWord`, `role`, `status`) VALUES
(1, 'defaultuserimage.jpg', 'Admin1', 'admin123@gmail.com', 'admin123', '', 'not verified'),
(2, 'geb.jpg', 'Geb Sanchez', 'gebsanchez@gmail.com', 'gebsanchez', '', 'not verified'),
(3, '', ' Mico Veriño', 'verinomico@gmail.com', 'micoVerino', '', 'not verified'),
(4, '', 'geb michael', 'kreidehsrmain@gmail.com', 'P@$$w0rd!', '', 'not verified'),
(5, NULL, 'Kailla Santos', 'kaillasantos@gmail.com', 'kailla123', 'user', 'not verified'),
(6, NULL, 'Kai Amboy', 'kaiamboy@gmail.com', 'kaiamboy', 'admin', 'not verified');

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `announce_id` int(11) NOT NULL,
  `announce_name` varchar(255) NOT NULL,
  `announce_text` text NOT NULL,
  `status` text NOT NULL DEFAULT 'Posted',
  `announce_date` date NOT NULL,
  `announce_img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`announce_id`, `announce_name`, `announce_text`, `status`, `announce_date`, `announce_img`) VALUES
(1, 'Test Title1', 'here lies the test part of the web and mobile', 'Posted', '2025-09-12', 'HiPaint_1745812131005.png'),
(2, 'Test Title2', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'Posted', '2025-09-12', 'announcement1.jpg'),
(3, 'archived title', 'nice ', 'Archived', '2025-02-02', 'curtain.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `pickup_date` date NOT NULL,
  `pickup_time` time NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `address`, `image_path`, `pickup_date`, `pickup_time`, `status`, `created_at`) VALUES
(2, 1, 'User address here', '1759277896_pickup.jpg', '2025-10-03', '09:30:00', 'Approved', '2025-10-01 00:18:16');

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `id` int(11) NOT NULL,
  `record_name` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `rec_img` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`id`, `record_name`, `date`, `rec_img`) VALUES
(11, 'Mico', '2025-02-09', '1759338106_waguri padoru.jpg'),
(12, 'awaa', '2025-02-04', ''),
(13, 'www', '2025-02-04', ''),
(14, 'qweqwe', '2220-02-22', ''),
(15, 'aaaa', '0312-02-12', ''),
(16, 'aaaaa', '1231-02-13', ''),
(17, 'asdw', '2025-12-21', '');

-- --------------------------------------------------------

--
-- Table structure for table `record_items`
--

CREATE TABLE `record_items` (
  `id` int(11) NOT NULL,
  `record_id` int(11) NOT NULL,
  `recyclable_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 0,
  `unit` varchar(10) NOT NULL DEFAULT 'kg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `record_items`
--

INSERT INTO `record_items` (`id`, `record_id`, `recyclable_id`, `quantity`, `unit`) VALUES
(0, 11, 4, 1, 'kg'),
(0, 11, 5, 1, 'kg'),
(0, 11, 6, 1, 'kg'),
(0, 11, 9, 1, 'kg'),
(0, 12, 4, 1, 'kg'),
(0, 12, 5, 1, 'kg'),
(0, 12, 6, 1, 'kg'),
(0, 12, 9, 1, 'kg'),
(0, 13, 4, 1, 'kg'),
(0, 13, 5, 1, 'kg'),
(0, 13, 6, 1, 'kg'),
(0, 13, 9, 1, 'kg'),
(0, 14, 4, 12, 'kg'),
(0, 14, 5, 32, 'kg'),
(0, 14, 6, 4, 'kg'),
(0, 14, 9, 2, 'kg'),
(0, 15, 4, 22, 'kg'),
(0, 15, 5, 22, 'kg'),
(0, 15, 6, 22, 'kg'),
(0, 15, 9, 22, 'kg'),
(0, 16, 4, 123, 'kg'),
(0, 16, 5, 12, 'kg'),
(0, 16, 6, 2, 'kg'),
(0, 17, 4, 12, 'kg'),
(0, 17, 5, 24, 'kg'),
(0, 17, 6, 7, 'kg'),
(0, 17, 9, 6, 'kg');

-- --------------------------------------------------------

--
-- Table structure for table `recyclable`
--

CREATE TABLE `recyclable` (
  `id` int(20) NOT NULL,
  `RM_name` varchar(20) NOT NULL COMMENT '20',
  `RM_img` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recyclable`
--

INSERT INTO `recyclable` (`id`, `RM_name`, `RM_img`) VALUES
(4, 'Plastic Bottle', 'plastic-bottle.jpg'),
(5, 'CardBoard', 'cardboard.jpg'),
(6, 'Tin Cans', 'tincans.jpg'),
(9, 'Bakal', '1757030543_1757030494_OIP.webp');

-- --------------------------------------------------------

--
-- Table structure for table `rewards`
--

CREATE TABLE `rewards` (
  `reward_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_description` text NOT NULL,
  `product_points` int(11) NOT NULL,
  `product_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rewards`
--

INSERT INTO `rewards` (`reward_id`, `product_name`, `product_description`, `product_points`, `product_date`, `product_img`) VALUES
(3, 'Rice', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Id sed reiciendis, molestias vitae consequatur in debitis cupiditate. Sint vitae ratione harum labore delectus sunt reprehenderit, eos repellat et. Distinctio, qui.', 100, '2025-10-14 16:00:00', 'roice.jpg'),
(4, 'bigas', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Id sed reiciendis, molestias vitae consequatur in debitis cupiditate. Sint vitae ratione harum labore delectus sunt reprehenderit, eos repellat et. Distinctio, qui.', 129381, '2025-11-14 16:00:00', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`announce_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recyclable`
--
ALTER TABLE `recyclable`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `RM_name` (`RM_name`);

--
-- Indexes for table `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`reward_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `announce_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `records`
--
ALTER TABLE `records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `recyclable`
--
ALTER TABLE `recyclable`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `reward_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
