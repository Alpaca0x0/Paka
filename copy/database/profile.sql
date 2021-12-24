-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2021-12-24 04:39:47
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
-- 資料表結構 `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL COMMENT 'account id',
  `nickname` varchar(16) COLLATE utf8mb4_bin DEFAULT NULL,
  `gender` varchar(16) COLLATE utf8mb4_bin NOT NULL DEFAULT 'secret',
  `birthday` int(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `profile`
--

INSERT INTO `profile` (`id`, `nickname`, `gender`, `birthday`) VALUES
(1, '羊駝葛格', 'secret', NULL);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
