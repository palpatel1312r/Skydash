-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 20, 2026 at 10:27 AM
-- Server version: 8.3.0
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_email_unique` (`email`),
  KEY `admins_role_id_foreign` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `role_id`, `phone`, `address`, `status`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@gmail.com', 1, NULL, NULL, 'Active', NULL, '$2y$10$km/13ULUFzZUhhd7I9CJGOtB365JI3ETRaK9IKOhgktSvxxNVQu8u', NULL, '2026-07-20 03:39:46', '2026-07-20 03:39:46'),
(2, 'Admin', 'admin@gmail.com', 2, NULL, NULL, 'Active', NULL, '$2y$10$R5AtnyjFoCfejItiK1XWy.xcEisVOPVC9vA5hOXb6HdmbBoxVU9hS', NULL, '2026-07-20 03:39:46', '2026-07-20 03:39:46');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_email_unique` (`email`),
  KEY `customer_role_id_foreign` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `fullname`, `email`, `password`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
(5, 'PAL', 'User2@gmail.com', '$2y$10$NlLAlgJjfor8tivXGPqmTONH/Lqtm8jLu7hiqJAz0NLqlEq5VuSAG', 3, 'Active', '2026-07-20 03:44:54', '2026-07-20 03:44:54'),
(6, 'Pal', 'User3@gmail.com', '$2y$10$/ncf8hyYH.oFkl6Fve2CX.pGhTzYn/cfdD2WFYLNh.dnCkwc6DxmS', 3, 'Active', '2026-07-20 03:45:04', '2026-07-20 03:45:04'),
(4, 'PAL PATEL', 'User1@gmail.com', '$2y$10$rtGmuB8EyRC6Cy7LjQYdmelDj0oIs2eLazum/ze6EmdhLXxDr573i', 3, 'Active', '2026-07-20 03:43:00', '2026-07-20 03:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `customer_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_address` text COLLATE utf8mb4_unicode_ci,
  `products` json NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Unpaid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  KEY `invoices_customer_id_foreign` (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `invoice_date`, `customer_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `products`, `subtotal`, `tax_rate`, `tax_amount`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(4, 'INV-20260720-134', '2026-07-20', 4, 'PAL PATEL', 'User1@gmail.com', 'N/A', 'N/A', '[{\"price\": \"3300.00\", \"quantity\": \"1\", \"subtotal\": 3300, \"product_id\": \"14\", \"product_name\": \"Running Shoes\"}]', 3300.00, 10.00, 330.00, 3630.00, 'Unpaid', '2026-07-20 04:25:07', '2026-07-20 04:36:20'),
(3, 'INV-20260720-586', '2026-07-20', 5, 'PAL', 'User2@gmail.com', 'N/A', 'N/A', '[{\"price\": \"1500.00\", \"quantity\": \"1\", \"subtotal\": 1500, \"product_id\": \"13\", \"product_name\": \"Smart LED Desk Lamp\"}]', 1500.00, 10.00, 150.00, 1650.00, 'Unpaid', '2026-07-20 04:24:50', '2026-07-20 04:53:35');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2025_04_12_064008_create_product_table', 1),
(3, '2025_04_12_114539_create_customer_table', 1),
(4, '2026_07_06_100532_create_admin_table', 1),
(5, '2026_07_06_102902_create_invoice_table', 1),
(6, '2026_07_16_062522_create_roles_table', 1),
(7, '2026_07_16_062535_update_admins_table_add_role_id', 1),
(8, '2026_07_18_083539_create_profiles_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `title`, `image`, `description`, `price`, `quantity`, `category`, `type`, `created_at`, `updated_at`) VALUES
(10, 'Wireless Bluetooth Headphones', 'uploads/products/1784539125_UgnQs6KjwY.jpg', 'Wireless Bluetooth Headphones', 4000.00, 30, 'Electronics', 'New Arrivals', '2026-07-20 03:48:45', '2026-07-20 03:48:45'),
(11, 'Cotton T-Shirt', 'uploads/products/1784539170_FHEfftKMqT.jpg', 'Cotton T-Shirt', 900.00, 99, 'Clothes', 'Sale', '2026-07-20 03:49:30', '2026-07-20 03:56:09'),
(12, 'Leather Wallet', 'uploads/products/1784539211_d8xqk5MVMe.jpg', 'Leather Wallet', 600.00, 75, 'Accessories', 'Featured', '2026-07-20 03:50:11', '2026-07-20 03:50:11'),
(9, 'Classic White Sneakers', 'uploads/products/1784539083_eRbKeWJ3Zj.jpg', 'Classic White Sneakers', 2500.00, 48, 'Shoes', 'Best Sellers', '2026-07-20 03:48:03', '2026-07-20 04:22:29'),
(13, 'Smart LED Desk Lamp', 'uploads/products/1784539247_tjZZUL8jbg.jpg', NULL, 1500.00, 39, 'Home', 'Best Sellers', '2026-07-20 03:50:47', '2026-07-20 04:53:35'),
(14, 'Running Shoes', 'uploads/products/1784539278_X1xLvrkOEu.jpg', NULL, 3300.00, 59, 'Shoes', 'Best Sellers', '2026-07-20 03:51:18', '2026-07-20 04:36:20'),
(15, 'Laptop Backpack', 'uploads/products/1784539313_HUqdk1ApoC.jpg', NULL, 1200.00, 45, 'Accessories', 'Sale', '2026-07-20 03:51:53', '2026-07-20 03:51:53'),
(16, 'Smartphone Stand', 'uploads/products/1784539346_gbLYLstwYO.jpg', NULL, 300.00, 200, 'Electronics', 'Featured', '2026-07-20 03:52:26', '2026-07-20 03:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `profileable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profileable_id` bigint UNSIGNED NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `profile_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profiles_profileable_type_profileable_id_index` (`profileable_type`,`profileable_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `profileable_type`, `profileable_id`, `phone`, `address`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Admin', 2, NULL, NULL, NULL, '2026-07-20 03:54:18', '2026-07-20 03:54:18');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Superadmin', '2026-07-20 03:39:47', '2026-07-20 03:39:47'),
(2, 'Admin', '2026-07-20 03:39:47', '2026-07-20 03:39:47'),
(3, 'Customer', '2026-07-20 03:39:47', '2026-07-20 03:39:47'),
(4, 'Shop Manager', '2026-07-20 03:39:47', '2026-07-20 03:39:47'),
(5, 'Inventory Manager', '2026-07-20 03:39:47', '2026-07-20 03:39:47'),
(6, 'Customer Support', '2026-07-20 03:39:47', '2026-07-20 03:39:47'),
(7, 'Delivery Boy', '2026-07-20 03:39:47', '2026-07-20 03:39:47');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
