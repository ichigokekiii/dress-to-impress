-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: pageant_db
-- ------------------------------------------------------
-- Server version	8.0.40
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
-- Table structure for table category_table
--
DROP TABLE IF EXISTS category_table;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
  category_table (
    category_id int NOT NULL AUTO_INCREMENT,
    fk_category_contest int DEFAULT NULL,
    category_name varchar(45) DEFAULT NULL,
    category_description varchar(45) DEFAULT NULL,
    PRIMARY KEY (category_id),
    KEY contest_id_idx (fk_category_contest),
    CONSTRAINT contest_id FOREIGN KEY (fk_category_contest) REFERENCES contest_table (contest_id)
  ) ENGINE = InnoDB AUTO_INCREMENT = 10 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '		';

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table category_table
--
LOCK TABLES category_table WRITE;

/*!40000 ALTER TABLE category_table DISABLE KEYS */;

INSERT INTO
  category_table
VALUES
  (2, NULL, 'bhihhoo', NULL),
  (6, 3, 'Teens', 'tite burat'),
  (7, 3, 'Teens', 'tite burat'),
  (8, 4, 'mama mo rin', 'asdad'),
  (9, 4, 'asdsadasdas', 'Question and Answer');

/*!40000 ALTER TABLE category_table ENABLE KEYS */;

UNLOCK TABLES;

--
-- Table structure for table contest_table
--
DROP TABLE IF EXISTS contest_table;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
  contest_table (
    contest_id int NOT NULL AUTO_INCREMENT,
    contest_name varchar(45) DEFAULT NULL,
    contest_date date DEFAULT NULL,
    location varchar(45) DEFAULT NULL,
    PRIMARY KEY (contest_id)
  ) ENGINE = InnoDB AUTO_INCREMENT = 6 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table contest_table
--
LOCK TABLES contest_table WRITE;

/*!40000 ALTER TABLE contest_table DISABLE KEYS */;

INSERT INTO
  contest_table
VALUES
  (
    3,
    'Binibining Infanta',
    '2025-05-31',
    'Infanta,Quezon'
  ),
  (4, 'Cainta Beauty', '2025-05-30', 'Cainta, Rizal'),
  (5, 'Manila Pageantry', '2025-05-03', 'Manila');

/*!40000 ALTER TABLE contest_table ENABLE KEYS */;

UNLOCK TABLES;

--
-- Table structure for table contestant_table
--
DROP TABLE IF EXISTS contestant_table;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
  contestant_table (
    contestant_id int NOT NULL AUTO_INCREMENT,
    fk_contestant_category int DEFAULT NULL,
    fk_contestant_contest int DEFAULT NULL,
    contestant_name varchar(45) DEFAULT NULL,
    contestant_number int DEFAULT NULL,
    title varchar(45) DEFAULT NULL,
    bio varchar(45) DEFAULT NULL,
    gender varchar(45) DEFAULT NULL,
    PRIMARY KEY (contestant_id),
    KEY fk_contestant_category_idx (fk_contestant_category),
    KEY fk_contestant_contest_idx (fk_contestant_contest),
    CONSTRAINT fk_contestant_category FOREIGN KEY (fk_contestant_category) REFERENCES category_table (category_id),
    CONSTRAINT fk_contestant_contest FOREIGN KEY (fk_contestant_contest) REFERENCES contest_table (contest_id)
  ) ENGINE = InnoDB AUTO_INCREMENT = 11 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table contestant_table
--
LOCK TABLES contestant_table WRITE;

/*!40000 ALTER TABLE contestant_table DISABLE KEYS */;

INSERT INTO
  contestant_table
VALUES
  (6, 8, 4, 'Matt', 2, 'Poki', 'asdasd', 'Male'),
  (7, 6, 3, 'John ', 5, 'Naku ', 'aosidasio', 'Male'),
  (
    10,
    7,
    3,
    'asdad',
    7,
    'asdasasdasd',
    'asdas',
    'Female'
  );

/*!40000 ALTER TABLE contestant_table ENABLE KEYS */;

UNLOCK TABLES;

