-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 03:31 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sbims_pro`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'Login', 'User logged into the system', '192.168.1.100', NULL, '2025-12-01 01:18:58'),
(2, 2, 'Login', 'User logged into the system', '192.168.1.101', NULL, '2025-12-01 01:18:58'),
(3, 3, 'Add Resident', 'Added new resident record: Juan Dela Cruz', '192.168.1.102', NULL, '2025-12-01 01:18:58'),
(4, 3, 'Create Blotter', 'Created new blotter case: BL-2024-0001', '192.168.1.102', NULL, '2025-12-01 01:18:58'),
(5, 2, 'Approve Certificate', 'Approved certificate: CLC-2024-0001', '192.168.1.101', NULL, '2025-12-01 01:18:58'),
(6, 1, 'Logout', 'User logged out of the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:20:21'),
(7, 1, 'Login', 'User logged into the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:21:47'),
(8, 1, 'Logout', 'User logged out of the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:22:02'),
(9, 2, 'Login', 'User logged into the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:28:05'),
(10, 1, 'Login', 'User logged into the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:30:53'),
(11, 2, 'Login', 'User logged into the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:31:01'),
(12, 3, 'Login', 'User logged into the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:31:11'),
(13, 4, 'Login', 'User logged into the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:31:17'),
(14, 2, 'Login', 'User logged into the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:31:37'),
(15, 3, 'Login', 'User logged into the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:37:02'),
(16, 3, 'Add Resident', 'Added new resident: John Doe', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:38:17'),
(17, 3, 'Update Blotter', 'Updated blotter case BL-2024-0002 status to Ongoing', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:47:18'),
(18, 3, 'Logout', 'User logged out of the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 01:59:39'),
(19, 1, 'Login', 'User logged into the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 02:03:58'),
(20, 1, 'Logout', 'User logged out of the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 02:04:10'),
(21, 3, 'Login', 'User logged into the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-12-01 02:04:17');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `posted_by` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `posted_by`, `is_active`, `created_at`) VALUES
(1, 'Community Clean-up Drive', 'There will be a community clean-up drive this Saturday, January 27, 2024. All residents are encouraged to participate. Meeting time: 6:00 AM at the barangay hall.', 2, 1, '2025-12-01 01:18:58'),
(2, 'Water Interruption Schedule', 'There will be water service interruption on January 26, 2024 from 8:00 AM to 4:00 PM for pipeline maintenance. Please store water accordingly.', 2, 1, '2025-12-01 01:18:58'),
(3, 'Livelihood Training Program', 'Free livelihood training on candle making will be conducted every Saturday starting February 3, 2024. Interested residents may register at the barangay hall.', 2, 1, '2025-12-01 01:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `barangay_info`
--

CREATE TABLE `barangay_info` (
  `id` int(11) NOT NULL,
  `barangay_name` varchar(100) NOT NULL,
  `barangay_captain` varchar(100) DEFAULT NULL,
  `barangay_secretary` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_info`
--

