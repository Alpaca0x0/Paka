-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 16, 2021 at 04:00 AM
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
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int NOT NULL,
  `username` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(77) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `identity` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'member',
  `email` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `username`, `password`, `identity`, `email`) VALUES
(1, 'alpaca0x0', '8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9', 'admin', 'alpaca0x0.tw@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int NOT NULL,
  `title` varchar(24) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `content` varchar(535) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `poster` int NOT NULL,
  `datetime` int NOT NULL,
  `status` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `title`, `content`, `poster`, `datetime`, `status`) VALUES
(1, 'Hello World', 'This is first post on this site, hello guys, hope you have fun on here.', 1, 1639563405, 'alive'),
(2, 'Here is the second post', 'hello guys, here is the second post on this site, I hope you guys enjoy :)', 1, 1639563406, 'removed'),
(3, '測試標題', '測試內容喔齁', 1, 1639566375, 'removed'),
(4, '這是測試文章', '嗨嗨測試文章的內容', 1, 1639567122, 'removed'),
(5, '這是第三篇文章', '這是第三篇文章的內容', 1, 1639568102, 'removed'),
(6, 'test', 'test content', 1, 1639569282, 'removed'),
(7, 'test', 'testccccc', 1, 1639569431, 'removed'),
(8, 'test', 'testcccc', 1, 1639569664, 'removed'),
(9, 'jsaoidjsda', 'jodpsajpods', 1, 1639569971, 'removed'),
(10, 'samdopsjapodjm', 'dosadosjpdmpo', 1, 1639570021, 'removed'),
(11, 'mdopsamdpo', 'odpsmapodp', 1, 1639570063, 'removed'),
(12, 'dmasopmdpod', 'jdpoajspodjpsad', 1, 1639572416, 'removed'),
(13, 'hello', 'world', 1, 1639572607, 'removed'),
(14, 'dsadsad', 'idjsaoidjsad', 1, 1639572663, 'removed'),
(15, 'dsad', 'dsaddd', 1, 1639572677, 'removed'),
(16, 'dsad', 'dsadsad', 1, 1639572715, 'removed'),
(17, 'dsadasd', 'dasdsadsad', 1, 1639572826, 'removed'),
(18, 'dasdsad', 'dsadsad', 1, 1639572882, 'removed'),
(19, 'dsaopdmpo', 'dkopaskpodksad', 1, 1639572944, 'removed'),
(20, 'dsadsad', 'dsadsad', 1, 1639572989, 'removed'),
(21, 'dsamiodjo', 'djiosajodjsaodjo', 1, 1639573122, 'removed'),
(22, 'dsad', 'dsadsad', 1, 1639573144, 'removed'),
(23, 'dasdmpoasdmp', 'mdpomsapdomp', 1, 1639573152, 'removed'),
(24, 'dmsaodjio', 'jdoisjaod', 1, 1639573245, 'removed'),
(25, 'fjdopsfjpo', 'jdopsajdpo', 1, 1639573260, 'removed'),
(26, 'dsadas', 'dsadsad', 1, 1639573339, 'removed'),
(27, 'dddd', 'dasdddd', 1, 1639573359, 'removed'),
(28, 'dsad', 'dsadsad', 1, 1639573379, 'removed'),
(29, 'sadaospdpo', 'dkopsakdpoa', 1, 1639573508, 'removed'),
(30, 'hello yoyo', 'content testing', 1, 1639573645, 'removed'),
(31, '這是一篇測試文章的標題', '這是一篇測試文章的內容', 1, 1639574282, 'removed'),
(32, '這是第三篇文章', '可惜等等第二篇就要被刪除了', 1, 1639575211, 'alive'),
(33, '第四篇', 'gadfsadsadsad', 1, 1639575293, 'removed'),
(34, '第五篇', 'dsadsads', 1, 1639575299, 'removed'),
(35, '你好', '你好世界', 1, 1639576859, 'alive'),
(36, '測試的時候一直發文好孤單', '幸好有 Bugs 陪我 :)', 1, 1639576891, 'alive'),
(37, '看到 Bug 並不可怕', '看不到的那種瑟瑟發抖', 1, 1639577564, 'alive'),
(38, '測試文章', '哈囉哈囉', 1, 1639583546, 'removed');

-- --------------------------------------------------------

--
-- Table structure for table `post_event`
--

CREATE TABLE `post_event` (
  `id` int NOT NULL,
  `action` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'reply,report,good,suck',
  `who` int NOT NULL,
  `datetime` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `reply`
--

CREATE TABLE `reply` (
  `id` int NOT NULL,
  `reply` int DEFAULT NULL COMMENT 'reply id or null for post',
  `content` varchar(320) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `replier` int NOT NULL COMMENT 'id who reply',
  `datetime` int NOT NULL,
  `post` int NOT NULL COMMENT 'in which post',
  `status` varchar(16) COLLATE utf8_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;

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
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_event`
--
ALTER TABLE `post_event`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reply`
--
ALTER TABLE `reply`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `post_event`
--
ALTER TABLE `post_event`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reply`
--
ALTER TABLE `reply`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