--
-- Table structure for table criteria_table
--
DROP TABLE IF EXISTS criteria_table;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
  criteria_table (
    criteria_id int NOT NULL AUTO_INCREMENT,
    fk_criteria_contest int DEFAULT NULL,
    criteria_name varchar(45) DEFAULT NULL,
    criteria_description varchar(45) DEFAULT NULL,
    max_score int DEFAULT NULL,
    PRIMARY KEY (criteria_id),
    KEY fk_criteria_contest_idx (fk_criteria_contest),
    CONSTRAINT fk_criteria_contest FOREIGN KEY (fk_criteria_contest) REFERENCES contest_table (contest_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '	';

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table criteria_table
--
LOCK TABLES criteria_table WRITE;

/*!40000 ALTER TABLE criteria_table DISABLE KEYS */;

/*!40000 ALTER TABLE criteria_table ENABLE KEYS */;

UNLOCK TABLES;

--
-- Table structure for table judge_table
--
DROP TABLE IF EXISTS judge_table;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
  judge_table (
    judge_id int NOT NULL AUTO_INCREMENT,
    fk_judge_contest int DEFAULT NULL,
    judge_name varchar(45) DEFAULT NULL,
    contact_information varchar(45) DEFAULT NULL,
    PRIMARY KEY (judge_id),
    KEY fk_judge_contest_idx (fk_judge_contest),
    CONSTRAINT fk_judge_contest FOREIGN KEY (fk_judge_contest) REFERENCES contest_table (contest_id)
  ) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table judge_table
--
LOCK TABLES judge_table WRITE;

/*!40000 ALTER TABLE judge_table DISABLE KEYS */;

INSERT INTO
  judge_table
VALUES
  (2, 3, 'Celsius', '06565656');

/*!40000 ALTER TABLE judge_table ENABLE KEYS */;

UNLOCK TABLES;

--
-- Table structure for table logs_table
--
DROP TABLE IF EXISTS logs_table;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
  logs_table (
    log_id int NOT NULL AUTO_INCREMENT,
    fk_logs_users int DEFAULT NULL,
    action varchar(300) DEFAULT NULL,
    log_time datetime DEFAULT NULL,
    PRIMARY KEY (log_id),
    KEY fk_logs_users_idx (fk_logs_users),
    CONSTRAINT fk_logs_users FOREIGN KEY (fk_logs_users) REFERENCES users_table (users_id)
  ) ENGINE = InnoDB AUTO_INCREMENT = 48 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table logs_table
--
LOCK TABLES logs_table WRITE;

/*!40000 ALTER TABLE logs_table DISABLE KEYS */;

INSERT INTO
  logs_table
VALUES
  (
    1,
    1,
    'Deleted contest: Binibining Infanta',
    '2025-05-28 22:13:25'
  ),
  (
    2,
    1,
    'Deleted contestant \' from contest \'',
    '2025-05-28 22:34:07'
  ),
  (
    3,
    1,
    'Added contestant \'Remiel\' to contest \'Binibining Infanta\'',
    '2025-05-28 22:38:11'
  ),
  (
    4,
    1,
    'Deleted contestant \'Remiel\' from contest \'Binibining Infanta\'',
    '2025-05-28 22:38:30'
  ),
  (
    5,
    1,
    'Deleted contestant \'Remiel\' from contest \'Binibining Infanta\'',
    '2025-05-28 22:44:13'
  ),
  (
    6,
    1,
    'Deleted contest: Binibining Infanta',
    '2025-05-28 23:00:21'
  ),
  (7, 1, 'User logged out', '2025-05-29 07:53:45'),
  (8, 2, 'User logged in', '2025-05-29 07:54:02'),
  (9, 2, 'User logged out', '2025-05-29 07:57:43'),
  (10, 2, 'User logged in', '2025-05-29 08:12:04'),
  (11, 2, 'User logged out', '2025-05-29 08:14:40'),
  (12, 1, 'User logged in', '2025-05-29 08:14:46'),
  (13, 1, 'User logged out', '2025-05-29 08:15:40'),
  (14, 1, 'User logged in', '2025-05-29 08:15:50'),
  (15, 1, 'User logged out', '2025-05-29 08:16:04'),
  (16, 1, 'User logged in', '2025-05-29 08:16:09'),
  (
    17,
    1,
    'Created new contest: Binibining Infanta',
    '2025-05-29 08:16:57'
  ),
  (
    18,
    1,
    'Added contestant \'Remiel\' to contest \'Binibining Infanta\'',
    '2025-05-29 08:22:45'
  ),
  (
    19,
    1,
    'Created new contest: Cainta Beauty',
    '2025-05-29 08:23:23'
  ),
  (
    20,
    1,
    'Added contestant \'Matt\' to contest \'Cainta Beauty\'',
    '2025-05-29 08:24:52'
  ),
  (21, 1, 'User logged out', '2025-05-29 10:14:50'),
  (22, 2, 'User logged in', '2025-05-29 10:14:56'),
  (23, 2, 'User logged out', '2025-05-29 10:22:02'),
  (24, 1, 'User logged in', '2025-05-29 10:22:07'),
  (25, 1, 'User logged out', '2025-05-29 10:45:59'),
  (26, 1, 'User logged in', '2025-05-29 11:24:18'),
  (27, 1, 'User logged out', '2025-05-29 12:01:57'),
  (28, 3, 'User logged in', '2025-05-29 12:02:05'),
  (29, 3, 'User logged out', '2025-05-29 12:02:20'),
  (30, 2, 'User logged in', '2025-05-29 12:02:34'),
  (31, 2, 'User logged out', '2025-05-29 12:08:16'),
  (32, 1, 'User logged in', '2025-05-29 12:08:21'),
  (
    33,
    1,
    'Added category \'asdsadasdas\' to contest \'Cainta Beauty\'',
    '2025-05-29 12:32:39'
  ),
  (34, 1, 'User logged out', '2025-05-29 12:39:41'),
  (35, 3, 'User logged in', '2025-05-29 12:39:48'),
  (36, 3, 'User logged out', '2025-05-29 12:40:06'),
  (37, 4, 'User logged in', '2025-05-29 12:40:30'),
  (38, 4, 'User logged out', '2025-05-30 13:04:18'),
  (39, 1, 'User logged in', '2025-05-30 13:05:17'),
  (
    40,
    1,
    'Created new contest: Manila Pageantry',
    '2025-05-30 13:40:05'
  ),
  (
    41,
    1,
    'Added contestant \'John \' (#5) to contest \'Binibining Infanta\'',
    '2025-05-30 14:06:40'
  ),
  (
    42,
    1,
    'Added contestant \'Maria Victoria del Rosario\' (#33) to contest \'Cainta Beauty\'',
    '2025-05-30 14:07:07'
  ),
  (
    43,
    1,
    'Deleted contestant \'Maria Victoria del Rosario\' (#33) from contest \'Cainta Beauty\'',
    '2025-05-30 14:07:25'
  ),
  (
    44,
    1,
    'Added contestant \'jkbh\' (#69) to contest \'Binibining Infanta\'',
    '2025-05-30 15:01:18'
  ),
  (
    45,
    1,
    'Added contestant \'asdad\' (#7) to contest \'Binibining Infanta\'',
    '2025-05-30 15:22:38'
  ),
  (
    46,
    1,
    'Deleted contestant \'Remiel\' (#1) from contest \'Binibining Infanta\'',
    '2025-05-30 15:27:30'
  ),
  (
    47,
    1,
    'Deleted contestant \'jkbh\' (#69) from contest \'Binibining Infanta\'',
    '2025-05-30 15:27:38'
  );

/*!40000 ALTER TABLE logs_table ENABLE KEYS */;

UNLOCK TABLES;

--
-- Table structure for table score_table
--
DROP TABLE IF EXISTS score_table;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
  score_table (
    score_id int NOT NULL,
    fk_score_judge int DEFAULT NULL,
    fk_score_contestant int DEFAULT NULL,
    fk_score_criteria int DEFAULT NULL,
    score_value decimal(10, 0) DEFAULT NULL,
    remarks varchar(45) DEFAULT NULL,
    PRIMARY KEY (score_id),
    KEY fk_score_judge_idx (fk_score_judge),
    KEY fk_score_contestant_idx (fk_score_contestant),
    KEY fk_score_criteria_idx (fk_score_criteria),
    CONSTRAINT fk_score_contestant FOREIGN KEY (fk_score_contestant) REFERENCES contestant_table (contestant_id),
    CONSTRAINT fk_score_criteria FOREIGN KEY (fk_score_criteria) REFERENCES criteria_table (criteria_id),
    CONSTRAINT fk_score_judge FOREIGN KEY (fk_score_judge) REFERENCES judge_table (judge_id)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table score_table
--
LOCK TABLES score_table WRITE;

/*!40000 ALTER TABLE score_table DISABLE KEYS */;

/*!40000 ALTER TABLE score_table ENABLE KEYS */;

UNLOCK TABLES;

--
-- Table structure for table users_table
--
DROP TABLE IF EXISTS users_table;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!50503 SET character_set_client = utf8mb4 */;

CREATE TABLE
  users_table (
    users_id int NOT NULL AUTO_INCREMENT,
    username varchar(45) DEFAULT NULL,
    password varchar(45) DEFAULT NULL,
    userType varchar(45) DEFAULT NULL,
    PRIMARY KEY (users_id)
  ) ENGINE = InnoDB AUTO_INCREMENT = 5 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table users_table
--
LOCK TABLES users_table WRITE;

/*!40000 ALTER TABLE users_table DISABLE KEYS */;

INSERT INTO
  users_table
VALUES
  (
    1,
    'admin',
    '202cb962ac59075b964b07152d234b70',
    'Admin'
  ),
  (
    2,
    'judge',
    '202cb962ac59075b964b07152d234b70',
    'Judge'
  ),
  (
    3,
    'judge123',
    '202cb962ac59075b964b07152d234b70',
    'Judge'
  ),
  (
    4,
    'staff',
    '202cb962ac59075b964b07152d234b70',
    'Staff'
  );

/*!40000 ALTER TABLE users_table ENABLE KEYS */;

UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-30 21:40:34