INSERT INTO `barangay_info` (`id`, `barangay_name`, `barangay_captain`, `barangay_secretary`, `address`, `contact_number`, `email`, `logo_path`, `updated_at`) VALUES
(1, 'Libertad', 'Juan Dela Cruz', 'Maria Santos', 'Libertad, Isabel, Leyte', '09123456789', 'barangaylibertad@email.com', NULL, '2025-12-01 01:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `blotters`
--

CREATE TABLE `blotters` (
  `id` int(11) NOT NULL,
  `case_id` varchar(20) NOT NULL,
  `complainant_id` int(11) NOT NULL,
  `respondent_name` varchar(100) NOT NULL,
  `respondent_address` text DEFAULT NULL,
  `incident_type` varchar(100) NOT NULL,
  `incident_date` datetime NOT NULL,
  `incident_location` text NOT NULL,
  `description` text NOT NULL,
  `status` enum('Pending','Ongoing','Settled','Referred') DEFAULT 'Pending',
  `resolution` text DEFAULT NULL,
  `resolved_date` datetime DEFAULT NULL,
  `handled_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blotters`
--

INSERT INTO `blotters` (`id`, `case_id`, `complainant_id`, `respondent_name`, `respondent_address`, `incident_type`, `incident_date`, `incident_location`, `description`, `status`, `resolution`, `resolved_date`, `handled_by`, `created_at`) VALUES
(1, 'BL-2024-0001', 1, 'Pedro Santos', 'Purok 3, Libertad, Isabel, Leyte', 'Boundary Dispute', '2024-01-15 14:30:00', 'Between Purok 1 and Purok 3', 'Dispute over property boundary between neighbors', 'Settled', NULL, NULL, 3, '2025-12-01 01:18:58'),
(2, 'BL-2024-0002', 2, 'Juan Dela Cruz', 'Purok 1, Libertad, Isabel, Leyte', 'Noise Complaint', '2024-01-20 22:00:00', 'Purok 2, Near the basketball court', 'Loud noise during nighttime disturbing the neighborhood', 'Ongoing', '', NULL, 3, '2025-12-01 01:18:58'),
(3, 'BL-2024-0003', 3, 'Maria Reyes', 'Purok 2, Libertad, Isabel, Leyte', 'Property Damage', '2024-01-25 10:00:00', 'Purok 3, Near the river', 'Damage to garden plants by stray animals', 'Pending', NULL, NULL, 3, '2025-12-01 01:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `certificate_id` varchar(20) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `certificate_type` enum('Clearance','Indigency','Residency') NOT NULL,
  `purpose` text NOT NULL,
  `status` enum('Pending','Approved','Released') DEFAULT 'Pending',
  `issued_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `issued_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `certificate_id`, `resident_id`, `certificate_type`, `purpose`, `status`, `issued_by`, `approved_by`, `issued_date`, `created_at`) VALUES
(1, 'CLC-2024-0001', 1, 'Clearance', 'Employment requirement', 'Released', 3, 2, '2024-01-10 09:00:00', '2025-12-01 01:18:58'),
(2, 'IND-2024-0001', 2, 'Indigency', 'Educational assistance', 'Approved', 3, 2, '2024-01-12 10:30:00', '2025-12-01 01:18:58'),
(3, 'RES-2024-0001', 3, 'Residency', 'Bank transaction', 'Pending', 3, NULL, NULL, '2025-12-01 01:18:58'),
(4, 'CLC-2024-0002', 4, 'Clearance', 'Business permit', 'Released', 3, 2, '2024-01-18 14:15:00', '2025-12-01 01:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

CREATE TABLE `residents` (
  `id` int(11) NOT NULL,
  `resident_id` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `civil_status` enum('Single','Married','Widowed','Divorced') NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text NOT NULL,
  `purok` varchar(50) NOT NULL,
  `household_number` varchar(20) DEFAULT NULL,
  `is_voter` tinyint(1) DEFAULT 0,
  `is_4ps` tinyint(1) DEFAULT 0,
  `is_senior` tinyint(1) DEFAULT 0,
  `is_pwd` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`id`, `resident_id`, `first_name`, `middle_name`, `last_name`, `birthdate`, `gender`, `civil_status`, `contact_number`, `email`, `address`, `purok`, `household_number`, `is_voter`, `is_4ps`, `is_senior`, `is_pwd`, `created_at`, `updated_at`) VALUES
(1, 'RES-2024-0001', 'Juan', 'Reyes', 'Dela Cruz', '1985-03-15', 'Male', 'Married', '09123456789', 'juan.delacruz@email.com', 'Purok 1, Libertad, Isabel, Leyte', 'Purok 1', 'HH-001', 1, 0, 0, 0, '2025-12-01 01:18:58', '2025-12-01 01:18:58'),
(2, 'RES-2024-0002', 'Maria', 'Santos', 'Reyes', '1990-07-22', 'Female', 'Married', '09123456790', 'maria.reyes@email.com', 'Purok 2, Libertad, Isabel, Leyte', 'Purok 2', 'HH-002', 1, 1, 0, 0, '2025-12-01 01:18:58', '2025-12-01 01:18:58'),
(3, 'RES-2024-0003', 'Pedro', 'Gomez', 'Santos', '1978-12-10', 'Male', 'Single', '09123456791', 'pedro.santos@email.com', 'Purok 3, Libertad, Isabel, Leyte', 'Purok 3', 'HH-003', 1, 0, 0, 1, '2025-12-01 01:18:58', '2025-12-01 01:18:58'),
(4, 'RES-2024-0004', 'Ana', 'Dela Cruz', 'Gomez', '1960-05-30', 'Female', 'Widowed', '09123456792', 'ana.gomez@email.com', 'Purok 1, Libertad, Isabel, Leyte', 'Purok 1', 'HH-004', 1, 0, 1, 0, '2025-12-01 01:18:58', '2025-12-01 01:18:58'),
(5, 'RES-2024-0005', 'Luis', 'Reyes', 'Tan', '1995-09-18', 'Male', 'Single', '09123456793', 'luis.tan@email.com', 'Purok 2, Libertad, Isabel, Leyte', 'Purok 2', 'HH-005', 0, 1, 0, 0, '2025-12-01 01:18:58', '2025-12-01 01:18:58'),
(6, 'RES-2025-0006', 'John', 'apple', 'Doe', '2003-12-18', 'Male', 'Single', '09293193213', 'john@gmail.com', 'ormoc city', 'Purok 1', 'h-001', 1, 0, 0, 1, '2025-12-01 01:38:17', '2025-12-01 01:38:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','captain','secretary','resident') NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `full_name`, `is_active`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@barangaylibertad.com', 'admin', 'System Administrator', 1, '2025-12-01 10:03:58', '2025-12-01 01:18:58'),
(2, 'captain', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'captain@barangaylibertad.com', 'captain', 'Barangay Captain Juan Dela Cruz', 1, '2025-12-01 09:31:37', '2025-12-01 01:18:58'),
(3, 'secretary', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'secretary@barangaylibertad.com', 'secretary', 'Barangay Secretary Maria Santos', 1, '2025-12-01 10:04:17', '2025-12-01 01:18:58'),
(4, 'resident1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'resident1@email.com', 'resident', 'Juan Tamad', 1, '2025-12-01 09:31:17', '2025-12-01 01:18:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posted_by` (`posted_by`);

--
-- Indexes for table `barangay_info`
--
ALTER TABLE `barangay_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blotters`
--
ALTER TABLE `blotters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `case_id` (`case_id`),
  ADD KEY `complainant_id` (`complainant_id`),
  ADD KEY `handled_by` (`handled_by`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_id` (`certificate_id`),
  ADD KEY `resident_id` (`resident_id`),
  ADD KEY `issued_by` (`issued_by`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `residents`
--
ALTER TABLE `residents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `resident_id` (`resident_id`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `barangay_info`
--
ALTER TABLE `barangay_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blotters`
--
ALTER TABLE `blotters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `residents`
--
ALTER TABLE `residents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blotters`
--
ALTER TABLE `blotters`
  ADD CONSTRAINT `blotters_ibfk_1` FOREIGN KEY (`complainant_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blotters_ibfk_2` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
