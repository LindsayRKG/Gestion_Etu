-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: etudiants
-- ------------------------------------------------------
-- Server version	8.4.3

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
-- Table structure for table `bulletins`
--

DROP TABLE IF EXISTS `bulletins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bulletins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int DEFAULT NULL,
  `moyenne` decimal(5,2) DEFAULT NULL,
  `mention` varchar(20) DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `etudiant_id` (`etudiant_id`),
  CONSTRAINT `bulletins_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bulletins`
--

LOCK TABLES `bulletins` WRITE;
/*!40000 ALTER TABLE `bulletins` DISABLE KEYS */;
INSERT INTO `bulletins` VALUES (4,25,1.03,'Passable','2025-01-12 21:49:53'),(6,25,1.03,'Passable','2025-01-12 21:49:53'),(7,47,0.00,'Passable','2025-01-04 18:10:47'),(8,27,0.00,'Passable','2025-01-04 18:15:57'),(9,44,0.00,'Passable','2025-01-04 18:16:33'),(10,76,6.45,'Passable','2025-01-04 18:17:47'),(12,59,0.00,'Passable','2025-01-08 15:02:15'),(13,78,0.00,'Passable','2025-01-12 17:24:11'),(14,79,4.40,'Passable','2025-01-12 21:27:19'),(15,66,0.00,'Passable','2025-01-12 22:06:04');
/*!40000 ALTER TABLE `bulletins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `pension` float NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cours`
--

DROP TABLE IF EXISTS `cours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cours` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `niveau` varchar(50) NOT NULL,
  `classe_id` int DEFAULT NULL,
  `credit` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cours_ibfk_1` (`classe_id`),
  CONSTRAINT `cours_ibfk_1` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cours`
--

LOCK TABLES `cours` WRITE;
/*!40000 ALTER TABLE `cours` DISABLE KEYS */;
INSERT INTO `cours` VALUES (1,'MERISE','B1',NULL,4,'2025-01-02 16:09:27','2025-01-02 16:09:27'),(2,'STATISTIQUES','B1',NULL,3,'2025-01-03 18:52:08','2025-01-03 18:52:08'),(3,'PHP-OO','B2',NULL,4,'2025-01-03 20:43:49','2025-01-03 20:43:49'),(4,'FRANCAIS 1','B1',NULL,3,'2025-01-04 17:04:03','2025-01-04 17:04:03'),(5,'ANGLAIS I','B1',NULL,3,'2025-01-04 17:04:20','2025-01-04 17:04:20'),(6,'CCNA 1 &amp; 2','B1',NULL,4,'2025-01-04 17:04:39','2025-01-04 17:04:39'),(7,'CCNA 2 &amp; 3','B2',NULL,4,'2025-01-04 17:04:59','2025-01-04 17:04:59'),(8,'SQL 2','B2',NULL,4,'2025-01-04 17:05:51','2025-01-04 17:05:51'),(9,'SQL 1','B1',NULL,4,'2025-01-04 17:06:08','2025-01-04 17:06:08'),(10,'PROBABILITES','B2',NULL,4,'2025-01-04 17:06:52','2025-01-04 17:06:52'),(11,'RESEAU 1','B1',NULL,3,'2025-01-09 14:29:37','2025-01-09 14:29:37'),(12,'RESEAU 3','B3',NULL,5,'2025-01-12 17:25:24','2025-01-12 17:25:24');
/*!40000 ALTER TABLE `cours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etudiants`
--

DROP TABLE IF EXISTS `etudiants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `etudiants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule` varchar(15) NOT NULL,
  `nom` varchar(45) NOT NULL,
  `prenom` varchar(45) NOT NULL,
  `dateNaiss` date NOT NULL,
  `Niveau` varchar(2) NOT NULL,
  `Email` varchar(45) NOT NULL,
  `Statut` varchar(15) NOT NULL,
  `dateIns` date NOT NULL,
  `nomPrt` varchar(20) NOT NULL,
  `emailPrt` varchar(45) NOT NULL,
  `pass` varchar(20) NOT NULL,
  `solde` int DEFAULT NULL,
  `total` int DEFAULT NULL,
  `image` longblob,
  `montant_total_verse` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `matricule` (`matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etudiants`
--

LOCK TABLES `etudiants` WRITE;
/*!40000 ALTER TABLE `etudiants` DISABLE KEYS */;
INSERT INTO `etudiants` VALUES (25,'24B1019','lyrddd','blala','2005-02-12','B1','tegbM@gmail.com','Solvable','2024-12-29','ty','lindsayrbhcc@gmail.com','24B1019',1000000,1000000,_binary 'uploads/IMG-20241105-WA0003.jpg',1000000.00),(27,'24B2021','rus','russl','2024-12-09','B2','lll@gmail.com','Insolvable','2024-12-29','BOBOA','mJJJp@gmail.com','24B2021',2000000,2000000,_binary 'uploads/IMG-20241105-WA0003.jpg',2000000.00),(28,'24B1022','lyr','b','2002-02-02','B1','Mael@gmail.com','Insolvable','2024-12-29','MAEL','lindsayrbcc@gmail.com','24B1022',1000000,1000000,_binary 'uploads/IMG-20241105-WA0003.jpg',2000000.00),(29,'24B1023','m','b','2003-12-12','B1','mael@gmail.com','Insolvable','2024-12-29','GAMGA','mp@gmail.com','24B1023',1000000,1000000,_binary 'uploads/IMG-20241105-WA0003.jpg',400000.00),(36,'24B2001','mop','fop','2024-12-10','B2','mael@gmail.com','Insolvable','2024-12-30','BOBO','mp@gmail.com','24B2001',2000000,2000000,_binary 'uploads/test1.jpg',0.00),(37,'24B1001','NMABOU','MABOU','2012-07-05','B1','mael@gmail.com','En cours','2024-12-30','GAMGAB','lindsayrbcc@gmail.com','24B1001',1000000,1000000,_binary 'uploads/test1.jpg',500200.00),(38,'24B1002','NMABOU','MABOU','2012-07-05','B1','mael@gmail.com','Insolvable','2024-12-30','GAMGAB','lindsayrbcc@gmail.com','24B1002',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(39,'24B1003','NMABOU','MABOU','2012-07-05','B1','mael@gmail.com','En cours','2024-12-30','GAMGAB','lindsayrbcc@gmail.com','24B1003',1000000,1000000,_binary 'uploads/test1.jpg',100.00),(40,'24B3001','v','v','2000-02-02','B3','mael@gmail.com','Insolvable','2024-12-30','MAEL','lindsayrbcc@gmail.com','24B3001',3000000,3000000,_binary 'uploads/IMG-20241105-WA0003.jpg',0.00),(41,'24B3002','v','v','2000-02-02','B3','mael@gmail.com','Insolvable','2024-12-30','MAEL','lindsayrbcc@gmail.com','24B3002',3000000,3000000,_binary 'uploads/test1.jpg',0.00),(42,'24B3005','voooooooooooo','v','2000-02-02','B3','mael@gmail.com','Insolvable','2024-12-30','MAEL','lindsayrbcc@gmail.com','24B3005',3000000,3000000,_binary 'uploads/test1.jpg',0.00),(43,'24B1004','ly','nnnnnn','2019-04-30','B1','mael@gmail.com','Insolvable','2024-12-30','N','mp@gmail.com','24B1004',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(44,'24B1005',' nnnn','oooo','2000-12-12','B1','jami@gmail.com','Insolvable','2024-12-30','MAEL','mpm@gmail.com','24B1005',1000000,1000000,_binary 'uploads/IMG-20241105-WA0003.jpg',0.00),(46,'24B3006','m','blala','2024-12-03','B3','jami@gmail.com','Insolvable','2024-12-30','BOBO','lindsayrbcc@gmail.com','24B3006',3000000,3000000,_binary 'uploads/IMG-20241105-WA0003.jpg',0.00),(47,'24B3007','mpppp','blala','2024-12-03','B3','jami@gmail.com','Insolvable','2024-12-30','BOBO','lindsayrbcc@gmail.com','24B3007',3000000,3000000,_binary 'uploads/IMG-20241105-WA0003.jpg',0.00),(49,'24B3010','moppy','foppy','2024-12-04','B3','mael@gmail.com','Insolvable','2024-12-30','MAEL','lindsayrbcc@gmail.com','24B3010',3000000,3000000,_binary 'uploads/test1.jpg',0.00),(50,'24B3011','moppy','foppy','2024-12-04','B3','mael@gmail.com','Insolvable','2024-12-30','MAEL','lindsayrbcc@gmail.com','24B3011',3000000,3000000,_binary 'uploads/test1.jpg',0.00),(51,'24B3012','moppy','foppy','2024-12-04','B3','mael@gmail.com','Insolvable','2024-12-30','MAEL','lindsayrbcc@gmail.com','24B3012',3000000,3000000,_binary 'uploads/test1.jpg',0.00),(52,'24B3013','moppyrrrrr','foppy','2024-12-04','B3','mael@gmail.com','Insolvable','2024-12-30','MAEL','lindsayrbcc@gmail.com','24B3013',3000000,3000000,_binary 'uploads/test1.jpg',0.00),(53,'24B1008','adminppppp','ppppp','2024-12-10','B1','jhgv@h.com','Insolvable','2024-12-30','Noop','lindsayrbcc@gmail.com','24B1008',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(54,'24B1009','adminppppp','pppppn','2024-12-10','B1','jhgv@h.com','Insolvable','2024-12-30','Noop','lindsayrbcc@gmail.com','24B1009',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(55,'24B1010','adminpppppppppp','pppppn','2024-12-10','B1','jhgv@h.com','Insolvable','2024-12-30','Noop','lindsayrbcc@gmail.com','24B1010',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(56,'24B1011','adminppppo','pppppn','2024-12-10','B1','jhgv@h.com','Insolvable','2024-12-30','Noop','lindsayrbcc@gmail.com','24B1011',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(57,'24B1012','FOU','FOU','2013-02-27','B1','mael@gmail.com','Insolvable','2024-12-30','Noop','lindsayrbcc@gmail.com','24B1012',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(58,'24B1013','M','M','2024-12-02','B1','jami@gmail.com','Insolvable','2024-12-30','MAEL','lindsayrbcc@gmail.com','24B1013',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(59,'24B1014','m','blala','2024-12-01','B1','jami@gmail.com','Insolvable','2024-12-30','MAEL','mp@gmail.com','24B1014',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(61,'24B3014','BBB','BBB','2024-12-11','B3','jami@gmail.com','Insolvable','2024-12-30','MAEL','lindsayrbcc@gmail.com','24B3014',3000000,3000000,_binary 'uploads/test1.jpg',0.00),(62,'25B3015','KENGNE','LINDSAY','2019-06-13','B3','lindsayrbcc@gmail.com','Insolvable','2025-01-01','BOBO','lyrebecca@gmail.com','25B3015',3000000,3000000,_binary 'uploads/test1.jpg',0.00),(63,'25B1016','admin','blala','2025-01-05','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-01','b','lyrebecca@gmail.com','25B1016',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(64,'25B3016','m','blala','2024-12-29','B3','lindsayrbcc@gmail.com','Solvable','2025-01-01','BOBO','lyrebecca@gmail.com','25B3016',3000000,3000000,_binary 'uploads/test1.jpg',3000000.00),(65,'25B3017','lolo','Dorian','2018-02-04','B3','lindsayrbcc@gmail.com','Insolvable','2025-01-01','BOBO','lyrebecca@gmail.com','25B3017',3000000,3000000,_binary 'uploads/test1.jpg',0.00),(66,'25B3018','Bla','MABOU','2024-12-29','B3','lyrebecca@gmail.com','Insolvable','2025-01-01','Noop','lindsayrbcc@gmail.com','25B3018',3000000,3000000,_binary 'uploads/test1.jpg',0.00),(67,'25B3019','Blao','MABOU','2024-12-29','B3','lyrebecca@gmail.com','Solvable','2025-01-01','Noop','lindsayrbcc@gmail.com','25B3019',3000000,3000000,_binary 'uploads/test1.jpg',3000000.00),(68,'25B2002','m','blala','2024-12-29','B2','lindsayrbcc@gmail.com','Insolvable','2025-01-01','BOBO','lindsayrbcc@gmail.com','25B2002',2000000,2000000,_binary 'uploads/test1.jpg',0.00),(69,'25B1017','KENGNE','GAMGA','2009-02-16','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-02','MAEL','lindsayrbcc@gmail.com','25B1017',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(70,'25B1018','Bla','blala','2024-12-31','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-02','MAEL','lindsayrbcc@gmail.com','25B1018',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(71,'25B1019','admin','Dorian','2024-12-29','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-02','MAEL','lindsayrbcc@gmail.com','25B1019',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(72,'25B3020','ly','foppy','2024-09-19','B3','lindsayrbcc@gmail.com','Solvable','2025-01-02','BOBOLA','lindsayrbcc@gmail.com','25B3020',3000000,3000000,_binary 'uploads/test1.jpg',3000000.00),(73,'25B1020','ly','Dorian','2024-12-30','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-02','MAEL','lindsayrbcc@gmail.com','25B1020',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(74,'25B1021','A','C','2024-12-29','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-02','MAEL','lindsayrbcc@gmail.com','25B1021',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(75,'25B1022','m','blala','2000-12-12','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-02','BOBO','lindsayrbcc@gmail.com','25B1022',1000000,1000000,_binary 'uploads/test1.jpg',0.00),(76,'25B2003','KENGNE','Tracey','2000-11-03','B2','lindsayrbcc@gmail.com','Insolvable','2025-01-04','PAHO','lindsayrbcc@gmail.com','25B2003',2000000,2000000,_binary 'uploads/businessman-avatar-icone-de-style-plat-2gr2n9h.jpg',0.00),(77,'25B2004','LOWE','KENGNE','2023-11-20','B2','lindsayrbcc@gmail.com','Insolvable','2025-01-09','MAEL','lindsayrbcc@gmail.com','25B2004',2000000,2000000,_binary 'uploads/les-hommes-l-icone-d-avatar-style-plat-2grb646.jpg',0.00),(78,'25B3021','BATOSINE','lolion','2007-06-07','B3','lindsayrbcc@gmail.com','Insolvable','2025-01-09','BATO','lindsayrbcc@gmail.com','25B3021',3000000,3000000,_binary 'uploads/garcon-icone-symbole-avatar-isolated-on-white-w21jbn.jpg',0.00),(79,'25B2005','KENGNE','cd','2006-01-31','B2','lindsayrbcc@gmail.com','En cours','2025-01-11','BOBO','lindsayrbcc@gmail.com','25B2005',2000000,2000000,_binary 'uploads/businessman-avatar-icone-de-style-plat-2gr2n9h.jpg',1500000.00),(80,'25B1025','NOMO','MONO','2007-09-11','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1025',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(81,'25B1026','NOMOP','MONO','2007-09-11','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1026',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(82,'25B1027','LOLO','MONO','2007-09-11','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1027',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(83,'25B1028','LOLOP','MONO','2007-09-11','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1028',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(84,'25B1029','FODOP','MONO','2007-08-28','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1029',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(85,'25B1030','FODOPO','MONO','2007-08-28','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1030',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(86,'25B1031','FODOPOL','MONO','2007-08-28','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1031',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(87,'25B1032','FODOPOLO','MONO','2007-08-28','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1032',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(88,'25B1033','FODOPOLON','MONO','2007-08-28','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1033',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(89,'25B1034','FODOPOLONN','MONO','2007-08-28','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1034',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(90,'25B1035','FODOPOLONNN','MONO','2007-08-28','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1035',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(91,'25B1036','FODOPOLONNNMM','MONO','2007-08-28','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1036',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(92,'25B1037','hol','MONO','2007-08-28','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1037',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00),(93,'25B1038','holnn','MONO','2007-08-28','B1','lindsayrbcc@gmail.com','Insolvable','2025-01-12','MAEL NOMO','lindsayrbcc@gmail.com','25B1038',1000000,1000000,_binary 'uploads/avatar-d-une-equipe-professionnelle-diversifiee-2g14fm4.jpg',0.00);
/*!40000 ALTER TABLE `etudiants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matricule_counter`
--

DROP TABLE IF EXISTS `matricule_counter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matricule_counter` (
  `id` int NOT NULL AUTO_INCREMENT,
  `niveau` varchar(10) NOT NULL,
  `compteur` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matricule_counter`
--

LOCK TABLES `matricule_counter` WRITE;
/*!40000 ALTER TABLE `matricule_counter` DISABLE KEYS */;
INSERT INTO `matricule_counter` VALUES (1,'B1',38),(2,'B2',5),(3,'B3',24);
/*!40000 ALTER TABLE `matricule_counter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `cours_id` int NOT NULL,
  `type_note` enum('CC','TP','Exam','Rattrapage') NOT NULL,
  `valeur` float NOT NULL,
  `annee_scolaire` varchar(9) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `etudiant_id` (`etudiant_id`),
  KEY `cours_id` (`cours_id`),
  CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
INSERT INTO `notes` VALUES (1,25,1,'CC',19,'2024/2025','2025-01-02 23:22:59','2025-01-04 11:02:58'),(2,28,2,'CC',18,'2024/2025','2025-01-03 20:16:56','2025-01-03 20:16:56'),(6,25,2,'CC',16,'2024/2025','2025-01-04 10:59:47','2025-01-04 10:59:47'),(8,76,8,'CC',15,'2024/2025','2025-01-04 17:07:11','2025-01-04 17:07:11'),(9,76,10,'Exam',16,'2024/2025','2025-01-04 17:07:35','2025-01-04 17:07:35'),(10,76,7,'Exam',14,'2024/2025','2025-01-04 17:24:30','2025-01-04 17:24:30'),(11,76,7,'CC',18,'2024/2025','2025-01-04 17:24:45','2025-01-04 17:24:45'),(12,76,7,'TP',18,'2024/2025','2025-01-04 17:25:04','2025-01-04 17:25:04'),(13,79,8,'CC',15,'2024/2025','2025-01-11 20:45:03','2025-01-11 20:45:03'),(14,78,12,'Exam',18,'2024/2025','2025-01-12 17:25:52','2025-01-12 17:25:52'),(15,79,10,'CC',19,'2024/2025','2025-01-12 17:28:12','2025-01-12 17:29:04'),(16,79,7,'Exam',18,'2024/2025','2025-01-12 17:28:35','2025-01-12 17:28:35'),(17,79,7,'CC',18,'2024/2025','2025-01-12 17:28:48','2025-01-12 17:28:48');
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rangs`
--

DROP TABLE IF EXISTS `rangs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rangs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `etudiant_id` int NOT NULL,
  `rang` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `etudiant_id` (`etudiant_id`),
  CONSTRAINT `rangs_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rangs`
--

LOCK TABLES `rangs` WRITE;
/*!40000 ALTER TABLE `rangs` DISABLE KEYS */;
/*!40000 ALTER TABLE `rangs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `password_changed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','admin',0),(2,'25B3021','25B3021',0),(3,'25B1024','$2y$10$sMak32cw433j79WSaxppQuX7UjnbKofeEf5jWOhqUkVrWZH2xZqpe',0),(4,'25B1024','$2y$10$sMak32cw433j79WSaxppQuX7UjnbKofeEf5jWOhqUkVrWZH2xZqpe',0),(5,'25B3024','$2y$10$cXH66WNKnTnQ0KEzYPPbLeB03rDohzlN7N7niVygOCO3ajokoF39m',0),(6,'25B3024','$2y$10$cXH66WNKnTnQ0KEzYPPbLeB03rDohzlN7N7niVygOCO3ajokoF39m',0),(7,'25B2005','$2y$10$oJleIGT2zjFYowxDZxY11eqLLTNQTLdAOtaSxq1Q33q1OKxZjlSja',0),(8,'25B1025','$2y$10$xrrzC3Sx8osJJxFDt308ye7ROyA5rMwjQ/oHgz/i0aNL0Q5pK2Fcq',0),(9,'25B1026','$2y$10$QctIZvRmhWaI07le1SPr1.M8Cb3SlsvGe1bvVttMaQqKGhxLKHKMC',0),(10,'25B1027','$2y$10$5k0Obcj/efVpY44eNYU4Te95lHpc6bvOFn5esjKQxwlkysNwCvHyq',0),(11,'25B1028','$2y$10$zgw/yXIXTF9dpEjZDNohPOUxM5leakOmVvEBHeFNvOzNune2Rlr.e',0),(12,'25B1029','$2y$10$Lyju53Kt68I3xll.S8Ekb.De5ZcBSyQcv3eXy0bGnkvl3HQIkU6r2',0),(13,'25B1030','$2y$10$AAEDrJibENM/1/pw3Dsmj.WvBms1zqOB7Dp5trN/L8x8SPZ7pqkX2',0),(14,'25B1031','$2y$10$E1W6C2rTu./4UgFJhGfFae8Fg.2vBz1s2v4V7BFkqGkMHjALXdKmG',0),(15,'25B1032','$2y$10$KoYrcB.sb4gl6Zpkwu9r2.oyv9DuUJi99UdHUL7Agcl/acLk1Ie2y',0),(16,'25B1033','$2y$10$P6QGmia/aZH7ETgus320AeXStUuK38n8kvYKf0RJQHGZ9L1tC87VK',0),(17,'25B1034','$2y$10$FMzgfxTp6avnsbXHSAQc5ujjMBZ0i1wD6IUCDYPNcK9mf5HsBN0jq',0),(18,'25B1035','$2y$10$H18QwpHAqEyE0MQnFPZXQeANL2LvKOdik33v0WpTYnZSEF9.2zsv2',0),(19,'25B1036','$2y$10$TqqfkyFvVxxO52ZVqIH35eTcwLa2PozZq8FHee74oSY9qT7MbGceO',0),(20,'25B1037','$2y$10$pyg/ALp800ir9lo0HC/qE.lD8i0ffzeXsM5EAM0WrbU5i3kUay8F2',0),(21,'25B1038','$2y$10$OwC1K6wX4Op5nKbH5KYTG.DQaJMD18ZG91O6gZBJCKWlyHk7VU4by',0);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `versements`
--

DROP TABLE IF EXISTS `versements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `versements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricule_etudiant` varchar(50) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `matricule_versement` varchar(100) NOT NULL,
  `date_versement` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `etudiant_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `matricule_etudiant` (`matricule_etudiant`),
  CONSTRAINT `versements_ibfk_1` FOREIGN KEY (`matricule_etudiant`) REFERENCES `etudiants` (`matricule`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `versements`
--

LOCK TABLES `versements` WRITE;
/*!40000 ALTER TABLE `versements` DISABLE KEYS */;
INSERT INTO `versements` VALUES (17,'24B1023',100000.00,'2024-24B1023-001','2024-12-29 16:53:28',0),(18,'24B1023',100000.00,'2024-24B1023-002','2024-12-29 16:53:28',0),(19,'24B1023',100000.00,'2024-24B1023-003','2024-12-29 16:53:37',0),(20,'24B1023',100000.00,'2024-24B1023-004','2024-12-29 16:53:37',0),(21,'24B1022',1000000.00,'2024-24B1022-001','2024-12-29 16:54:38',0),(22,'24B1022',1000000.00,'2024-24B1022-002','2024-12-29 16:54:38',0),(30,'24B1019',500000.00,'2024-24B1019-001','2024-12-29 20:48:09',0),(31,'24B1019',500000.00,'2024-24B1019-002','2024-12-29 20:48:40',0),(33,'24B1001',100000.00,'2024-24B1001-001','2024-12-31 10:04:50',0),(34,'24B1001',100000.00,'2024-24B1001-002','2024-12-31 10:08:28',0),(35,'24B1001',100000.00,'2024-24B1001-003','2024-12-31 10:09:52',0),(36,'24B1001',100000.00,'2024-24B1001-004','2024-12-31 16:55:21',0),(37,'24B1001',100000.00,'2024-24B1001-005','2024-12-31 16:55:58',0),(38,'24B1001',100.00,'2024-24B1001-006','2024-12-31 16:56:28',0),(39,'24B1003',100.00,'2024-24B1003-001','2024-12-31 16:56:56',0),(40,'24B1001',100.00,'2024-24B1001-007','2024-12-31 17:06:29',0),(41,'25B3020',1000000.00,'2024-25B3020-001','2025-01-09 08:34:54',0),(42,'25B3020',2000000.00,'2024-25B3020-002','2025-01-09 08:35:03',0),(43,'25B3019',1000000.00,'2024-25B3019-001','2025-01-09 09:42:03',0),(44,'25B3019',2000000.00,'2024-25B3019-002','2025-01-09 09:42:12',0),(45,'25B3016',1000000.00,'2024-25B3016-001','2025-01-09 10:21:45',0),(46,'25B3016',2000000.00,'2024-25B3016-002','2025-01-09 10:23:20',0),(47,'25B2005',1500000.00,'2024-25B2005-001','2025-01-11 20:44:30',79);
/*!40000 ALTER TABLE `versements` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-15 12:33:30
