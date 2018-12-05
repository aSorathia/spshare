-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2018 at 01:35 AM
-- Server version: 10.1.35-MariaDB
-- PHP Version: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `g_id` int(11) NOT NULL,
  `g_name` varchar(50) NOT NULL,
  `g_limit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`g_id`, `g_name`, `g_limit`) VALUES
(1, 'Spshare Default', 2097152),
(2, 'Spshare_notes', 2097152),
(3, 'Secure Programing', 122880);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `p_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `g_id` int(11) NOT NULL,
  `p_type` varchar(20) NOT NULL,
  `p_file_extension` varchar(4) NOT NULL,
  `p_desc` varchar(500) NOT NULL,
  `p_time_create` int(10) UNSIGNED NOT NULL,
  `p_time_access` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `p_filename` varchar(68) NOT NULL,
  `p_new_fileName` varchar(8) NOT NULL,
  `fileSize` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`p_id`, `u_id`, `g_id`, `p_type`, `p_file_extension`, `p_desc`, `p_time_create`, `p_time_access`, `p_filename`, `p_new_fileName`, `fileSize`) VALUES
(1, 2, 1, 'image/jpeg', 'jpg', 'fdgdfg', 1542916551, 0, 'newNameim2', 'Rh5g3FfT', 51405),
(2, 2, 1, 'application/octet-st', 'php', '66573426351550907459\r\n50494572323240742859\r\n88910463074188235312\r\n35137674742250972078\r\n76719775620773005906\r\n95822331009915868086\r\n99831022669635923582\r\n24538446590937178947\r\n27773496065695415348\r\n73122890186248459590\r\n68169626739806686369\r\n26035212690828918367\r\n03750256882259000274\r\n10619051537808989610\r\n13904499088965749279\r\n64305320083878834276\r\n40413361063277902439\r\n43148711586657579683\r\n46048717770786022510\r\n97305109814479882608\r\n06455222224281770037\r\n78603286532941766948\r\n3506838004500948', 1542916580, 0, 'header', '5fhfdfjv', 9509),
(3, 2, 2, 'text/plain', 'py', 'flaww', 1542916601, 0, 'flawfinder', 'F7TCDFTh', 88860),
(4, 2, 1, 'text/plain', 'py', 'some assignement', 1542916625, 0, 'Assignment', 'FhCFvY8T', 13935),
(5, 2, 2, 'application/octet-st', 'md', 'changelog file', 1542916652, 0, 'rust log', 'hh8FhhFE', 33541),
(6, 2, 1, 'application/octet-st', 'cfg', 'sad', 1543273641, 0, 'header', 'vs5hgTh8', 317),
(7, 2, 1, 'application/octet-st', 'cfg', 'djhsdfgkeu', 1543278401, 0, '@#$%^&*(%^&*.php', 'vshgGTdR', 317);

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `r_id` int(11) NOT NULL,
  `r_name` varchar(80) NOT NULL,
  `r_login` varchar(16) NOT NULL,
  `r_pass` varchar(100) NOT NULL,
  `g_id` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`r_id`, `r_name`, `r_login`, `r_pass`, `g_id`) VALUES
(1, 'aSorathia', 'aSorathia', '$argon2i$v=19$m=1024,t=2,p=2$cTRvNmF0MzREb0hqV1gzOQ$FO6fyZcD4e04sV/cXfbXvN71he1znC84CG/wxcLidEk', 1),
(2, 'sheeba', 'khansheeba', '$argon2i$v=19$m=1024,t=2,p=2$Q2lob040YmpubjEvUEtSQg$79jH7t0TxVtayQVw4d4lFfW84AjgTBlpNJoL1dkI47s', 2),
(3, 'abdullah', 'abdullahsn', '$argon2i$v=19$m=1024,t=2,p=2$U2tMMEFzeld3U250VlkwTw$+lY7dLsyPq37wmD8bAKQ3UMXrb6vMdVKsKmo60w4Zn0', 4);

