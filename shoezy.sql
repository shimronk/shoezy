-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2025 at 07:12 AM
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
-- Database: `shoezy`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `shipping_method` enum('pickup','delivery') NOT NULL DEFAULT 'delivery',
  `payment_method` enum('cod','card') NOT NULL DEFAULT 'cod',
  `status` enum('pending','paid','shipped','completed','canceled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `created_at`, `full_name`, `phone`, `address`, `city`, `postal_code`, `note`, `shipping_method`, `payment_method`, `status`) VALUES
(1, 9, 16000.00, '2025-09-17 01:13:02', NULL, NULL, NULL, NULL, NULL, NULL, 'delivery', 'cod', 'pending'),
(2, 2, 16000.00, '2025-09-17 01:14:39', NULL, NULL, NULL, NULL, NULL, NULL, 'delivery', 'cod', 'pending'),
(3, 2, 22200.00, '2025-09-17 01:44:26', NULL, NULL, NULL, NULL, NULL, NULL, 'delivery', 'cod', 'pending'),
(4, 2, 24500.00, '2025-09-17 02:25:20', NULL, NULL, NULL, NULL, NULL, NULL, 'delivery', 'cod', 'pending'),
(5, 2, 8600.00, '2025-09-17 02:53:15', NULL, NULL, NULL, NULL, NULL, NULL, 'delivery', 'cod', 'pending'),
(6, 14, 14200.00, '2025-09-17 02:56:04', NULL, NULL, NULL, NULL, NULL, NULL, 'delivery', 'cod', 'pending'),
(7, 14, 27950.00, '2025-09-17 02:56:40', NULL, NULL, NULL, NULL, NULL, NULL, 'delivery', 'cod', 'pending'),
(8, 14, 24500.00, '2025-09-17 02:58:30', NULL, NULL, NULL, NULL, NULL, NULL, 'delivery', 'cod', 'pending'),
(9, 14, 9250.00, '2025-09-17 03:00:48', NULL, NULL, NULL, NULL, NULL, NULL, 'delivery', 'cod', 'pending'),
(10, 14, 27600.00, '2025-09-17 03:33:51', NULL, NULL, NULL, NULL, NULL, NULL, 'delivery', 'cod', 'pending'),
(11, 2, 27950.00, '2025-09-17 04:05:58', NULL, NULL, NULL, NULL, NULL, NULL, 'delivery', 'cod', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size` varchar(10) NOT NULL,
  `qty` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `size`, `qty`, `price_at_purchase`) VALUES
(1, 1, 11, '', 1, NULL),
(2, 2, 90, '', 1, NULL),
(3, 3, 96, '', 1, NULL),
(4, 4, 95, '', 1, NULL),
(5, 5, 35, '38', 1, 2200.00),
(6, 5, 34, '38', 1, 6400.00),
(7, 6, 23, '38', 1, 14200.00),
(8, 7, 94, '36', 1, 27950.00),
(9, 8, 95, '35', 1, 24500.00),
(10, 9, 22, '44', 1, 9250.00),
(11, 10, 12, '44', 3, 9200.00),
(12, 11, 94, '39', 1, 27950.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `category` varchar(32) DEFAULT 'general',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `category`, `image`, `created_at`) VALUES
(1, 'Chestnut Lace-Up Formals', '', 14500.00, 10, 'men', 'images/m6.jpg', '2025-09-17 00:15:54'),
(2, 'Urban Leather Slide-Ons', '', 14500.00, 10, 'men', 'images/m6.jpg', '2025-09-17 00:15:54'),
(3, 'ShadowFlex Trainers', '', 11200.00, 10, 'men', 'images/m1.jpg', '2025-09-17 00:15:54'),
(4, 'Dockside Drift Decks', '', 15600.00, 10, 'men', 'images/m12.jpg', '2025-09-17 00:15:54'),
(5, 'CozyWalk Indoor Slippers', '', 20500.00, 10, 'men', 'images/m11.jpg', '2025-09-17 00:15:54'),
(6, 'CloudStride Essentials', '', 4200.00, 10, 'men', 'images/m2.jpg', '2025-09-17 00:15:54'),
(7, 'Sahara Soft-Step Moccasins', '', 19400.00, 10, 'men', 'images/m14.jpg', '2025-09-17 00:15:54'),
(8, 'Rustico Slip-On Loafers', '', 13600.00, 10, 'men', 'images/m17.jpg', '2025-09-17 00:15:54'),
(9, 'CoastCraft Twin-Tone Decks', '', 13900.00, 10, 'men', 'images/m7.jpg', '2025-09-17 00:15:54'),
(10, 'Harborline Classic Decks', '', 9750.00, 10, 'men', 'images/m9.jpg', '2025-09-17 00:15:54'),
(11, 'MetroGrip Casual Sandals', '', 16000.00, 10, 'men', 'images/m13.jpg', '2025-09-17 00:15:54'),
(12, 'Mariner Luxe Moc Decks', '', 9200.00, 10, 'men', 'images/m5.jpg', '2025-09-17 00:15:54'),
(13, 'Strappy Block Heels', '', 13950.00, 10, 'women', 'images/w3.jpg', '2025-09-17 00:15:54'),
(14, 'Tan Slip-On Sandals', '', 13950.00, 10, 'women', 'images/w3.jpg', '2025-09-17 00:15:54'),
(15, 'White Mid-Heel Sandals', '', 10750.00, 10, 'women', 'images/w19.jpg', '2025-09-17 00:15:54'),
(16, 'Elegant Strappy Heels', '', 9200.00, 10, 'women', 'images/w10.jpg', '2025-09-17 00:15:54'),
(17, 'Casual White Sneakers', '', 16500.00, 10, 'women', 'images/w21.jpg', '2025-09-17 00:15:54'),
(18, 'Luxe Gold Sandals', '', 13200.00, 10, 'women', 'images/w23.jpg', '2025-09-17 00:15:54'),
(19, 'Classic Ivory Block Sandals', '', 9900.00, 10, 'women', 'images/w20.jpg', '2025-09-17 00:15:54'),
(20, 'Blush Pink Slingbacks', '', 11550.00, 10, 'women', 'images/w14.jpg', '2025-09-17 00:15:54'),
(21, 'Sleek Black Flats', '', 14850.00, 10, 'women', 'images/w15.jpg', '2025-09-17 00:15:54'),
(22, 'Scarlet Pointed Slingbacks', '', 9250.00, 10, 'women', 'images/w13.jpg', '2025-09-17 00:15:54'),
(23, 'Pearl White Slip-Ons ', '', 14200.00, 10, 'women', 'images/w16.jpg', '2025-09-17 00:15:54'),
(24, 'Noir Chunky Platforms', '', 12200.00, 10, 'women', 'images/w18.jpg', '2025-09-17 00:15:54'),
(25, 'SmartStride Formal Walks', '', 7950.00, 10, 'kids', 'images/k19.jpg', '2025-09-17 00:15:54'),
(26, 'GlamStep Shine Heels', '', 7950.00, 10, 'kids', 'images/k19.jpg', '2025-09-17 00:15:54'),
(27, 'CozyHop Soft Walks', '', 5550.00, 10, 'kids', 'images/k17.jpg', '2025-09-17 00:15:54'),
(28, 'PrincessCharm Dress Walks', '', 2200.00, 10, 'kids', 'images/k16.jpg', '2025-09-17 00:15:54'),
(29, 'DockFlex Smart Steps', '', 3700.00, 10, 'kids', 'images/k4.jpg', '2025-09-17 00:15:54'),
(30, 'MiniCaptain Deckers', '', 4999.00, 10, 'kids', 'images/k6.jpg', '2025-09-17 00:15:54'),
(31, 'BoldStep Adjustable Slips', '', 3500.00, 10, 'kids', 'images/k1.jpg', '2025-09-17 00:15:54'),
(32, 'TwinkleDash Light Steps', '', 2000.00, 10, 'kids', 'images/k8.jpg', '2025-09-17 00:15:54'),
(33, 'SailCharm Step Ons', '', 3550.00, 10, 'kids', 'images/k12.jpg', '2025-09-17 00:15:54'),
(34, 'TrekSlide Strap Ons', '', 6400.00, 10, 'kids', 'images/k13.jpg', '2025-09-17 00:15:54'),
(35, 'LuxeFluff Kids Slippers', '', 2200.00, 10, 'kids', 'images/k18.jpg', '2025-09-17 00:15:54'),
(36, 'GlowBelle Fancy Shoes', '', 2000.00, 10, 'kids', 'images/k15.jpg', '2025-09-17 00:15:54'),
(85, 'BlazeCore Trail Runners', '', 23950.00, 10, 'sport', 'images/r2.jpg', '2025-09-17 00:39:51'),
(86, 'AeroFit Flex Runners', '', 23950.00, 10, 'sport', 'images/r2.jpg', '2025-09-17 00:39:51'),
(87, 'VeloGrip Pro Cycling Shoes', '', 19250.00, 10, 'sport', 'images/r6.jpg', '2025-09-17 00:39:51'),
(88, 'Alpine White Sneakers', '', 22800.00, 10, 'sport', 'images/r13.jpg', '2025-09-17 00:39:51'),
(89, 'AeroStride Mesh Runners', '', 15750.00, 10, 'sport', 'images/r5.jpg', '2025-09-17 00:39:51'),
(90, 'SilverNova Sports Shoes', '', 16000.00, 10, 'sport', 'images/r10.jpg', '2025-09-17 00:39:51'),
(91, 'ShadowFlex Trainers', '', 23900.00, 10, 'sport', 'images/r4.jpg', '2025-09-17 00:39:51'),
(92, 'RoseWave Performance Sneakers', '', 21700.00, 10, 'sport', 'images/r1.jpg', '2025-09-17 00:39:51'),
(93, 'Titan Trek Boots', '', 25350.00, 10, 'sport', 'images/r9.jpg', '2025-09-17 00:39:51'),
(94, 'PowerStep Terrain Boots', '', 27950.00, 10, 'sport', 'images/r14.jpg', '2025-09-17 00:39:51'),
(95, 'Men White Sports Sneakers', '', 24500.00, 10, 'sport', 'images/r8.jpg', '2025-09-17 00:39:51'),
(96, 'TurboStrike Edge Cleats', '', 22200.00, 10, 'sport', 'images/r12.jpg', '2025-09-17 00:39:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `address_line` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `phone`, `address_line`, `city`, `postal_code`, `role`, `created_at`) VALUES
(1, 'admin', '', 'admin123', '', '', '', '', 'admin', '2025-09-17 00:08:06'),
(2, 'shim1', '', '$2y$10$/mv5ikSC9HHHP3ImtRafq.gR.TMIIkXWx3IuiLGeLrtQUvEbNX5hG', '', '', '', '', 'admin', '2025-09-17 00:11:26'),
(4, 'shim2', '', '$2y$10$pnyeGsVc.Id5ZoCiJCwc2.lajhOKb/QctsDmBFzsV3VLrVLUvGPDG', '', '', '', '', 'customer', '2025-09-17 00:26:52'),
(8, 'admin2', '', '$2y$10$2QvkZy9ldgUlm74/ike1Q.XefKSRwmEUdzp6vPrcUNuFBqdF3uz.S', '', '', '', '', 'customer', '2025-09-17 00:40:41'),
(9, 'shim3', '', '$2y$10$o856CdoAWhzjjqiEIz2B1.9RzVoSjjRZJ84XdxMHiVYVkIGHyJAam', '', '', '', '', 'customer', '2025-09-17 01:12:51'),
(10, 'shimron', 'shimronkavindhu@gmail.com', '$2y$10$anBW2aMbYfO.Akek6XcEeuMXSKtApz.BcybWL0Ezhl.Pna2g7HMkW', '0757549949', '97/d,ebert silva estate maikkulama,chilaw', 'chilaw', '6100', 'customer', '2025-09-17 02:02:02'),
(14, 'haseni', 'hasenipehesari@gmail.com', '$2y$10$LGPjEbwbFCYl0ZvuYcEzBupC.L04xlV2sdDBacyqk9pqUM4ZuJ/a.', '0727878481', '23,sedawaththa,kurunalaga road', 'chilaw', '6100', 'admin', '2025-09-17 02:55:38'),
(15, 'prabu', 'shimronkavindu@gmail.com', '$2y$10$c4OoEvXdVOPYN2T/pHxbRu6e3dn0Dsyvwq4TMPWg2rELhDv0YyDDS', '0757549949', '97/d,ebert silva estate maikkulama,chilaw', 'chilaw', '6100', 'customer', '2025-09-17 04:59:08'),
(16, 'wew', 'hasenipehesar@gmail.com', '$2y$10$7/iXSntUDgsnJTKbAgRMfOm9QIxqiydibKhkIftCctOMSPnBt1dU.', '0727878481', '21', '121', '2', 'customer', '2025-09-17 05:00:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
