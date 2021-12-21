-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2021-12-21 04:44:55
-- 伺服器版本： 10.4.21-MariaDB
-- PHP 版本： 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫: `alpacatech`
--

-- --------------------------------------------------------

--
-- 資料表結構 `account`
--

CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(77) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `identity` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'member',
  `email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- 傾印資料表的資料 `account`
--

INSERT INTO `account` (`id`, `username`, `password`, `identity`, `email`) VALUES
(1, 'alpaca0x0', '8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9', 'admin', 'alpaca0x0.tw@gmail.com');

-- --------------------------------------------------------

--
-- 資料表結構 `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `reply` int(11) DEFAULT NULL COMMENT 'reply id or null for post',
  `content` varchar(320) COLLATE utf8mb4_bin NOT NULL,
  `commenter` int(11) NOT NULL COMMENT 'id who commented',
  `datetime` int(11) NOT NULL,
  `post` int(11) NOT NULL COMMENT 'in which post',
  `status` varchar(16) COLLATE utf8mb4_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `comment`
--

INSERT INTO `comment` (`id`, `reply`, `content`, `commenter`, `datetime`, `post`, `status`) VALUES
(1, NULL, 'dsadsad', 1, 1639586919, 37, 'alive'),
(2, 1, 'test reply reply', 1, 1639586999, 37, 'alive'),
(3, NULL, 'omg 我完成了回覆功能', 1, 1639596016, 37, 'alive'),
(4, NULL, '這是一個回覆測試', 1, 1639596097, 37, 'alive'),
(5, NULL, 'hello', 1, 1639596251, 37, 'alive'),
(6, NULL, 'test', 1, 1640050006, 1, 'alive'),
(7, NULL, 'hello', 1, 1640050274, 43, 'alive'),
(8, NULL, 'test', 1, 1640050473, 43, 'alive'),
(9, 8, 'hi', 1, 1640050502, 43, 'alive'),
(10, NULL, 'test', 1, 1640057444, 43, 'alive');

-- --------------------------------------------------------

--
-- 資料表結構 `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `title` varchar(24) COLLATE utf8mb4_bin NOT NULL,
  `content` varchar(535) COLLATE utf8mb4_bin NOT NULL,
  `poster` int(11) NOT NULL,
  `datetime` int(11) NOT NULL,
  `status` varchar(16) COLLATE utf8mb4_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `post`
--

INSERT INTO `post` (`id`, `title`, `content`, `poster`, `datetime`, `status`) VALUES
(1, 'Hello World', 'This is first post on this site, hello guys, hope you have fun on here.', 1, 1639563405, 'alive'),
(43, 'test', 'testing content', 1, 1640050269, 'alive');

-- --------------------------------------------------------

--
-- 資料表結構 `post_event`
--

CREATE TABLE `post_event` (
  `id` int(11) NOT NULL,
  `action` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'reply,report,good,suck',
  `who` int(11) NOT NULL,
  `datetime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `post_event`
--
ALTER TABLE `post_event`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `post_event`
--
ALTER TABLE `post_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
