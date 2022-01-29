-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2022-01-14 08:20:49
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
  `email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review, unverified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- 傾印資料表的資料 `account`
--

INSERT INTO `account` (`id`, `username`, `password`, `identity`, `email`, `status`) VALUES
(24, 'alpaca0x0', '8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9', 'admin', 'alpaca0x0.tw@gmail.com', 'alive'),
(25, 'alpaca0x02', '8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9', 'member', 'alpacaknoyh@gmail.com', 'alive'),
(26, 'alpaca0x03', '8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9', 'member', 'zmalxnskqp8246@gmail.com', 'unverified');

-- --------------------------------------------------------

--
-- 資料表結構 `account_event`
--

CREATE TABLE `account_event` (
  `id` int(11) NOT NULL,
  `account` int(11) NOT NULL COMMENT 'account id',
  `action` varchar(16) COLLATE utf8mb4_bin NOT NULL,
  `target` varchar(64) COLLATE utf8mb4_bin DEFAULT NULL,
  `ip` varchar(40) COLLATE utf8mb4_bin NOT NULL,
  `expire` int(11) NOT NULL DEFAULT 0,
  `datetime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `account_event`
--

INSERT INTO `account_event` (`id`, `account`, `action`, `target`, `ip`, `expire`, `datetime`) VALUES
(14, 24, 'register', '0edcca7c76e41718c330dddae126ab9b1d41097776eb360551a107ed15e02e0c', '127.0.0.1', 0, 1641712449),
(15, 25, 'register', '72c1da2072bc4a55409f3359e0407d54cd0c5c12f1efd9983026e422cd4d224c', '127.0.0.1', 0, 1641719490),
(16, 24, 'login', 'bb753734322aa200fb9234ac3bc60e51112c0b08cedef1003323d633df55e8dc', '::1', 0, 1642140865),
(17, 24, 'login', '1ad4c63b4e9eaf17e9006a19b86a75742bca046b276c7011996302da1ed9317b', '::1', 0, 1642140867),
(18, 24, 'login', '5b2a395a1f96feba4f362c3b31fdf4c26bed1ca28e4a6f35381dfa61d33ad86a', '::1', 0, 1642141821),
(19, 24, 'login', 'c1a75e33b6c67ff3431d443f1a6de679f2af33cdabf2377774117552e67af261', '::1', 0, 1642141942),
(20, 24, 'login', '2399208d288c87fc9d78d13c114950f6e3495a6fe8d0000d0a0aabc92d60bfa4', '::1', 0, 1642142248),
(21, 24, 'login', 'c537a20895bd01146e5d9fb47fde58e292c34341f005915026a9f87c541aff6f', '::1', 0, 1642142295);

-- --------------------------------------------------------

--
-- 資料表結構 `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `reply` int(11) DEFAULT NULL COMMENT 'reply id or null for post',
  `content` varchar(535) COLLATE utf8mb4_bin NOT NULL,
  `commenter` int(11) NOT NULL COMMENT 'id who commented',
  `datetime` int(11) NOT NULL,
  `post` int(11) NOT NULL COMMENT 'in which post',
  `status` varchar(16) COLLATE utf8mb4_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `comment_edited`
--

CREATE TABLE `comment_edited` (
  `id` int(11) NOT NULL,
  `editor` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  `comment` int(11) NOT NULL,
  `content` varchar(535) COLLATE utf8mb4_bin NOT NULL,
  `datetime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `poster` int(11) NOT NULL,
  `title` varchar(24) COLLATE utf8mb4_bin NOT NULL,
  `content` varchar(535) COLLATE utf8mb4_bin NOT NULL,
  `datetime` int(11) NOT NULL,
  `status` varchar(16) COLLATE utf8mb4_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `post`
--

INSERT INTO `post` (`id`, `poster`, `title`, `content`, `datetime`, `status`) VALUES
(2, 24, 'adsasddada', 'dasasssda', 1641748726, 'alive'),
(3, 24, 'dassd', 'Marihuana, [2022/1/10 上午 01:17] 現在開始，註冊帳號的部分需要使用信箱認證，也就是大家熟悉的驗證信。👉 我官方的 email 為「alpaca.tech.service@gmail.com」，請小心其他釣魚連結。另外也開始陸續再重要的功能上使用驗證碼機制👊以防止機器人刷資料庫。💁‍♂️雖然有了這些，但好的社群環境依然需要大家共同維護，請大家盡力配合。😀謝謝各位的光臨。', 1641748753, 'alive');

-- --------------------------------------------------------

--
-- 資料表結構 `post_edited`
--

CREATE TABLE `post_edited` (
  `id` int(11) NOT NULL,
  `editor` int(11) NOT NULL,
  `post` int(11) NOT NULL,
  `title` varchar(24) COLLATE utf8mb4_bin NOT NULL,
  `content` varchar(535) COLLATE utf8mb4_bin NOT NULL,
  `datetime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `post_event`
--

CREATE TABLE `post_event` (
  `id` int(11) NOT NULL,
  `committer` int(11) NOT NULL,
  `action` varchar(16) COLLATE utf8_bin NOT NULL COMMENT 'good,suck',
  `post` int(11) NOT NULL,
  `datetime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- 資料表結構 `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL COMMENT 'account id',
  `nickname` varchar(16) COLLATE utf8mb4_bin DEFAULT NULL,
  `gender` varchar(16) COLLATE utf8mb4_bin NOT NULL DEFAULT 'secret',
  `birthday` varchar(10) COLLATE utf8mb4_bin DEFAULT NULL,
  `avatar` mediumblob DEFAULT NULL COMMENT 'avatar, max 16mb'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 傾印資料表的資料 `profile`
--

INSERT INTO `profile` (`id`, `nickname`, `gender`, `birthday`, `avatar`) VALUES
(24, NULL, 'secret', NULL, NULL),
(25, NULL, 'secret', NULL, NULL),
(26, NULL, 'secret', NULL, NULL);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `account_event`
--
ALTER TABLE `account_event`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `comment_edited`
--
ALTER TABLE `comment_edited`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `post_edited`
--
ALTER TABLE `post_edited`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `post_event`
--
ALTER TABLE `post_event`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `account_event`
--
ALTER TABLE `account_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `comment_edited`
--
ALTER TABLE `comment_edited`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `post_edited`
--
ALTER TABLE `post_edited`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `post_event`
--
ALTER TABLE `post_event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
