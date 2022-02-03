-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 03, 2022 at 03:34 PM
-- Server version: 8.0.27-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alpacatech`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int NOT NULL,
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(77) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `identity` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'member',
  `email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review, unverified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `username`, `password`, `identity`, `email`, `status`) VALUES
(24, 'alpaca0x0', '8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9', 'admin', 'alpaca0x0.tw@gmail.com', 'alive'),
(25, 'alpaca0x02', '8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9', 'member', 'alpacaknoyh@gmail.com', 'alive'),
(26, 'alpaca0x03', '8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9', 'member', 'zmalxnskqp8246@gmail.com', 'unverified');

-- --------------------------------------------------------

--
-- Table structure for table `account_event`
--

CREATE TABLE `account_event` (
  `id` int NOT NULL,
  `account` int NOT NULL COMMENT 'account id',
  `action` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `target` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `expire` int NOT NULL DEFAULT '0',
  `datetime` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `account_event`
--

INSERT INTO `account_event` (`id`, `account`, `action`, `target`, `ip`, `expire`, `datetime`) VALUES
(14, 24, 'register', '0edcca7c76e41718c330dddae126ab9b1d41097776eb360551a107ed15e02e0c', '127.0.0.1', 0, 1641712449),
(15, 25, 'register', '72c1da2072bc4a55409f3359e0407d54cd0c5c12f1efd9983026e422cd4d224c', '127.0.0.1', 0, 1641719490),
(16, 24, 'login', 'bb753734322aa200fb9234ac3bc60e51112c0b08cedef1003323d633df55e8dc', '::1', 0, 1642140865),
(17, 24, 'login', '1ad4c63b4e9eaf17e9006a19b86a75742bca046b276c7011996302da1ed9317b', '::1', 0, 1642140867),
(18, 24, 'login', '5b2a395a1f96feba4f362c3b31fdf4c26bed1ca28e4a6f35381dfa61d33ad86a', '::1', 0, 1642141821),
(19, 24, 'login', 'c1a75e33b6c67ff3431d443f1a6de679f2af33cdabf2377774117552e67af261', '::1', 0, 1642141942),
(20, 24, 'login', '2399208d288c87fc9d78d13c114950f6e3495a6fe8d0000d0a0aabc92d60bfa4', '::1', 0, 1642142248),
(21, 24, 'login', 'c537a20895bd01146e5d9fb47fde58e292c34341f005915026a9f87c541aff6f', '::1', 0, 1642142295),
(22, 24, 'login', 'ccb0870f32b18316286c4681debebba16c5c2e270157a4a1b933fd7c929600b4', '127.0.0.1', 1643468915, 1643447188),
(23, 24, 'login', '4167914c946ed732242c40d607e32547bba56fd64a473f06ef0035cdcf148b0c', '127.0.0.1', 1643895085, 1643868231);

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int NOT NULL,
  `reply` int DEFAULT NULL COMMENT 'reply id or null for post',
  `content` varchar(535) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `commenter` int NOT NULL COMMENT 'id who commented',
  `datetime` int NOT NULL,
  `post` int NOT NULL COMMENT 'in which post',
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `comment_edited`
--

CREATE TABLE `comment_edited` (
  `id` int NOT NULL,
  `editor` int NOT NULL,
  `post` int NOT NULL,
  `comment` int NOT NULL,
  `content` varchar(535) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `datetime` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int NOT NULL,
  `poster` int NOT NULL,
  `title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `content` varchar(535) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `datetime` int NOT NULL,
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `post_edited`
--

CREATE TABLE `post_edited` (
  `id` int NOT NULL,
  `editor` int NOT NULL,
  `post` int NOT NULL,
  `title` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `content` varchar(535) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `datetime` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `post_event`
--

CREATE TABLE `post_event` (
  `id` int NOT NULL,
  `committer` int NOT NULL,
  `action` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'good,suck',
  `post` int NOT NULL,
  `datetime` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE `profile` (
  `id` int NOT NULL COMMENT 'account id',
  `nickname` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `gender` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'secret',
  `birthday` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `avatar` mediumblob COMMENT 'avatar, max 16mb'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id`, `nickname`, `gender`, `birthday`, `avatar`) VALUES
(24, '羊駝葛格', 'secret', NULL, NULL),
(25, NULL, 'secret', NULL, NULL),
(26, NULL, 'secret', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `account_event`
--
ALTER TABLE `account_event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment_edited`
--
ALTER TABLE `comment_edited`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_edited`
--
ALTER TABLE `post_edited`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_event`
--
ALTER TABLE `post_event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `account_event`
--
ALTER TABLE `account_event`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comment_edited`
--
ALTER TABLE `comment_edited`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_edited`
--
ALTER TABLE `post_edited`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_event`
--
ALTER TABLE `post_event`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
