-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: user
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB
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
-- Create and select the database
--
CREATE DATABASE IF NOT EXISTS `user`;

USE `user`;

--
-- Table structure for table `users_table`
--
DROP TABLE IF EXISTS `users_table`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
    `users_table` (
        `users_id` int (11) NOT NULL AUTO_INCREMENT,
        `username` varchar(45) DEFAULT NULL,
        `password` varchar(45) DEFAULT NULL,
        `userType` varchar(45) DEFAULT NULL,
        PRIMARY KEY (`users_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_table`
--
LOCK TABLES `users_table` WRITE;

/*!40000 ALTER TABLE `users_table` DISABLE KEYS */;

INSERT INTO
    `users_table`
VALUES
    (1, 'admin', '123', 'Admin'),
    (2, 'judge', '123', 'Judge'),
    (3, 'judge123', '123', 'Judge'),
    (4, 'staff', '123', 'Staff');

/*!40000 ALTER TABLE `users_table` ENABLE KEYS */;

UNLOCK TABLES;

--
-- Table structure for table `contest_table`
--
DROP TABLE IF EXISTS `contest_table`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
    `contest_table` (
        `contest_id` int (11) NOT NULL AUTO_INCREMENT,
        `contest_name` varchar(45) DEFAULT NULL,
        `contest_date` date DEFAULT NULL,
        `location` varchar(45) DEFAULT NULL,
        PRIMARY KEY (`contest_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `category_table`
--
DROP TABLE IF EXISTS `category_table`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
    `category_table` (
        `category_id` int (11) NOT NULL AUTO_INCREMENT,
        `fk_category_contest` int (11) DEFAULT NULL,
        `category_name` varchar(45) DEFAULT NULL,
        `category_description` varchar(45) DEFAULT NULL,
        PRIMARY KEY (`category_id`),
        KEY `contest_id_idx` (`fk_category_contest`),
        CONSTRAINT `contest_id` FOREIGN KEY (`fk_category_contest`) REFERENCES `contest_table` (`contest_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contestant_table`
--
DROP TABLE IF EXISTS `contestant_table`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
    `contestant_table` (
        `contestant_id` int (11) NOT NULL AUTO_INCREMENT,
        `fk_contestant_category` int (11) DEFAULT NULL,
        `fk_contestant_contest` int (11) DEFAULT NULL,
        `contestant_name` varchar(45) DEFAULT NULL,
        `contestant_number` int (11) DEFAULT NULL,
        `title` varchar(45) DEFAULT NULL,
        `bio` varchar(45) DEFAULT NULL,
        `gender` varchar(45) DEFAULT NULL,
        `profile_image` varchar(255) DEFAULT NULL,
        `expanded_image` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`contestant_id`),
        KEY `fk_contestant_category_idx` (`fk_contestant_category`),
        KEY `fk_contestant_contest_idx` (`fk_contestant_contest`),
        CONSTRAINT `fk_contestant_category` FOREIGN KEY (`fk_contestant_category`) REFERENCES `category_table` (`category_id`),
        CONSTRAINT `fk_contestant_contest` FOREIGN KEY (`fk_contestant_contest`) REFERENCES `contest_table` (`contest_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `criteria_table`
--
DROP TABLE IF EXISTS `criteria_table`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
    `criteria_table` (
        `criteria_id` int (11) NOT NULL AUTO_INCREMENT,
        `fk_criteria_contest` int (11) DEFAULT NULL,
        `criteria_name` varchar(45) DEFAULT NULL,
        `criteria_description` varchar(45) DEFAULT NULL,
        `max_score` int (11) DEFAULT NULL,
        PRIMARY KEY (`criteria_id`),
        KEY `fk_criteria_contest_idx` (`fk_criteria_contest`),
        CONSTRAINT `fk_criteria_contest` FOREIGN KEY (`fk_criteria_contest`) REFERENCES `contest_table` (`contest_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `judge_table`
--
DROP TABLE IF EXISTS `judge_table`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
    `judge_table` (
        `judge_id` int (11) NOT NULL AUTO_INCREMENT,
        `fk_judge_contest` int (11) DEFAULT NULL,
        `judge_name` varchar(45) DEFAULT NULL,
        `contact_information` varchar(45) DEFAULT NULL,
        PRIMARY KEY (`judge_id`),
        KEY `fk_judge_contest_idx` (`fk_judge_contest`),
        CONSTRAINT `fk_judge_contest` FOREIGN KEY (`fk_judge_contest`) REFERENCES `contest_table` (`contest_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logs_table`
--
DROP TABLE IF EXISTS `logs_table`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
    `logs_table` (
        `log_id` int (11) NOT NULL AUTO_INCREMENT,
        `fk_logs_users` int (11) DEFAULT NULL,
        `action` varchar(300) DEFAULT NULL,
        `log_time` datetime DEFAULT NULL,
        PRIMARY KEY (`log_id`),
        KEY `fk_logs_users_idx` (`fk_logs_users`),
        CONSTRAINT `fk_logs_users` FOREIGN KEY (`fk_logs_users`) REFERENCES `users_table` (`users_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `score_table`
--
DROP TABLE IF EXISTS `score_table`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
    `score_table` (
        `score_id` int (11) NOT NULL AUTO_INCREMENT,
        `fk_score_judge` int (11) DEFAULT NULL,
        `fk_score_contestant` int (11) DEFAULT NULL,
        `fk_score_criteria` int (11) DEFAULT NULL,
        `score_value` decimal(10, 0) DEFAULT NULL,
        `remarks` varchar(45) DEFAULT NULL,
        PRIMARY KEY (`score_id`),
        KEY `fk_score_judge_idx` (`fk_score_judge`),
        KEY `fk_score_contestant_idx` (`fk_score_contestant`),
        KEY `fk_score_criteria_idx` (`fk_score_criteria`),
        CONSTRAINT `fk_score_contestant` FOREIGN KEY (`fk_score_contestant`) REFERENCES `contestant_table` (`contestant_id`),
        CONSTRAINT `fk_score_criteria` FOREIGN KEY (`fk_score_criteria`) REFERENCES `criteria_table` (`criteria_id`),
        CONSTRAINT `fk_score_judge` FOREIGN KEY (`fk_score_judge`) REFERENCES `judge_table` (`judge_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;