-- MySQL dump 10.13  Distrib 9.1.0, for Win64 (x86_64)
--
-- Host: localhost    Database: wbss
-- ------------------------------------------------------
-- Server version	9.1.0

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
-- Table structure for table `arbitres`
--

DROP TABLE IF EXISTS `arbitres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `arbitres` (
  `id` int NOT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mot_de_passe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arbitres`
--

LOCK TABLES `arbitres` WRITE;
/*!40000 ALTER TABLE `arbitres` DISABLE KEYS */;
INSERT INTO `arbitres` VALUES (1,'Arbitre 1',NULL),(2,'Arbitre 2',NULL),(3,'Arbitre 3',NULL),(4,'arbitre','passer');
/*!40000 ALTER TABLE `arbitres` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `boxeurs`
--

DROP TABLE IF EXISTS `boxeurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boxeurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pays` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `age` int NOT NULL,
  `classement` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `boxeurs`
--

LOCK TABLES `boxeurs` WRITE;
/*!40000 ALTER TABLE `boxeurs` DISABLE KEYS */;
INSERT INTO `boxeurs` VALUES (1,'Canelo Alvarez','Mexique',33,0),(2,'Oleksandr Usyk','Ukraine',37,0),(3,'Tyson Fury','Royaume-Uni',35,0),(4,'Naoya Inoue','Japon',30,0),(5,'Gervonta Davis','USA',29,0),(6,'Dmitry Bivol','Russie',33,0),(7,'Artur Beterbiev','Canada',39,0),(8,'Teofimo Lopez','USA',26,0),(9,'Muhammad Ali','USA',74,0),(10,'Mike Tyson','USA',57,0),(11,'Floyd Mayweather','USA',46,0),(12,'Manny Pacquiao','Philippines',45,0),(13,'Sugar Ray Leonard','USA',67,0),(14,'Rocky Marciano','USA',45,0),(15,'Joe Frazier','USA',67,0),(16,'Oscar De La Hoya','USA',51,0);
/*!40000 ALTER TABLE `boxeurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matches`
--

DROP TABLE IF EXISTS `matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `boxeur1` varchar(255) DEFAULT NULL,
  `boxeur2` varchar(255) DEFAULT NULL,
  `vainqueur` varchar(255) DEFAULT NULL,
  `mode_victoire` varchar(40) DEFAULT NULL,
  `date_combat` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `phase` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matches`
--

LOCK TABLES `matches` WRITE;
/*!40000 ALTER TABLE `matches` DISABLE KEYS */;
INSERT INTO `matches` VALUES (1,'Canelo Alvarez','Oleksandr Usyk','Oleksandr Usyk','TKO','2025-03-01','11:01:00','8eme'),(2,'Naoya Inoue','Muhammad Ali','Muhammad Ali','Abandon','2025-03-12','12:02:00','8eme'),(3,NULL,NULL,NULL,NULL,NULL,NULL,'8eme'),(4,NULL,NULL,NULL,NULL,NULL,NULL,'8eme'),(5,NULL,NULL,NULL,NULL,NULL,NULL,'8eme'),(6,NULL,NULL,NULL,NULL,NULL,NULL,'8eme'),(7,NULL,NULL,NULL,NULL,NULL,NULL,'8eme'),(8,NULL,NULL,NULL,NULL,NULL,NULL,'8eme'),(9,'Canelo Alvarez','Oleksandr Usyk',NULL,NULL,NULL,NULL,'quart'),(10,NULL,NULL,NULL,NULL,NULL,NULL,'quart'),(11,NULL,NULL,NULL,NULL,NULL,NULL,'quart'),(12,NULL,NULL,NULL,NULL,NULL,NULL,'quart'),(13,NULL,NULL,NULL,NULL,NULL,NULL,'demi'),(14,NULL,NULL,NULL,NULL,NULL,NULL,'demi'),(15,NULL,NULL,NULL,NULL,NULL,NULL,'finale');
/*!40000 ALTER TABLE `matches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stats`
--

DROP TABLE IF EXISTS `stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stats` (
  `id` int NOT NULL AUTO_INCREMENT,
  `boxeur_id` int DEFAULT NULL,
  `victoires` int DEFAULT '0',
  `defaites` int DEFAULT '0',
  `KO` int DEFAULT '0',
  `abandon` int DEFAULT '0',
  `TKO` int DEFAULT '0',
  `nom` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `boxeur_id` (`boxeur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stats`
--

LOCK TABLES `stats` WRITE;
/*!40000 ALTER TABLE `stats` DISABLE KEYS */;
INSERT INTO `stats` VALUES (1,NULL,6,14,4,0,1,'Oleksandr Usyk'),(2,NULL,4,4,4,2,0,'Artur Beterbiev'),(3,NULL,2,11,1,0,1,'Dmitry Bivol'),(4,NULL,7,8,6,1,1,'Naoya Inoue'),(5,NULL,14,6,13,0,0,'Tyson Fury'),(6,NULL,16,4,16,0,0,'Canelo Alvarez'),(7,NULL,2,6,2,0,0,'Teofimo Lopez'),(8,NULL,3,4,3,0,0,'Gervonta Davis'),(9,NULL,6,1,6,0,0,'Manny Pacquiao'),(10,NULL,6,9,5,0,0,'Muhammad Ali'),(11,NULL,1,4,1,0,0,'Mike Tyson'),(12,NULL,7,2,7,0,0,'Floyd Mayweather'),(13,NULL,1,3,1,0,0,'Sugar Ray Leonard'),(14,NULL,4,1,4,0,0,'Rocky Marciano'),(15,NULL,1,2,1,0,0,'Joe Frazier'),(16,NULL,1,2,1,0,0,'Oscar De La Hoya');
/*!40000 ALTER TABLE `stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tournoi`
--

DROP TABLE IF EXISTS `tournoi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tournoi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ronde` int NOT NULL,
  `boxeur1_id` int NOT NULL,
  `boxeur2_id` int DEFAULT NULL,
  `gagnant_id` int DEFAULT NULL,
  `termine` tinyint(1) DEFAULT '0',
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `categorie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `annee` int NOT NULL,
  `arbitre_id` int DEFAULT NULL,
  `date_combat` datetime DEFAULT NULL,
  `type` enum('round','semi_finale','finale') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'round',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tournoi`
--

LOCK TABLES `tournoi` WRITE;
/*!40000 ALTER TABLE `tournoi` DISABLE KEYS */;
INSERT INTO `tournoi` VALUES (1,1,1,4,NULL,0,'','',0,1,'2025-03-23 09:31:00','round'),(2,1,8,7,NULL,0,'','',0,NULL,'2025-03-23 09:31:00','round'),(3,1,2,6,NULL,0,'','',0,NULL,'2025-03-23 09:31:00','round'),(4,1,3,5,NULL,0,'','',0,NULL,'2025-03-23 09:31:00','round');
/*!40000 ALTER TABLE `tournoi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','arbitre') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'0','0','arbitre'),(2,'arbitre','passer','arbitre'),(5,'lamine','passer','admin');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-24 15:10:48
