-- MySQL dump 10.13  Distrib 8.0.41, for macos15 (x86_64)
--
-- Host: 127.0.0.1    Database: dti_schema
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Category`
--

DROP TABLE IF EXISTS `Category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Category` (
  `category_id` int(11) NOT NULL,
  `categroy_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Category`
--

LOCK TABLES `Category` WRITE;
/*!40000 ALTER TABLE `Category` DISABLE KEYS */;
INSERT INTO `Category` VALUES (1,'swimming');
/*!40000 ALTER TABLE `Category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Contestant`
--

DROP TABLE IF EXISTS `Contestant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Contestant` (
  `contestant_id` int(11) NOT NULL,
  `contestant_name` varchar(45) DEFAULT NULL,
  `contestant_number` int(11) DEFAULT NULL,
  `category` varchar(45) DEFAULT NULL,
  `description` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`contestant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Contestant`
--

LOCK TABLES `Contestant` WRITE;
/*!40000 ALTER TABLE `Contestant` DISABLE KEYS */;
INSERT INTO `Contestant` VALUES (1,'name',NULL,NULL,NULL),(2,'remiel',1,'summer','nigga');
/*!40000 ALTER TABLE `Contestant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Criteria`
--

DROP TABLE IF EXISTS `Criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Criteria` (
  `criteria_id` int(11) NOT NULL,
  `criteria_name` varchar(45) DEFAULT NULL,
  `criteria_weight` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`criteria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Criteria`
--

LOCK TABLES `Criteria` WRITE;
/*!40000 ALTER TABLE `Criteria` DISABLE KEYS */;
/*!40000 ALTER TABLE `Criteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Judge`
--

DROP TABLE IF EXISTS `Judge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Judge` (
  `judge_id` int(11) NOT NULL,
  `judge_name` varchar(45) DEFAULT NULL,
  `contact_information` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`judge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Judge`
--

LOCK TABLES `Judge` WRITE;
/*!40000 ALTER TABLE `Judge` DISABLE KEYS */;
INSERT INTO `Judge` VALUES (1,'NIGGA','email'),(2,'tron','wala eh');
/*!40000 ALTER TABLE `Judge` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Score`
--

DROP TABLE IF EXISTS `Score`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Score` (
  `score_id` int(11) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `contestant_id` int(11) NOT NULL,
  `score_value` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`score_id`),
  KEY `judgeid` (`judge_id`),
  KEY `contesanttid` (`contestant_id`),
  CONSTRAINT `contesanttid` FOREIGN KEY (`contestant_id`) REFERENCES `Contestant` (`contestant_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `judgeid` FOREIGN KEY (`judge_id`) REFERENCES `Judge` (`judge_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Score`
--

LOCK TABLES `Score` WRITE;
/*!40000 ALTER TABLE `Score` DISABLE KEYS */;
/*!40000 ALTER TABLE `Score` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `userType` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login`
--

LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` VALUES (1,'admin','1234','admin'),(2,'user','1234','user'),(3,'remiel','123','admin'),(4,'nigga','123','user');
/*!40000 ALTER TABLE `login` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-17 19:58:28
