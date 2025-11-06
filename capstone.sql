-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2025 at 06:16 AM
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
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `qr_code` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`userid`, `userimg`, `userName`, `email`, `purok`, `passWord`, `role`, `status`, `qr_code`, `reset_token`, `token_expiry`) VALUES
(25, NULL, 'Erecycle', 'erecyclematimbubong@gmail.com', 0, 'erecycle2025', 'superAdmin', 'approved', NULL, NULL, NULL),
(36, '1759298796_pickup.jpg', 'Admin', 'Admin@gmail.com', 0, 'Samic5709', 'admin', 'approved', 'qr_36.png', NULL, NULL),
(37, '1759298796_pickup.jpg', 'Geb Sanchez', 'sanchez.aquino.092@gmail.com', 3, 'Samic5709', 'user', 'approved', 'qr_37.png', NULL, NULL),
(38, NULL, 'Kailla Santos', 'kaillasantos.basc@gmail.com', 4, 'Kai04santos', 'user', 'approved', 'qr_38.png', '7cdc5f47a90526a43bd376b74aa642dd484abe224a909619ed253b0a186413b9', '2025-11-05 15:34:47'),
(39, NULL, 'Marron', 'kreidehsrmain@gmail.com', 4, 'Samic5709', 'user', 'approved', 'qr_39.png', NULL, NULL);

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
(10, 'Announcement 1', 'Test CSS', 'Archived', '2025-11-05', '1184118.jpg'),
(11, 'Si Eli tigang nanaman na Boang', 'Down bad chinese mofo', 'Posted', '2025-11-06', 'Screenshot 2025-11-04 211904.png');

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

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `id` int(11) NOT NULL,
  `record_name` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `rec_img` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`id`, `record_name`, `date`, `rec_img`, `user_id`) VALUES
(40, 'Geb Sanchez', '2025-11-05', '1762325623_1757030543_1757030494_OIP.webp', 37),
(42, 'Kailla Santos', '2025-11-05', NULL, 38),
(43, 'Geb Sanchez', '2025-11-06', NULL, 37);

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
(6, 40, 13, 15, 'kg'),
(7, 41, 13, 26, 'kg'),
(8, 42, 13, 26, 'kg'),
(9, 43, 13, 35, 'kg');

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
(13, 'Bakal', '1762324760_1761829012_1757030543_1757030494_OIP.webp'),
(14, 'Plastic', '');

-- --------------------------------------------------------

--
-- Table structure for table `rewards`
--

CREATE TABLE `rewards` (
  `reward_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_description` text NOT NULL,
  `product_points` int(11) NOT NULL,
  `sup_quantity` int(11) NOT NULL,
  `product_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rewards`
--

INSERT INTO `rewards` (`reward_id`, `product_name`, `product_description`, `product_points`, `sup_quantity`, `product_date`, `product_img`) VALUES
(8, 'Kape ni wally Bayola', 'bigyan ng KOPIKO YAN!!!!', 15, 0, '2025-11-04 16:00:00', 'Kopiko_BlancaTwinPack58g-Resized1_grande.jpeg'),
(9, 'Bigas', 'Jasmine Rice', 50, 0, '2025-11-04 16:00:00', '');

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
(3, 37, 8, 'Approved', '2025-11-05 06:56:38'),
(4, 37, 9, 'Approved', '2025-11-06 02:48:28');

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
-- Indexes for table `record_items`
--
ALTER TABLE `record_items`
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
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `announce_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `records`
--
ALTER TABLE `records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `record_items`
--
ALTER TABLE `record_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `recyclable`
--
ALTER TABLE `recyclable`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `reward_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_rewards`
--
ALTER TABLE `user_rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
