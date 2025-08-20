-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2025 at 11:50 PM
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
-- Database: `csa`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(190) NOT NULL,
  `pass_hash` char(60) NOT NULL,
  `role` enum('OFFICER','PRESIDENT','ADVISOR') NOT NULL DEFAULT 'OFFICER',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `pass_hash`, `role`, `created_at`) VALUES
(3, 'arian@gmail.com', '$2y$10$n62KBXQflgy.2aCuvdqZw.maG2oaPZg8IvN.xXtcJwqfmDKtKKQUG', 'PRESIDENT', '2025-08-17 16:24:42'),
(4, 'admin@test.local', '$2y$10$gM2yNi1slgXcERS8i.RWD.dJCQ1HSnPsysI0xr6ndTI5fe5Y/LM4a', 'PRESIDENT', '2025-08-20 12:19:28');

-- --------------------------------------------------------

--
-- Table structure for table `admin_settings`
--

CREATE TABLE `admin_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_settings`
--

INSERT INTO `admin_settings` (`id`, `setting_key`, `setting_value`, `updated_by`, `updated_at`) VALUES
(1, 'events_auto_cleanup', '1', 3, '2025-08-17 22:20:08');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `summary` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `max_capacity` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `rsvp_url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `summary`, `description`, `start_time`, `end_time`, `location`, `max_capacity`, `created_by`, `rsvp_url`, `created_at`, `updated_at`) VALUES
(2, 'CSA Spring Kickoff Social', 'Reconnect with CSA friends and meet the board at our Spring Kickoff Social. Enjoy great food, hear the latest club updates, and find out how to get involved this semester, all you need to bring is good mood.', 'Join us for the CSA Spring Kickoff Social, a relaxed gathering where members and board officers can reconnect, meet new faces, and set the tone for a strong semester. We’ll share quick updates on upcoming workshops, project teams, hackathons, and ways to get involved across CSA. Expect plenty of good food and casual conversation—just bring your good mood.\r\n\r\nWhat to expect:\r\n* Brief club update from the CSA board\r\n* Meet-and-greet with officers and project leads\r\n* Overview of spring events, volunteer roles, and committees\r\n* Time to socialize, network, and swap ideas\r\n\r\nWho should attend:\r\n* Current CSA members\r\n* HCC students interested in computing or any STEM field who want to learn more about CSA\r\n\r\nLogistics:\r\n* Cost: Free for HCC students\r\n* Food: Provided while supplies last', '2025-09-10 18:00:00', '2025-09-10 20:00:00', 'Westloop Campus', 20, 3, NULL, '2025-08-17 17:16:01', '2025-08-17 22:26:47'),
(5, 'Past Event', 'TESTTESTTESTTEST', 'This is a test of the system', '2024-02-03 02:30:00', '2024-02-04 14:04:00', 'Past', 100, 3, NULL, '2025-08-17 17:29:21', NULL),
(6, 'TEST', 'TEST TEST TEST TEST TEST', '', '2025-09-09 09:32:00', '2025-09-10 09:32:00', 'NOWHERE', 200, 3, NULL, '2025-08-17 18:51:51', '2025-08-20 17:10:02'),
(11, 'Copy of Past Event', 'TESTTESTTESTTEST', 'This is a test of the system', '2024-02-03 02:30:00', '2024-02-04 14:04:00', 'Past', 100, 3, NULL, '2025-08-17 19:15:36', NULL),
(12, 'Copy of Copy of Past Event', 'TESTTESTTESTTEST', 'This is a test of the system', '2024-02-03 02:30:00', '2024-02-04 14:04:00', 'Past', 100, 3, NULL, '2025-08-17 19:15:39', NULL),
(13, 'Copy of Copy of Copy of Past Event', 'TESTTESTTESTTEST', 'This is a test of the system', '2024-02-03 02:30:00', '2024-02-04 14:04:00', 'Past', 100, 3, NULL, '2025-08-17 19:15:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `email` varchar(190) NOT NULL,
  `major` varchar(120) DEFAULT NULL,
  `campus` varchar(120) DEFAULT NULL,
  `year_level` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `consent_comms` tinyint(1) NOT NULL DEFAULT 0,
  `accepted_code` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('PENDING','VERIFIED','BLOCKED') NOT NULL DEFAULT 'PENDING',
  `verification_token` char(64) DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rate_limits`
--

CREATE TABLE `rate_limits` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `email` varchar(190) DEFAULT NULL,
  `endpoint` varchar(50) NOT NULL,
  `attempts` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `last_attempt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rate_limits`
--

INSERT INTO `rate_limits` (`id`, `ip_address`, `email`, `endpoint`, `attempts`, `last_attempt`) VALUES
(34, '::1', 'arian@gmail.com', 'admin_login', 1, '2025-08-20 12:09:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `admin_settings`
--
ALTER TABLE `admin_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_start_time` (`start_time`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `rate_limits`
--
ALTER TABLE `rate_limits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_endpoint` (`ip_address`,`endpoint`),
  ADD KEY `idx_email_endpoint` (`email`,`endpoint`),
  ADD KEY `idx_last_attempt` (`last_attempt`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_settings`
--
ALTER TABLE `admin_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rate_limits`
--
ALTER TABLE `rate_limits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