-- --------------------------------------------------------

--
-- Table structure for table `requestex`
--

CREATE TABLE `requestex` (
  `r_id` int(11) NOT NULL,
  `u_login` varchar(16) NOT NULL,
  `g_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `requestex`
--

INSERT INTO `requestex` (`r_id`, `u_login`, `g_name`) VALUES
(3, 'aSorathia', 'Secure Programing'),
(4, 'aSorathia', 'wdm');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `r_id` int(11) NOT NULL,
  `r_name` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`r_id`, `r_name`) VALUES
(1, 'user'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `s_id` int(11) NOT NULL,
  `s_token` varchar(32) NOT NULL,
  `s_key` varchar(32) NOT NULL,
  `s_date` int(10) UNSIGNED NOT NULL,
  `s_uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`s_id`, `s_token`, `s_key`, `s_date`, `s_uid`) VALUES
(18, 'he58FTh4Fhbvd7h3YdTThghREvff4R8', '4RTfRjffEh1vdFhfgGFhshGvfh8dwFD', 11, 3),
(19, 'E1YvhdFfgRfff4jRhGfTh6vRFjeT8gv', 'dFdvRRFYhvfFDReFEw67egTCfj8bTfh', 11, 1),
(21, 'egTgdh8RT71ffdECvFs5YhdfF4fvCjF', 'hvfgh77wdT8dFj3T4FR5TvfRYFFvfGG', 11, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `u_id` int(11) NOT NULL,
  `u_name` varchar(80) NOT NULL,
  `u_login` varchar(16) NOT NULL,
  `u_pass` varchar(100) NOT NULL,
  `r_id` int(11) NOT NULL,
  `created` int(10) UNSIGNED NOT NULL,
  `last_login` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `is_active` int(1) NOT NULL DEFAULT '0',
  `fileLimit` int(11) NOT NULL,
  `spaceLimit` int(11) NOT NULL,
  `userUsedSpace` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`u_id`, `u_name`, `u_login`, `u_pass`, `r_id`, `created`, `last_login`, `is_active`, `fileLimit`, `spaceLimit`, `userUsedSpace`) VALUES
(1, 'as', 'admin', '$argon2i$v=19$m=1024,t=2,p=2$MUlGZzhzdkg1bnBKR2dRMA$IUxsi2MrvHAqTBdUko3MaymidNQZCmSJGRzHSb6oG7A', 2, 1541517637, 0, 0, 0, 0, 0),
(2, 'aSorathia', 'aSorathia', '$argon2i$v=19$m=1024,t=2,p=2$cTRvNmF0MzREb0hqV1gzOQ$FO6fyZcD4e04sV/cXfbXvN71he1znC84CG/wxcLidEk', 1, 1542913518, 0, 0, 92160, 1048576, 197884);

-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--

CREATE TABLE `user_group` (
  `ug_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `g_id` int(11) NOT NULL,
  `create_epoch` int(11) NOT NULL,
  `gUsedSpace` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_group`
--

INSERT INTO `user_group` (`ug_id`, `u_id`, `g_id`, `create_epoch`, `gUsedSpace`) VALUES
(1, 2, 1, 1542913600, 75483),
(2, 2, 2, 1542915248, 122401);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`g_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`p_id`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`r_id`);

--
-- Indexes for table `requestex`
--
ALTER TABLE `requestex`
  ADD PRIMARY KEY (`r_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`r_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`s_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`u_id`),
  ADD UNIQUE KEY `u_login` (`u_login`);

--
-- Indexes for table `user_group`
--
ALTER TABLE `user_group`
  ADD PRIMARY KEY (`ug_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `g_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `r_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `requestex`
--
ALTER TABLE `requestex`
  MODIFY `r_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `r_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `s_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_group`
--
ALTER TABLE `user_group`
  MODIFY `ug_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
