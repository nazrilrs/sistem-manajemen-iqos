-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2026 at 06:11 AM
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
-- Database: `iqos`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) DEFAULT 0,
  `min_stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `price`, `stock`, `min_stock`, `created_at`) VALUES
(5, 'A101', 'Blends Warm', 22000.00, 98, 0, '2025-11-28 06:35:39'),
(6, 'A102', 'Blends Summer', 22000.00, 58, 0, '2025-11-28 06:38:23'),
(7, 'A103', 'Blends Blassom', 22000.00, 51, 0, '2025-11-28 08:09:48'),
(8, 'A104', 'Blends Purple', 22000.00, 7, 0, '2025-11-28 08:10:26'),
(9, 'A105', 'Blends Rich', 20000.00, 52, 0, '2025-11-28 08:10:47'),
(10, 'B201', 'Veev Now Strawberry', 350000.00, 99, 0, '2025-11-28 08:11:42'),
(11, 'B202', 'Veev Now Mango', 35000.00, 80, 0, '2025-11-28 08:12:20'),
(12, 'B203', 'Veev Now Red Melon', 35000.00, 2, 0, '2025-11-28 08:13:01'),
(13, 'C301', 'Veev Ultra Red Melon', 50000.00, 10, 0, '2025-11-28 08:13:35'),
(14, 'C302', 'Veev  Ultra Strawberry', 50000.00, 19, 0, '2025-11-28 08:14:02'),
(15, 'C303', 'Veev Ultra Sour Apple', 50000.00, 5, 0, '2025-11-28 08:14:36'),
(16, 'C304', 'Veev Ultra Grafe', 50000.00, 59, 0, '2025-11-28 08:15:08'),
(17, 'D401', 'Iluma i One', 349000.00, 99, 0, '2025-11-28 08:16:52');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `total_items` int(11) NOT NULL,
  `payment_method` varchar(30) DEFAULT 'cash',
  `payment_status` varchar(30) DEFAULT 'success',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `invoice_no`, `user_id`, `total_amount`, `total_items`, `payment_method`, `payment_status`, `created_at`) VALUES
(26, 'INV-20251128164711', 3, 4424000.00, 0, 'cash', 'success', '2025-11-28 15:47:11'),
(27, 'INV-20251128164801', 3, 2695000.00, 0, 'cash', 'success', '2025-11-28 15:48:01'),
(28, 'INV-20251129043350', 4, 22000.00, 0, 'cash', 'success', '2025-11-29 03:33:50'),
(29, 'INV-20251129043701', 3, 22000.00, 0, 'cash', 'success', '2025-11-29 03:37:01');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `qty`, `price`, `subtotal`) VALUES
(52, 26, 13, 60, 50000.00, 3000000.00),
(53, 26, 14, 10, 50000.00, 500000.00),
(54, 26, 8, 42, 22000.00, 924000.00),
(55, 27, 12, 27, 35000.00, 945000.00),
(56, 27, 15, 35, 50000.00, 1750000.00),
(57, 28, 7, 1, 22000.00, 22000.00),
(58, 29, 5, 1, 22000.00, 22000.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock_mutations`
--

CREATE TABLE `stock_mutations` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `change_qty` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_mutations`
--

INSERT INTO `stock_mutations` (`id`, `product_id`, `user_id`, `change_qty`, `type`, `reason`, `created_at`) VALUES
(36, 6, 4, 10, 'in', 'Kiriman dari jakarta', '2025-11-28 15:35:17'),
(37, 7, NULL, 1, 'out', NULL, '2025-11-28 15:43:34'),
(38, 5, NULL, 1, 'out', NULL, '2025-11-28 15:43:34'),
(39, 12, NULL, 1, 'out', NULL, '2025-11-28 15:43:34'),
(40, 10, NULL, 1, 'out', NULL, '2025-11-28 15:43:34'),
(41, 17, NULL, 1, 'out', NULL, '2025-11-28 15:43:34'),
(42, 8, NULL, 1, 'out', NULL, '2025-11-28 15:43:34'),
(43, 9, NULL, 1, 'out', NULL, '2025-11-28 15:43:34'),
(44, 14, NULL, 1, 'out', NULL, '2025-11-28 15:43:34'),
(45, 16, NULL, 1, 'out', NULL, '2025-11-28 15:43:34'),
(46, 13, NULL, 60, 'out', NULL, '2025-11-28 15:47:11'),
(47, 14, NULL, 10, 'out', NULL, '2025-11-28 15:47:11'),
(48, 8, NULL, 42, 'out', NULL, '2025-11-28 15:47:11'),
(49, 12, NULL, 27, 'out', NULL, '2025-11-28 15:48:01'),
(50, 15, NULL, 35, 'out', NULL, '2025-11-28 15:48:01'),
(51, 7, NULL, 1, 'out', NULL, '2025-11-29 03:33:50'),
(52, 5, NULL, 1, 'out', NULL, '2025-11-29 03:37:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `is_active`, `created_at`) VALUES
(3, 'Kasir', 'kasir', 'kasir123', 'kasir', 1, '2025-11-26 15:40:38'),
(4, 'Admin Owner', 'owner', 'owner123', 'owner', 1, '2025-11-28 03:21:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stock_mutations`
--
ALTER TABLE `stock_mutations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

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
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `stock_mutations`
--
ALTER TABLE `stock_mutations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `stock_mutations`
--
ALTER TABLE `stock_mutations`
  ADD CONSTRAINT `stock_mutations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
