-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2026 at 01:19 PM
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
-- Database: `agriman_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `crops`
--

CREATE TABLE `crops` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_mr` varchar(100) NOT NULL DEFAULT '',
  `family` varchar(100) NOT NULL DEFAULT '',
  `family_mr` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crops`
--

INSERT INTO `crops` (`id`, `name`, `name_mr`, `family`, `family_mr`) VALUES
(7, 'Tomato', 'टोमॅटो', 'Solanaceae', 'सोलेनेसी'),
(11, 'Cabbage', 'कोबी', 'Brassicaceae', 'ब्रॅसिकेसी');

-- --------------------------------------------------------

--
-- Table structure for table `fields`
--

CREATE TABLE `fields` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `area_sq_m` decimal(10,2) NOT NULL DEFAULT 0.00,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fields`
--

INSERT INTO `fields` (`id`, `user_id`, `name`, `area_sq_m`, `latitude`, `longitude`, `created_at`) VALUES
(1, 1, 'North', 1000.00, 999.999999, 999.999999, '2026-03-09 12:32:13'),
(2, 2, 'KTHM College', 3000.00, 19.890000, 73.678000, '2026-03-12 07:58:32'),
(3, 3, 'Yadnesh Tomato Field', 2000.00, 20.302470, 73.799940, '2026-03-16 12:09:39');

-- --------------------------------------------------------

--
-- Table structure for table `harvests`
--

CREATE TABLE `harvests` (
  `id` int(10) UNSIGNED NOT NULL,
  `planting_id` int(10) UNSIGNED NOT NULL,
  `harvest_date` date NOT NULL,
  `quantity_kg` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `harvests`
--

INSERT INTO `harvests` (`id`, `planting_id`, `harvest_date`, `quantity_kg`, `notes`, `created_at`) VALUES
(1, 1, '2026-03-09', 900.00, '', '2026-03-09 12:33:51');

-- --------------------------------------------------------

--
-- Table structure for table `plantings`
--

CREATE TABLE `plantings` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `field_id` int(10) UNSIGNED NOT NULL,
  `crop_id` int(10) UNSIGNED NOT NULL,
  `variety_name` varchar(120) DEFAULT NULL,
  `planting_date` date NOT NULL,
  `expected_harvest_date` date DEFAULT NULL,
  `status` enum('active','completed') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plantings`
--

INSERT INTO `plantings` (`id`, `user_id`, `field_id`, `crop_id`, `variety_name`, `planting_date`, `expected_harvest_date`, `status`, `notes`, `created_at`) VALUES
(1, 1, 1, 7, 'Aryaman', '2026-03-01', '2026-06-03', 'completed', '', '2026-03-09 12:33:07');

-- --------------------------------------------------------

--
-- Table structure for table `rotations`
--

CREATE TABLE `rotations` (
  `id` int(10) UNSIGNED NOT NULL,
  `field_id` int(10) UNSIGNED NOT NULL,
  `year` year(4) NOT NULL,
  `crop_id` int(10) UNSIGNED NOT NULL,
  `season` enum('kharif','rabi','zaid') NOT NULL DEFAULT 'kharif',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `planting_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `task_type` enum('irrigation','fertilization','pesticide','weeding','pruning','harvesting','other') NOT NULL DEFAULT 'other',
  `due_date` date NOT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `planting_id`, `title`, `task_type`, `due_date`, `status`, `notes`, `created_at`) VALUES
(1, 1, 'apply fertilizer', 'irrigation', '2026-03-26', 'pending', '', '2026-03-09 12:33:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `lang` enum('en','mr') NOT NULL DEFAULT 'en',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `lang`, `created_at`) VALUES
(1, 'yadnesh saindane', 'yadneshsaindane7@gmail.com', '$2y$10$z2O7qaY9vhtn47lue4bxUub.x6h/1d50rMixn0My9O2VvvtyBthMG', 'en', '2026-03-09 12:30:05'),
(2, 'Pooja Purkar', 'poojapurkar10@gmail.com', '$2y$10$GWse2eO9csT8zJ8BEN/YsuqoWwKXmdG7t0gcDWF5ATOxqtUnYFpFe', 'en', '2026-03-12 07:48:03'),
(3, 'Yadnesh Saindane', 'saindaneyadnesh5@gmail.com', '$2y$10$vNjA/M3a1NbUGv2kUpYDCOr5/WpRSr6JQX.Vimv3Y7Dhd.K6xGyhS', 'en', '2026-03-16 12:06:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crops`
--
ALTER TABLE `crops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fields`
--
ALTER TABLE `fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fields_user` (`user_id`);

--
-- Indexes for table `harvests`
--
ALTER TABLE `harvests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_harvests_planting` (`planting_id`);

--
-- Indexes for table `plantings`
--
ALTER TABLE `plantings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_plantings_user` (`user_id`),
  ADD KEY `idx_plantings_field` (`field_id`),
  ADD KEY `idx_plantings_crop` (`crop_id`);

--
-- Indexes for table `rotations`
--
ALTER TABLE `rotations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rotations_field` (`field_id`),
  ADD KEY `idx_rotations_crop` (`crop_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tasks_planting` (`planting_id`),
  ADD KEY `idx_tasks_due` (`due_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crops`
--
ALTER TABLE `crops`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `fields`
--
ALTER TABLE `fields`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `harvests`
--
ALTER TABLE `harvests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `plantings`
--
ALTER TABLE `plantings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rotations`
--
ALTER TABLE `rotations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fields`
--
ALTER TABLE `fields`
  ADD CONSTRAINT `fk_fields_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `harvests`
--
ALTER TABLE `harvests`
  ADD CONSTRAINT `fk_harvests_planting` FOREIGN KEY (`planting_id`) REFERENCES `plantings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plantings`
--
ALTER TABLE `plantings`
  ADD CONSTRAINT `fk_plantings_crop` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`),
  ADD CONSTRAINT `fk_plantings_field` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_plantings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rotations`
--
ALTER TABLE `rotations`
  ADD CONSTRAINT `fk_rotations_crop` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`),
  ADD CONSTRAINT `fk_rotations_field` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_tasks_planting` FOREIGN KEY (`planting_id`) REFERENCES `plantings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
