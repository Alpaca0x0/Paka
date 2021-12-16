-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 16, 2021 at 08:24 PM
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
-- Database: `AlpacaTech`
--

-- --------------------------------------------------------

--
-- Table structure for table `reply`
--

CREATE TABLE `reply` (
  `id` int NOT NULL,
  `reply` int DEFAULT NULL COMMENT 'reply id or null for post',
  `content` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `replier` int NOT NULL COMMENT 'id who reply',
  `datetime` int NOT NULL,
  `post` int NOT NULL COMMENT 'in which post',
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Dumping data for table `reply`
--

INSERT INTO `reply` (`id`, `reply`, `content`, `replier`, `datetime`, `post`, `status`) VALUES
(1, NULL, 'dsadsad', 1, 1639586919, 37, 'alive'),
(2, 1, 'test reply reply', 1, 1639586999, 37, 'alive'),
(3, NULL, 'omg 我完成了回覆功能', 1, 1639596016, 37, 'alive'),
(4, NULL, '這是一個回覆測試', 1, 1639596097, 37, 'alive'),
(5, NULL, 'hello', 1, 1639596251, 37, 'alive');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reply`
--
ALTER TABLE `reply`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reply`
--
ALTER TABLE `reply`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
