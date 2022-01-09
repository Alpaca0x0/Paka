-- MySQL dump 10.13  Distrib 8.0.27, for Linux (x86_64)
--
-- Host: localhost    Database: alpacatech
-- ------------------------------------------------------
-- Server version	8.0.27-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(77) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `identity` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'member',
  `email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review, unverified',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account`
--

LOCK TABLES `account` WRITE;
/*!40000 ALTER TABLE `account` DISABLE KEYS */;
INSERT INTO `account` VALUES (1,'alpaca0x0','8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9','admin','alpaca0x0.tw@gmail.com','alive'),(8,'tester','8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9','member','dasdas@dsadasd.sadasd','alive'),(11,'alpaca0x02','8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9','member','alpaca0x02.tw@gmail.com','unverified'),(12,'hello123','8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9','member','hellower@gmail.com','unverified'),(13,'hello321','21a1e823f08990213401d49f8c0724040b810dc79d664ebf35e3d49bcd9eeb13','member','das@fs.dfdsf','alive'),(14,'dsadasd','ceb3b771ff9b66de568a228d026e3fbe2fe299f60721b7aea41f3f707f5a0666','member','fwq@dasd.fr','removed'),(15,'tester2','8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9','member','tester2@gmail.com','alive'),(16,'jdsajdpo','8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9','member','jdsajdpo@dsad.sad','unverified'),(17,'wqdqwdqw','a42f01e426d8c6deb2af6789efaf1afca811ba8af0ddd2afc769df03dbb5b0e6','member','djisaojdo@dasd.sad','unverified'),(18,'dwqdwqdwq','fa16a4c10a1fd33fc9dab4a57527240b677e6db557ce792f64307c8787a19599','member','sajdo@fsfds.fdsf','unverified'),(22,'gzmalxnsk8246','8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9','member','gzmalxnsk8246@gmail.com','unverified'),(23,'alpacaknoyh','8f0e2f76e22b43e2855189877e7dc1e1e7d98c226c95db247cd1d547928334a9','member','alpacaknoyh@gmail.com','unverified');
/*!40000 ALTER TABLE `account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `account_event`
--

DROP TABLE IF EXISTS `account_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_event` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account` int NOT NULL COMMENT 'account id',
  `action` varchar(16) COLLATE utf8mb4_bin NOT NULL,
  `detail` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip` varchar(40) COLLATE utf8mb4_bin NOT NULL,
  `datetime` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_event`
--

LOCK TABLES `account_event` WRITE;
/*!40000 ALTER TABLE `account_event` DISABLE KEYS */;
INSERT INTO `account_event` VALUES (2,11,'register',NULL,'127.0.0.1',1641654358),(3,13,'register','093e2bdea164b05697f0f5d73915b770a2b50cbc8ccb47ab19b9179dba08dd59','127.0.0.1',1641654688),(4,14,'register','0a0a569f7414a9e4fc7d81c5fcf104f611170081d10641fe65ed76ab020c484f','127.0.0.1',1641655105),(5,15,'register','099fdcb0e2e4f1fd30b3b0bd81b3525cfe93667a9320258764a23231f824a6a7','127.0.0.1',1641660870),(6,16,'register','30a905d6d4282092ef2533067e1570278e6624d26ea61b18786d191cdc015f6a','127.0.0.1',1641664231),(7,17,'register','4c8b9f2d7dbb94b94161b71a81d9bd1413a724db84c8ad718d244c3a364b8a18','127.0.0.1',1641664246),(8,18,'register','9f25abd7adf7c535f5b47e02ed3585ac1fd11bba21726d6a882527cf15abfb73','127.0.0.1',1641664343),(9,19,'register','e8eab801770a997ffdb2eea6221072092697f76169663795418daa2c1a5de08b','127.0.0.1',1641665482),(10,20,'register','8d8263b669f620b319685c9d4cd7cd81ed5bd00bbec37992e842032003d5211c','127.0.0.1',1641665537),(11,21,'register','59a7d39c51bd28bafaac5005ae4c30b6ea8e73b7207b11ed710ca3d61b681e71','127.0.0.1',1641665625),(12,22,'register','27b9d63d70a1d0c219caf1b4ccf8165a1008753d32c2798d96faf7a4a6f56ebe','127.0.0.1',1641666220),(13,23,'register','d3d413b4676ab7a8386b2a6a0db6f92a6d3d8b0c60f648043126beeef53c8e2a','127.0.0.1',1641666818);
/*!40000 ALTER TABLE `account_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reply` int DEFAULT NULL COMMENT 'reply id or null for post',
  `content` varchar(320) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `commenter` int NOT NULL COMMENT 'id who commented',
  `datetime` int NOT NULL,
  `post` int NOT NULL COMMENT 'in which post',
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment`
--

LOCK TABLES `comment` WRITE;
/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` VALUES (1,NULL,'87',8,1641575931,1,'alive'),(2,1,'耖媽的',8,1641575941,1,'alive');
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment_edited`
--

DROP TABLE IF EXISTS `comment_edited`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comment_edited` (
  `id` int NOT NULL AUTO_INCREMENT,
  `editor` int NOT NULL,
  `post` int NOT NULL,
  `comment` int NOT NULL,
  `content` varchar(535) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `datetime` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment_edited`
--

LOCK TABLES `comment_edited` WRITE;
/*!40000 ALTER TABLE `comment_edited` DISABLE KEYS */;
INSERT INTO `comment_edited` VALUES (1,8,1,1,'21',1641575936),(2,8,1,2,'456456',1641575955);
/*!40000 ALTER TABLE `comment_edited` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post`
--

DROP TABLE IF EXISTS `post`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post` (
  `id` int NOT NULL AUTO_INCREMENT,
  `poster` int NOT NULL,
  `title` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `content` varchar(535) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `datetime` int NOT NULL,
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'alive' COMMENT 'alive, removed, review',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post`
--

LOCK TABLES `post` WRITE;
/*!40000 ALTER TABLE `post` DISABLE KEYS */;
INSERT INTO `post` VALUES (1,8,'哈囉今天過得好嗎','還行',1641575924,'alive');
/*!40000 ALTER TABLE `post` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_edited`
--

DROP TABLE IF EXISTS `post_edited`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_edited` (
  `id` int NOT NULL AUTO_INCREMENT,
  `editor` int NOT NULL,
  `post` int NOT NULL,
  `title` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `content` varchar(535) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `datetime` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_edited`
--

LOCK TABLES `post_edited` WRITE;
/*!40000 ALTER TABLE `post_edited` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_edited` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_event`
--

DROP TABLE IF EXISTS `post_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_event` (
  `id` int NOT NULL AUTO_INCREMENT,
  `committer` int NOT NULL,
  `action` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'good,suck',
  `post` int NOT NULL,
  `datetime` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_event`
--

LOCK TABLES `post_event` WRITE;
/*!40000 ALTER TABLE `post_event` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile` (
  `id` int NOT NULL COMMENT 'account id',
  `nickname` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `gender` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'secret',
  `birthday` int DEFAULT NULL,
  `avatar` mediumblob COMMENT 'avatar, max 16mb',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
INSERT INTO `profile` VALUES (1,NULL,'secret',NULL,NULL),(8,NULL,'secret',NULL,NULL),(11,NULL,'secret',NULL,NULL),(12,NULL,'secret',NULL,NULL),(13,NULL,'secret',NULL,NULL),(14,NULL,'secret',NULL,NULL),(15,NULL,'secret',NULL,NULL),(16,NULL,'secret',NULL,NULL),(17,NULL,'secret',NULL,NULL),(18,NULL,'secret',NULL,NULL),(19,NULL,'secret',NULL,NULL),(20,NULL,'secret',NULL,NULL),(21,NULL,'secret',NULL,NULL),(22,NULL,'secret',NULL,NULL),(23,NULL,'secret',NULL,NULL);
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-01-09 14:34:07
