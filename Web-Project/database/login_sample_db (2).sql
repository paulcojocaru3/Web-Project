-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2025 at 05:42 PM
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
-- Database: `login_sample_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` bigint(20) NOT NULL,
  `event_name` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `location_lat` decimal(10,8) NOT NULL,
  `location_lon` decimal(11,8) NOT NULL,
  `max_participants` int(11) NOT NULL DEFAULT 10,
  `created_by` bigint(20) NOT NULL,
  `status` enum('pending','approved','rejected','expired') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `min_age` int(11) DEFAULT NULL,
  `required_gender` enum('M','F','ANY') DEFAULT 'ANY',
  `min_events_participated` int(11) DEFAULT 0,
  `event_type` varchar(50) DEFAULT 'Sport',
  `event_description` text DEFAULT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `participation_policy` varchar(20) DEFAULT 'first-come',
  `min_participations` int(11) DEFAULT 0,
  `duration` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `location`, `description`, `event_date`, `location_lat`, `location_lon`, `max_participants`, `created_by`, `status`, `created_at`, `min_age`, `required_gender`, `min_events_participated`, `event_type`, `event_description`, `lat`, `lng`, `participation_policy`, `min_participations`, `duration`) VALUES
(1, 'Alergat', 'Teren%20de%20multi', 'Alergat', '2025-05-07 23:00:00', 47.18025670, 27.56939310, 10, 4, 'expired', '2025-05-25 18:27:28', NULL, 'ANY', 0, 'Sport', 'Alergat', 47.18025670, 27.56939310, 'first-come', 0, 1),
(2, 'test', 'Teren%20de%20shooting', 'test', '2025-05-25 21:59:00', 47.21199410, 27.60113640, 10, 4, 'expired', '2025-05-25 20:06:49', NULL, 'ANY', 0, 'Sport', 'test', 47.21199410, 27.60113640, 'first-come', 0, 1),
(3, 'Alergat', 'Teren%20de%20soccer', 'fdsa', '2025-05-25 23:06:00', 47.18453040, 27.56119540, 10, 4, 'expired', '2025-05-25 21:07:07', NULL, 'ANY', 0, 'Sport', 'fdsa', 47.18453040, 27.56119540, 'first-come', 0, 1),
(4, 'Alergat', 'Teren%20de%20tennis', 'Desfgsdafgasdf', '2025-05-28 15:28:00', 47.18060300, 27.59868600, 10, 4, 'expired', '2025-05-28 11:28:28', NULL, 'ANY', 0, 'Sport', 'Desfgsdafgasdf', 47.18060300, 27.59868600, 'first-come', 0, 1),
(5, 'gasdfgadsfs', 'Teren%20de%20sport', 'TWEFSDAGSDFGAS', '2025-05-28 15:29:00', 47.16964400, 27.58581200, 10, 4, 'expired', '2025-05-28 11:29:58', NULL, 'ANY', 0, 'Sport', 'TWEFSDAGSDFGAS', 47.16964400, 27.58581200, 'first-come', 0, 1),
(6, 'Alergat', 'Teren%20de%20sport', 'Fasfadgasdfsdfs', '2025-05-28 16:15:00', 47.18765200, 27.60002590, 10, 4, 'expired', '2025-05-28 12:15:30', NULL, 'ANY', 0, 'Sport', 'Fasfadgasdfsdfs', 47.18765200, 27.60002590, 'first-come', 0, 1),
(7, 'bgadf', 'Teren%20de%20sport', 'fsdafasdfsdafasdfasd', '2025-05-28 16:26:00', 47.18498090, 27.60309250, 10, 4, 'expired', '2025-05-28 12:26:47', NULL, 'ANY', 0, 'Sport', 'fsdafasdfsdafasdfasd', 47.18498090, 27.60309250, 'first-come', 0, 1),
(8, 'sdgfagsda', 'Teren%20de%20sport', 'sfasdfsdfasdfasdfas', '2025-05-28 16:31:00', 47.18765200, 27.60002590, 10, 4, 'expired', '2025-05-28 12:31:39', NULL, 'ANY', 0, 'Sport', 'sfasdfsdfasdfasdfas', 47.18765200, 27.60002590, 'first-come', 0, 1),
(9, 'Alergat', 'Mihai Kogalniceanu', 'TESTESTSETSE', '2025-05-31 19:28:00', 47.18069770, 27.55982520, 10, 4, 'expired', '2025-05-28 15:29:05', NULL, 'ANY', 0, 'Sport', 'TESTESTSETSE', 47.18069770, 27.55982520, 'first-come', 0, 1),
(10, 'gasdgasd', 'Teren%20de%20tennis', 'gasdgasdfasd', '2025-05-28 20:44:00', 47.18062390, 27.59867030, 10, 4, 'expired', '2025-05-28 16:47:13', NULL, 'ANY', 0, 'Sport', 'gasdgasdfasd', 47.18062390, 27.59867030, 'first-come', 0, 5),
(11, 'gsdagasdgasd', 'Teren de tennis', 'gasdfgasdfasdfsdfds', '2025-05-28 21:35:00', 47.18062390, 27.59867030, 10, 4, 'expired', '2025-05-28 17:35:52', NULL, 'ANY', 0, 'Sport', 'gasdfgasdfasdfsdfds', 47.18062390, 27.59867030, 'first-come', 0, 1),
(12, 'gdsagadftgasdgasd', 'Teren de tennis', 'fasdfasdfasdfasdfasdfasd', '2025-05-28 21:48:00', 47.18062390, 27.59867030, 10, 4, 'expired', '2025-05-28 17:48:55', NULL, 'ANY', 0, 'Sport', 'fasdfasdfasdfasdfasdfasd', 47.18062390, 27.59867030, 'first-come', 0, 2),
(13, 'test', 'Teren%20de%20tennis', 'teststestse', '2025-05-28 22:11:00', 47.18329580, 27.57112600, 10, 5, 'expired', '2025-05-28 18:11:54', NULL, 'ANY', 0, 'Sport', 'teststestse', 47.18329580, 27.57112600, 'first-come', 0, 2),
(14, 'Alergat', 'Teren%20de%20sport', 'gsdagdsfsdfasdfds', '2025-05-28 22:15:00', 47.18765200, 27.60002590, 10, 4, 'expired', '2025-05-28 18:15:19', NULL, 'ANY', 0, 'Sport', 'gsdagdsfsdfasdfds', 47.18765200, 27.60002590, 'first-come', 0, 2),
(15, 'vxc', 'Teren%20de%20sport', 'hfdagfadsfsda', '2025-05-31 15:09:00', 47.17683490, 27.59842200, 10, 4, 'expired', '2025-05-31 11:10:35', NULL, 'ANY', 0, 'Sport', 'hfdagfadsfsda', 47.17683490, 27.59842200, 'first-come', 0, 2),
(16, 'TEST', 'Teren%20de%20tennis', 'GDSAGDSGASD', '2025-05-31 15:50:00', 47.18064070, 27.59892920, 10, 4, 'expired', '2025-05-31 11:51:06', NULL, 'ANY', 0, 'Sport', 'GDSAGDSGASD', 47.18064070, 27.59892920, 'first-come', 0, 2),
(17, 'Alergat', 'Teren%20de%20sport', 'dfsafdsasdfasd', '2025-05-31 14:11:00', 47.17683490, 27.59842200, 10, 4, 'expired', '2025-05-31 15:11:16', NULL, 'ANY', 0, 'Sport', 'dfsafdsasdfasd', 47.17683490, 27.59842200, 'first-come', 0, 1),
(18, 'Alergat', 'Teren%20de%20sport', 'fdsagsdgadsfdsf', '2025-05-31 12:17:00', 47.17683490, 27.59842200, 10, 4, 'expired', '2025-05-31 15:17:58', NULL, 'ANY', 0, 'Sport', 'fdsagsdgadsfdsf', 47.17683490, 27.59842200, 'first-come', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `event_chat`
--

CREATE TABLE `event_chat` (
  `id` int(11) NOT NULL,
  `event_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_chat`
--

INSERT INTO `event_chat` (`id`, `event_id`, `user_id`, `message`, `sent_at`) VALUES
(1, 5, 4, 'Salut', '2025-05-28 11:32:17'),
(2, 9, 4, 'salutbro', '2025-05-28 16:51:42'),
(3, 9, 4, 'ce zici', '2025-05-28 16:51:45'),
(4, 9, 4, 'CF BRO', '2025-05-28 16:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `event_participants`
--

CREATE TABLE `event_participants` (
  `event_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `join_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'registered',
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_participants`
--

INSERT INTO `event_participants` (`event_id`, `user_id`, `join_date`, `status`, `registration_date`) VALUES
(5, 4, '2025-05-28 11:32:04', 'registered', '2025-05-28 11:32:04'),
(9, 4, '2025-05-28 16:49:39', 'registered', '2025-05-28 16:49:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `events_participated` int(11) NOT NULL DEFAULT 0,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `birth_date` date DEFAULT NULL,
  `gender` enum('M','F') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `firstname`, `lastname`, `events_participated`, `date`, `role`, `birth_date`, `gender`) VALUES
(1, 'paul', '$2y$10$bpzlid7bNh3hapc6qnIRYuejm9jc5xvkGZZbEtCb9FsXQGrZwTVQ2', '', '', '', 0, '2025-05-28 11:26:30', 'user', NULL, NULL),
(4, 'user1', 'user1', NULL, '', '', 0, '2025-05-28 11:26:30', 'user', NULL, NULL),
(5, 'test', 'test1', NULL, '', '', 0, '2025-05-28 18:11:14', 'user', NULL, NULL),
(6, 'test2', '$2y$10$lU/tLEo6kSgGHOXNDtRT7OXA1LMo8R669nmcUj9RIEUOXpKVWrZCO', 'aq5ni3d@familyplanets.com', 'Tpas', 'Cojocaru', 0, '2025-05-28 11:26:30', 'user', '2003-01-28', 'M'),
(7, 'testttttt', '$2y$10$K1yqGbQ5CSptpCrBtaazo.Ozq9mbdRv3ZrtgE5e/okAcGKTCHhnC.', 'gdsagsd@gmail.com', 'gsdgasdgs', 'gasdfasddfas', 0, '2025-05-31 12:37:30', 'user', '2003-02-28', 'M');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_events_date` (`event_date`),
  ADD KEY `idx_events_type` (`event_type`);

--
-- Indexes for table `event_chat`
--
ALTER TABLE `event_chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chat_event` (`event_id`),
  ADD KEY `idx_chat_user` (`user_id`);

--
-- Indexes for table `event_participants`
--
ALTER TABLE `event_participants`
  ADD PRIMARY KEY (`event_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_profile_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `event_chat`
--
ALTER TABLE `event_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `event_participants`
--
ALTER TABLE `event_participants`
  ADD CONSTRAINT `event_participants_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`),
  ADD CONSTRAINT `event_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
