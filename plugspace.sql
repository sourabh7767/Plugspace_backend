-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2021 at 12:09 PM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `plugspace`
--

-- --------------------------------------------------------

--
-- Table structure for table `key_master`
--

CREATE TABLE `key_master` (
  `key_id` int(10) NOT NULL,
  `key_name` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `key_master`
--

INSERT INTO `key_master` (`key_id`, `key_name`, `created_at`, `updated_at`) VALUES
(1, '2b223e5cee713615ha54ac203b24e9a123703011VT', '2020-01-10 10:00:00', '2021-12-09 18:09:21');

-- --------------------------------------------------------

--
-- Table structure for table `key_token_master`
--

CREATE TABLE `key_token_master` (
  `key_token_id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `token` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `key_token_master`
--

INSERT INTO `key_token_master` (`key_token_id`, `key`, `token`, `created_at`, `updated_at`) VALUES
(1, '2b223e5cee713615ha54ac203b24e9a123703011VT', 'Rokk7uW0jrwQqMWteTDCqP25TxWqSR8o', '2021-12-10 06:15:11', '2021-12-10 06:15:11'),
(2, '2b223e5cee713615ha54ac203b24e9a123703011VT', 'KitaDBYjkD41TcZM0UH0DRhECsFsSl8c', '2021-12-10 06:15:53', '2021-12-10 06:15:53'),
(3, '2b223e5cee713615ha54ac203b24e9a123703011VT', 'MjaVwvwHsE1DFfXgZ0S0EwxITfYvs79D', '2021-12-10 06:16:48', '2021-12-10 06:16:48'),
(4, '2b223e5cee713615ha54ac203b24e9a123703011VT', '6xsJEMHlhoLCk5mUFLhoVorK4VcpjIzQ', '2021-12-10 06:17:58', '2021-12-10 06:17:58'),
(5, '2b223e5cee713615ha54ac203b24e9a123703011VT', 'lH8HHn1Fe2h1QJj0jwa9by3JTVNJsFhw', '2021-12-10 06:18:59', '2021-12-10 06:18:59'),
(6, '2b223e5cee713615ha54ac203b24e9a123703011VT', 'TNZ1nrJ7q8GpV6TQX28SRtAgG0InDbO6', '2021-12-10 06:19:26', '2021-12-10 06:19:26'),
(7, '2b223e5cee713615ha54ac203b24e9a123703011VT', 'EgvJGN1LedgZ5xfwbFA0VPVmgebQvH4V', '2021-12-10 07:07:49', '2021-12-10 07:07:49'),
(8, '2b223e5cee713615ha54ac203b24e9a123703011VT', '5gfd4j4MTmVdCY2x3m2HNrTCZDYc6Iww', '2021-12-10 08:33:13', '2021-12-10 08:33:13'),
(9, '2b223e5cee713615ha54ac203b24e9a123703011VT', '6LrsvET25PoWAmYpq92JRfl1ZiXgWPgo', '2021-12-10 08:35:31', '2021-12-10 08:35:31');

-- --------------------------------------------------------

--
-- Table structure for table `send_otp_master`
--

CREATE TABLE `send_otp_master` (
  `otp_id` int(11) NOT NULL,
  `ccode` text NOT NULL,
  `mobile` text NOT NULL,
  `otp_code` varchar(255) NOT NULL,
  `is_verified` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `send_otp_master`
--

INSERT INTO `send_otp_master` (`otp_id`, `ccode`, `mobile`, `otp_code`, `is_verified`, `created_at`, `updated_at`) VALUES
(1, '+91', '8347387407', '27058', 1, '2021-12-10 06:55:15', '2021-12-10 01:46:09'),
(2, '+91', '9992325689', '65374', 1, '2021-12-10 07:14:06', '2021-12-10 01:45:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` text NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_date`, `updated_date`, `deleted_at`) VALUES
(1, 'admin', 'admin@gmail.com', NULL, '$2y$10$GmoWXdI5pdC.tNV8QJFTOe6hwdK5jdhP/nLbf4Ll3NekKHpRGAbaW', NULL, '2021-12-09 12:14:22', '2021-12-09 12:14:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_master`
--

CREATE TABLE `user_master` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ccode` varchar(255) NOT NULL,
  `phone` text NOT NULL,
  `is_verified` int(11) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `woman_rank` varchar(255) NOT NULL,
  `profile` varchar(150) NOT NULL,
  `is_geo_location` varchar(255) NOT NULL,
  `ucode` varchar(100) DEFAULT NULL,
  `is_apple` int(11) NOT NULL,
  `apple_id` varchar(255) NOT NULL,
  `is_insta` int(11) NOT NULL,
  `insta_id` varchar(255) NOT NULL,
  `is_manual_email` int(11) NOT NULL,
  `height` varchar(255) NOT NULL,
  `weight` varchar(255) NOT NULL,
  `education_status` varchar(255) NOT NULL,
  `dob` date DEFAULT NULL,
  `children` varchar(255) NOT NULL,
  `want_childrens` varchar(255) NOT NULL,
  `marring_range` varchar(255) NOT NULL,
  `relationship_status` varchar(255) NOT NULL,
  `ethinicity` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `make_over` varchar(255) NOT NULL,
  `dress_size` varchar(255) NOT NULL,
  `signiat_bills` varchar(255) NOT NULL,
  `times_of_engaged` varchar(255) NOT NULL,
  `your_body_tatto` varchar(255) NOT NULL,
  `age_range_marriage` varchar(255) NOT NULL,
  `my_self_men` varchar(255) NOT NULL,
  `about_you` text NOT NULL,
  `men_rank` varchar(255) NOT NULL,
  `nice_meet` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `device_type` varchar(50) NOT NULL,
  `device_token` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_master`
--

INSERT INTO `user_master` (`user_id`, `name`, `ccode`, `phone`, `is_verified`, `gender`, `woman_rank`, `profile`, `is_geo_location`, `ucode`, `is_apple`, `apple_id`, `is_insta`, `insta_id`, `is_manual_email`, `height`, `weight`, `education_status`, `dob`, `children`, `want_childrens`, `marring_range`, `relationship_status`, `ethinicity`, `company_name`, `job_title`, `make_over`, `dress_size`, `signiat_bills`, `times_of_engaged`, `your_body_tatto`, `age_range_marriage`, `my_self_men`, `about_you`, `men_rank`, `nice_meet`, `created_at`, `updated_at`, `device_type`, `device_token`) VALUES
(1, 'Virag', '91', '8347387407', 0, 'Male', '10', '', '1', NULL, 0, '0', 0, '0', 1, '10', '10', 'DONE', '2021-11-01', '1', '3', '12', 'ok', 'i am sigle', 'Kmphitech', 'Devloper', 'okk', '125', 'are you sure', '1', '10', '39', 'i am klokbox', 'i am sigle person', '12', '454', '2021-12-10 07:07:49', '2021-12-10 10:20:17', '', ''),
(2, 'Virag', '91', '9992345678', 0, 'Male', '10', '', '1', NULL, 0, '0', 0, '0', 1, '10', '10', 'DONE', '2021-11-01', '1', '3', '12', 'ok', 'i am sigle', 'Kmphitech', 'Devloper', 'okk', '125', 'are you sure', '1', '10', '39', 'i am klokbox', 'i am sigle person', '12', '454', '2021-12-10 08:33:13', '2021-12-10 10:20:20', '', ''),
(3, 'Virag', '91', '9992345670', 0, 'Male', '10', '', '1', NULL, 0, '0', 0, '0', 1, '10', '10', 'DONE', '2021-11-01', '1', '3', '12', 'ok', 'i am sigle', 'Kmphitech', 'Devloper', 'okk', '125', 'are you sure', '1', '10', '39', 'i am klokbox', 'i am sigle person', '12', '454', '2021-12-10 08:35:31', '2021-12-10 10:20:22', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_media_master`
--

CREATE TABLE `user_media_master` (
  `media_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile` text NOT NULL,
  `media_type` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_media_master`
--

INSERT INTO `user_media_master` (`media_id`, `user_id`, `profile`, `media_type`, `created_at`, `updated_at`) VALUES
(1, 3, '163912533161b31153b4fc9.jpg', 'image', '2021-12-10 03:05:31', '2021-12-10 03:05:31'),
(2, 3, '163912533161b31153bd842.png', 'image', '2021-12-10 03:05:31', '2021-12-10 03:05:31');

-- --------------------------------------------------------

--
-- Table structure for table `version_master`
--

CREATE TABLE `version_master` (
  `version_id` int(11) NOT NULL,
  `device_type` enum('android','iOS') NOT NULL,
  `version_code` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `version_master`
--

INSERT INTO `version_master` (`version_id`, `device_type`, `version_code`, `created_date`, `updated_date`) VALUES
(1, 'android', '4', '2021-08-16 15:02:09', '2021-08-16 15:02:09'),
(2, 'iOS', '1', '2021-08-16 15:02:19', '2021-08-16 15:02:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `key_master`
--
ALTER TABLE `key_master`
  ADD PRIMARY KEY (`key_id`);

--
-- Indexes for table `key_token_master`
--
ALTER TABLE `key_token_master`
  ADD PRIMARY KEY (`key_token_id`);

--
-- Indexes for table `send_otp_master`
--
ALTER TABLE `send_otp_master`
  ADD PRIMARY KEY (`otp_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_master`
--
ALTER TABLE `user_master`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_media_master`
--
ALTER TABLE `user_media_master`
  ADD PRIMARY KEY (`media_id`);

--
-- Indexes for table `version_master`
--
ALTER TABLE `version_master`
  ADD PRIMARY KEY (`version_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `key_master`
--
ALTER TABLE `key_master`
  MODIFY `key_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `key_token_master`
--
ALTER TABLE `key_token_master`
  MODIFY `key_token_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `send_otp_master`
--
ALTER TABLE `send_otp_master`
  MODIFY `otp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_master`
--
ALTER TABLE `user_master`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_media_master`
--
ALTER TABLE `user_media_master`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `version_master`
--
ALTER TABLE `version_master`
  MODIFY `version_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
