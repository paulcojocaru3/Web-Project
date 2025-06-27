-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2025 at 11:07 PM
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
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `min_age` int(11) DEFAULT NULL,
  `required_gender` enum('M','F','ANY') DEFAULT 'ANY',
  `min_events_participated` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `location`, `description`, `event_date`, `location_lat`, `location_lon`, `max_participants`, `created_by`, `status`, `created_at`, `min_age`, `required_gender`, `min_events_participated`) VALUES
(1, 'Alergat', 'Teren%20de%20multi', 'Alergat', '2025-05-07 23:00:00', 47.18025670, 27.56939310, 10, 4, 'approved', '2025-05-25 18:27:28', NULL, 'ANY', 0),
(2, 'test', 'Teren%20de%20shooting', 'test', '2025-05-25 21:59:00', 47.21199410, 27.60113640, 10, 4, 'pending', '2025-05-25 20:06:49', NULL, 'ANY', 0),
(3, 'Alergat', 'Teren%20de%20soccer', 'fdsa', '2025-05-25 23:06:00', 47.18453040, 27.56119540, 10, 4, 'pending', '2025-05-25 21:07:07', NULL, 'ANY', 0);

-- --------------------------------------------------------

--
-- Table structure for table `event_participants`
--

CREATE TABLE `event_participants` (
  `event_id` bigint(20) NOT NULL,
  `id` bigint(20) NOT NULL,
  `join_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
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

INSERT INTO `users` (`id`, `username`, `password`, `email`, `firstname`, `lastname`, `events_participated`, `date`, `role`, `birth_date`, `gender`) VALUES
(1, 'paul', '$2y$10$bpzlid7bNh3hapc6qnIRYuejm9jc5xvkGZZbEtCb9FsXQGrZwTVQ2', '', '', '', 0, '2025-05-25 17:26:08', 'user', NULL, NULL),
(4, 'user1', 'user1', NULL, '', '', 0, '2025-05-25 17:40:01', 'user', NULL, NULL),
(5, 'test', '$2y$10$FaS5G492IvArY8xLuwq/uOIu5GpJIH7/lokyxi/E0Pk2oa1PAc/Tm', NULL, '', '', 0, '2025-05-25 17:33:13', 'user', NULL, NULL),
(6, 'test2', '$2y$10$lU/tLEo6kSgGHOXNDtRT7OXA1LMo8R669nmcUj9RIEUOXpKVWrZCO', 'aq5ni3d@familyplanets.com', 'Tpas', 'Cojocaru', 0, '2025-05-25 21:02:32', 'user', '2003-01-28', 'M');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `event_participants`
--
ALTER TABLE `event_participants`
  ADD PRIMARY KEY (`event_id`,`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

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
