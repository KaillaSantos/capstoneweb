-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2025 at 09:08 AM
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
  `purok` int(11) NOT NULL,
  `passWord` varchar(20) NOT NULL,
  `role` varchar(10) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'not verified',
  `qr_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`userid`, `userimg`, `userName`, `email`, `purok`, `passWord`, `role`, `status`, `qr_code`) VALUES
(6, NULL, 'Kai Admin', 'kaiamboy@gmail.com', 0, 'kaiadmmin123', 'admin', 'not verified', NULL),
(8, NULL, 'Geb Micahel', 'user@gmail.com', 1, 'Samic57', 'user', 'not verified', NULL),
(15, NULL, 'Miguel', 'j@gmail.com', 3, '12345', 'user', 'not verified', NULL),
(19, NULL, 'Throy Dafielmoto', 'fuhrer@gmail.com', 6, '12345', 'user', 'approved', 'qr_19.png'),
(20, NULL, 'mico', 'gg@gmail.com', 6, '12345', 'user', 'approved', 'qr_20.png');

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
  `rec_img` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`id`, `record_name`, `date`, `rec_img`, `user_id`) VALUES
(21, 'Geb Micahel', '2025-10-27', '', 8),
(22, 'Kailla', '2025-10-26', '', 9),
(23, 'Plastic Bottles', '2025-10-30', '', 17),
(24, 'throy', '2025-10-30', '', 18),
(25, 'Throy Dafielmoto', '2025-10-30', '', 19),
(26, 'Throy Dafielmoto', '2025-10-30', '', 19),
(27, 'Throy Dafielmoto', '2025-11-02', '', 19);

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
(0, 21, 4, 21, 'kg'),
(0, 21, 5, 18, 'kg'),
(0, 21, 6, 15, 'kg'),
(0, 21, 9, 9, 'kg'),
(0, 22, 4, 32, 'pcs'),
(0, 22, 5, 14, 'kg'),
(0, 22, 6, 21, 'kg'),
(0, 22, 9, 7, 'kg'),
(0, 25, 4, 21, 'kg'),
(0, 25, 5, 16, 'pcs'),
(0, 26, 4, 100, 'kg'),
(0, 26, 5, 21, 'pcs'),
(0, 26, 6, 13, 'kg'),
(0, 26, 9, 5, 'kg'),
(0, 27, 4, 12, 'kg'),
(0, 27, 5, 34, 'pcs'),
(0, 27, 6, 10, 'kg'),
(0, 27, 9, 10, 'kg');

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

-- --------------------------------------------------------

--
-- Table structure for table `user_rewards`
--

CREATE TABLE `user_rewards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reward_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `date_redeemed` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_rewards`
--

INSERT INTO `user_rewards` (`id`, `user_id`, `reward_id`, `status`, `date_redeemed`) VALUES
(1, 19, 3, 'Pending', '2025-10-30 04:06:01');

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
-- Indexes for table `user_rewards`
--
ALTER TABLE `user_rewards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reward_id` (`reward_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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

--
-- AUTO_INCREMENT for table `user_rewards`
--
ALTER TABLE `user_rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_rewards`
--
ALTER TABLE `user_rewards`
  ADD CONSTRAINT `user_rewards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `account` (`userid`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_rewards_ibfk_2` FOREIGN KEY (`reward_id`) REFERENCES `rewards` (`reward_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
